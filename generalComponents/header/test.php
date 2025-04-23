<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.gaoogleapis.com/icon?family=Material+Icons">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
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
        
        .white-line{
            position: absolute;
            left: 0;
            width: 100%;
            height: auto;
        }
        .white-line {
            top: 0;
            height: 2.8vh;
            background-color: white;
            border-width: 0.3vw;
            border-style: solid;
            border-color: transparent transparent  #fbaf41 transparent;
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

    </style>
    <div class="yellow-line"></div>
    <div class="image"></div>
    <div class="white-line"></div>
    <div class="blue-line"></div>
    <div class="copyright-label">Copyright © 2024 OPTIQUAL. All rights reserved</div>


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
            Sign out
        </div>
    </div>
</body>
</html>