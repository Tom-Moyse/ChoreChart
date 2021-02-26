<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: index.php");
    exit();
}

include ("./database.php");
$connection = new Database();

// Check if entries match expected values
$stmt = $connection->prepare('SELECT pass FROM User WHERE username=:user');
$stmt->bindValue(':user', $_POST['user'], SQLITE3_TEXT);
$results = $stmt->execute();
$res = $results->fetchArray(SQLITE3_ASSOC);
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


//Login user
$stmt = $connection->prepare('SELECT ID FROM User WHERE username=:user');
$stmt->bindValue(':user', $_POST['user'], SQLITE3_TEXT);
$results = $stmt->execute();

if (!isset($_SESSION)){
    session_start();
}
$_SESSION['uid'] = $results->fetchArray(SQLITE3_ASSOC)['ID'];

echo 0;
?>