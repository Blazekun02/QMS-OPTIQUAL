<?php
include("../../connect.php");  

?>

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

<div class="Process-Tracker-Panel" style="display: none;">
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
                $processTrack = "SELECT p.*, s.policyStatusName 
                                FROM policytbl p 
                                LEFT JOIN policystatus s 
                                ON p.policyStatusID = s.policyStatusID 
                                ORDER BY dateSubmitted DESC";

                $my_sqli = mysqli_query($conn, $processTrack);  

                while ($row = mysqli_fetch_assoc($my_sqli)) { 
            ?>
                <tr class="folder-row">
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['dateSubmitted']) ?></td>
                    <td><?= htmlspecialchars($row['versionNo'] ?? 'New') ?></td>
                    <td><?= htmlspecialchars($row['policyStatusName'] ?? 'Pending') ?></td>
                </tr>
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
        justify-content: space-between;
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
        justify-content: space-around; /* or space-between / gap */
        align-items: center;
        height: 75px;
       
        
    }

    .First_Circle, .Second_Circle, .Third_Circle, .Fourth_Circle, .Fifth_Circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        background-color:rgb(70, 70, 70);

    }

    .circle_container {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: white;
    font-size: 14px;
    text-align: center;
    gap: 4px; /* consistent spacing */
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
    

</style>

<div class="Process-Tracker-Content">
    <div class="Process-Tracker-Content-Header">
        <h1>Process Tracker</h1>
        <div class="PTC-Header-Buttons">
            <button id="PTCtoggleButton">VIEW FEEDDBACKS</button>
        </div>
        <div class="PTC-Divider"></div>
    </div>

    <?php 
    // Replace this with your actual policy ID condition if needed
    $query = "
        SELECT p.*, 
               r.lName AS reviewerName, 
               v.lName AS verifierName, 
               a.lName AS approverName
        FROM policytbl p
        LEFT JOIN accdatatbl r ON p.policyReviewer = r.accID
        LEFT JOIN accdatatbl v ON p.policyVerifier = v.accID
        LEFT JOIN accdatatbl a ON p.policyApprover = a.accID
        ORDER BY p.dateSubmitted DESC
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);
    $policy = mysqli_fetch_assoc($result);
?>


    <div class="PTC-Trackers">
        <div class="circle_container">
        <p><em>Submitted</em></p>
            <div class="First_Circle"></div>
            <span><?= htmlspecialchars($policy['dateSubmitted']) ?></span>
        </div>
        <div class="circle_container">
        <p><em>Reviewed</em></p>
            <div class="Second_Circle"></div>
            <span><?= htmlspecialchars($policy['dateReviewed'] ?? '-') ?></span>
            <span><?= htmlspecialchars($policy['reviewerName'] ?? 'No Reviewer') ?></span>
        </div>
        <div class="circle_container">
        <p><em>Verified</em></p>
            <div class="Third_Circle"></div>
            <span><?= htmlspecialchars($policy['dateVerified'] ?? '-') ?></span>
            <span><?= htmlspecialchars($policy['verifierName'] ?? 'No Verifier') ?></span>
        </div>
        <div class="circle_container">
        <p><em>Approved</em></p>
            <div class="Fourth_Circle"></div>
            <span><?= htmlspecialchars($policy['dateApproved'] ?? '-') ?></span>
            <span><?= htmlspecialchars($policy['approverName'] ?? 'No Approver') ?></span>
        </div>
        <div class="circle_container">
        <p><em>Uploaded</em></p>
            <div class="Fifth_Circle"></div>
            <span><?= htmlspecialchars($policy['dateUploaded'] ?? '-') ?></span>
            
        </div>

    </div>


</div>

<!-- <script>
 
    function showPolicyTracker() {
    document.getElementById('policy-repo-content').style.display = 'none';
    document.querySelector('policy-submission-content').style.display = 'none';
    document.querySelector('.Department-Manager-Panerl').style.display = 'none';
    document.querySelector('.Process-Tracker-Panel').style.display = 'block';

    }
</script> -->