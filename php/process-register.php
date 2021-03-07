<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: register.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/database.php");
$connection = new Database();

// Verify data server-side to avoid issues from bypassing client verification
$userExpr = "/^[a-z0-9_-]+$/i";
$passExpr = "/\s+/";
$error = false;
// Verify username
if (preg_match($userExpr, $_POST['username'])){
    $stmt = $connection->prepare('SELECT ID FROM User WHERE username=:user');
    $stmt->bindValue(':user', $_POST['username'], SQLITE3_TEXT);
    $results = $stmt->execute();

    if ($results->fetchArray(SQLITE3_ASSOC) != null || strlen($_POST['username']) == 0){
        $error = true;
        echo 1;
        exit();
    }
}
else{
    $error = true;
}

// Verify email
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
    $error = true;
}

// Verify password
if (!(strlen($_POST['password1']) < 8 || preg_match($passExpr, $_POST['password1']))){
    if ($_POST['password1'] == $_POST['password2']){
        $password = password_hash($_POST['password1'], PASSWORD_DEFAULT);
    }
    else{
        $error = true;
    }
}
else{
    $error = true;
}

// Check if any validation errors occured and redirect appropriately
if ($error){
    header("Location: index.php");
    exit();
}

// Insert data into table
$stmt = $connection->prepare('INSERT INTO User VALUES (NULL, :email, :username, :username, :pass, 0, NULL)');
$stmt->bindValue(':email', $_POST['email'], SQLITE3_TEXT);
$stmt->bindValue(':username', $_POST['username'], SQLITE3_TEXT);
$stmt->bindValue(':pass', $password, SQLITE3_TEXT);
$stmt->execute();

if (!isset($_SESSION)){
    session_start();
}

// Setup session and redirect user
$stmt = $connection->prepare('SELECT ID FROM User WHERE email=:em');
$stmt->bindValue(':em', $_POST['email'], SQLITE3_TEXT);
$results = $stmt->execute();
$_SESSION['uid'] = $results->fetchArray(SQLITE3_ASSOC)['ID'];

echo 0;
exit();
?>