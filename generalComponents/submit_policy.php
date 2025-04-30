<?php
    if (!session_id()){
        session_start();
    }
            include '../connect.php';
    
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $policyTitle = $_POST['policyTitle'];
                $file = $_FILES['policyFile'];
                $accID  = $_SESSION['accID'];

                // Foder to place the to be uploaded files
                $targetDir = "../../uploads/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $fileName = basename($file["name"]);
                $targetFilePath = $targetDir . $fileName;
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

                // Allowed file types
                $allowedTypes = array('pdf', 'doc', 'docx', 'txt');

                if (in_array($fileType, $allowedTypes)) {
                    // Upload Files to Server
                    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                        // Insert to Database
                        $stmt =  $conn->prepare("INSERT INTO policytbl (title, contentPath, policyAuthor) VALUES (?, ?, ?)");
                        $stmt -> bind_param("ssi", $policyTitle, $targetFilePath, $accID);
                        if ($stmt -> execute()) {
                            header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php");
                            exit(); // Ensure no further code is executed after redirect
                        } else {
                            echo "❌ Error saving to database: " . $stmt->error;
                        }

                        $stmt -> close();
                    } else {
                        echo "<script>alert('Error moving uploaded file.');</script>";
                    } 
                } else {
                    echo "<script>alert('Invalid file type.');</script>";
                }
            }
            $conn -> close();
        ?>