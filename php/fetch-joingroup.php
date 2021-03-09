<?php
// File that returns thr groupname of the currently logged in user
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: ./signout.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();
require_joining_status();

$connection = new Database();
$stmt = $connection->prepare('SELECT ChoreGroup.gname FROM JoinRequest INNER JOIN ChoreGroup 
                            ON JoinRequest.GroupID = ChoreGroup.ID WHERE JoinRequest.UserID=:id');
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$results = $stmt->execute();
$res = h($results->fetchArray(SQLITE3_ASSOC)['gname']);
echo $res;
?>