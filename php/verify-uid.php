<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: signout.php");
    exit();
}
if (!isset($_SESSION)){
    session_start();
}

include_once("../main.php");
include(ROOT."/php/database.php");
$connection = new Database();

$stmt = $connection->prepare("SELECT displayname, moderator FROM User WHERE ID=:id AND GroupID=:gid");
$stmt->bindValue(":id", $_POST['uid'], SQLITE3_INTEGER);
$stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);

$returnArr = ["0", $res['displayname'], $res['moderator']];
echo (json_encode($returnArr));
?>