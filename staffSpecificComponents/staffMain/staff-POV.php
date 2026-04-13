<?php 
    if(!session_id()){ 
        session_start(); 
    }
    
    // ✨ Safe Database Routing
    $connectPath = __DIR__ . '/../../connect.php';
    if (file_exists($connectPath)) {
        require_once $connectPath; 
    } else {
        die("<h2 style='color:red; text-align:center;'>CRITICAL ERROR: connect.php not found.</h2>");
    }
    
    if (isset($conn) && $conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // Fetch user's full name safely
    if (!isset($_SESSION['fullName']) && isset($_SESSION['accID']) && isset($conn)) {
        $accID = $_SESSION['accID'];
        $query = "SELECT fullName FROM accdatatbl WHERE accID = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
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
        <title>Faculty and Staff Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
        <script>
            if (typeof pdfjsLib !== 'undefined') {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
            }
        </script>

        <link rel="stylesheet" href="STAFFs-POV.css?v=<?php echo time(); ?>">
    </head>

    <body>
        <div class="Sidebar">
            <div class="Sidebar-Logo">
                <img src="../../assets/logos/logo.png" alt="Logo" class="Logo">
                <span class="extended-text" id="extended-text">ASIA<br> PACIFIC<br> COLLEGE<br> </span>
            </div>
            <ul class="Sidebar-Menu">
                <li class="menu-icons" onclick="showPolicyRepository()">
                    <img src="../../assets/policy lib-notClicked.png" alt="Icon 1">
                    <span class="icon-label">Policies Repository</span>
                </li>
                <li class="menu-icons" onclick="showPolicySubmission()">
                    <img src="../../assets/policy create-notClicked.png" alt="Icon 2">
                    <span class="icon-label">Policy Submission</span>
                </li>
                <li class="menu-icons" onclick="showWorkspace()">
                    <img src="../../assets/task manager-notClicked.png" alt="Icon 4">
                    <span class="icon-label">My Workspace</span>
                </li>
                <li class="menu-icons" onclick="showInformation()">
                    <img src="../../assets/info - notClicked.png" alt="Icon 5">
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
                        <?php echo isset($_SESSION['fullName']) ? htmlspecialchars($_SESSION['fullName']) : 'Staff Member'; ?>
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
                try {
                    $notifPath = __DIR__ . '/../../generalComponents/header/Notification-Overlay.php';
                    if(file_exists($notifPath)) include_once $notifPath; 
                } catch (Throwable $e) {} 
            ?>
        </div>

        <div class="Welcome-Panel" id="Welcome-Panel">
            <div class="welcome-text-container">
                <h1 class="welcome-title">Welcome to OPTIQUAL</h1>
                <p class="welcome-subtitle">Faculty & Staff Dashboard</p>
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
                    <label><input type="text" placeholder="Search" id="searchInput"></label>
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
                            
                            $queryParentPols = "SELECT * FROM policytbl WHERE categoryID = " . $row['categoryID'] . " AND policyStatusID = 5";
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
                        
                                    $queryPol = "SELECT * FROM policytbl WHERE categoryID = " . $rowCF['categoryID'] . " AND policyStatusID = 5";
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
                        <button id="pr_prevPage" class="pdf-btn"><i class="fas fa-chevron-left"></i></button>
                        <span class="page-info">Page <span id="pr_pageNum">1</span> of <span id="pr_pageCount">?</span></span>
                        <button id="pr_nextPage" class="pdf-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="pdf-tools-right">
                        <button id="pr_zoomOut" class="pdf-btn"><i class="fas fa-search-minus"></i></button>
                        <span id="pr_zoomLevel">120%</span>
                        <button id="pr_zoomIn" class="pdf-btn"><i class="fas fa-search-plus"></i></button>
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
                    <button type="button" id="cancelBtn">Cancel</button>
                    <button type="submit" id="submitBtn">Submit</button>
                </div>
                </form> 
            </div>
        </div>

        <div class="Workspace-Panel" style="display: none;">
            <?php 
                try {
                    $tmPath = __DIR__ . '/../../generalComponents/taskManager/taskManager.php';
                    if (file_exists($tmPath)) { include_once $tmPath; } 
                    else { echo "<h3 style='color:white; padding:30px; text-align:center;'>Workspace module not yet linked.</h3>"; }
                } catch (Throwable $e) {}
            ?>
        </div>

        <div class="information" style="display: none;">
            <h2 class="info-header"> Guidelines <br> </h2>
            <div class="infoWhite-line" style="display:flex;"></div>

            <div class="moduleCategory" data-category="policyRepository">
                <div class="module-text">Policy Repository</div>
                <i class="fas fa-chevron-right expand-icon"></i>
                <div class="nested-moduleSubcategory-content" style="display: none;">
                    <div class="nested-moduleSubcategory" data-subcategory="about">
                        <h4 style="margin-bottom:1.5vh;font-weight: Bold; margin-top: -0.5vh;">About<br></h4>
                        <div class="nested-blackLine" style="margin-top:1vh; display:flex;"><br></div>
                        <p style="padding-left: 2.5vw; padding-top:4vh; color: black; font-size: 18px; font-weight: normal;">It contains all the policies of Asia Pacific College</p>
                    </div>
                </div>
            </div>
        </div>

        <script src="STAFF-POV.js?v=<?php echo time(); ?>"></script>
    </body>
</html>