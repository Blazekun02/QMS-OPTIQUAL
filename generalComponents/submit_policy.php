<?php
    if (!session_id()){
        session_start();
    }
    include '../connect.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $policyTitle = $_POST['policyTitle'];
        $file = $_FILES['policyFile'];
        $accID  = $_SESSION['accID'];

        // Folder to place the uploaded files
        $targetDir = "uploads";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $maxFileSize = 10 * 1024 * 1024; // 5 MB in bytes

        if ($file["size"] > $maxFileSize) {
            echo "<script>alert('File size exceeds the 10MB limit.');</script>";
            exit();
        }

        $fileName = basename($file["name"]);
        $targetFilePath = $targetDir . $fileName;
        $relativePath = "/qms_optiqual/files/" . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Allowed file types
        $allowedTypes = array('pdf', 'doc', 'docx', 'txt');

        if (in_array($fileType, $allowedTypes)) {
            // Upload Files to Server
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                // Insert into policytbl
                $stmt = $conn->prepare("INSERT INTO policytbl (title, contentPath, policyAuthor) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $policyTitle, $relativePath, $accID);
                if ($stmt->execute()) {
                // Get the last inserted policyID
                $policyID = $conn->insert_id;

                // Insert into tasktbl
                $taskStmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedBy) VALUES (?, ?)");
                $taskStmt->bind_param("ii", $policyID, $accID); // Bind both policyID and accID
                if ($taskStmt->execute()) {
                    // Redirect after successful insertion
                    header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php");
                    exit(); // Ensure no further code is executed after redirect
                } else {
                    echo "❌ Error saving to tasktbl: " . $taskStmt->error;
                }

                $taskStmt->close();
            } else {
                echo "❌ Error saving to policytbl: " . $stmt->error;
            }

                $stmt->close();
            } else {
                echo "<script>alert('Error moving uploaded file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type.');</script>";
        }
    }
    $conn->close();

    // For assignedTo for now it is directly assigned to accID 54 chenge it so that it checks the role of the user which is QAP
?>