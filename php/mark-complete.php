<?php
// Simple file that given a choreitem id and a completed status sets the database to match the
// requested values
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: signout.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();

$connection = new Database();

// Create a new join request
$stmt = $connection->prepare('UPDATE ChoreItem SET completed=:comp WHERE ID=:id AND UserID=:id2');
$stmt->bindValue(':comp', $_POST['complete'], SQLITE3_INTEGER);
$stmt->bindValue(':id', $_POST['ID'], SQLITE3_INTEGER);
$stmt->bindValue(':id2', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->execute();

echo 0;
?>