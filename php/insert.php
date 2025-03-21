<?php
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];
$roleID = $_POST['4'];

if (!empty($firstname) || !empty($lastname) || !empty($email) || !empty($password)) {
    $host = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "qms";
    //create connection
    $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
    if (mysqli_connect_error()) {
        die('Connect Error('.mysqli_connect_errno().')'.mysqli_connect_error());
    } else {
        $SELECT = "SELECT email From accdatatbl Where email = ? Limit 1";
        $INSERT = "INSERT Into accdatatbl (firstname, lastname, email, password, roleID) values(?, ?, ?, ?, ?)";
       
        //Prepare statement
        $stmt = $conn->prepare($SELECT);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->store_result();
        $rnum = $stmt->num_rows;
        if ($rnum==0) {
            $stmt->close();
            $stmt = $conn->prepare($INSERT);
            $stmt->bind_param("ssssi", $firstname, $lastname, $email, $password, $roleID);
            $stmt->execute();
            echo "New record inserted sucessfully";
        } else {
            echo "Someone already register using this email";
        }
        $stmt->close();
        $conn->close();
    }
} else {
    //Display error message
    echo "All fields are required.";
    die();
}

?>