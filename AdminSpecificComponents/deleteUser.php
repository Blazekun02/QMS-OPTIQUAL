<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set back to 0 so HTML doesn't break JSON

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session safely
}

// Include database connection
require_once "../connect.php";

// Get the JSON POST body
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['accID'])) {
    $accID = $data['accID'];

    // Safety Block: Prevent the admin from deleting their own currently logged-in account
    if (isset($_SESSION['accID']) && $_SESSION['accID'] == $accID) {
        echo json_encode(["success" => false, "message" => "Safety block: You cannot delete your own active session account."]);
        exit();
    }

    try {
        // STEP 1: Safely remove OTP records linked to this user (Foreign Key Constraint fix)
        $stmtOTP = $conn->prepare("DELETE FROM otptbl WHERE requestBy = ?");
        if ($stmtOTP) {
            $stmtOTP->bind_param("i", $accID);
            $stmtOTP->execute();
            $stmtOTP->close();
        }

        // STEP 2: Safely remove department assignments linked to this user (Foreign Key Constraint fix)
        $stmtEmp = $conn->prepare("DELETE FROM empperdeptbl WHERE accID = ?");
        if ($stmtEmp) {
            $stmtEmp->bind_param("i", $accID);
            $stmtEmp->execute();
            $stmtEmp->close();
        }

        // STEP 3: Finally, delete the actual account from the system
        $stmt = $conn->prepare("DELETE FROM accdatatbl WHERE accID = ?");
        if ($stmt) {
            $stmt->bind_param("i", $accID);
            $stmt->execute();
            $stmt->close();
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Prepare statement failed: " . $conn->error]);
        }
    } catch (\Throwable $e) {
        // Catch database exceptions (like missing tables or remaining foreign key blocks)
        echo json_encode(["success" => false, "message" => "Database exception: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input provided."]);
}

$conn->close();
?>