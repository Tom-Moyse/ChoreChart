<?php
include(ROOT.'/php/database.php');

if (!isset($_SESSION)){
    session_start();
}

function require_login(){
    if (!isset($_SESSION['uid'])){
        header("Location: index.php");
    }
}

function require_joining_status(){
    $connection = new Database();
    $stmt = $connection->prepare('SELECT ID FROM JoinRequest WHERE UserID=:id');
    $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
    $results = $stmt->execute();
    $res = $results->fetchArray(SQLITE3_ASSOC);
    if ($res == false){
        header("Location: ./php/routepage.php");
    }
}

function require_no_joining_status(){
    $connection = new Database();
    $stmt = $connection->prepare('SELECT ID FROM JoinRequest WHERE UserID=:id');
    $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
    $results = $stmt->execute();
    $res = $results->fetchArray(SQLITE3_ASSOC);
    if ($res != false){
        header("Location: ./php/routepage.php");
    }
}

function require_group(){
    if (!isset($_SESSION['gid'])){
        header("Location: ./php/routepage.php");
    }
}

function require_no_group(){
    if (isset($_SESSION['gid'])){
        header("Location: ./php/routepage.php");
    }
}

function is_mod(){
    $connection = new Database();
    $stmt = $connection->prepare('SELECT moderator FROM User WHERE ID=:id');
    $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
    $results = $stmt->execute();
    $res = $results->fetchArray(SQLITE3_ASSOC)['moderator'];

    return ($res == 1);
}
?>