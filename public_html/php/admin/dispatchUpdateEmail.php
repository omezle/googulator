<?php
header('Content-type: application/json');
require_once "../../configuration.php";
require_once "../../include.php";

if ($AUTO_ESCAPE){
    $google_token = stripslashes($_GET["googletoken"]);
}
else{
    $google_token = $_GET["googletoken"];
}
$google_id = getGoogleId($google_token);

$con = mysql_connect($MYSQL_HOSTNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD);
mysql_select_db($MYSQL_DATABASE, $con);

$numEmailsPerSession = 1;

if (hasRole($google_id,"ROLE_ADMIN",$con)){
    $startPosition = $_GET["startId"];
    if ($startPosition == NULL){
        $startPosition = 0;
    }
    $query = mysql_query("select * from emailUpdates where id>$startPosition order by id asc limit $numEmailsPerSession;",$con);
    $numRows = mysql_num_rows($query);
    $lastId = $startPosition;
    while ($row = mysql_fetch_assoc($query)){
        //TODO: do something (not sure what) when an email fails to send
        sendEmail($row["email"],$_GET["message"]);
        $lastId = $row["id"];
    }
    echo json_encode(["status"=>"success","done"=>($numRows < $numEmailsPerSession),"lastId"=>$lastId,"numSent" => $numRows]);
}
else{
    echo json_encode(["status"=>"failed","error"=>"PERMISSION_DENIED"]);
}
mysql_close($con);