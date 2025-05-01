
<style>
    .white-line{
        position: absolute;
        left: 0;
        width: 100%;
        height: auto;
    }
    .white-line {
        top: 0;
        height: 2.8vw;
        background-color: white;
        border-width: 0.2vw;
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