<style>
/* ✨ FIX: Removed the #popupOverlay styling so we don't accidentally create an invisible wall! 
 Only the specific notification box itself gets moved to the front.
*/
.notif-wrapper {
    position: fixed; 
    top: 75px;       
    right: 150px;    
    z-index: 99999 !important; 
    width: 250px;
    background-color: #343A40;
    padding: 15px;
    
    width: 250px;
    height: auto;
    max-height: 350px;
    background-color: #343A40;
    border-radius: 10px;
    padding: 15px;
    box-sizing: border-box;
    font-family: 'Istok Web', sans-serif;
    color: white;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6); 
}

.notif-header {
    font-size: 20px;
    margin-top: 0;
    margin-bottom: 15px;
    font-weight: bold;
}

.notif-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.notif-btn-toggle {
    background-color: transparent;
    font-size: 13px;
    border: 2px solid white;
    border-radius: 20px;
    color: white;
    cursor: pointer;
    padding: 3px 15px;
    font-family: 'Istok Web', sans-serif;
    font-weight: bold;
    transition: 0.2s;
}

.notif-btn-toggle:hover {
    background-color: #fbaf41;
    color: black;
    border-color: #fbaf41;
}

.notif-divider {
    width: 100%;
    height: 1px;
    background-color: rgba(255, 255, 255, 0.2);
    margin: 10px 0;
}

.notification-list {
    max-height: 180px;
    overflow-y: auto;
    padding-right: 5px;
}

.notification-list::-webkit-scrollbar { width: 5px; }
.notification-list::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 5px; }
.notification-list::-webkit-scrollbar-thumb { background: #fbaf41; border-radius: 5px; }

.notification-item {
    background-color: #4963D4;
    color: white;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 8px;
    font-size: 13px;
    line-height: 1.4;
}

.no-notifications {
    color: #d3d3d3;
    font-size: 14px;
    text-align: center;
    margin-top: 20px;
}
</style>

<div class="notif-wrapper">
    <h2 class="notif-header">Notifications</h2>

    <div class="notif-buttons">
        <button class="notif-btn-toggle" onclick="document.getElementById('notif-unread-list').style.display='block'; document.getElementById('notif-read-list').style.display='none';">Unread</button>
        <button class="notif-btn-toggle" onclick="document.getElementById('notif-unread-list').style.display='none'; document.getElementById('notif-read-list').style.display='block';">Read</button>
    </div>

    <div class="notif-divider"></div>

    <div id="notif-unread-list" class="notification-list" style="display: block;">
        <?php
        try {
            $currentUserID = isset($_SESSION['accID']) ? (int)$_SESSION['accID'] : 0;
            
            if (isset($conn)) {
                $query = "SELECT * FROM notiftbl WHERE notifStatus = 0 AND receivedBy = " . $currentUserID . " ORDER BY dateTimeSent DESC";
                $result = $conn->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $msg = htmlspecialchars((string)$row['message']);
                        // ✨ ADDED: notifID, the 'unread' class, and cursor:pointer
                        $notifID = $row['notifID'];
                        echo "<div class='notification-item unread' data-id='$notifID' style='border-left: 4px solid #fbaf41; cursor: pointer;'><p style='margin:0; font-weight:bold;'>$msg</p></div>";    
                    }
                } else {
                    echo "<p class='no-notifications'><i class='fas fa-bell-slash' style='display:block; font-size:24px; margin-bottom:10px; opacity:0.5;'></i>No unread notifications.</p>";
                }
            } else {
                echo "<p style='color:red;'>Database connection missing.</p>";
            }
        } catch (\Throwable $e) {
            // Safely catches ANY backend error so it doesn't break the HTML
            echo "<p style='color:red;'>Error loading notifications.</p>";
        }
        ?>
    </div>

    <div id="notif-read-list" class="notification-list" style="display: none;">
        <?php
        try {
            if (isset($conn)) {
                $query = "SELECT * FROM notiftbl WHERE notifStatus = 1 AND receivedBy = $currentUserID ORDER BY dateTimeSent DESC";
                $result = $conn->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // ✨ ADDED: Dimmed gray background and normal text for READ
                        $msg = htmlspecialchars((string)$row['message']);
                        echo "<div class='notification-item' style='background-color:#555; border-left: 4px solid transparent;'><p style='margin:0; color:#d3d3d3;'>$msg</p></div>";
                    }
                } else {
                    echo "<p class='no-notifications'><i class='fas fa-envelope-open' style='display:block; font-size:24px; margin-bottom:10px; opacity:0.5;'></i>No read notifications.</p>";
                }
            }
        } catch (\Throwable $e) {
            echo "<p style='color:red;'>Error loading notifications.</p>";
        }
        ?>
    </div>

</div>

<script>
    function toggleNotifications() {
        const notifBox = document.querySelector('.notif-wrapper');
        if (notifBox.style.display === 'none' || notifBox.style.display === '') {
            notifBox.style.display = 'block';
        } else {
            notifBox.style.display = 'none';
        }
    }
</script>