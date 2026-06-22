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

.notif-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #ff3b30;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: bold;
    display: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
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
                        echo "<div class='notification-item unread' data-id='$notifID' onclick='handleNotificationClick(this)' style='border-left: 4px solid #fbaf41; cursor: pointer;'><p style='margin:0; font-weight:bold;'>$msg</p></div>";    
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
                        $notifID = $row['notifID'];
                        echo "<div class='notification-item' data-id='$notifID' onclick='handleNotificationClick(this)' style='background-color:#555; border-left: 4px solid transparent; cursor: pointer;'><p style='margin:0; color:#d3d3d3;'>$msg</p></div>";
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
    document.addEventListener("DOMContentLoaded", function() {
        const notifBtn = document.getElementById('notifButton');
        if (notifBtn) {
            // Ensure the button is relatively positioned so the absolute badge sticks to it
            notifBtn.style.position = 'relative';
            let badge = document.getElementById('notifBadge');
            if (!badge) {
                badge = document.createElement('span');
                badge.id = 'notifBadge';
                badge.className = 'notif-badge';
                notifBtn.appendChild(badge);
            }
            
            updateNotifBadgeCount();

            // Automatically update the badge if unread notifications change in the DOM
            const unreadList = document.getElementById('notif-unread-list');
            if (unreadList) {
                const observer = new MutationObserver(updateNotifBadgeCount);
                observer.observe(unreadList, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });
            }
        }
    });

    function updateNotifBadgeCount() {
        const badge = document.getElementById('notifBadge');
        if (badge) {
            const unreadCount = document.querySelectorAll('#notif-unread-list .notification-item.unread').length;
            if (unreadCount > 0) {
                badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }
    }



    function handleNotificationClick(item) {
        const notifID = item.getAttribute('data-id');
        const isUnread = item.classList.contains('unread');

        // Only mark as read if it is unread
        if (isUnread) {
            fetch('/qms_optiqual/generalComponents/header/markNotifsReadBE.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ notifID: notifID })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Visually change the notification to 'read'
                    item.classList.remove('unread');
                    item.style.backgroundColor = '#555';
                    item.style.borderLeft = '4px solid transparent';
                    const p = item.querySelector('p');
                    if (p) {
                        p.style.fontWeight = 'normal';
                        p.style.color = '#d3d3d3';
                    }
                    
                    // Move to read list
                    const readList = document.getElementById('notif-read-list');
                    const noReadNotif = readList.querySelector('.no-notifications');
                    if (noReadNotif) noReadNotif.style.display = 'none';
                    
                    readList.prepend(item);

                    // Check if unread list is now empty
                    const unreadList = document.getElementById('notif-unread-list');
                    if (unreadList.querySelectorAll('.notification-item.unread').length === 0) {
                        let noUnreadNotif = unreadList.querySelector('.no-notifications');
                        if (!noUnreadNotif) {
                            noUnreadNotif = document.createElement('p');
                            noUnreadNotif.className = 'no-notifications';
                            noUnreadNotif.innerHTML = "<i class='fas fa-bell-slash' style='display:block; font-size:24px; margin-bottom:10px; opacity:0.5;'></i>No unread notifications.";
                            unreadList.appendChild(noUnreadNotif);
                        }
                        noUnreadNotif.style.display = 'block';
                    }
                    updateNotifBadgeCount();
                }
            })
            .catch(err => console.error(err));
        }

        // Redirect based on message content
        const msgText = item.querySelector('p').innerText.toLowerCase();
        
        // ✨ FIX: Properly close the main popup overlay instead of hiding the inner wrapper
        const popup = document.getElementById('popupOverlay');
        if (popup) popup.style.display = 'none';
        if (msgText.includes('feedback') || msgText.includes('reply') || msgText.includes('policy') && !msgText.includes('task') && !msgText.includes('submission')) {
            if (typeof showPolicyRepository === 'function') {
                showPolicyRepository();
            } else if (typeof showWorkspace === 'function') {
                showWorkspace();
            }
        } else if (msgText.includes('assigned a new task') || msgText.includes('task') || msgText.includes('submission') || msgText.includes('pending') || msgText.includes('verify') || msgText.includes('approve') || msgText.includes('reject')) {
            if (typeof showWorkspace === 'function') {
                showWorkspace();
            }
        } else {
            // Default to workspace
            if (typeof showWorkspace === 'function') {
                showWorkspace();
            }
        }
    }
</script>