<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: register.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();

$connection = new Database();

// Check if code given was valid
$stmt = $connection->prepare('SELECT ID FROM ChoreGroup WHERE code = :code');
$stmt->bindValue(':code', $_POST['code'], SQLITE3_TEXT);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);
if ($res['ID'] == null){
    echo 1;
    exit();
}

// Create a new join request
$stmt = $connection->prepare('INSERT INTO JoinRequest VALUES (NULL, :gid, :id)');
$stmt->bindValue(':gid', $res['ID'], SQLITE3_INTEGER);
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->execute();

echo 0;
?>