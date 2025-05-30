<?php 
    session_start(); // Start the session if not already started
    include '../../connect.php';
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    } else {
        echo "<script>alert('✅ Connected successfully');</script>";
    }

?>

<!DOCTYPE html>
<?php include '../../generalComponents/Refresh/Policy_Repo_Refresh.php';?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quality Assurance Director</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="your-integrity-hash" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                    <span class="icon-label">Process Tracker</span>
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
                <li class="menu-icons">
                    <img src="../../assets/QAP Sidebar/Not Clicked/reports.png" alt="Icon 9">                    
                    <span class="icon-label">Reports</span>
                </li>
                <li>
                    <img src="../QAP Sidebar Images/Not Clicked/Info.png" alt="Icon 10" onclick="showInformation()">
                    <span class="icon-label">Information</span>
                </li>
            </ul>
                 </div>
            </div>           
            

        <div class="blue-line">Copyright © 2024 OPTIQUAL. All rights reserved</div>
        <div class="yellow-line"></div>
        <img src="../QAP Sidebar Images/Not Clicked/OIP.jpeg" alt="Menu" class="hamburger-icon" id="hamburger-icon">
        <div>
            <button type="button" class="button user-btn" id="userButton">
                <i class="fa fa-user-circle" style="font-size:24px"></i>
                <?php echo isset($_SESSION['fullName']) ? htmlspecialchars($_SESSION['fullName']) : 'User'; ?>
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
                        
                            echo '<div class="PR-Parent-Folders" data-id="' . $row['categoryID'] . '">';
                            echo '<p class="PR-Parent-Folder-Name">' . $row['categoryName'] . '</p>';
                            echo '</div>';
                        
                            $queryCF = "SELECT * FROM categorytbl WHERE parentCategoryID = " . $row['categoryID'];
                            $resultCF = mysqli_query($conn, $queryCF);
                        
                            echo '<div class="child-folders" data-parent-id="' . $row['categoryID'] . '" style="display: none;">'; // hide initially
                        
                            if (mysqli_num_rows($resultCF) > 0) {
                                while ($rowCF = mysqli_fetch_assoc($resultCF)) {
                                    echo '<div class="PR-Child-Folders" data-id="' . $rowCF['categoryID'] . '">';
                                    echo '<p class="PR-Child-Folder-Name">' . $rowCF['categoryName'] . '</p>';
                                    echo '</div>';
                        
                                    $queryPol = "SELECT * FROM policytbl WHERE categoryID = " . $rowCF['categoryID'];
                                    $resultPol = mysqli_query($conn, $queryPol);
                        
                                    echo '<div class="Policies-Folder" data-pol-id="' .$rowCF['categoryID']. '" style="display: none;">'; // correct id
                         
                                    if (mysqli_num_rows($resultPol) > 0) {
                                        while ($rowPol = mysqli_fetch_assoc($resultPol)) {
                                          
                                            echo '<div class="PR-Policies" data-file="' . $rowPol['contentPath'] . '">';
                                            echo '<p class="PR-Policies-Name">' . $rowPol['title'] . '</p>';
                                            echo '</div>';
                                        }
                                    }
                                    echo '</div>'; // close Policies-Folder
                                }
                            }
                            echo '</div>'; // close child-folders
                            echo '</div>'; 
                        }
                    }
                    ?>
                </div>

                
        </div>
        <div class="Policy_Repo_pdfViewer" id="Policy_Repo_pdfViewer" style="display:none; width:100%; height:600px; margin-top:20px;">
            <div class="pdfViewer-header">
                <button class="btn" id="closePdfViewer"><i class="fa fa-times"></i></button>
                
            </div>
                    <?php include '../../generalComponents/pdfViewer/pdfViewer.php';?>
        </div>
                    
        <!-- POLICY SUBMISSION -->
        <div class="policy-submission-content" id="policy-submission-content" >
            <div class="policy-submission">
                <h2>Policy Submission</h2>
                <div class="policy-submission-buttons">
                    <button class="btn"><i class="fa fa-download" id=".policy-submission-buttons button:first-child"></i> 
                    <span class=".policy-submission-buttons button:first-child">New Policy Template</span>
                    </button>
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
                <form action="/qms_optiqual/generalComponents/submit_policy.php" method="POST" enctype="multipart/form-data">
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


       


