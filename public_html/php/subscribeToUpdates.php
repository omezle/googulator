<?php
header('Content-type: application/json');
require_once "../configuration.php";
require_once "../include.php";
if ($AUTO_ESCAPE){
    $email = stripslashes($_GET["address"]);
}
else{
    $email = $_GET["address"];
}

$emailRegex = '/\A[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}\z/';

if (!preg_match($emailRegex,$email)){
    echo json_encode(["result" => "invalid"]);
}

if (sendEmail($email,"This is a test email to verify that we can send emails to you.")){


    $con = mysql_connect('localhost', $MYSQL_USERNAME, $MYSQL_PASSWORD);
    mysql_select_db($MYSQL_DATABASE, $con);
    mysql_query("insert into emailUpdates (email) values ('$email');",$con);
    mysql_close($con);



    echo json_encode(["result" => "success"]);
}
else{
    echo json_encode(["result" => "invalid"]);
}
