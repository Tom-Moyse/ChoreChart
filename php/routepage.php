<?php
if (!isset($_SESSION)){
    session_start();
}
if(!isset($_SESSION['uid'])){
    header("Location:index.php");
    exit();
}

include ("./database.php");
$connection = new Database();

$stmt = $connection->prepare('SELECT GroupID FROM User WHERE ID=:id');
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$results = $stmt->execute();
$res = $results->fetchArray(SQLITE3_ASSOC);
print_r($res);
?>