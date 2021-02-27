<?php
include_once("../main.php");
include(ROOT.'/php/utils.php');
require_login();

$connection = new Database();

$stmt = $connection->prepare('SELECT GroupID FROM User WHERE ID=:id');
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$results = $stmt->execute();
$res = $results->fetchArray(SQLITE3_ASSOC);
// If not part of group then see if currentlty joining or not
if ($res['GroupID'] == null){
    $stmt = $connection->prepare('SELECT ID FROM JoinRequest WHERE UserID=:id');
    $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
    $results = $stmt->execute();
    $res = $results->fetchArray(SQLITE3_ASSOC);
    if ($res == false){
        $connection = null;
        header("Location: ../joingroup.php");
        exit();
    }
    else{
        header("Location: ../joinstatus.php");
        exit();
    }
}
else{
    $_SESSION['gid'] = $res['GroupID'];
    header("Location: ../chores.php");
    exit();
}
?>