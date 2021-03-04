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

if ($_POST['add'] == 1){
    $stmt = $connection->prepare("SELECT UserID FROM JoinRequest WHERE ID=:rid");
    $stmt->bindValue(":rid", $_POST['reqID'], SQLITE3_INTEGER);
    $results = $stmt->execute();
    $id = $results->fetchArray(SQLITE3_ASSOC)['UserID'];

    $stmt = $connection->prepare("UPDATE User SET GroupID=:gid WHERE ID=:id");
    $stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
    $stmt->bindValue(":id", $id, SQLITE3_INTEGER);
    
    $result = $stmt->execute();
}

$stmt = $connection->prepare("DELETE FROM JoinRequest WHERE ID=:id");
$stmt->bindValue(":id", $_POST['reqID'], SQLITE3_INTEGER);
$result = $stmt->execute();

echo json_encode(["0", $id]);
?>