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
        <style>
            /* Visually center the sidebar icons and spread them out */
            .Sidebar-Menu {
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                gap: 10vh !important; /* Adjust this value to increase or decrease the gap */
                height: calc(100vh - 100px) !important; /* Constrain to the remaining height below the logo */
            }
            .Sidebar-Menu li.menu-icons {
                height: auto !important; /* Prevent the default 100% height stretching from breaking alignment */
                width: 100%;
            }
        </style>
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
                        <div class="signOut-content" onclick="window.location.href='/qms_optiqual/auth/log_out/logout.php'" style="cursor: pointer;">
                        <div class="signOut-content" onclick="window.location.href='../../auth/log_out/logout.php'" style="cursor: pointer;">
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
                    <label><input type="text" placeholder="Search" id="searchInput">
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
                            echo '<p class="PR-Parent-Folder-Name"><i class="fas fa-caret-right folder-toggle-icon"></i> ' . $row['categoryName'] . '</p>';
                            echo '</div>';
                        
                            echo '<div class="child-folders" data-parent-id="' . $row['categoryID'] . '" style="display: none;">'; 
                            
                            $queryParentPols = "SELECT * FROM policytbl WHERE categoryID = " . $row['categoryID'] . " AND policyStatusID >= 4 AND policyStatusID != 6";
                            $resultParentPols = mysqli_query($conn, $queryParentPols);
                            if ($resultParentPols && mysqli_num_rows($resultParentPols) > 0) {
                                while ($rowPol = mysqli_fetch_assoc($resultParentPols)) {
                                    echo '<div class="PR-Policies" data-id="' . $rowPol['policyID'] . '" data-file="' . $rowPol['contentPath'] . '" data-upload-date="' . ($rowPol['dateUploaded'] ?? '') . '">';
                                    echo '<p class="PR-Policies-Name"><i class="fas fa-file-pdf" style="margin-right:8px; color:#fbaf41;"></i>' . $rowPol['title'] . '</p>';
                                    echo '</div>';
                                }
                            }
    
                            $queryCF = "SELECT * FROM categorytbl WHERE parentCategoryID = " . $row['categoryID'];
                            $resultCF = mysqli_query($conn, $queryCF);
                        
                            if ($resultCF && mysqli_num_rows($resultCF) > 0) {
                                while ($rowCF = mysqli_fetch_assoc($resultCF)) {
                                    echo '<div class="PR-Child-Folders" data-id="' . $rowCF['categoryID'] . '">';
                                    echo '<p class="PR-Child-Folder-Name"><i class="fas fa-caret-right folder-toggle-icon"></i> ' . $rowCF['categoryName'] . '</p>';
                                    echo '</div>';
                        
                                    $queryPol = "SELECT * FROM policytbl WHERE categoryID = " . $rowCF['categoryID'] . " AND policyStatusID >= 4 AND policyStatusID != 6";
                                    $resultPol = mysqli_query($conn, $queryPol);
                        
                                    echo '<div class="Policies-Folder" data-pol-id="' .$rowCF['categoryID']. '" style="display: none;">'; 
                            
                                    if ($resultPol && mysqli_num_rows($resultPol) > 0) {
                                        while ($rowPol = mysqli_fetch_assoc($resultPol)) {
                                            echo '<div class="PR-Policies" data-id="' . $rowPol['policyID'] . '" data-file="' . $rowPol['contentPath'] . '" data-upload-date="' . ($rowPol['dateUploaded'] ?? '') . '">';
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

                    // Fetch policies that are published directly to the "Main Repository" (No Folder)
                    $queryMainPols = "SELECT * FROM policytbl WHERE categoryID IS NULL AND policyStatusID >= 4 AND policyStatusID != 6";
                    $resultMainPols = mysqli_query($conn, $queryMainPols);
                    if ($resultMainPols && mysqli_num_rows($resultMainPols) > 0) {
                        while ($rowPol = mysqli_fetch_assoc($resultMainPols)) {
                            echo '<div class="PR-Policies" style="margin-left: 0; width: 100%;" data-id="' . $rowPol['policyID'] . '" data-file="' . $rowPol['contentPath'] . '" data-upload-date="' . ($rowPol['dateUploaded'] ?? '') . '">';
                            echo '<p class="PR-Policies-Name"><i class="fas fa-file-pdf" style="margin-right:8px; color:#fbaf41;"></i>' . $rowPol['title'] . '</p>';
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
                    <div style="display: flex; gap: 10px;">
                        <button class="pdf-btn" onclick="openFeedbackModal(window.currentSelectedPolicyId)" style="background-color: #fbaf41; color: #1a2035; font-weight: bold; padding: 5px 15px; border-radius: 5px; cursor: pointer; border: none;">
                            <i class="fas fa-comment-alt"></i> Remark
                        </button>
                        <button class="pdf-btn" onclick="openDocumentHistoryModal(window.currentSelectedPolicyId)" style="background-color: #293A82; color: white; font-weight: bold; padding: 5px 15px; border-radius: 5px; cursor: pointer; border: 1px solid #fbaf41;">
                            <i class="fas fa-history"></i> Document History
                        </button>
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
                <div id="policyRemarksSection" class="policy-remarks-section" style="display:none; background: #ffffff; border-top: 1px solid #d1d5db; padding: 20px;">
                    <h3 style="margin: 0 0 10px; color: #1a2035;">Remarks</h3>
                    <textarea id="policyRemarkText" placeholder="Type your feedback here..." style="width: 100%; min-height: 120px; padding: 12px; border-radius: 8px; border: 1px solid #d1d5db; resize: vertical; margin-bottom: 15px;"></textarea>
                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button id="cancelRemarkBtn" class="pdf-btn" style="background: transparent; color: #1a2035; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 18px; cursor: pointer;">Cancel</button>
                        <button id="submitRemarkBtn" class="pdf-btn" style="background: #fbaf41; color: #1a2035; border: none; border-radius: 8px; padding: 10px 18px; font-weight: bold; cursor: pointer;">Submit Remark</button>
                    </div>
                </div>
            </div>
        </div>
                    
        <div class="policy-submission-content" id="policy-submission-content" >
            <div class="policy-submission">
                <h2>Policy Submission</h2>
                <div class="policy-submission-buttons">
                    <button class="btn" id="downloadTemplateBtn"><i class="fa fa-download"></i> 
                    <span>New Policy Template</span>
                    </button>
                    <button class="btn" id="submitButton">Submit</button>
                </div>
            </div>
        </div>
        
        <div class="confirm-dl" id="confirm-dl">
            <div class= "confirm-popUp">
                <h2> Confirm Download?</h2>
                <div class="cf-buttons">
                    <button id="cancelDownloadBtn">No</button>
                    <button id="confirmDownloadBtn">Yes</button>
                </div>
            </div>
        </div>

        <div class="submit-overlay" id="submitOverlay">
            <div class="submit-popUp">
                <h2>Submission</h2>
                <form action="/qms_optiqual/generalComponents/submit_policy.php" method="POST" enctype="multipart/form-data">
                <form action="../../generalComponents/submit_policy.php" method="POST" enctype="multipart/form-data">
                <div class="submit-field">
                    <p>Policy Title</p>
                </div>
        
                <div class="submit-input">
                    <input type="text" name="policyTitle" id="policyTitle" placeholder="Enter policy title" required><br>
                    <input type="file" name="policyFile" accept=".pdf" required>
                </div>
                <div class="revision-toggle" style="margin-top: 15px; text-align: left;">
                    <input type="checkbox" id="staffIsRevision" name="isRevision" onchange="document.getElementById('staffRevFields').style.display = this.checked ? 'block' : 'none';">
                    <label for="staffIsRevision">Mark as Policy Revision</label>
                </div>
                
                <div id="staffRevFields" style="display: none; text-align: left; margin-top: 10px;">
                    <label>Select Policy to Revise:</label>
                    <select name="originalPolicyID" style="width: 100%; padding: 8px; margin-bottom: 10px;" onchange="if(this.value) document.getElementById('policyTitle').value = this.options[this.selectedIndex].text;">
                        <option value="">-- Select a Policy --</option>
                        <?php
                            if(isset($conn)){
                                $polQ = $conn->query("SELECT policyID, title FROM policytbl WHERE policyStatusID >= 3 AND policyStatusID != 6");
                                if($polQ) {
                                    while($p = $polQ->fetch_assoc()){
                                        echo "<option value='".$p['policyID']."'>".htmlspecialchars($p['title'])."</option>";
                                    }
                                }
                            }
                        ?>
                    </select>
                    <label>Revision Type:</label>
                    <select name="revisionType" style="width: 100%; padding: 8px; margin-bottom: 10px;">
                        <option value="minor">Minor Revision</option>
                        <option value="major">Major Revision</option>
                    </select>
                    <label for="changesDescription">Description of Changes:</label>
                    <textarea name="changesDescription" rows="3" style="width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>

                    <label>Upload Revision Form (PDF):</label>
                    <input type="file" name="changeLogFile" accept=".pdf" style="margin-top: 5px;">
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
            <h2 class="info-header" style="margin-top:0;"> System Guidelines & Modules </h2>
            <div class="infoWhite-line" style="display:flex; position: relative; top: 0; width: 100%; margin-bottom: 20px;"></div>

            <!-- Policies Repository -->
            <div class="moduleCategory" onclick="toggleInfoAccordion(this)" style="display: flex; flex-direction: column; align-items: stretch; height: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div class="module-text"><i class="fas fa-book" style="margin-right: 10px;"></i> Policies Repository</div>
                    <i class="fas fa-chevron-right expand-icon" style="transition: transform 0.3s;"></i>
                </div>
                <div class="nested-moduleSubcategory-content" style="display: none; margin-top: 15px; width: 100%; box-sizing: border-box; text-align: left;">
                    <h4 style="margin: 0 0 10px 0; font-weight: bold; font-size: 18px;">Purpose & Function</h4>
                    <div class="nested-blackLine" style="width: 100%;"></div>
                    <p style="color: black; font-size: 16px; font-weight: normal; margin-top: 10px;">The Policies Repository is the centralized library containing all approved and active policies of the institution. You can use this module to search, read, and download official documents.</p>
                </div>
            </div>

            <!-- Policy Submission -->
            <div class="moduleCategory" onclick="toggleInfoAccordion(this)" style="display: flex; flex-direction: column; align-items: stretch; height: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div class="module-text"><i class="fas fa-upload" style="margin-right: 10px;"></i> Policy Submission</div>
                    <i class="fas fa-chevron-right expand-icon" style="transition: transform 0.3s;"></i>
                </div>
                <div class="nested-moduleSubcategory-content" style="display: none; margin-top: 15px; width: 100%; box-sizing: border-box; text-align: left;">
                    <h4 style="margin: 0 0 10px 0; font-weight: bold; font-size: 18px;">Purpose & Function</h4>
                    <div class="nested-blackLine" style="width: 100%;"></div>
                    <p style="color: black; font-size: 16px; font-weight: normal; margin-top: 10px;">This module allows faculty and staff to propose new policies or submit revisions to existing ones. It provides downloadable templates and an upload form that automatically routes your submission to the Quality Assurance team.</p>
                </div>
            </div>

            <!-- My Workspace -->
            <div class="moduleCategory" onclick="toggleInfoAccordion(this)" style="display: flex; flex-direction: column; align-items: stretch; height: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div class="module-text"><i class="fas fa-tasks" style="margin-right: 10px;"></i> My Workspace</div>
                    <i class="fas fa-chevron-right expand-icon" style="transition: transform 0.3s;"></i>
                </div>
                <div class="nested-moduleSubcategory-content" style="display: none; margin-top: 15px; width: 100%; box-sizing: border-box; text-align: left;">
                    <h4 style="margin: 0 0 10px 0; font-weight: bold; font-size: 18px;">Purpose & Function</h4>
                    <div class="nested-blackLine" style="width: 100%;"></div>
                    <p style="color: black; font-size: 16px; font-weight: normal; margin-top: 10px;">My Workspace is your personal task management area. Here, you can track the status of your submitted policies, respond to feedback or revision requests, and sign documents that require your authorization.</p>
                </div>
            </div>
        </div>

        <!-- Document History Modal -->
        <div id="documentHistoryOverlay" class="overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center;">
            <div class="confirm-reply-modal" style="background-color: #293A82; width: 70%; max-width: 900px; padding: 2vw; border-radius: 1vw; display: flex; flex-direction: column; color: white; font-family: 'Istok Web', sans-serif;">
                <h2 style="margin-top: 0; margin-bottom: 10px; font-size: 2vw;">Document History</h2>
                <p style="margin-bottom: 20px; font-size: 16px; color: #d3d3d3;" id="docHistorySubtitle">Loading history...</p>
                
                <div style="max-height: 50vh; overflow-y: auto; background-color: white; border-radius: 10px; padding: 15px; color: black; text-align: left;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="background-color: #fbaf41; color: black; padding: 10px; border-bottom: 2px solid #ccc;">Version</th>
                                <th style="background-color: #fbaf41; color: black; padding: 10px; border-bottom: 2px solid #ccc;">Title</th>
                                <th style="background-color: #fbaf41; color: black; padding: 10px; border-bottom: 2px solid #ccc;">Author</th>
                                <th style="background-color: #fbaf41; color: black; padding: 10px; border-bottom: 2px solid #ccc;">Approver</th>
                                <th style="background-color: #fbaf41; color: black; padding: 10px; border-bottom: 2px solid #ccc;">Published</th>
                                <th style="background-color: #fbaf41; color: black; padding: 10px; border-bottom: 2px solid #ccc;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="documentHistoryTableBody">
                            <tr><td colspan="6" style="text-align: center; padding: 20px;">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="confirm-actions" style="margin-top: 20px; display: flex; justify-content: center;">
                    <button class="cancel-button" onclick="document.getElementById('documentHistoryOverlay').style.display='none'" style="padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; background-color: #D3D3D3; color: black;">Close</button>
                </div>
            </div>
        </div>

        <!-- Secondary PDF Viewer for Comparison -->
        <div id="Secondary_PdfViewer" style="display:none; position: fixed; top: 2%; left: 10%; width: 80%; height: 96%; z-index: 3000; box-shadow: 0 10px 30px rgba(0,0,0,0.8); background: #525659; border-radius: 10px; flex-direction: column;">
            <div class="introduction-header" style="background: #293A82; padding: 15px 20px; display: flex; align-items: center; border-radius: 10px 10px 0 0;">
                <h2 id="secPdfViewerTitle" style="margin: 0; font-size: 20px; color: white; flex-grow: 1;">Compare Document</h2>
                <button id="closeSecondaryPdfViewer" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; transition: color 0.2s;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="custom-pdf-toolbar" style="display: flex; justify-content: space-between; align-items: center; background-color: #343A40; color: white; padding: 10px 20px;">
                <div class="pdf-tools-left">
                    <button id="sec_prevPage" class="pdf-btn" style="background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer;"><i class="fas fa-chevron-left"></i></button>
                    <span class="page-info" style="margin: 0 15px; font-size: 14px; font-family: 'Istok Web', sans-serif;">Page <span id="sec_pageNum">1</span> of <span id="sec_pageCount">?</span></span>
                    <button id="sec_nextPage" class="pdf-btn" style="background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer;"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="pdf-tools-right">
                    <button id="sec_zoomOut" class="pdf-btn" style="background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer;"><i class="fas fa-search-minus"></i></button>
                    <span id="sec_zoomLevel" style="margin: 0 15px; font-size: 14px; font-family: 'Istok Web', sans-serif;">120%</span>
                    <button id="sec_zoomIn" class="pdf-btn" style="background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer;"><i class="fas fa-search-plus"></i></button>
                </div>
            </div>

            <div class="pdf-canvas-container" style="flex-grow: 1; overflow: auto; text-align: center; padding: 20px 0;">
                <canvas id="sec_pdfCanvas" style="box-shadow: 0 4px 8px rgba(0,0,0,0.5); margin: 0 auto;"></canvas>
            </div>
        </div>

        <script src="STAFF-POV.js?v=<?php echo time(); ?>"></script>
        <script>
            // Ensure we globally track the selected policy ID if the external JS file hasn't already
            document.addEventListener('click', function(e) {
                const policyItem = e.target.closest('.PR-Policies');
                if (policyItem) {
                    window.currentSelectedPolicyId = policyItem.getAttribute('data-id');
                }
            });

            window.openFeedbackModal = function() {
                const policyId = window.currentSelectedPolicyId;
                if (!policyId) return alert("Error: Please select a valid policy.");
                
                window.currentPolicyId = policyId; 
                const remarksSection = document.getElementById('policyRemarksSection');
                const remarkInput = document.getElementById('policyRemarkText');
                if (remarksSection) {
                    remarksSection.style.display = (remarksSection.style.display === 'block') ? 'none' : 'block';
                }
                if (remarkInput) remarkInput.focus();
            };

            document.addEventListener('DOMContentLoaded', () => {
                const submitRemarkBtn = document.getElementById('submitRemarkBtn');
                const cancelRemarkBtn = document.getElementById('cancelRemarkBtn');
                const policyRemarkText = document.getElementById('policyRemarkText');
                
                if (cancelRemarkBtn) {
                    cancelRemarkBtn.addEventListener('click', () => {
                        document.getElementById('policyRemarksSection').style.display = 'none';
                        if (policyRemarkText) policyRemarkText.value = '';
                    });
                }

                if (submitRemarkBtn) {
                    submitRemarkBtn.addEventListener('click', function() {
                        const content = policyRemarkText ? policyRemarkText.value.trim() : '';
                        if (!content) return alert("Please write a remark.");
                        const btn = this;
                        btn.innerHTML = "Submitting...";
                        btn.disabled = true;

                        fetch('../../generalComponents/policyManagerPHP/submitFeedback.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ policyId: window.currentPolicyId, content: content })
                        }).then(res => res.json()).then(data => {
                            if (data.success) {
                                alert("Feedback submitted successfully!");
                                if (policyRemarkText) policyRemarkText.value = '';
                                document.getElementById('policyRemarksSection').style.display = 'none';
                            } else {
                                alert("Error: " + data.message);
                            }
                        }).catch(err => alert("Network error.")).finally(() => {
                            btn.innerHTML = "Submit Remark";
                            btn.disabled = false;
                        });
                    });
                }
            });

            window.openDocumentHistoryModal = function(policyId) {
                if (!policyId) return alert("Error: Please select a valid policy.");
                document.getElementById('documentHistoryOverlay').style.display = 'flex';
                document.getElementById('documentHistoryTableBody').innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Loading history...</td></tr>';
                
                fetch(`../../generalComponents/policyManagerPHP/getDocumentHistory.php?policyID=${policyId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.history && data.history.length > 0) {
                            document.getElementById('docHistorySubtitle').textContent = `History for: ${data.history[0].title}`;
                            let html = '';
                            data.history.forEach(item => {
                                html += `<tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px; font-weight: bold;">${item.versionNo}</td>
                                    <td style="padding: 12px;">${item.title}</td>
                                    <td style="padding: 12px;">${item.authorName}</td>
                                    <td style="padding: 12px;">${item.approverName}</td>
                                    <td style="padding: 12px;">${item.datePublished}</td>
                                    <td style="padding: 12px; display: flex; flex-direction: column; gap: 5px;">
                                        <button class="action-btn-inline" onclick="openSecondaryPdfViewer('${item.contentPath}', '${item.title} (${item.versionNo})')" style="background:#293A82; color:white; border:none; padding: 6px 15px; border-radius: 5px; cursor:pointer;">View Policy</button>
                                        ${item.revisionFormPath ? `<button class="action-btn-inline" onclick="openSecondaryPdfViewer('${item.revisionFormPath}', 'Change Log')" style="background:#fbaf41; color:black; border:none; padding: 6px 15px; border-radius: 5px; cursor:pointer;">Change Log</button>` : ''}
                                    </td></tr>`;
                            });
                            document.getElementById('documentHistoryTableBody').innerHTML = html;
                        } else {
                            document.getElementById('documentHistoryTableBody').innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">No history found.</td></tr>`;
                        }
                    }).catch(err => {
                        document.getElementById('documentHistoryTableBody').innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">Network error.</td></tr>`;
                    });
            };

            // Secondary PDF Engine
            var sec_pdfDoc = null, sec_pageNum = 1, sec_pageRendering = false, sec_pageNumPending = null, sec_scale = 1.2, sec_canvas = null, sec_ctx = null;
            function sec_renderPage(num) {
                sec_pageRendering = true;
                if (sec_pdfDoc) {
                    sec_pdfDoc.getPage(num).then(function(page) {
                        let viewport = page.getViewport({scale: sec_scale});
                        sec_canvas.height = viewport.height; sec_canvas.width = viewport.width;
                        page.render({canvasContext: sec_ctx, viewport: viewport}).promise.then(function() {
                            sec_pageRendering = false;
                            if (sec_pageNumPending !== null) { sec_renderPage(sec_pageNumPending); sec_pageNumPending = null; }
                        });
                    });
                    document.getElementById('sec_pageNum').textContent = num;
                    document.getElementById('sec_zoomLevel').textContent = Math.round(sec_scale * 100) + '%';
                }
            }
            function sec_queueRenderPage(num) { if (sec_pageRendering) sec_pageNumPending = num; else sec_renderPage(num); }
            window.openSecondaryPdfViewer = function(filePath, documentTitle) {
                if (!filePath || filePath === 'null' || filePath.trim() === '') return alert("No document available.");
                document.getElementById('secPdfViewerTitle').textContent = documentTitle || "Document Compare Viewer";
                document.getElementById('Secondary_PdfViewer').style.display = 'flex'; 
                if (typeof pdfjsLib !== 'undefined') {
                    pdfjsLib.getDocument(encodeURI(filePath)).promise.then(function(pdfDoc_) {
                        sec_pdfDoc = pdfDoc_;
                        document.getElementById('sec_pageCount').textContent = sec_pdfDoc.numPages;
                        sec_pageNum = 1; sec_scale = 1.2; sec_renderPage(sec_pageNum);
                    });
                }
            };
            document.addEventListener('DOMContentLoaded', () => {
                sec_canvas = document.getElementById('sec_pdfCanvas');
                if(sec_canvas) sec_ctx = sec_canvas.getContext('2d');
                document.getElementById('closeSecondaryPdfViewer').addEventListener('click', () => { document.getElementById('Secondary_PdfViewer').style.display = 'none'; });
                document.getElementById('sec_prevPage').addEventListener('click', () => { if(sec_pageNum <= 1) return; sec_pageNum--; sec_queueRenderPage(sec_pageNum); });
                document.getElementById('sec_nextPage').addEventListener('click', () => { if(!sec_pdfDoc || sec_pageNum >= sec_pdfDoc.numPages) return; sec_pageNum++; sec_queueRenderPage(sec_pageNum); });
                document.getElementById('sec_zoomIn').addEventListener('click', () => { sec_scale += 0.2; sec_queueRenderPage(sec_pageNum); });
                document.getElementById('sec_zoomOut').addEventListener('click', () => { if(sec_scale <= 0.6) return; sec_scale -= 0.2; sec_queueRenderPage(sec_pageNum); });
            });

            window.toggleInfoAccordion = function(element) {
                const content = element.querySelector('.nested-moduleSubcategory-content');
                const icon = element.querySelector('.expand-icon');
                if (content.style.display === 'none' || content.style.display === '') {
                    content.style.display = 'block';
                    if(icon) icon.style.transform = 'rotate(90deg)';
                } else {
                    content.style.display = 'none';
                    if(icon) icon.style.transform = 'rotate(0deg)';
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');
                const confirmDlPopup = document.getElementById('confirm-dl');
                const cancelDownloadBtn = document.getElementById('cancelDownloadBtn');
                const confirmDownloadBtn = document.getElementById('confirmDownloadBtn');

                if (downloadTemplateBtn && confirmDlPopup) {
                    downloadTemplateBtn.addEventListener('click', function() {
                        confirmDlPopup.style.display = 'flex';
                    });
                }

                if (cancelDownloadBtn && confirmDlPopup) {
                    cancelDownloadBtn.addEventListener('click', function() {
                        confirmDlPopup.style.display = 'none';
                    });
                }

                if (confirmDownloadBtn && confirmDlPopup) {
                    confirmDownloadBtn.addEventListener('click', function() {
                        const link = document.createElement('a');
                        link.href = '/qms_optiqual/Policy_Templates/QMS Template.docx'; // Make sure this path points to your actual template file
                        link.href = '../../Policy_Templates/QMS Template.docx';
                        link.download = 'QMS Template.docx';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        confirmDlPopup.style.display = 'none';
                    });
                }
            });
        </script>
    </body>
</html>