<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: ./signout.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();
require_group();
require_no_joining_status();

// Verify password server side
$passExpr = "/\s+/";

if (!(strlen($_POST['pass1']) < 8 || preg_match($passExpr, $_POST['pass1']))){
    if ($_POST['pass1'] == $_POST['pass2']){
        $password = password_hash($_POST['pass1'], PASSWORD_DEFAULT);
    }
    else{
        echo 1;
        exit();
    }
}
else{
    echo 1;
    exit();
}


$connection = new Database();
$stmt = $connection->prepare("UPDATE User SET pass=:pass WHERE ID=:id");
$stmt->bindValue(':pass', $password, SQLITE3_TEXT);
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->execute();

echo 0;
?>