<?php
// File handles process of editing a given single chore and updating fields as appropriate
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
$newtype = false;

// Remove existing choreitem
$stmt = $connection->prepare("DELETE FROM ChoreItem WHERE ChoreID=:id");
$stmt->bindValue(':id', $_POST['id']);
$stmt->execute();


// Update chore based upon given parameters
// First update basic parameters that must have been provided
$date = date("Y-m-d H:i:s", strtotime($_POST['date']));
$stmt = $connection->prepare("UPDATE Chore SET contents=:con, startdate=:dte, lastchoreitemdate=:dte
    WHERE ID=:id");
$stmt->bindValue(':con', $_POST['contents'], SQLITE3_TEXT);
$stmt->bindValue(':dte', $date, SQLITE3_TEXT);
$stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
$stmt->execute();

// If chore is now a repeating chore update fields appropriately
if (isset($_POST['repeats'])){
    $newtype = true;
    $interval = " +".$_POST['frequency']." ".$_POST['interval'];
    $stmt = $connection->prepare("UPDATE Chore SET repeats=1, interval=:ite WHERE ID=:id");
    $stmt->bindValue(':ite', $interval, SQLITE3_TEXT);
    $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
    $stmt->execute();
}
// If chore now has 'auto choreholder' update chore fields appropriately
if (isset($_POST['fixed'])){
    $stmt = $connection->prepare("UPDATE Chore SET fixed=0, UserID=NULL WHERE ID=:id");
    $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
    $stmt->execute();
}
else{
    $stmt = $connection->prepare("UPDATE Chore SET fixed=1, UserID=:cho WHERE ID=:id");
    $stmt->bindValue(':cho', $_POST['choreholder'], SQLITE3_INTEGER);
    $stmt->bindValue(':id', $_POST['id'], SQLITE3_INTEGER);
    $stmt->execute();
}

// Insert a new chore item to replace deleted item
$stmt = $connection->prepare("INSERT INTO ChoreItem VALUES (NULL, :con, 0, :dte, :cid, :id)");
$stmt->bindValue(':con', $_POST['contents'], SQLITE3_TEXT);
$stmt->bindValue(':dte', $_POST['date'], SQLITE3_TEXT);
$stmt->bindValue(':cid', $_POST['id'], SQLITE3_TEXT);
if (!isset($_POST['fixed'])){
    $stmt->bindValue(':id', $_POST['choreholder'], SQLITE3_INTEGER);
}
else{
    // Calculate chore item user id
    $substmt = $connection->prepare("SELECT ID FROM User EXCEPT SELECT ID FROM 
         (SELECT COUNT(User.ID), User.GroupID, User.ID FROM User INNER JOIN ChoreItem ON
         ChoreItem.UserID=User.ID GROUP BY ChoreItem.UserID ORDER BY COUNT(User.ID) ASC) 
         WHERE GroupID=:gid");
    $substmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
    $results = $substmt->execute();
    $res = $results->fetchArray(SQLITE3_ASSOC);
    // If no user has 0 chore items select user with least chore items
    if ($res == false){
        // Selects user with least chore items (must have at least 1)
        $substmt = $connection->prepare("SELECT ID FROM (SELECT COUNT(User.ID), User.GroupID, User.ID
        FROM User INNER JOIN ChoreItem ON ChoreItem.UserID=User.ID GROUP BY ChoreItem.UserID 
        ORDER BY COUNT(User.ID) ASC) 
        WHERE GroupID=:gid");
        $substmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
        $results = $substmt->execute();
        $res = $results->fetchArray(SQLITE3_ASSOC);
        if ($res == false){
            $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
        }
        else{
            $stmt->bindValue(':id', $res['ID'], SQLITE3_INTEGER);
        }
    }
    else{
        $stmt->bindValue(':id', $res['ID'], SQLITE3_INTEGER);
    }
}

$stmt->execute();

echo (json_encode(["0", $newtype]));
?>