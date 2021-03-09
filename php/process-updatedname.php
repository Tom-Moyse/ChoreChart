<?php
// File handles user editing their displayname
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: ./signout.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();
require_group();
require_no_joining_status();

// Verify displayname server side
if ($_POST['name'] == ""){
    echo 1;
    exit();
}

// Reflect changes to displayname in user table
$connection = new Database();
$stmt = $connection->prepare("UPDATE User SET displayname=:dname WHERE ID=:id");
$stmt->bindValue(':dname', $_POST['name'], SQLITE3_TEXT);
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->execute();

echo 0;
?>