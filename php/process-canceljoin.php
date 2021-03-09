<?php
// File handles case that user wishes to cancel their join request
// Removes relevant joinrequest record from table
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: ./signout.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();
require_joining_status();

$connection = new Database();
$stmt = $connection->prepare('DELETE FROM JoinRequest WHERE UserID=:id');
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->execute();
echo 0;
?>