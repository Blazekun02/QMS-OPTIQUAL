<?php include '../../connect.php';
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    } else {
        echo "<script>alert('✅ Connected successfully');</script>";
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quality Assurance Director</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="QAD-POV.css">
    </head>

    <body>
    
        <!-- Sidebar -->
        <div class="Sidebar">
            <div class="Sidebar-Logo">
                <img src="../QAP Sidebar Images/Not Clicked/logo.png" alt="Logo" class="Logo">
                <span class="extended-text" id="extended-text">ASIA<br> PACIFIC<br> COLLEGE<br> </span>
            </div>
            <ul class="Sidebar-Menu">
                <li class="menu-icons">
                    <img src="../QAP Sidebar Images/Not Clicked/Policy_Repo.png" alt="Icon 1">
                    <span class="icon-label">Policies Repository</span>
                </li>
                <li class="menu-icons">
                    <img src="../QAP Sidebar Images/Not Clicked/Create_Poli.png" alt="Icon 2">
                    <span class="icon-label">Policy Submission</span>
                </li>
                <li class="menu-icons">
                    <img src="../QAP Sidebar Images/Not Clicked/Pro_Track.png" alt="Icon 3">
                    <span class="icon-label">Request Tracker</span>
                </li>
                <li class="menu-icons">
                    <img src="../QAP Sidebar Images/Not Clicked/Task_Manage.png" alt="Icon 4">
                    <span class="icon-label">Task Manager</span>
                </li>
                <li class="menu-icons">
                    <img src="../QAP Sidebar Images/Not Clicked/Role_Manage.png" alt="Icon 6">
                    <span class="icon-label">Manage Roles</span>
                </li>
                <li class="menu-icons">
                    <img src="../QAP Sidebar Images/Not Clicked/QD-Dept_Manage.png" alt="Icon 7" >
                    <span class="icon-label">Department Manager</span>
                </li>
                <li class="menu-icons">
                    <img src="../QAP Sidebar Images/Not Clicked/QD_Policy_Manage.png" alt="Icon 8">
                    <span class="icon-label">Policy Manager</span>
                </li>
                <li>
                    <img src="../QAP Sidebar Images/Not Clicked/Info.png" alt="Icon 9">
                    <span class="icon-label">Information</span>
                </li>
            </ul>
        </div>

        <div class="blue-line"></div>
        <div class="yellow-line"></div>
        <img src="../QAP Sidebar Images/Not Clicked/OIP.jpeg" alt="Menu" class="hamburger-icon" id="hamburger-icon">
        <div>
            <button type="button" class="button user-btn" id="userButton">
                <i class="fa fa-user-circle" style="font-size:24px"></i>
                Name of the user
            </button>
            <button type="button" class="button notif-btn" id="notifButton">
                <i class="fa fa-bell" style="font-size:24px"></i>
            </button>
        </div>

        <div class="popupOverlay" id="popupOverlay" style="display: none;">
            <?php include '../../generalComponents/header/Notification-Overlay.php';?>
            
        </div>

        <div class="signOut-overlay" id="signOutOverlay">
            <div class="signOut-content">
                Sign out
            </div>
        </div>

        

        <!-- Policy Repository --> 
        <div class="policy-repo-content" id="policy-repo-content">
        <div class="Poli-Repo-Header">
                <h1>Policy Repository</h1>

                <div class="PR-Search-Container">
                    <label>
                        <input type="text" placeholder="Search" id="searchInput">
                    </label>
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
        </div>

                <div class="PS-Divider"></div>
                <div class="PR-Folders">
                    <?php
                    $queryPF = "SELECT * FROM categorytbl WHERE parentCategoryID IS NULL" ;
                    $resultPF = mysqli_query($conn, $queryPF);
                    $PRSubfolders = [];
                    if (mysqli_num_rows($resultPF) > 0) {
                        while ($row = mysqli_fetch_assoc($resultPF)) {
                            echo '<div class="Parent-Block">'; // <--- ADD THIS WRAPPER!
                        
                            echo '<div class="PS-Parent-Folders" data-id="' . $row['categoryID'] . '">';
                            echo '<p class="PS-Parent-Folder-Name">' . $row['categoryName'] . '</p>';
                            echo '</div>';
                        
                            $queryCF = "SELECT * FROM categorytbl WHERE parentCategoryID = " . $row['categoryID'];
                            $resultCF = mysqli_query($conn, $queryCF);
                        
                            echo '<div class="child-folders" data-parent-id="' . $row['categoryID'] . '" style="display: none;">'; // hide initially
                        
                            if (mysqli_num_rows($resultCF) > 0) {
                                while ($rowCF = mysqli_fetch_assoc($resultCF)) {
                                    echo '<div class="PS-Child-Folders" data-id="' . $rowCF['categoryID'] . '">';
                                    echo '<p class="PS-Child-Folder-Name">' . $rowCF['categoryName'] . '</p>';
                                    echo '</div>';
                        
                                    $queryPol = "SELECT * FROM policytbl WHERE categoryID = " . $rowCF['categoryID'];
                                    $resultPol = mysqli_query($conn, $queryPol);
                        
                                    echo '<div class="Policies-Folder" data-pol-id="' .$rowCF['categoryID']. '" style="display: none;">'; // correct id
                        
                                    if (mysqli_num_rows($resultPol) > 0) {
                                        while ($rowPol = mysqli_fetch_assoc($resultPol)) {
                                            echo '<div class="PS-Policies">';
                                            echo '<p class="PS-Policies-Name">' . $rowPol['title'] . '</p>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '<div class="PS-Policies">';
                                        echo '<p class="PS-Policies-Name">No policies available</p>';
                                        echo '</div>';
                                    }
                        
                                    echo '</div>'; // close Policies-Folder
                                }
                            }
                        
                            echo '</div>'; // close child-folders
                            echo '</div>'; // <--- CLOSE Parent-Block here!
                        }
                    }
                    

                    ?>
                </div>
        </div>
                    
        <!-- POLICY SUBMISSION -->
        <div class="policy-submission-content" id="policy-submission-content" >
            <div class="policy-submission">
                <h2>Policy Submission</h2>
                <div class="policy-submission-buttons">
                    <button class="btn"><i class="fa fa-download" id=".policy-submission-buttons button:first-child"></i> <span class=".policy-submission-buttons button:first-child">New Policy Template</span></button>
                    <button class="btn" id="submitButton">Submit</button>
        
                </div>
            </div>
        </div>
        
            <div class="confirm-dl" id="confirm-dl">
                <div class= "confirm-popUp">
                    <h2> Confirm Download?</h2>
                    <div class="cf-buttons">
                        <button id="first-child">No</button>
                        <button id="last-child">Yes</button>
                    </div>
                </div>
            </div>

        
        <div class="submit-overlay" id="submitOverlay">
            <div class="submit-popUp">
                <h2>Submission</h2>
                <form action="submit_policy.php" method="POST" enctype="multipart/form-data">
                <div class="submit-field">
                    <p>Policy Title</p>
                </div>
        
            <div class="submit-input">
                <input type="text" name="policyTitle" id="policyTitle" placeholder="Enter policy title" required><br>
                <input type="file" name="policyFile" required style="margin-top:10px;">
            </div>
            <div class="submit-buttons">
                <button id="cancelBtn">Cancel</button>
                <button id="submitBtn">Submit</button>
            </div>
            </form> 
        </div>
        </div>

        <?php
            include '../../connect.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $policyTitle = $_POST['policyTitle'];
                $file = $_FILES['policyFile'];

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
                        $stmt =  $conn->prepare("INSERT INTO policytbl (title, contentPath) VALUES (?, ?)");
                        $stmt -> bind_param("ss", $policyTitle, $targetFilePath);
                        if ($stmt -> execute()) {
                            echo "<script>alert('File uploaded successfully.'); window.location.href='/QMS-OPTIQUAL/QADSpecificComponents/QADMain/QAD-POV.php';</script>";
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

         <!-- Department Manager -->
          <div class="Department-Manager-Panel" style="display: none;">
            <div class="Department-Manager-Header">
                <h1>Department Manager</h1>

                <div class="DM-Search-Container">
                    <label>
                        <input type="text" placeholder="Search" id="searchInput">
                    </label>
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
                 
          </div>
          <div class="DMP-Divider"></div>

          
          <div class="add-department-button">
            <button class="add-department-button">+</button>
    <script src="QAD-POV.js"></script>
    </body>
</html>