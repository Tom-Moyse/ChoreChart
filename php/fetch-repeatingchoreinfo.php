<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: ./signout.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();
require_group();
require_no_joining_status();

$connection = new Database();

$stmt = $connection->prepare("SELECT Chore.contents, User.ID, Chore.startdate, Chore.interval FROM Chore 
    INNER JOIN User ON Chore.UserID = User.ID WHERE Chore.ID = :id");
$stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);

if ($res != false){
    echo (json_encode(["0", $res['contents'], $res['ID'], $res['startdate'], $res['interval']]));
}
else{
    $stmt = $connection->prepare("SELECT Chore.contents, Chore.startdate, Chore.interval FROM Chore
        WHERE Chore.ID = :id");
    $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
    $result = $stmt->execute();
    $res = $result->fetchArray(SQLITE3_ASSOC);
    echo (json_encode(["0", $res['contents'], "0", $res['startdate'], $res['interval']]));
}
?>