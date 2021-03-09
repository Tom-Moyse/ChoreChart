<?php
// Page that user is redirected to upon successful login, this page redirects the user to the
// appropriate landing page based upon the current session variables as well as user status, e.g.
// if they are an existing member of a group
include_once("../main.php");
include(ROOT.'/php/utils.php');
require_login();

$connection = new Database();

$stmt = $connection->prepare('SELECT GroupID FROM User WHERE ID=:id');
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$results = $stmt->execute();
$res = $results->fetchArray(SQLITE3_ASSOC);
// If not part of group then see if currentlty joining or not
if ($res['GroupID'] == null){
    $stmt = $connection->prepare('SELECT ID FROM JoinRequest WHERE UserID=:id');
    $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
    $results = $stmt->execute();
    $res = $results->fetchArray(SQLITE3_ASSOC);
    // If not joining then redirect to joingroup page
    if ($res == false){
        $connection = null;
        header("Location: ../joingroup.php");
        exit();
    }
    // Otherwise redirect to the join status page
    else{
        header("Location: ../joinstatus.php");
        exit();
    }
}
// If user part of a group redirect to the chores page
else{
    $_SESSION['gid'] = $res['GroupID'];
    header("Location: ../chores.php");
    exit();
}
?>