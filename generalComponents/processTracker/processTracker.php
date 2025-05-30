
<style>
/* Process Tracker */
.Process-Tracker-Panel {
    position: absolute;
    background-color: #293A82;
    border-radius: 20px;
    top: 100px;
    left: 0.9in;    
    width: calc(100% - 1.3in);
    height: calc(100vh - 169px);
    border: 2px solid red;
    z-index: 1;
    padding-left: 10px;
   padding-right: 10px;
}

.PT-Divider {
    position: absolute;
    top: 60px;
    background-color:  black;
    width: 100%;
    height: 2px;
}

.Process-Tracker-Header {
    display: flex;
    position: relative;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}

table {
    width: 100%;
    border-collapse: collapse;
    
}
th {
    background-color: #E7AE41;
    color: white;
    padding: 10px;
}

td {
    background-color: #F2F2F2;
    padding: 10px;
    
    text-align: center;
}

tr.folder-row:hover td {
    background-color: gray;
}

tr.folder-row td:first-child {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
}
tr.folder-row td:last-child {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
}

tr.folder-row td:nth-child(4) {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
}

thead th:first-child {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
}

thead th:last-child {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
}

#toggleButton {
    background-color: white;
    color: black;
    border: none;
    padding: 5px 10px;
    margin-bottom: 10px;
    font-size: 16px;
    border-radius: 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#toggleButton:hover {
    background-color: #1f2d66;
    color: white;
}
</style>

<div class="Process-Tracker-Panel" style="display: block;">
    <div class="Process-Tracker-Header">
        <h1>Process Tracker</h1>

        <div class="PT-Header-Buttons">
            <button id="toggleButton" >Submissions</button>
        </div>
        <div class="PT-Divider"></div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Policy Title</th>
                <th>Date Submitted</th>
                <th>Version No.</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                

                include("../../connect.php");
                
                // Validate and assign $currentAccID
                if (isset($_SESSION['accID'])) {
                    $currentAccID = (int) $_SESSION['accID']; // Cast to integer for safety
                } else {
                    die("User not logged in or session expired."); // Handle missing session
                }
                
                // SQL query
                $processTrack = "SELECT p.*, s.policyStatusName 
                 FROM policytbl p 
                 LEFT JOIN policystatus s ON p.policyStatusID = s.policyStatusID 
                 WHERE p.policyAuthor = $currentAccID 
                 ORDER BY dateSubmitted DESC";
                
                $my_sqli = mysqli_query($conn, $processTrack);
                
                if (!$my_sqli) {
                    die("Error in SQL query: " . mysqli_error($conn)); // Debugging SQL errors
                }

                while ($row = mysqli_fetch_assoc($my_sqli)) { 
            ?>
                <form method="POST" style="margin: 0;">
                <tr class="folder-row" data-id="<?= $row['policyID'] ?>" data-title="<?= htmlspecialchars($row['title']) ?>">
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['dateSubmitted']) ?></td>
                    <td><?= htmlspecialchars($row['versionNo'] ?? 'New') ?></td>
                    <td><?= htmlspecialchars($row['policyStatusName'] ?? 'Pending') ?></td>
                    <td style= "display: none"><?= htmlspecialchars($row['policyID'] ?? '0') ?></td>
                </tr>
                </form>
            <?php } ?>
        </tbody>
    </table>
</div>

<style>
.Process-Tracker-Content {
    position: absolute;
    background-color: #293A82;
    border-radius: 20px;
    top: 100px;
    left: 0.9in;    
    width: calc(100% - 1.3in);
    height: calc(100vh - 169px);
    border: 2px solid red;
    z-index: 1;
    padding-left: 10px;
    padding-right: 10px;
}

.Process-Tracker-Content-Header {
    display: flex;
    position: relative;
    flex-direction: row;
    align-items: center;

}

.PTC-Divider {
    position: absolute;
    top: 60px;
    background-color:  black;
    width: 100%;
    height: 2px;
}

.PTC-Trackers {
    display: flex;
    justify-content: space-around;
    align-items: center;
    height: 75px;
}

.PTC-Header-Buttons {
    margin-left: auto; 

}

.PTCtoggleButton {
    background: none;
    border: none;
    outline: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 10px;
    transition: color 0.3s ease;
}

.PTCtoggleButton:hover {
    background-color:rgb(9, 10, 85); 
}

.circle_container {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: white;
    font-size: 14px;
    text-align: center;
    gap: 4px; 
    padding: 5px 0;
}

.circle_container p {
    margin: 0;
    font-weight: bold;
}

.circle_container span {
    margin: 0;
    font-size: 12px;
    line-height: 1.2;
}

.circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.status-green {
    background-color: green;
}

.status-red {
    background-color: red;
}

.status-gray {
    background-color: gray;
}

.back-button {
    background: none;
    border: none;
    outline: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 10px;
    transition: color 0.3s ease;
}

.back-button:hover {
    background-color:rgb(9, 10, 85);
}

</style>

<div class="Process-Tracker-Content" style = "display: none;">
    
    <div class="Process-Tracker-Content-Header">
    <button id="backButton" class="back-button">←</button>
        <h1 id="PTC-Header-Title">Process Tracker</h1>
        <div class="PTC-Header-Buttons">
            <button class="PTCtoggleButton"id="PTCtoggleButton">VIEW FEEDDBACKS</button>
        </div>
        <div class="PTC-Divider"></div>
    </div>
    <div class="PTC-Trackers">

    <?php
        include ("processTracker_PolicyDetails.php");
        
        
        ?>
    
  



<script>
    document.querySelectorAll('.folder-row').forEach(row => {
        row.addEventListener('click', function() {
            const title = row.getAttribute('data-title');
            document.querySelector('.Process-Tracker-Content').style.display = 'block';
            document.querySelector('.Process-Tracker-Panel').style.display = 'none';
            document.getElementById('PTC-Header-Title').textContent = title;
        });
    });

    document.getElementById('backButton').addEventListener('click', function() {
        document.querySelector('.Process-Tracker-Content').style.display = 'none';
        document.querySelector('.Process-Tracker-Panel').style.display = 'block';
    });


    document.querySelectorAll('.folder-row').forEach(row => {
    row.addEventListener('click', function () {
        const policyID = row.getAttribute('data-id');
        const title = row.getAttribute('data-title');

        // Hide panel, show content
        document.querySelector('.Process-Tracker-Panel').style.display = 'none';
        document.querySelector('.Process-Tracker-Content').style.display = 'block';
        document.getElementById('PTC-Header-Title').textContent = title;

        // Fetch data from PHP using AJAX
        fetch('../../generalComponents/processTracker/processTracker_PolicyDetails.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'policyID=' + encodeURIComponent(policyID)
        })
        .then(response => response.text())  // Expecting HTML
        .then(html => {
            document.querySelector('.PTC-Trackers').innerHTML = html;  // Insert the HTML
        });
    });
});





    


</script>
<!-- <script>
 
    function showPolicyTracker() {
    document.getElementById('policy-repo-content').style.display = 'none';
    document.querySelector('policy-submission-content').style.display = 'none';
    document.querySelector('.Department-Manager-Panerl').style.display = 'none';
    document.querySelector('.Process-Tracker-Panel').style.display = 'block';
    }
</script> -->