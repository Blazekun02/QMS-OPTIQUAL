<?php 
    if(!session_id()){ 
        session_start(); 
    }
    
    // ✨ FIX 1: Use __DIR__ to create absolute paths. This prevents the "Blank Screen on Login" crash!
    $connectPath = __DIR__ . '/../../connect.php';
    if (file_exists($connectPath)) {
        require_once $connectPath; 
    } else {
        echo "<h2 style='color:red; text-align:center;'>CRITICAL ERROR: connect.php not found at $connectPath</h2>";
    }
    
    if (isset($conn) && $conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // Fetch user's full name safely
    if (!isset($_SESSION['fullName']) && isset($_SESSION['accID']) && isset($conn)) {
        $accID = $_SESSION['accID'];
        $query = "SELECT fullName FROM accdatatbl WHERE accID = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) { // Safety check to prevent fatal errors
            $stmt->bind_param("i", $accID);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['fullName'] = $row['fullName'];
            }
            $stmt->close();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quality Assurance Staff</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
        <script>
            if (typeof pdfjsLib !== 'undefined') {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
            }
        </script>

        <link rel="stylesheet" href="QAP-POV.css?v=<?php echo time(); ?>">
    </head>

    <body>
        <div class="Sidebar">
            <div class="Sidebar-Logo">
                <img src="../../assets/logos/logo.png" alt="Logo" class="Logo">
                <span class="extended-text" id="extended-text">ASIA<br> PACIFIC<br> COLLEGE<br> </span>
            </div>
            <ul class="Sidebar-Menu">
                <li class="menu-icons">
                    <img src="../../assets/policy lib-notClicked.png" alt="Icon 1" onclick="showPolicyRepository()">
                    <span class="icon-label">Policies Repository</span>
                </li>
                <li class="menu-icons">
                    <img src="../../assets/policy create-notClicked.png" alt="Icon 2" onclick="showPolicySubmission()">
                    <span class="icon-label">Policy Submission</span>
                </li>
                
                <li class="menu-icons">
                    <img src="../../assets/task manager-notClicked.png" alt="Icon 3" onclick="showWorkspace()">
                    <span class="icon-label">My Workspace</span>
                </li>
                
                <li class="menu-icons">
                    <img src="../../assets/QAP Sidebar/Not Clicked/Role_Manage.png" alt="Icon 6" onclick="showRoleManager()">
                    <span class="icon-label">Manage Roles</span>
                </li>
                <li class="menu-icons">
                    <img src="../../assets/info - notClicked.png" alt="Icon 10" onclick="showInformation()">
                    <span class="icon-label">Information</span>
                </li>
            </ul>
        </div>           
            
        <div class="blue-line">Copyright © 2024 OPTIQUAL. All rights reserved</div>
        <div class="yellow-line"></div>
        
        <div class="top-nav-bar">
            <img src="../../assets/hamburger.jpeg" alt="Menu" class="hamburger-icon" id="hamburger-icon">
            <div class="top-nav-right">
                <button type="button" class="button notif-btn" id="notifButton">
                    <i class="fa fa-bell" style="font-size:24px"></i>
                </button>
                <div class="user-menu-container">
                    <button type="button" class="button user-btn" id="userButton">
                        <i class="fa fa-user-circle" style="font-size:24px"></i>
                        <?php echo isset($_SESSION['fullName']) ? htmlspecialchars($_SESSION['fullName']) : 'QA Staff'; ?>
                    </button>
                    <div class="signOut-overlay" id="signOutOverlay">
                        <div class="signOut-content">
                            Sign out
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="popupOverlay" id="popupOverlay" style="display: none;">
            <?php 
                $notifPath = __DIR__ . '/../../generalComponents/header/Notification-Overlay.php';
                if(file_exists($notifPath)) include_once $notifPath; 
            ?>
        </div>

        <div class="Welcome-Panel" id="Welcome-Panel" style="position: absolute; top: 60px; left: 0.8in; width: calc(100% - 0.9in); height: calc(100vh - 100px); background: linear-gradient(180deg, #fdfdfd 0%, #e0e8f0 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; overflow: hidden; z-index: 1;">
            <div class="welcome-text-container" style="text-align: center; z-index: 10; margin-top: -15vh;">
                <h1 class="welcome-title" style="color: #293A82; font-size: 3.5rem; margin-bottom: 5px; font-family: 'Istok Web', sans-serif; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">Welcome to OPTIQUAL</h1>
                <p class="welcome-subtitle" style="color: #fbaf41; font-size: 1.5rem; font-weight: bold; letter-spacing: 1px; margin: 0;">Quality Assurance Staff Dashboard</p>
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
                if (isset($conn)) {
                    $queryPF = "SELECT * FROM categorytbl WHERE parentCategoryID IS NULL";
                    $resultPF = mysqli_query($conn, $queryPF);
                    
                    if ($resultPF && mysqli_num_rows($resultPF) > 0) {
                        while ($row = mysqli_fetch_assoc($resultPF)) {
                            echo '<div class="Parent-Block">'; 
                            echo '<div class="PR-Parent-Folders" data-id="' . $row['categoryID'] . '">';
                            echo '<p class="PR-Parent-Folder-Name">' . $row['categoryName'] . '</p>';
                            echo '</div>';
                        
                            echo '<div class="child-folders" data-parent-id="' . $row['categoryID'] . '" style="display: none;">'; 
                            
                            $queryParentPols = "SELECT * FROM policytbl WHERE categoryID = " . $row['categoryID'] . " AND policyStatusID = 4";
                            $resultParentPols = mysqli_query($conn, $queryParentPols);
                            if ($resultParentPols && mysqli_num_rows($resultParentPols) > 0) {
                                while ($rowPol = mysqli_fetch_assoc($resultParentPols)) {
                                    echo '<div class="PR-Policies" data-file="' . $rowPol['contentPath'] . '">';
                                    echo '<p class="PR-Policies-Name"><i class="fas fa-file-pdf" style="margin-right:8px; color:#fbaf41;"></i>' . $rowPol['title'] . '</p>';
                                    echo '</div>';
                                }
                            }
    
                            $queryCF = "SELECT * FROM categorytbl WHERE parentCategoryID = " . $row['categoryID'];
                            $resultCF = mysqli_query($conn, $queryCF);
                        
                            if ($resultCF && mysqli_num_rows($resultCF) > 0) {
                                while ($rowCF = mysqli_fetch_assoc($resultCF)) {
                                    echo '<div class="PR-Child-Folders" data-id="' . $rowCF['categoryID'] . '">';
                                    echo '<p class="PR-Child-Folder-Name">' . $rowCF['categoryName'] . '</p>';
                                    echo '</div>';
                        
                                    $queryPol = "SELECT * FROM policytbl WHERE categoryID = " . $rowCF['categoryID'] . " AND policyStatusID = 4";
                                    $resultPol = mysqli_query($conn, $queryPol);
                        
                                    echo '<div class="Policies-Folder" data-pol-id="' .$rowCF['categoryID']. '" style="display: none;">'; 
                            
                                    if ($resultPol && mysqli_num_rows($resultPol) > 0) {
                                        while ($rowPol = mysqli_fetch_assoc($resultPol)) {
                                            echo '<div class="PR-Policies" data-file="' . $rowPol['contentPath'] . '">';
                                            echo '<p class="PR-Policies-Name"><i class="fas fa-file-pdf" style="margin-right:8px; color:#fbaf41;"></i>' . $rowPol['title'] . '</p>';
                                            echo '</div>';
                                        }
                                    }
                                    echo '</div>'; 
                                }
                            }
                            echo '</div>'; 
                            echo '</div>'; 
                        }
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
                    <button id="cancelBtn" type="button">Cancel</button>
                    <button id="submitBtn" type="submit">Submit</button>
                </div>
                </form> 
            </div>
        </div>

        <div class="Workspace-Panel" style="display: none;">
            <?php 
                // Checks for a dedicated Workspace folder first, then falls back to TaskManager
                $workspacePath = __DIR__ . '/../../generalComponents/Workspace/workspace.php';
                $tmPath = __DIR__ . '/../../generalComponents/taskManager/taskManager.php';
                
                if (file_exists($workspacePath)) {
                    include_once $workspacePath;
                } else if (file_exists($tmPath)) {
                    include_once $tmPath;
                } else {
                    echo "<h3 style='color:white; padding:30px; text-align:center;'>Workspace module not yet linked.</h3>";
                }
            ?>
        </div>

        <div class="Role-Manager-Panel" style="display:none;">
            <h1 class="rm-title">Quality Assurance Team Directory</h1>
            
            <div class="rm-controls">
                <div class="rm-search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="find employee" id="rmSearchInput">
                </div>
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

        <script src="QAP-POV.js?v=<?php echo time(); ?>"></script>
    </body>
</html>