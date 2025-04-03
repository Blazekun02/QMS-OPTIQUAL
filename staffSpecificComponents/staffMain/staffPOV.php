<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty and Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="staffPOV.css">
</head>

<body>
<div class="yellow-line"></div>
<div class="image"></div>
<div class="white-line"></div>
<div class="blue-line"></div>
<div class="copyright-label">Copyright © 2024 OPTIQUAL. All rights reserved</div>
<div class="grey-line" id="grey-line">
    <div class="logo-wrapper">
        <img src="/.idea/images/logo.png" alt="Logo" class="logo">
    </div>
        <span class="extended-text" id="extended-text">ASIA<br> PACIFIC<br> COLLEGE<br> </span>
<div class="menu-icons">
        <div class="icon-item">
            <img src="/.idea/images/policy lib-notClicked.png" alt="Icon 1">
            <span class="icon-label">Policies Repository</span>
        </div>
        <div class="icon-item">
            <img src="/.idea/images/policy create-notClicked.png" alt="Icon 2">
            <span class="icon-label">Policy Submission</span>
        </div>
        <div class="icon-item">
            <img src="/.idea/images/req tracker-notClicked.png" alt="Icon 3">
            <span class="icon-label">Request Tracker</span>
        </div>
        <div class="icon-item">
            <img src="/.idea/images/task manager-notClicked.png" alt="Icon 4">
            <span class="icon-label">Task Manager</span>
        </div>
        <div class="icon-item">
            <img src="/.idea/images/info - notClicked.png" alt="Icon 5">
            <span class="icon-label">Information</span>
        </div>
    </div>
</div>

<img src="/.idea/images/hamburger.jpeg" alt="Menu" class="hamburger-icon" id="hamburger-icon">
<div>
    <button type="button" class="button user-btn" id="userButton">
        <i class="fa fa-user-circle" style="font-size:24px"></i>
        Name of the user
    </button>
    <button type="button" class="button notif-btn" id="notifButton">
        <i class="fa fa-bell" style="font-size:24px"></i>
    </button>
</div>

<div class="notification-overlay" id="notificationOverlay">
    <div class="notification-content">
    </div>
</div>
<div class="signOut-overlay" id="signOutOverlay">
    <div class="signOut-content">
        Sign out
    </div>
</div>

<script src="staffPOV.js"></script>
</body>
</html>