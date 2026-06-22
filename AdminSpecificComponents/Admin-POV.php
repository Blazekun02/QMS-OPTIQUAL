<?php
    session_start();
    include '../../connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Admin-POV.css">
</head>

<body>

    <div class="Sidebar" id="sidebar">
        <div class="Sidebar-Logo">
            <img src="../assets/logos/logo.png" alt="Logo" class="Logo">
            <span class="extended-text" id="extended-text">ASIA<br> PACIFIC<br> COLLEGE<br> </span>
        </div>
        <ul class="Sidebar-Menu">
            <li class="menu-icons" id="dataPrivacyIcon"> <img src="../assets/QAP Sidebar/Not Clicked/UpdateDPA.png" alt="Icon 1">
                <span class="icon-label">Data Privacy Update</span>
            </li>
            <li class="menu-icons" id="roleManagerIcon"> <img src="../assets/QAP Sidebar/Not Clicked/Role_Manage.png" alt="Icon 2">
                <span class="icon-label">Role Manager</span>
            </li>
        </ul>
    </div>

   <div class="hamburger-icon" onclick="toggleSidebar()">
        <img src="../assets/hamburger.jpeg" alt="Menu">
    </div>

    <div class="header">
        <div class="header-icons">
            <button type="button" class="header-icon" id="notifButton">
                <i class="fa fa-bell"></i>
            </button>
            <button type="button" class="header-icon" id="userButton">
                <i class="fa fa-user-circle"></i>
                <span class="header-username">Name of the user</span>
            </button>
        </div>
    </div>

    <div class="yellow-line"></div>
    <div class="blue-line">Copyright © 2024 OPTIQUAL. All rights reserved</div>


    <div class="signOut-overlay" id="signOutOverlay">
        <div class="signOut-content" onclick="window.location.href='/qms_optiqual/auth/log_out/logout.php'" style="cursor: pointer;">
            Sign out
        </div>
    </div>

    <div class="main-content" id="mainContentDataPrivacy"> 
        <div class="dpa-container">
            <h1 class="dpa-heading">Data Privacy Update</h1>
            <p class="dpa-description">
                Review and update the data privacy agreements. Ensure all users are informed and compliant with the latest privacy policies.
            </p>
            <div class="dpa-content-area" id="currentDPAContent">
                <?php
                    if (isset($conn)) {
                        $dpaQuery = "SELECT dpaContents FROM dpatbl ORDER BY dpaVersion DESC LIMIT 1";
                        $dpaResult = $conn->query($dpaQuery);
                        if ($dpaResult && $dpaResult->num_rows > 0) {
                            $dpaRow = $dpaResult->fetch_assoc();
                            echo '<p>' . nl2br(htmlspecialchars($dpaRow['dpaContents'])) . '</p>';
                        } else {
                            echo '<p>No Data Privacy Agreement found. Click "Update" to add one.</p>';
                        }
                    }
                ?>
            </div>
            <button class="update-button" id="openDPAEditPopup">Update</button>
        </div>
    </div>

    <div class="main-content role-manager" id="mainContentRoleManager">
    <div class="role-manager-container">
        <div class="header-and-search">
            <h1 class="role-manager-header">Account Manager</h1>
            <div class="search-bar-container">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" placeholder="find employee">
            </div>
        </div>
        <div class="white-line-separator"></div>
        <div class="table-container">
            <table class="role-manager-table">
                <thead>
                    <tr>
                        <th>Full name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="roleManagerTableBody">
                    <!-- User rows will be dynamically populated here via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>    

<div class="popup-overlay" id="editDPAOverlay"> 
    <div class="popup-container">
        <h2 class="popup-heading">Edit Data Privacy Agreement</h2>
        <textarea id="dpaEditTextarea" class="dpa-edit-textarea"></textarea>
        <div class="popup-buttons">
            <button class="popup-button cancel-button" id="cancelDPAEdit">Cancel</button>
            <button class="popup-button submit-button" id="submitDPAEdit">Submit</button>
        </div>
    </div>
</div>

<div class="popup-overlay" id="confirmUpdateOverlay"> 
    <div class="confirmation-popup">
        <p class="confirmation-text">Confirm update?</p>
        <div class="popup-buttons">
            <button class="popup-button cancel-button" id="noConfirmUpdate">No</button>
            <button class="popup-button submit-button" id="yesConfirmUpdate">Yes</button>
        </div>
    </div>
</div>

    <script src="Admin-POV.js"></script>

</body>
</html>