<?php
/**
 * rejectTaskBE.php — hardened
 *
 * Changes vs. original:
 *  - Error logging + shutdown handler, same as submit_policy.php, so the
 *    *next* fatal (if any) tells you exactly what broke instead of a
 *    blank 500.
 *  - safePrepare() wrapper so a failed prepare() can't itself become an
 *    uncaught fatal.
 *  - Input validation/casting for policyID (was used raw from JSON).
 *  - Removed the FOREIGN_KEY_CHECKS=0/1 bypass. If a foreign key fails,
 *    that means $authorID, $qadID, or $policyID doesn't actually exist
 *    in its parent table — silently disabling the check just lets
 *    orphaned rows into feedbacktbl/notiftbl. We now log the real
 *    constraint error instead.
 *  - Logic (status reset, task delete, notification, feedback insert)
 *    is otherwise unchanged.
 */

// ── DIAGNOSTIC MODE ──────────────────────────────────────────────
// Since there's no SSH/terminal access to read server-side log files,
// this version puts the real error directly into the JSON response
// itself, so it's visible in the browser console / Network tab no
// matter what. Once the root cause is found, dial this back — don't
// leave raw error detail exposed to the client long-term.

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php_errors_rejectTaskBE.log');
error_reporting(E_ALL);

function emitFatalJson(string $message, string $source) {
    if (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
    }
    echo json_encode([
        'success'      => false,
        'message'      => 'Server error',
        'debug_source' => $source,
        'debug_error'  => $message,
    ]);
}

register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $msg = "{$err['message']} in {$err['file']} on line {$err['line']}";
        error_log("FATAL in rejectTaskBE.php: $msg");
        emitFatalJson($msg, 'shutdown_handler');
    }
});

set_exception_handler(function (Throwable $e) {
    $msg = $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
    error_log("UNCAUGHT EXCEPTION in rejectTaskBE.php: $msg");
    emitFatalJson($msg, 'exception_handler');
    exit;
});

ob_start();
session_start();
include '../../connect.php';

// Force mysqli to surface errors as catchable exceptions consistently,
// regardless of whatever mode connect.php may or may not already set.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function safePrepare(mysqli $conn, string $sql, string $context) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("prepare() failed [$context]: " . $conn->error . " | SQL: $sql");
        if (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Database error ($context)."]);
        $conn->close();
        exit;
    }
    return $stmt;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['policyID']) || !isset($data['reason']) || !is_numeric($data['policyID'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing or invalid data inputs.']);
    exit;
}

$policyID = (int)$data['policyID'];
$reason   = trim((string)$data['reason']);
$qadID    = (int)($_SESSION['accID'] ?? 0);

if ($qadID <= 0) {
    error_log("rejectTaskBE.php called with no valid accID in session (qadID=$qadID) — feedbacktbl insert would violate FK on remarksBy.");
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Session expired or invalid. Please log in again.']);
    exit;
}

try {
    // 1. Get the Author of the policy so we can notify them
    $getAuthor = safePrepare($conn, "SELECT policyAuthor, title FROM policytbl WHERE policyID = ?", "author lookup");
    $getAuthor->bind_param("i", $policyID);
    $getAuthor->execute();
    $authorResult = $getAuthor->get_result();

    if ($authorResult->num_rows > 0) {
        $row = $authorResult->fetch_assoc();
        $authorID    = (int)$row['policyAuthor'];
        $policyTitle = $row['title'];

        if ($authorID <= 0) {
            error_log("policytbl.policyAuthor is invalid/0 for policyID=$policyID — notiftbl/feedbacktbl inserts referencing this author would violate FK constraints.");
        }

        // 2. Reset the policy's review/verify/approve trail
        $updatePolicy = safePrepare($conn, "
            UPDATE policytbl
            SET policyStatusID = 6, policyReviewer = NULL, dateReviewed = NULL,
                policyVerifier = NULL, dateVerified = NULL,
                policyApprover = NULL, dateApproved = NULL
            WHERE policyID = ?
        ", "policy status reset");
        $updatePolicy->bind_param("i", $policyID);
        $updatePolicy->execute();
        $updatePolicy->close();

        // 3. Remove the task from the current user's inbox
        $deleteTask = safePrepare($conn, "DELETE FROM tasktbl WHERE policyAssigned = ?", "task delete");
        $deleteTask->bind_param("i", $policyID);
        $deleteTask->execute();
        $deleteTask->close();

        // 4. Notify the author. dateTimeRead intentionally omitted -> column
        //    must allow NULL (run: ALTER TABLE notiftbl MODIFY dateTimeRead
        //    DATETIME NULL DEFAULT NULL;) since "unread" is a null state.
        $notifMessage = "Your document '$policyTitle' was returned. Reason: $reason";
        $notifStmt = safePrepare($conn, "INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)", "notification insert");
        $notifStmt->bind_param("is", $authorID, $notifMessage);
        $notifStmt->execute();
        $notifStmt->close();

        // 5. Feedback entry — no FK bypass. If this throws, the caught
        //    exception below will say exactly which constraint failed
        //    (remarksOn -> policytbl, or remarksBy -> accdatatbl).
        $feedbackStmt = safePrepare($conn, "
            INSERT INTO feedbacktbl (remarksOn, remarksBy, content, fbType, dateSubmitted)
            VALUES (?, ?, ?, 2, CURDATE())
        ", "feedback insert");
        $feedbackStmt->bind_param("iis", $policyID, $qadID, $reason);
        $feedbackStmt->execute();
        $feedbackStmt->close();

        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'message' => 'Policy not found.'];
    }

    $getAuthor->close();
    $conn->close();

    if (ob_get_level()) { ob_end_clean(); }
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (mysqli_sql_exception $e) {
    // This is almost certainly what's been causing the 500s. The message
    // below will tell you the exact table/column/constraint at fault.
    error_log("mysqli_sql_exception in rejectTaskBE.php: " . $e->getMessage());
    emitFatalJson($e->getMessage(), 'mysqli_sql_exception');
} catch (Throwable $e) {
    error_log("Throwable in rejectTaskBE.php: " . $e->getMessage());
    emitFatalJson($e->getMessage(), 'generic_throwable');
}