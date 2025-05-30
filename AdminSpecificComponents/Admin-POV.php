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
        <div class="signOut-content">
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
                <p>Initial DPA Text: This document outlines Asia Pacific College's policies regarding the collection, processing, and management of employee personal information, in accordance with Republic Act 10173, the Data Privacy Act (DPA) of 2012. It covers data privacy principles, data subject rights, security measures, and procedures for data breaches and inquiries. By continuing to use our services, you acknowledge and agree to the terms herein. For more details, please refer to the complete DPA policy available through official channels.</p>
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