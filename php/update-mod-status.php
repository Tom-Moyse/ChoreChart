<?php
// Basic file that given a user id via post will update the moderator status for the given user
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
$stmt = $connection->prepare("UPDATE User SET moderator=:mod WHERE ID=:id");
$stmt->bindValue(":mod", $_POST['moderator'], SQLITE3_INTEGER);
$stmt->bindValue(":id", $_POST['uid'], SQLITE3_INTEGER);
$stmt->execute();
echo 0;
?>