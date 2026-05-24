<?php 
    session_start(); // Start the session if not already started
    include '../../connect.php';
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // Fetch user's full name if not already set in session
    if (!isset($_SESSION['fullName']) && isset($_SESSION['accID'])) {
        $accID = $_SESSION['accID'];
        $query = "SELECT fullName FROM accdatatbl WHERE accID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $accID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['fullName'] = $row['fullName'];
        }
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
        </script>

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
        <div class="top-nav-bar">
            <img src="../QAP Sidebar Images/Not Clicked/OIP.jpeg" alt="Menu" class="hamburger-icon" id="hamburger-icon">
            <div class="top-nav-right">
                <button type="button" class="button notif-btn" id="notifButton">
                    <i class="fa fa-bell" style="font-size:24px"></i>
                </button>
                <div class="user-menu-container">
                    <button type="button" class="button user-btn" id="userButton">
                        <i class="fa fa-user-circle" style="font-size:24px"></i>
                        <?php echo isset($_SESSION['fullName']) ? htmlspecialchars($_SESSION['fullName']) : 'User'; ?>
                    </button>
                    <div class="signOut-overlay" id="signOutOverlay">
                        <div class="signOut-content" onclick="window.location.href='/qms_optiqual/generalComponents/logout.php'" style="cursor: pointer;">
                        <div class="signOut-content" onclick="window.location.href='/qms_optiqual/auth/log_out/logout.php'" style="cursor: pointer;">
                            Sign out
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="popupOverlay" id="popupOverlay" style="display: none;">
            <?php include '../../generalComponents/header/Notification-Overlay.php';?>
            
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
                    $queryPF = "SELECT * FROM categorytbl WHERE parentCategoryID IS NULL";
                    $resultPF = mysqli_query($conn, $queryPF);
                    
                    if (mysqli_num_rows($resultPF) > 0) {
                        while ($row = mysqli_fetch_assoc($resultPF)) {
                            echo '<div class="Parent-Block">'; 
                        
                            echo '<div class="PR-Parent-Folders" data-id="' . $row['categoryID'] . '">';
                            echo '<p class="PR-Parent-Folder-Name">' . $row['categoryName'] . '</p>';
                            echo '</div>';
                        
                            echo '<div class="child-folders" data-parent-id="' . $row['categoryID'] . '" style="display: none;">'; 
                            
                            // ✨ FIX: Check for policies attached DIRECTLY to this Parent Folder!
                            $queryParentPols = "SELECT * FROM policytbl WHERE categoryID = " . $row['categoryID'] . " AND policyStatusID = 5";
                            $resultParentPols = mysqli_query($conn, $queryParentPols);
                            if (mysqli_num_rows($resultParentPols) > 0) {
                                while ($rowPol = mysqli_fetch_assoc($resultParentPols)) {
                                    echo '<div class="PR-Policies" data-file="' . $rowPol['contentPath'] . '">';
                                    echo '<p class="PR-Policies-Name"><i class="fas fa-file-pdf" style="margin-right:8px; color:#fbaf41;"></i>' . $rowPol['title'] . '</p>';
                                    echo '</div>';
                                }
                            }

                            // Now check for Child Folders
                            $queryCF = "SELECT * FROM categorytbl WHERE parentCategoryID = " . $row['categoryID'];
                            $resultCF = mysqli_query($conn, $queryCF);
                        
                            if (mysqli_num_rows($resultCF) > 0) {
                                while ($rowCF = mysqli_fetch_assoc($resultCF)) {
                                    echo '<div class="PR-Child-Folders" data-id="' . $rowCF['categoryID'] . '">';
                                    echo '<p class="PR-Child-Folder-Name">' . $rowCF['categoryName'] . '</p>';
                                    echo '</div>';
                        
                                    $queryPol = "SELECT * FROM policytbl WHERE categoryID = " . $rowCF['categoryID'] . " AND policyStatusID = 5";
                                    $resultPol = mysqli_query($conn, $queryPol);
                        
                                    echo '<div class="Policies-Folder" data-pol-id="' .$rowCF['categoryID']. '" style="display: none;">'; 
                            
                                    if (mysqli_num_rows($resultPol) > 0) {
                                        while ($rowPol = mysqli_fetch_assoc($resultPol)) {
                                            echo '<div class="PR-Policies" data-file="' . $rowPol['contentPath'] . '">';
                                            echo '<p class="PR-Policies-Name"><i class="fas fa-file-pdf" style="margin-right:8px; color:#fbaf41;"></i>' . $rowPol['title'] . '</p>';
                                            echo '</div>';
                                        }
                                    }
                                    echo '</div>'; // close Policies-Folder
                                }
                            }
                            echo '</div>'; // close child-folders
                            echo '</div>'; // close Parent-Block
                        }
                    }
                    ?>
                </div>

                
        </div>
        <div class="Policy_Repo_pdfViewer" id="Policy_Repo_pdfViewer" style="display:none;">
            
            <div class="introduction-header" style="border-bottom: 2px solid white; padding-bottom: 15px; margin-bottom: 15px; display: flex; align-items: center;">
                <button id="closePdfViewer" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; margin-right: 15px; transition: color 0.2s;">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <span class="introduction-title" style="color: white; font-size: 28px; font-weight: bold;">Policy Viewer</span>
            </div>

            <div class="pdf-container-wrapper" style="display: flex; flex-direction: column; flex-grow: 1; background-color: white; border-radius: 8px; overflow: hidden;">
                
                <div class="custom-pdf-toolbar" style="display: flex; justify-content: space-between; align-items: center; background-color: #343A40; color: white; padding: 10px 20px; border-radius: 8px 8px 0 0;">
                    <div class="pdf-tools-left">
                        <button id="pr_prevPage" class="pdf-btn" style="background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer;"><i class="fas fa-chevron-left"></i></button>
                        <span class="page-info" style="margin: 0 15px; font-size: 14px; font-family: 'Istok Web', sans-serif;">Page <span id="pr_pageNum">1</span> of <span id="pr_pageCount">?</span></span>
                        <button id="pr_nextPage" class="pdf-btn" style="background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer;"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="pdf-tools-right">
                        <button id="pr_zoomOut" class="pdf-btn" style="background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer;"><i class="fas fa-search-minus"></i></button>
                        <span id="pr_zoomLevel" style="margin: 0 15px; font-size: 14px; font-family: 'Istok Web', sans-serif;">120%</span>
                        <button id="pr_zoomIn" class="pdf-btn" style="background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer;"><i class="fas fa-search-plus"></i></button>
                    </div>
                </div>

                <div class="pdf-canvas-container" style="background-color: #525659; height: 68vh; overflow: auto; display: block; text-align: center; padding: 20px 0; border-radius: 0 0 8px 8px;">
                    <canvas id="pr_pdfCanvas" style="box-shadow: 0 4px 8px rgba(0,0,0,0.5); margin: 0 auto;"></canvas>
                </div>
            </div>
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

        <div id="departmentListContainer"></div>

        <button class="pm-big-add-btn" id="addDepartmentButton" style="margin-top: 20px;">
            <i class="fas fa-plus"></i>
        </button>


        <div id="overlay"></div>

        <div id="assignNameContainer" class="pm-modal-container" style="display: none;">
            <h2 style="font-size: 32px; margin-bottom: 20px;">Assign Name</h2>
            <input type="text" id="departmentNameInput" placeholder="Enter Department Name" style="width: 100%; padding: 12px; border-radius: 8px; border: none; margin-bottom: 25px; box-sizing: border-box;">
            <div class="pm-modal-buttons">
                <button id="cancelAssignName" class="cancel-btn">Cancel</button>
                <button id="confirmAssignName" class="confirm-btn">Confirm</button>
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

    <div class="Task-Manager-Panel" style="display: none;">
        <?php include '../../generalComponents/taskManager/taskManager.php'; ?>
    </div>
    
