<?php include '../../connect.php';
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    } else {
        echo "<script>alert('✅ Connected successfully');</script>";
    }
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty and Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.gaoogleapis.com/icon?family=Material+Icons">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="QAP-POV.css">
</head>

<body>
    <!-- just the design lawl -->
    <div class="yellow-line"></div>
    <div class="image"></div>
    <div class="white-line"></div>
    <div class="blue-line"></div>
    <div class="copyright-label">Copyright © 2024 OPTIQUAL. All rights reserved</div>
    
    <!-- sidebar, logo, and icons -->
    <div class="grey-line" id="grey-line">
        <div class="logo-wrapper">
            <img src="../../assets/logos/logo.png" alt="Logo" class="logo">
            <span class="extended-text" id="extended-text">ASIA<br> PACIFIC<br> COLLEGE<br> </span>
        </div>
    <div class="menu-icons">
            <div class="icon-item">
                <img src="../../assets/policy lib-notClicked.png" alt="Icon 1" onclick="showPoliciesRepository()">
                <span class="icon-label">Policies Repository</span>
            </div>
            <div class="icon-item">
                <img src="../../assets/policy create-notClicked.png" alt="Icon 2" onclick="showPolicySubmission()">
                <span class="icon-label">Policy Submission</span>
            </div>
            <div class="icon-item">
                <img src="../../assets/req tracker-notClicked.png" alt="Icon 3" onclick="showProcessTracker()">
                <span class="icon-label">Process Tracker</span>
            </div>
            <div class="icon-item">
                <img src="../../assets/task manager-notClicked.png" alt="Icon 4" onclick="showTaskManager()">
                <span class="icon-label">Task Manager</span>
            </div>  
            <div class="icon-item">
                <img src="../../assets/QAP Sidebar/Not Clicked/Role_Manage.png" alt="Icon 6" onclick="showRoleManager()">
                <span class="icon-label">Role Manager</span>
            </div>
            <div class="icon-item">
            <img src="../../assets/info - notClicked.png" alt="Icon 5" onclick="showInformation()">
            <span class="icon-label">Information</span>
        </div>
    </div>
</div>   
    <!-- Hamburger, notif, and sign out -->
    
    <img src="../../assets/hamburger.jpeg" alt="Menu" class="hamburger-icon" id="hamburger-icon">
    <div class="header-buttons">
        <button type="button" class="button notif-btn" id="notifButton">
            <i class="fa fa-bell" style="font-size: 1.5em;"></i>
        </button>
        <button type="button" class="button user-btn" id="userButton">
            <i class="fa fa-user-circle" style="font-size: 1.5em;"></i>
            <span class="user-name">Name of the user</span>
        </button>
    </div>

    <div class="notification-overlay" id="notificationOverlay">
        <div class="notification-content"></div>
    </div>
    <div class="signOut-overlay" id="signOutOverlay">
        <div class="signOut-content">Sign out</div>
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

        

    <script src="QAP-POV.js"></script>
    </body>
</html>