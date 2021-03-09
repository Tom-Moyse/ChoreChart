<?php
// File processes a given login attempt and in case of success updates session uid
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: index.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/database.php");
$connection = new Database();

// Check if login is using either username or email and grab associated password from table
if (str_contains($_POST['user'],"@")){
    $stmt = $connection->prepare('SELECT pass FROM User WHERE email=:email');
    $stmt->bindValue(':email', $_POST['user'], SQLITE3_TEXT);
}
else{
    $stmt = $connection->prepare('SELECT pass FROM User WHERE username=:user');
    $stmt->bindValue(':user', $_POST['user'], SQLITE3_TEXT);
}
$results = $stmt->execute();
$res = $results->fetchArray(SQLITE3_ASSOC);
// If entries dont match return value 1 which will be handled by js/ajax appropriately
if ($res != null){  
    if (!password_verify($_POST['pass'],$res['pass'])){
        echo 1;
        exit();
    }
}
else{
    echo 1;
    exit();
}


//Otherwise login user
if (str_contains($_POST['user'],"@")){
    $stmt = $connection->prepare('SELECT ID FROM User WHERE email=:email');
    $stmt->bindValue(':email', $_POST['user'], SQLITE3_TEXT);
}
else{
    $stmt = $connection->prepare('SELECT ID FROM User WHERE username=:user');
    $stmt->bindValue(':user', $_POST['user'], SQLITE3_TEXT);
}

$results = $stmt->execute();

if (!isset($_SESSION)){
    session_start();
}
$_SESSION['uid'] = $results->fetchArray(SQLITE3_ASSOC)['ID'];

echo 0;
?>