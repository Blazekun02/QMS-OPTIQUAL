<?php
session_start();
include '../../connect.php';
header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$accID = isset($_SESSION['accID']) ? (int)$_SESSION['accID'] : 0;

$data = [
    'success' => true,
    'kpi' => [
        'active' => 0,
        'pending' => 0,
        'rejected' => 0,
        'feedbacks' => 0
    ],
    'notifications' => [
        'unreadCount' => 0,
        'unreadHtml' => '',
        'readHtml' => ''
    ],
    'taskState' => 0
];

// Task State Checksum (for QAP and Staff auto-refresh without HTML downloads)
$res = $conn->query("SELECT (IFNULL(SUM(taskStatus), 0) + COUNT(*)) as ts FROM tasktbl");
if ($res && $row = $res->fetch_assoc()) $data['taskState'] = (int)$row['ts'];

// Active Policies
$res = $conn->query("SELECT COUNT(policyID) as cnt FROM policytbl WHERE policyStatusID IN (4, 5)");
if ($res && $row = $res->fetch_assoc()) $data['kpi']['active'] = (int)$row['cnt'];

// Pending Tasks
$res = $conn->query("SELECT COUNT(taskID) as cnt FROM tasktbl WHERE taskStatus = 0");
if ($res && $row = $res->fetch_assoc()) $data['kpi']['pending'] = (int)$row['cnt'];

// Rejected Policies
$res = $conn->query("SELECT COUNT(policyID) as cnt FROM policytbl WHERE policyStatusID = 6");
if ($res && $row = $res->fetch_assoc()) $data['kpi']['rejected'] = (int)$row['cnt'];

// Number of Feedbacks
$res = $conn->query("SELECT COUNT(feedbackID) as cnt FROM feedbacktbl");
if ($res && $row = $res->fetch_assoc()) $data['kpi']['feedbacks'] = (int)$row['cnt'];

// Unread Notifications for the user
if ($accID > 0) {
    // Unread Count & HTML
    $queryUnread = "SELECT * FROM notiftbl WHERE notifStatus = 0 AND receivedBy = $accID ORDER BY dateTimeSent DESC";
    $resultUnread = $conn->query($queryUnread);
    $unreadHtml = '';
    $unreadCount = 0;
    if ($resultUnread && $resultUnread->num_rows > 0) {
        $unreadCount = $resultUnread->num_rows;
        while ($row = $resultUnread->fetch_assoc()) {
            $msg = htmlspecialchars((string)$row['message']);
            $notifID = $row['notifID'];
            $unreadHtml .= "<div class='notification-item unread' data-id='$notifID' onclick='handleNotificationClick(this)' style='border-left: 4px solid #fbaf41; cursor: pointer;'><p style='margin:0; font-weight:bold;'>$msg</p></div>";
        }
    } else {
        $unreadHtml = "<p class='no-notifications'><i class='fas fa-bell-slash' style='display:block; font-size:24px; margin-bottom:10px; opacity:0.5;'></i>No unread notifications.</p>";
    }
    $data['notifications']['unreadCount'] = $unreadCount;
    $data['notifications']['unreadHtml'] = $unreadHtml;

    // Read HTML
    $queryRead = "SELECT * FROM notiftbl WHERE notifStatus = 1 AND receivedBy = $accID ORDER BY dateTimeSent DESC";
    $resultRead = $conn->query($queryRead);
    $readHtml = '';
    if ($resultRead && $resultRead->num_rows > 0) {
        while ($row = $resultRead->fetch_assoc()) {
            $msg = htmlspecialchars((string)$row['message']);
            $notifID = $row['notifID'];
            $readHtml .= "<div class='notification-item' data-id='$notifID' onclick='handleNotificationClick(this)' style='background-color:#555; border-left: 4px solid transparent; cursor: pointer;'><p style='margin:0; color:#d3d3d3;'>$msg</p></div>";
        }
    } else {
        $readHtml = "<p class='no-notifications'><i class='fas fa-envelope-open' style='display:block; font-size:24px; margin-bottom:10px; opacity:0.5;'></i>No read notifications.</p>";
    }
    $data['notifications']['readHtml'] = $readHtml;
}

echo json_encode($data);
?>
