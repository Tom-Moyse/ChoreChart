<?php
// File handles user updating their email address
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: ./signout.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();
require_group();
require_no_joining_status();

$connection = new Database();

// Verify email server side
if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    $stmt = $connection->prepare('SELECT ID FROM User WHERE email=:em');
    $stmt->bindValue(':em', $_POST['email'], SQLITE3_TEXT);
    $results = $stmt->execute();

    if ($results->fetchArray(SQLITE3_ASSOC) != null){
        echo 1;
        exit();
    }
}
else{
    echo 1;
    exit();
}

// Update email address stored in db table to match newly provided email address
$stmt = $connection->prepare("UPDATE User SET email=:email WHERE ID=:id");
$stmt->bindValue(':email', $_POST['email'], SQLITE3_TEXT);
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->execute();

echo 0;
?>