<!-- Policy Manager -->

   <div class="Policy-Manager-Panel" style="display:none;">
        <div class="pm-outer-wrapper">
            <div class="pm-inner-card">
                
                <div class="Policy-Manager-Header">
                    <h1 class="PM-Title">Policies Manager</h1>
                    <div class="PM-Search-Container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" placeholder="search" id="pmSearchInput">
                    </div>
                </div>
                
                <div class="PMP-Divider"></div>
                
                <div id="pmFoldersContainer"></div>
                
                <button class="pm-big-add-btn" id="pmAddPolicyBtn">
                    <i class="fas fa-plus"></i>
                </button>

            </div>
        </div>
    </div>   

    <div id="pmCreateFolderModal" class="pm-modal-container" style="display: none;">
        <h2>Assign Name</h2>
        <input type="text" id="pmFolderNameInput" placeholder="Folder Name">
        <div class="pm-modal-buttons">
            <button id="pmCancelCreateFolder" class="cancel-btn">Cancel</button>
            <button id="pmConfirmCreateFolder" class="confirm-btn">Confirm</button>
        </div>
    </div>

    <div id="pmRenameFolderModal" class="pm-modal-container" style="display: none;">
        <h2>Rename Folder</h2>
        <input type="text" id="pmRenameInput" placeholder="New Folder Name">
        <div class="pm-modal-buttons">
            <button id="pmCancelRename" class="cancel-btn">Cancel</button>
            <button id="pmConfirmRename" class="confirm-btn">Confirm</button>
        </div>
    </div>

    <div id="pmDeleteFolderModal" class="pm-modal-container" style="display: none;">
        <h2>Delete Folder?</h2>
        <p style="margin-bottom: 20px;">Are you sure? This cannot be undone.</p>
        <div class="pm-modal-buttons">
            <button id="pmCancelDelete" class="cancel-btn">Cancel</button>
            <button id="pmConfirmDelete" class="confirm-btn" style="background-color: #f44336; color: white;">Delete</button>
        </div>
    </div>

    <div id="pmAddFileModal" class="pm-modal-container" style="display: none;">
        <h2>Add Policy</h2>
        <p style="margin-bottom: 15px; font-size: 14px; color: #d3d3d3;">Only policies with status 'Approved' are available.</p>
        
        <select id="pmPolicySelect" style="width: 100%; padding: 12px; border-radius: 10px; margin-bottom: 25px; font-size: 16px; color: black;">
            <option value="">Loading policies...</option>
        </select>
        
        <div class="pm-modal-buttons">
            <button id="pmCancelAddFile" class="cancel-btn">Cancel</button>
            <button id="pmConfirmAddFile" class="confirm-btn">Add</button>
        </div>
    </div>

    <div id="pmRemovePolicyModal" class="pm-modal-container" style="display: none;">
        <h2>Remove Policy?</h2>
        <p style="margin-bottom: 20px;">This will remove the policy from the folder and return it to the 'Approved' list.</p>
        <div class="pm-modal-buttons">
            <button id="pmCancelRemovePolicy" class="cancel-btn">Cancel</button>
            <button id="pmConfirmRemovePolicy" class="confirm-btn" style="background-color: #f44336; color: white;">Remove</button>
        </div>
    </div>

    <!-- Role Manager Panel -->

    <div class="Role-Manager-Panel" style="display:none;">
    <h1 class="rm-title">Quality Assurance Team Manager</h1>
    
    <div class="rm-controls">
        <div class="rm-search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="find employee" id="rmSearchInput">
        </div>
        <button class="rm-icon-btn" id="rmAddRoleBtn" title="Add User"><i class="fas fa-user-plus"></i></button>
        
        <button class="rm-icon-btn rm-delete-btn" id="rmDeleteRoleBtn" title="Remove Users"><i class="fas fa-trash-alt"></i></button>
        
        <button class="rm-icon-btn" id="rmConfirmDeleteBtn" title="Confirm Removal" style="display: none; color: #4CAF50;"><i class="fas fa-check-circle"></i></button>
        <span id="rmDeleteInstruction" style="display: none; color: #f44336; font-family: 'Istok Web', sans-serif; font-weight: bold; margin-left: 10px; font-size: 16px;">Select users to be removed.</span>
    </div>
    <div class="rm-grid-container" id="rmGridContainer"></div>
