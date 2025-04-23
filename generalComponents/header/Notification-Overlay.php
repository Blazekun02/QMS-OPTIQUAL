<style>
.overlay {
    position: fixed;
    width: 250px;
    height: 250px;
    top: 6%;
    right: 15%;
    background-color: #343A40;
    border-radius: 10px;
    z-index: 9999;
    padding: 10px;
}

.overlay-header {
    color: white;
    font-size: 20px;
    margin-bottom: 10px;
}

.notif-buttons {
    margin-bottom: 10px;
}

.read-button,
.unread-button {
    display: inline-block;
    background-color: #343A40;
    font-size: 13px;
    border: 2px solid white;
    border-radius: 20px;
    color: white;
    cursor: pointer;
    padding: 1px 10px;
    text-align: center;
    text-decoration: none;
    margin-right: 5px;
}

.divider {
    width: 100%;
    height: 0.5px;
    background-color: white;
    margin: 10px 0;
}

.notification-list {
    max-height: 130px;
    overflow-y: auto;
    margin-left: 10px;
}

.read-notification-list,
.unread-notification-list {
    display: none;
}

.notification-item {
    min-height: 30px;
    padding: 5px;
}

.notification-text {
    font-size: 16px;
    color: white;
    margin: 0;
}

.notification-text:hover {
    color: #fbaf41;
}

.notification-item:hover {
    background-color: #495057;
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const readBtn = document.querySelector('.read-button');
    const unreadBtn = document.querySelector('.unread-button');
    const readList = document.querySelector('.read-notification-list');
    const unreadList = document.querySelector('.unread-notification-list');

    // Default state
    unreadList.style.display = 'block';

    readBtn.addEventListener('click', function () {
        unreadList.style.display = 'none';
        readList.style.display = 'block';
    });

    unreadBtn.addEventListener('click', function () {
        readList.style.display = 'none';
        unreadList.style.display = 'block';
    });
});

</script>

<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "qms";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
?>

<div class="overlay" id="overlay">
    <h2 class="overlay-header">Notifications</h2>

    <div class="notif-buttons">
        <button class="unread-button">Unread</button>
        <button class="read-button">Read</button>
    </div>

    <div class="divider"></div>

    <div class="notification-list unread-notification-list">
        <?php
        $query = "SELECT * FROM notiftbl WHERE notifStatus = 0 ORDER BY dateTimeSent DESC";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="notification-item">';
                echo '<p class="notification-text">' . $row['message'] . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p class="no-notifications">No unread notifications.</p>';
        }
        ?>
    </div>

    <div class="notification-list read-notification-list">
        <?php
        $query = "SELECT * FROM notiftbl WHERE notifStatus = 1 ORDER BY dateTimeSent DESC";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="notification-item">';
                echo '<p class="notification-text">' . $row['message'] . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p class="no-notifications">No read notifications.</p>';
        }
        ?>
    </div>
</div>
