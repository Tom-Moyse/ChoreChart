<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: signout.php");
    exit();
}
if (!isset($_SESSION)){
    session_start();
}
if ($_SESSION['uid'] == $_POST['uid']){
    echo 0;
}
else{
    echo 1;
}
?>