<!-- Department Manager  -->
<div class="Department-Manager-Panel" >
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
        <button id="addDepartmentButton" style=" white-space:nowrap; margin-left:1em; height:2em; width: 4em; margin-top: 0.9em;">+ Add </button>
    </div>
    <div id="departmentListContainer">
    </div>


<div id="overlay"></div>

<div id="assignNameContainer">
    <h2>Assign Name</h2>
    <input type="text" id="departmentNameInput" placeholder="Enter Department Name">
    <div class="assign-name-buttons">
        <button id="cancelAssignName">Cancel</button>
        <button id="confirmAssignName">Confirm</button>
    </div>
</div>

<div id="assignRoleContainer" class="popup-container" style="display: none;">
    <h2>Assign Role</h2>
    <div class="form-group">
        <label for="positionInput">Position</label>
        <input type="text" id="positionInput" placeholder="Enter Position Here">
    </div>
    <div class="form-group">
        <label for="nameInput">Name</label>
        <input type="text" id="nameInput" placeholder="Name will display here" readonly>
    </div>
    <div class="form-group">
        <label for="accountInput">Assign role to account</label>
        <div class="scrollable-account-list">
            <?php
            // Query to fetch accounts from the database
            $query = "SELECT accID, fullName, email FROM accdatatbl";
            $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="account-item" data-account-id="' . $row['accID'] . '">';
                    echo '<input type="radio" id="account-' . $row['accID'] . '" name="selectedAccount" value="' . $row['accID'] . '">';
                    echo '<label for="account-' . $row['accID'] . '">' . $row['fullName'] . ' (' . $row['email'] . ')</label>';
                    echo '</div>';
                }
            } else {
                echo '<p>No accounts available</p>';
            }
            ?>
        </div>
    </div>
    <div class="button-group">
        <button id="cancelAssignRole">Cancel</button>
        <button id="confirmAssignRole">Confirm</button>
    </div>
</div>

<div id="departmentStructureContainer" style="display: none;">
    <h2>Assign Name</h2>
    <div class="form-group">
        <input type="text" id="structureNameInput" placeholder="Enter Name">
    </div>
    <div class="button-group">
        <button id="cancelStructure">Cancel</button>
        <button id="confirmStructure">Confirm</button>
    </div>
</div>

<div id="renameDepartmentContainer" class="popup-container" style="display: none;">
    <h2>Rename Folder</h2>
    <div class="form-group">
        <input type="text" id="renameDepartmentInput" placeholder="Enter New Name">
    </div>
    <div class="button-group">
        <button id="cancelRename">Cancel</button>
        <button id="confirmRenameButton">Confirm</button>
    </div>
</div>


<div id="deleteConfirmationContainer" class="popup-container" style="display: none;">
    <h2>Confirm Deletion?</h2>
    <div class="button-group">
        <button id="cancelDelete">Cancel</button>
        <button id="confirmDelete">Confirm</button>
    </div>
</div>

<div id="renameRoleContainer" class="renameroleContainer" style="display: none;">
    <h2>Rename Role</h2>
    <div class="form-group">
        <input type="text" id="renameRoleInput" placeholder="Enter New Role Name">
    </div>
    <div class="button-group">
        <button id="cancelRenameRole">Cancel</button>
        <button id="confirmRenameRole">Confirm</button>
    </div>
</div>
</div>

    <div class="Process-Tracker-Panel2" style ="display: none;">
        <?php include '../../generalComponents/processTracker/processTracker.php';?>
    </div>

    <div class="Task-Manager-Panel" style="display: block;">
        <?php include '../../generalComponents/taskManager/taskManager.php'; ?>
    </div>






<!-- Policy Manager -->
          <div class="Policy-Manager-Panel" style="display:none;">
            <div class="Policy-Manager-Header">
                <h1>Policy Manager</h1>

                <div class="PM-Search-Container">
                    <label>
                        <input type="text" placeholder="Search" id="searchInput">
                    </label>
                    <button id="searchButton"><i class="fas fa-search"></i></button>
                </div>
                 
          </div>
          <div class="PMP-Divider"></div>
 
        <div class="add-policy-button">
            <button class="add-policy-button">+</button>
        </div>
</div>   

<script src="QAD-POV.js"></script>

</body>
</html>