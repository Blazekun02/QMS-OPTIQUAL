<?php
include("../../connect.php");

if (isset($_POST['policyID'])) {
    $policyID = (int) $_POST['policyID'];

    $query = "
        SELECT p.*, 
               r.lName AS reviewerName, 
               v.lName AS verifierName, 
               a.lName AS approverName
        FROM policytbl p
        LEFT JOIN accdatatbl r ON p.policyReviewer = r.accID
        LEFT JOIN accdatatbl v ON p.policyVerifier = v.accID
        LEFT JOIN accdatatbl a ON p.policyApprover = a.accID
        WHERE p.policyID = $policyID
        LIMIT 1
    ";
    $result = mysqli_query($conn, $query);
    $policy = mysqli_fetch_assoc($result);

    if ($policy) {
        function getCircleClass($step, $statusID) {
            if ($step < $statusID) return 'status-green';
            if ($step == $statusID) return 'status-green';
            if ($step == $statusID + 1) return 'status-red';
            return 'status-gray';
        }
        
        echo '
        <div class="circle_container">
            <p><em>Submitted</em></p>
            <div class="circle ' . getCircleClass(1, $policy['policyStatusID']) . '"></div>
            <span>' . htmlspecialchars($policy['dateSubmitted']) . '</span>
        </div>
        <div class="circle_container">
            <p><em>Reviewed</em></p>
            <div class="circle ' . getCircleClass(2, $policy['policyStatusID']) . '"></div>
            <span>' . htmlspecialchars($policy['dateReviewed'] ?? '-') . '</span>
            <span>' . htmlspecialchars($policy['reviewerName'] ?? 'No Reviewer') . '</span>
        </div>
        <div class="circle_container">
            <p><em>Verified</em></p>
            <div class="circle ' . getCircleClass(3, $policy['policyStatusID']) . '"></div>
            <span>' . htmlspecialchars($policy['dateVerified'] ?? '-') . '</span>
            <span>' . htmlspecialchars($policy['verifierName'] ?? 'No Verifier') . '</span>
        </div>
        <div class="circle_container">
            <p><em>Approved</em></p>
            <div class="circle ' . getCircleClass(4, $policy['policyStatusID']) . '"></div>
            <span>' . htmlspecialchars($policy['dateApproved'] ?? '-') . '</span>
            <span>' . htmlspecialchars($policy['approverName'] ?? 'No Approver') . '</span>
        </div>
        <div class="circle_container">
            <p><em>Uploaded</em></p>
            <div class="circle ' . getCircleClass(5, $policy['policyStatusID']) . '"></div>
            <span>' . htmlspecialchars($policy['dateUploaded'] ?? '-') . '</span>
        </div>';
    } else {
        echo json_encode(['error' => 'Policy not found or query error']);
    }
}
?>