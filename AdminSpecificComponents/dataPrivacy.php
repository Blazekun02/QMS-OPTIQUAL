<?php
    $connectPath = __DIR__ . '/../connect.php';
    if (file_exists($connectPath)) {
        include $connectPath;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Privacy Agreement</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="dataPrivacy.css?v=1.1">
</head>
<body>

<div class="dataPriv-overlay">
  <div class="dataPrivy-container">
      <button type="button" class="back-button" id="backButton">
          <i class="fa-solid fa-arrow-left"></i>
      </button>
      
      <div class="header-group">
          <img src="../../assets/logos/logo.png" alt="APC Logo" class="apc-logo">
          <h1>Data Privacy Agreement</h1>
      </div>
        
        <div class="label">
            <?php
                if (isset($conn)) {
                    $dpaQuery = "SELECT dpaContents FROM dpatbl ORDER BY dpaVersion DESC LIMIT 1";
                    $dpaResult = $conn->query($dpaQuery);
                    if ($dpaResult && $dpaResult->num_rows > 0) {
                        $dpaRow = $dpaResult->fetch_assoc();
                        echo nl2br(htmlspecialchars($dpaRow['dpaContents']));
                    } else {
                        echo 'Data Privacy Agreement content is currently unavailable.';
                    }
                } else {
                    echo 'Could not connect to the database to load the agreement.';
                }
            ?>
        </div>
        
        <div class="agree-container">
            <input type="checkbox" id="agree" value="Agree;">
            <label for="agree">I agree to the Data Privacy Agreement</label>
        </div>

        <button type="button" class="submit-button" id="submitButton">Submit</button>

        <div id="messageBox" class="messageBox">
            Please agree to the Data Privacy Agreement to proceed.
        </div>
    </div>
</div>

<script src="dataPrivacy.js"></script>
</body>

</html>