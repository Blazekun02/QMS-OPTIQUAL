<?php
    if (!session_id()){
        session_start();
    }
    include '../connect.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $policyTitle = $_POST['policyTitle'];
        $file = $_FILES['policyFile'];
        $accID  = $_SESSION['accID'];

        // ✨ FIX 1: Point the physical upload to the correct "files" folder
        // Using ../ goes up one level from generalComponents to qms_optiqual
        $targetDir = "../files/"; 
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $maxFileSize = 10 * 1024 * 1024; // 10 MB in bytes

        if ($file["size"] > $maxFileSize) {
            echo "<script>alert('File size exceeds the 10MB limit.');</script>";
            exit();
        }

        // ✨ FIX 2: Clean the filename to prevent spaces from breaking the URL
        $fileName = basename($file["name"]);
        $cleanFileName = str_replace(' ', '_', $fileName); // Replaces spaces with underscores
        
        // The physical path where the file is moved on the server
        $targetFilePath = $targetDir . $cleanFileName;
        
        // The URL path saved to the database for the iframe to read
        $relativePath = "/qms_optiqual/files/" . $cleanFileName;
        
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Allowed file types
        $allowedTypes = array('pdf', 'doc', 'docx', 'txt');

        
        if (in_array($fileType, $allowedTypes)) {
            // Upload Files to Server
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                
                // Explicitly set categoryID to NULL for new submissions
                $stmt = $conn->prepare("INSERT INTO policytbl (title, contentPath, policyAuthor, categoryID) VALUES (?, ?, ?, NULL)");
                $stmt->bind_param("ssi", $policyTitle, $relativePath, $accID);
                
                if ($stmt->execute()) {
                    // Get the last inserted policyID
                    $policyID = $conn->insert_id;

                    // Insert into tasktbl
                    $taskStmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedBy) VALUES (?, ?)");
                    $taskStmt->bind_param("ii", $policyID, $accID); 
                    
                    if ($taskStmt->execute()) {
                        // Redirect after successful insertion
                        header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php");
                        exit(); 
                    } else {
                        echo "❌ Error saving to tasktbl: " . $taskStmt->error;
                    }
                    $taskStmt->close();

                } else {
                    echo "❌ Error saving to policytbl: " . $stmt->error;
                }
                $stmt->close();
                
            } else {
                echo "<script>alert('Error moving uploaded file. Check folder permissions.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only PDF, DOC, DOCX, and TXT are allowed.');</script>";
        }
    }
    $conn->close();
?>