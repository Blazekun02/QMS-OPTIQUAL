<?php
header('Content-Type: application/json');

// Include database connection
require_once "../connect.php";

// Get the JSON POST body
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['accID']) && isset($data['role'])) {
    $accID = $data['accID'];
    $role = $data['role'];

    // Prepare and execute the update
    $stmt = $conn->prepare("UPDATE accdatatbl SET roleID = ? WHERE accID = ?");
    $stmt->bind_param("ii", $role, $accID); // "ii" stands for integer, integer

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error on updating role."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid input provided."]);
}

$conn->close();
?>