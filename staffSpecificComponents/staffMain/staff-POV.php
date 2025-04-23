<?php
// Include message generation
require_once '../../genMsg/message_box.php';
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

    <style>
        /* General styling */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0; 
            padding: 0;
            font-family: 'Istok Web', sans-serif;
            background-color: white;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            overflow-x: hidden;
        }

        .image {
            flex: 1;
            background-image: url('.idea/images/apc.jpg');
            background-position: 4.5vw -9vh;
            background-size: 100vw;
            background-repeat: no-repeat;
            width: 100vw;
            height: auto;
        }

        /* Header styling */
        .white-line,.blue-line {
            position: absolute;
            left: 0;
            width: 100%;
            height: auto;
        }
        .white-line {
            top: 0;
            height: 2.8vw;
            background-color: white;
            border-width: 0.3vw;
            border-style: solid;
            border-color: transparent transparent  #fbaf41 transparent;
        }

        .blue-line {
            position: fixed;
            width: 100%;
            bottom: 0;
            height: 2.5vw;
            background-color: #293A82;
            z-index: 15;
        }

        .copyright-label {
            position: fixed;
            bottom: 0;
            left: 4.5vw;
            width: auto;
            max-width: 80%;
            font-size: 16px;
            background-color: #293A82;
            color: white;
            padding: 0.3vw;
            border-radius: 5px;
            z-index: 15;
        }


        .grey-line {
            position: fixed;
            top: 0;
            left: 0;
            width: 4.5vw;
            height: 100%;
            background-color: #343A40;
            transition: width 0.3s ease;
            z-index: 20;
            display: flex;
            align-items: flex-start;
        }

        .grey-line.extended {
            width: 14vw;
        }


        .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 5vw;
            margin-top: 0.5vw;
        }

        .logo {
            max-width: 90%;
            max-height: 90%;
            display: block;
        }

        .extended-text {
            color: #fbaf41;
            font-size: 16px;
            font-family:"Bebas Neue";
            margin-left: 0.8vw;
            display: none;
            margin-top: 0.4vw;
        }

        .grey-line.extended .extended-text {
            display: inline;
        }


        .hamburger-icon {
            position: absolute;
            top: 1.2vw;
            left: 5.5vw;
            transform: translateY(-50%);
            width: 2vw;
            height: 2vw;
            background-color: white;
            cursor: pointer;
            transition: left 0.5s ease;        
        }

        .hamburger-icon::before,
        .hamburger-icon::after {
            content: "";
            position: absolute;
            width: 100%;
            background-color: #343A40;
        }

        .user-btn, .notif-btn {
            position: absolute;
            top: 0.2vw;
            background-color: white;
            border: none;
            color: black;
            text-align: center;
            font-size: 17px;
            cursor: pointer;
            z-index: 10;
        }

        .user-btn {
            left: 86vw;
        }

        .notif-btn {
            left: 83.4vw;
        }


        .notification-overlay {
            position: fixed;
            top: 2.1vw;
            right: 15.5vw;
            display: none;
            z-index: 1000;

        }

        .notification-content {
            color: white;
            background-color: #343A40;
            width: 15vw;
            height: 17vw;
            border-radius: 5px;
        }


        .signOut-overlay {
            position: absolute;
            top: 2.4vw;
            right: 6.7vw;
            background-color: #343A40;
            border-radius: 20px;
            padding: 0.8vh 1.7vw;
            display: none;
            z-index: 1002;
            color: white;
            font-size: 12px;
        }

        .signOut-overlay::before {
            content: '';
            position: absolute;
            bottom: 98%;
            left: 1.3vw;
            transform: translateX(-50%);
            border-width: 0.5vw;
            border-style: solid;
            border-color: transparent transparent #343A40 transparent;
        }

        .signOut-content {
            cursor: pointer;
            transition: color 0.3s;
            text-align: center;
            width: 3.7vw;
        }

        .signOut-content:hover {
            color: red;
        }


        .menu-icons {
            position: absolute;
            top: 12vw;
            left: 1.1vw;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            height: auto;
        }

        .menu-icons .icon-item {
            display: flex;
            align-items: center;
            margin-bottom: 4vw;
        }

        .menu-icons img {
            width: 2vw;
            height: 2vw;
            cursor: pointer;
            transition: filter 0.3s ease;
        }

        .icon-label {
            color: white;
            margin-left: 0.3vw;
            display: none;
            transition: filter 0.3s ease;
        }

        .icon-item:hover .icon-label {
            color: #fbaf41;
         }

        .grey-line.extended .icon-label {
            display: inline;
        }

        .policies-repository-content {
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: left;
            padding: 0.2vw;
            background-color: white;
            min-height: calc(100vh - 0.4in);
            overflow-y: auto;
            padding-bottom: 0.6vw;
        }

        .policies-repository-content h2 {
           margin-bottom: 0.5vw;
            display: inline-block;
            margin-left: 5.10vw;
            margin-top: 2.3vw;
            position: absolute;

        }

        .search-container {
            width: 30vw;
            margin-bottom: 2vw;
            margin-top: 2.6vw;
            margin-right: -7.60vw;
            display: inline-flex;
            align-items: center;
            align-self: flex-end;
            position: absolute;
        }


        .policies-repository-content .search-container .filter-button {
            background-color: white !important;
            color: #293A82 !important;
            padding: 0.8vw 1.5vh;
            border: 0.10vw solid black;
            border-radius: 10px;
            cursor: pointer;
            margin-left: 0.5vw;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .policies-repository-content .search-container .filter-button:hover{
            background-color: #ccc !important;
            border: 0.15vw solid black;
            color: #0A185F;
        }

        .policies-repository-content .search-container .filter-button svg {
            width: 0.1vw;
            height: 0.1vh;
            fill: none !important;
            stroke: #ccc !important;
        }

        .policies-repository-content .search-container .filter-button svg:hover {
            stroke: #0A185F !important;
        }

        .blackLine {
            position: absolute;
            top: 21vh;
            width: 93%;
            margin-left: 4.5em;
            height: 0.2vw;
            background-color: black;
            z-index: 15;
            display: none;
        }

        .filter-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .filter-popup {
            background-color: #293A82;
            color: white;
            padding: 0.8vw;
            border-radius: 10px;
            width: 20.40vw;
            height: 42vh;
            text-align: left;
        }

        .filter-popup h2 {
            margin-top: 0.1vw;
            text-align: center;
            margin-left: 2.8vw;
        }

        .filter-section {
            margin-bottom: 1vh;
        }

        .filter-section label {
            display: block;
            margin-top: 2.9vw;
            margin-bottom: 0.7vh;

        }

        .filter-section select {
            width: 100%;
            padding: 0.4vw;
            border-radius: 5px;
            border: 0.1vw solid #ccc;
            background-color: white;
            color: black;
        }

        .filter-buttons {
            text-align: center;
            margin-top: 0.7vw;
        }

        .filter-buttons button {
            color: black;
            padding: 0.3vw 2.7vh;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            margin-left: 1.2vw;
            margin-right: 1.2vw;
        }

        #applyFilter {
            background-color: #fbaf41;
        }

        .search-container input[type="text"] {
            flex: 1;
            padding: 0.5vw;
            border: 0.1vw solid #ccc;
            border-radius: 0.9vw 0 0 0.9vw;
        }

        .search-container button {
            background-color: #293A82;
            color: white;
            padding: 1.2vh 0.9vw;
            border: none;
            border-radius: 0 0.9vw 0.9vw 0;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #0A185F;

        }

        .category {
            width: calc(100vw - 9.3vw);
            max-height: 5.5vh;
            margin-left: 5.4vw;
            background-color: #4963D4;
            color: white;
            padding: 0.5vw;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.4vh;
            position: relative;
            top: 16.5vh;
        }

        .category:hover{
            background-color: #23278D;
        }

        .category .expand-icon {
            transform: rotate(0deg);
            transition: transform 0.3s ease;
        }

        .category.expanded .expand-icon {
            transform: rotate(90deg);
        }

        .category-content {
            width: calc(100vw - 9.3vw);
            display: none;
            padding: 0;
            color: #293A82;
            border-radius: 5px 5px 5px 5px;
            margin-left: 5.1vw;
            overflow-y: auto;
            overflow-x: hidden;
            position: absolute;
            top: 5.7vh;
            left: -5.10vw;
            z-index: 10;
            background-color: white;
            max-height: 35vh;
        }

        .category-content::-webkit-scrollbar {
            width: 0.5vw;
        }

        .category-content::-webkit-scrollbar-thumb {
            background: #808080;
            border-radius: 8px;
        }

        .category-content::-webkit-scrollbar-track {
            background: #D3D3D3;
        }

        .category.expanded .category-content {
            display: block;
        }

        .subcategory {
            width: calc(100vw - 10vw);
            height: 5.5vh;
            cursor: pointer;
            background-color: #7B8EDE;
            color: white;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            border-radius: 10px 10px 10px 10px;
            margin-left: 0.2vw;
            margin-bottom: 0.1vh;

        }

        .subcategory:hover {
            background-color: #5d74d9;
        }


        .policy-submission-content {
            flex: 1;
            width: calc(100vw - 55vw);
            display: none;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 0.4vh);
            background-color: white;
        }

        .policy-submission {
            border-radius: 10px;
            width: 22.40vw;
            height: 45vh;
            background-color: #293A82;
            text-align: center;
            padding: 2vh;
            margin-bottom: 1vh;
        }

        .policy-submission h2 {
            color: white;
            margin-bottom: 1vw;
            margin-top: 0.3vw;
            font-size: 24px;
        }


        .policy-submission-buttons button {
            width: 100%;
            padding: 1vh 1vw;
            border: none;
            border-radius: 5px;
            font-size: 18px;
        }


        .policy-submission-buttons button:first-child  {
            background-color: white;
            border-radius: 15px;
            margin-top: 0.7vh;
            margin-bottom: 20vh;
            color: #343A40;
            text-align: left;

        }

        .policy-submission-buttons button:first-child:hover {
            background-color: #343A40;
            color:white;
        }

        .policy-submission-buttons button:last-child {
            background-color: #fbaf41;
            color: white;
        }

        .policy-submission-buttons button:last-child:hover {
            background-color: #db8804;
            color:black;
        }

        .submit-overlay{
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .submit-popUp{
            background-color: #293A82;
            color: white;
            padding: 1vw;
            border-radius: 10px;
            width: 30vw;
            height: 35vh;
            text-align: center;
            align-items: center;
            margin-left: 35.2vw;
            margin-top: 33vh;
            font-size: 20px;
        }

        .submit-field {
            text-align: left;
            font-size: 14px;
            margin-left: 0.3vw;
            margin-top: -0.4vw;
        }

        .submit-buttons {
            text-align: center;
        }

        .submit-buttons button {
            color: black;
            padding: 0.3vw 2.5vh ;
            border-radius: 20px;
            border: #fbaf41;
            cursor: pointer;
            background-color: #fbaf41;
            font-size: 18px;
            margin-left: 1vw;
            margin-right: 0.6vw;
            align-items: center;
            margin-top: 1.5vh;
        }

        .submit-buttons button:hover{
            background-color: #db8804;
            color:black;
        }

        .submit-input {
            border-radius: 9em;
            align-items: left;
            margin-top: -1.3vh;

        }

        .confirm-dl{
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .confirm-popUp{
            background-color: #293A82;
            color: white;
            padding: 1vw;
            border-radius: 10px;
            width: 28vw;
            height: 20vh;
            margin-left: 35.5vw;
            margin-top: 38vh;
            text-align: center;
            align-items: center;
            font-size: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cf-buttons {
            text-align: center;
        }

        .cf-buttons button {
            color: white;
            padding: 0.8vh 2.3vw;
            border-radius: 10px;
            border: #fbaf41;
            cursor: pointer;
            margin-top: 3vh;
            background-color: #fbaf41;
            font-size: 18px;
            margin-left: 1vw;
            margin-right: 1vw;
        }

        .cf-buttons button:hover{
            background-color: #db8804;
            color:black;
        }

        .confirmSubmit-dl{
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .confirmSubmit-popUp{
            background-color: #293A82;
            color: white;
            padding: 1vw;
            border-radius: 10px;
            width: 28vw;
            height: 20vh;
            margin-left: 35.5vw;
            margin-top: 38vh;
            text-align: center;
            align-items: center;
            font-size: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cfSubmit-buttons {
            text-align: center;
        }

        .cfSubmit-buttons button {
            color: white;
            padding: 0.8vh 2.3vw;
            border-radius: 10px;
            border: #fbaf41;
            cursor: pointer;
            margin-top: 3vh;
            background-color: #fbaf41;
            font-size: 18px;
            margin-left: 1vw;
            margin-right: 1vw;
        }

        .cf-buttons button:hover{
            background-color: #db8804;
            color:black;
        }


        .process-tracker {
            width: 93%;
            margin-top: 2.1vh;
            background-color:#293A82;
            display: none;
            height: 93%;
            padding: 2vw;
            margin-left: 3.5vw;
            color: white;
            position: relative;
            border-radius: 20px;
        }

        .process-tracker .tracker-header {
           font-size: 24px;
          font-weight: bold;

        }

        .ptWhite-line {
            position: absolute;
            top: 11vh;
            width: 96.5%;
            margin-left: -0.3vw;
            height: 0.2vw;
            background-color: white;
            z-index: 10;
            display: none;

        }

        .process-tracker .submissions-button {
            padding: 1vh 2.3vw;
            border-radius: 15px;
            border: none;
            color: grey;
            font-size: 0.7em;
            align-self: flex-end;
            margin-left: 66.6vw;
            margin-bottom: 79vh;
            cursor: pointer;
        }

        .process-tracker .submissions-button:hover {
            color: white;
            background-color: #A9A9A9;
        }

        .process-tracker-table {
          width: 96%;
          color: black;
          font-family: Istok-Web;
          margin-left: 1.7vw;
          margin-top: 15.3vh;
          position: absolute;
          top: 0;
          left: 0;
        }

    .process-tracker-table th,
    .process-tracker-table td {
          padding: 1vh 2vw;
          text-align: left;
        }

        .process-tracker-table tHead th {
          background-color: #fbaf41;
          color: black;
          text-align: left;

        }

        .process-tracker-table tBody tr:nth-child(odd) td {
          background-color: #E0E0E0;
        }

        .process-tracker-table tBody tr:nth-child(even) td {
          background-color: #FFFFFF;
        }

          .process-tracker-table tBody tr:hover td {
            background-color: grey;
        }

        .process-tracker-table tBody tr:nth-child(odd):hover td {
            background-color: #343A40;
        }

        .process-tracker-table td:nth-child(4) {
          text-align: left;
        }

        .process-tracker-table .status-reviewed {
          color: black;
        }

        .process-tracker-table .status-verified {
          color: #2196F3;
        }


        .process-tracker-table .status-approved {
           color: #4CAF50;
        }

        .process-tracker-table .status-submitted {
            color: black;
        }

        .process-tracker-table .status-addressed {
            color: black;
        }




    </style>
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
    </div>
        <span class="extended-text" id="extended-text">ASIA<br> PACIFIC<br> COLLEGE<br> </span>
    <div class="menu-icons">
        <div class="icon-item">
            <img src=".idea/images/policy lib-notClicked.png" alt="Icon 1" onclick="showPoliciesRepository()">
            <span class="icon-label">Policies Repository</span>
        </div>
        <div class="icon-item">
            <img src=".idea/images/policy create-notClicked.png" alt="Icon 2" onclick="showPolicySubmission()">
            <span class="icon-label">Policy Submission</span>
        </div>
        <div class="icon-item">
            <img src=".idea/images/req tracker-notClicked.png" alt="Icon 3" onclick="showProcessTracker()">
            <span class="icon-label">Process Tracker</span>
        </div>
        <div class="icon-item">
            <img src=".idea/images/task manager-notClicked.png" alt="Icon 4">
            <span class="icon-label">Task Manager</span>
        </div>
        <div class="icon-item">
            <img src=".idea/images/info - notClicked.png" alt="Icon 5">
            <span class="icon-label">Information</span>
        </div>
    </div>
</div>

<!-- Hamburger, notif, and sign out -->
<img src=".idea/images/hamburger.jpeg" alt="Menu" class="hamburger-icon" id="hamburger-icon">
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
    <div class="notification-content"> Notifications </div>
</div>
<div class="signOut-overlay" id="signOutOverlay">
    <div class="signOut-content">
        <form action="../../auth/sign-out/signoutBE.php" method="POST">
            <button type="submit" class="signout-button">Sign Out</button>
        </form>
    </div>
</div>



<!-- POLICY REPOSITORY -->
<div class="policies-repository-content" id="policies-repository-content" style="display: none;">
    <h2>Policies Repository<br></h2>
    <div
    class="blackLine" id="blackLine" style="display:flex;">
    </div>
    <div class="search-container">
        <label>
            <input type="text" placeholder="Search" id="searchInput">
        </label>
        <button id="searchButton"><i class="fas fa-search"></i></button>
        <button class="filter-button" id="filterButton">
            <i class="fas fa-filter"></i>
        </button>
    </div>

    <div class="filter-overlay" id="filterOverlay">
        <div class="filter-popup">
            <h2>Filter & Sort</h2>
            <div class="filter-section">
                <label for="categoryFilter">Filter By Category</label>
                <select id="categoryFilter">
                    <option value="">-Select Category-</option>
                    <option value="guidance">Guidance Office</option>
                    <option value="office-guidelines">Office Guidelines</option>
                    <option value="organizational-profile">Organizational Profile</option>
                    <option value="discipline">Discipline Office</option>
                    <option value="blue-slip">Blue Slip procedures</option>
                    <option value="behavioral-sanctions">Behavioral sanctions</option>
                    <option value="registrar">Office of the Registrar</option>
                </select>
            </div>
            <div class="filter-section">
                <label for="sortFilter">Sort By</label>
                <select id="sortFilter">
                    <option value="a-z">A-Z</option>
                    <option value="z-a">Z-A</option>
                </select>
            </div>
            <div class="filter-buttons">
                <button id="cancelFilter">Cancel</button>
                <button id="applyFilter">Apply</button>
            </div>
        </div>
    </div>
    <div class="category" data-category="academic">
        Academic Services
        <i class="fas fa-chevron-right expand-icon"></i>
        <div class="category-content">
            <div class="subcategory" data-subcategory="guidance">Guidance Office</div>
            <div class="subcategory" data-subcategory="office-guidelines">Office Guidelines</div>
            <div class="subcategory" data-subcategory="organizational-profile">Organizational Profile</div>
            <div class="subcategory" data-subcategory="discipline">Discipline Office</div>
            <div class="subcategory" data-subcategory="blue-slip">Blue Slip procedures</div>
            <div class="subcategory" data-subcategory="behavioral-sanctions">Behavioral sanctions</div>
            <div class="subcategory" data-subcategory="registrar">Office of the Registrar</div>
        </div>
    </div>
    <div class="category" data-category="school">
        School Programs
        <i class="fas fa-chevron-right expand-icon"></i>
        <div class="category-content">
            <div class="subcategory" data-subcategory="guidance">School of Engineering</div>
            <div class="subcategory" data-subcategory="office-guidelines">School of Computing and Information Technology</div>
            <div class="subcategory" data-subcategory="organizational-profile">School of Management</div>
            <div class="subcategory" data-subcategory="discipline">School of Multimedia Arts</div>
            <div class="subcategory" data-subcategory="blue-slip">School of Architecture</div>
            <div class="subcategory" data-subcategory="behavioral-sanctions">Senior High School</div>
        </div>
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
        <div class="submit-field">
            <p>Policy Title</p>
        </div>

    <div class="submit-input">
        <input type="text" id="policyTitle" placeholder="Enter policy title" size="33vw" required><br>
        <button style="font-size:14px; color:white; background-color:#293A82; border:#293A82; margin-top:0.2vh; margin-left: -1vw; display: flex;gap: 0.4vw; padding: 1vh 1.2vw;">
            <i class="material-icons" style="vertical-align: middle;">attachment</i> Attach File
        </button>
    </div>
    <div class="submit-buttons">
    <button id="cancelBtn">Cancel</button>
    <button id="submitBtn">Submit</button>
    </div>
</div>
</div>


<div class="process-tracker">
    <h2 class="tracker-header">Process Tracker <br> </h2>
    <div class = "ptWhite-line" style = "display:flex;"></div>
    <button class="submissions-button" id ="processTrackerSub-btn">Submissions</button>
    <table class="process-tracker-table">
        <thead>
        <tr>
            <th>Policy Title</th>
            <th>Date Submitted</th>
            <th>Version no.</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Student Handbook</td>
            <td>10/24/24</td>
            <td>5.0</td>
            <td class="status-reviewed">Reviewed</td>
        </tr>
        <tr>
            <td>Organizational Profile</td>
            <td>03/03/24</td>
            <td>3.0</td>
            <td class="status-verified">Verified</td>
        </tr>
        <tr>
            <td>Student Dress Code</td>
            <td>06/07/24</td>
            <td>4.0</td>
            <td class="status-approved">Approved</td>
        </tr>
        </tbody>
    </table>
</div>


</body>
<script>

// JS FOR SIDEBAR
    const greyLine = document.getElementById('grey-line');
    const hamburgerIcon = document.getElementById('hamburger-icon');


    greyLine.addEventListener('mouseenter', () => {
        greyLine.classList.add('extended');
        hamburgerIcon.style.left = '14.5vw';
    });

    greyLine.addEventListener('mouseleave', () => {
        greyLine.classList.remove('extended');
        hamburgerIcon.style.left = '5.5vw';
    });

    hamburgerIcon.addEventListener('click', () => {
            const isExtended = greyLine.classList.toggle('extended');
            hamburgerIcon.style.left = isExtended ? '13vw' : '4vw';
        });



// JS for notif and sign out

    const notificationOverlay = document.getElementById('notificationOverlay');
    const notifButton = document.getElementById('notifButton');
    notifButton.addEventListener('click', () => {
        notificationOverlay.style.display = notificationOverlay.style.display === 'block' ? 'none' : 'block';
    });

    const signOutOverlay = document.getElementById('signOutOverlay');
    const userButton = document.getElementById('userButton');
    userButton.addEventListener('click', () => {
        signOutOverlay.style.display = signOutOverlay.style.display === 'block' ? 'none' : 'block';
    });

    document.getElementById("signOutOverlay").addEventListener("click", function () {
        window.location.href = "landingPage.html";
    });



// js for policy repository

    function showPoliciesRepository() {
        document.getElementById('policies-repository-content').style.display = 'flex';
        document.getElementById('policy-submission-content').style.display='none';
        document.querySelector('.process-tracker').style.display='none';

    }
    document.querySelectorAll('.category').forEach(category => {
        category.addEventListener('click', function() {
            this.classList.toggle('expanded');
        });
    });

    document.querySelectorAll('.subcategory').forEach(subcategory => {
        subcategory.addEventListener('click', function() {
            const subcategoryName = this.getAttribute('data-subcategory');
            // Handle the subcategory click, e.g., display policies for the selected department
            console.log(`Subcategory clicked: ${subcategoryName}`);
            // You can add logic here to load and display the relevant policies
        });
    });





// js for filter
        const filterButton = document.getElementById('filterButton');
        const filterOverlay = document.getElementById('filterOverlay');
        const cancelFilter = document.getElementById('cancelFilter');
        const applyFilter = document.getElementById('applyFilter');
        const categoryFilter = document.getElementById('categoryFilter');
        const sortFilter = document.getElementById('sortFilter');

        filterButton.addEventListener('click', () => {
            filterOverlay.style.display = 'flex';
        });

        cancelFilter.addEventListener('click', () => {
            filterOverlay.style.display = 'none';
        });

        applyFilter.addEventListener('click', () => {
            const selectedCategory = categoryFilter.value;
            const selectedSort = sortFilter.value;

            filterSubcategories(selectedCategory, selectedSort);
            filterOverlay.style.display = 'none';
        });

    function filterSubcategories(category, sort) {
    const subcategories = document.querySelectorAll('.subcategory');
    subcategories.forEach(subcategory => {
        const subcategoryName = subcategory.getAttribute('data-subcategory');
        const showCategory = !category || category === subcategoryName;

        if (showCategory) {
            subcategory.style.display = 'flex';
        } else {
            subcategory.style.display = 'none';
        }
    });

}



// js for policy submission

    function showPolicySubmission() {
        document.getElementById('policies-repository-content').style.display = 'none';
        document.getElementById('policy-submission-content').style.display = 'flex';
        document.querySelector('.process-tracker').style.display= 'none';
    }

    const cfOverlay = document.getElementById('confirm-dl');
    const dlBtn = document.querySelector('.policy-submission-buttons button:first-child');

    dlBtn.addEventListener('click', () => {
        cfOverlay.style.display = cfOverlay.style.display === 'block' ? 'none' : 'block';
    });

    document.getElementById("first-child").addEventListener("click", function () {
        cfOverlay.style.display = "none";
    });

    document.getElementById("last-child").addEventListener("click", function () {
        cfOverlay.style.display = "none";
    });

    const submitOverlay = document.getElementById('submitOverlay');
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');


    submitButton.addEventListener('click', () => {
        submitOverlay.style.display = submitOverlay.style.display === 'block' ? 'none' : 'block';
    });

    document.getElementById("submitBtn").addEventListener("click", function () {
        submitOverlay.style.display = "none";
    });

    document.getElementById("cancelBtn").addEventListener("click", function () {
        submitOverlay.style.display = "none";
    });



// js for process tracker

    function showProcessTracker() {
        document.getElementById('policies-repository-content').style.display = 'none';
        document.getElementById('policy-submission-content').style.display = 'none';
        document.querySelector('.process-tracker').style.display = 'flex';
    }


document.addEventListener('DOMContentLoaded', function() {
  const submissionsButton = document.getElementById('processTrackerSub-btn');
  const table = document.querySelector('.process-tracker-table');

  if (submissionsButton && table) {
    submissionsButton.addEventListener('click', function() {
      if (this.textContent === 'Submissions') {
        this.textContent = 'Feedbacks';
        // Change table to 3 columns
        table.innerHTML = `
          <thead>
            <tr>
              <th>Policy Reported</th>
              <th>Date Submitted</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Student Handbook</td>
              <td>10/24/24</td>
              <td class="status-reviewed">Reviewed</td>
            </tr>
            <tr>
              <td>Organizational Profile</td>
              <td>03/03/24</td>
              <td class="status-submitted">Submitted</td>
            </tr>
            <tr>
              <td>Student Dress Code</td>
              <td>06/07/24</td>
              <td class="status-addressed">Addressed</td>
            </tr>
          </tbody>
        `;
      } else {
        this.textContent = 'Submissions';
        // Change table back to original columns
        table.innerHTML = `
          <thead>
            <tr>
              <th>Policy Title</th>
              <th>Date Submitted</th>
              <th>Version no.</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Student Handbook</td>
              <td>10/24/24</td>
              <td>5.0</td>
              <td class="status-reviewed">Reviewed</td>
            </tr>
            <tr>
              <td>Organizational Profile</td>
              <td>03/03/24</td>
              <td>3.0</td>
              <td class="status-verified">Verified</td>
            </tr>
            <tr>
              <td>Student Dress Code</td>
              <td>06/07/24</td>
              <td>4.0</td>
              <td class="status-approved">Approved</td>
            </tr>
          </tbody>
        `;
      }
    });
  }
});


</script>
</html>