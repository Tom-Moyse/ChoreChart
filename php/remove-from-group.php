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

$stmt = $connection->prepare("UPDATE User SET GroupID=NULL WHERE ID=:id");
$stmt->bindValue(":id", $_POST['uid'], SQLITE3_INTEGER);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);

echo "0";
?>