</div>

<div id="rmAddUserModal" class="pm-modal-container" style="display: none;">
    <h2 class="rm-modal-title">Add to Team</h2>
    
    <div class="rm-input-group">
        <label class="rm-input-label">Name</label>
        <select id="rmUserSelectInput" class="rm-modal-input rm-modal-select">
            <option value="" disabled selected>Select an employee...</option>
        </select>
    </div>
    
    <div class="rm-input-group rm-input-group-last">
        <label class="rm-input-label">Email</label>
        <input type="email" id="rmUserEmailInput" class="rm-modal-input rm-readonly-input" placeholder="Email will automatically fill..." readonly>
    </div>

    <div class="pm-modal-buttons">
        <button id="rmCancelAddUser" class="cancel-btn">Cancel</button>
        <button id="rmConfirmAddUser" class="confirm-btn">Confirm</button>
    </div>
</div>

<div class="Welcome-Panel" id="Welcome-Panel">
            
            <div class="welcome-text-container">
                <h1 class="welcome-title">Welcome to OPTIQUAL</h1>
                <p class="welcome-subtitle">Quality Assurance Director Dashboard</p>
            </div>

            <div class="mountain-layer back-mountain"></div>
            <div class="mountain-layer mid-mountain"></div>
            <div class="mountain-layer front-mountain"></div>

            <div class="ram-container">
                <svg viewBox="0 0 120 100" class="ram-svg">
                    <circle cx="20" cy="45" r="6" fill="#343A40" />
                    <line x1="45" y1="65" x2="35" y2="90" class="leg back-leg" />
                    <line x1="75" y1="60" x2="65" y2="85" class="leg back-leg" />
                    <ellipse cx="50" cy="50" rx="35" ry="22" fill="#343A40" />
                    <line x1="35" y1="65" x2="45" y2="90" class="leg front-leg" />
                    <line x1="65" y1="65" x2="75" y2="90" class="leg front-leg" />
                    <circle cx="85" cy="38" r="14" fill="#343A40" />
                    <path d="M 85 30 C 105 15, 115 45, 90 50 C 80 52, 75 40, 80 35" fill="none" stroke="#fbaf41" stroke-width="5" stroke-linecap="round"/>
                </svg>
            </div>
            
        </div>

<script src="QAD-POV.js"></script>

</body>
</html>