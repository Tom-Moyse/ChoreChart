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

$connection = new Database();

// Verify choreid given belongs to user in group (cannot delete chore of foreign user)
$stmt = $connection->prepare("SELECT Chore.GroupID FROM User INNER JOIN Chore ON Chore.GroupID = User.GroupID
    WHERE Chore.ID = :id");
$stmt->bindValue(':id', $_POST['id']);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);
if (!$res){
    echo 1;
    exit();
}
if ($res['GroupID'] != $_SESSION['gid']){
    echo 1;
    exit();
}

// Only delete future chores
//$stmt = $connection->prepare("DELETE FROM ChoreItem WHERE ChoreID=:id AND julianday(deadline) > julianday(date('now'))");
// Delete all chores
$stmt = $connection->prepare("DELETE FROM ChoreItem WHERE ChoreID=:id");
$stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
$stmt->execute();
$stmt = $connection->prepare("DELETE FROM Chore WHERE ID=:id");
$stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
$stmt->execute();
echo 0;
?>