<?php
// Processes a user being forcibly removed from the group by a moderator and handles the request
// in the same manner as leave-group but uses the posted id as to the session id
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: signout.php");
    exit();
}
if (!isset($_SESSION)){
    session_start();
}

include_once("../main.php");
include(ROOT."/php/database.php");
$connection = new Database();

$stmt = $connection->prepare("UPDATE User SET GroupID=NULL WHERE ID=:id");
$stmt->bindValue(":id", $_POST['uid'], SQLITE3_INTEGER);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);

// Remove all their associated chore items wheriein the chore was assigned to this user (remove chore as well)
// Or user had choreitem from 'mixed chore' (give choreitem to random other group member)
// Remove all fixed chores
// First select all chore items id's that should be deleted
$stmt = $connection->prepare("SELECT ChoreItem.ID FROM ChoreItem INNER JOIN Chore ON Chore.ID = ChoreItem.ChoreID
    WHERE Chore.fixed = 1 AND Chore.UserID = :id");
$stmt->bindValue(':id', $_POST['uid'], SQLITE3_INTEGER);
$result = $stmt->execute();
// Remove all chore items with identified ids
while ($res = $result->fetchArray(SQLITE3_ASSOC)){
    $stmt = $connection->prepare("DELETE FROM ChoreItem WHERE ID=:id");
    $stmt->bindValue(':id', $res['ID'], SQLITE3_INTEGER);
    $stmt->execute();
}
// Remove all the associated chores
$stmt = $connection->prepare("DELETE FROM Chore WHERE fixed = 1 AND UserID = :id");
$stmt->bindValue(':id', $_POST['uid']);
$stmt->execute();

// Reallocate all chore items that aren't fixed
// First select all chore items to reallocate
$stmt = $connection->prepare("SELECT ChoreItem.ID FROM ChoreItem INNER JOIN Chore ON Chore.ID = ChoreItem.ChoreID
    WHERE Chore.fixed = 0 AND ChoreItem.UserID = :id");
$stmt->bindValue(':id', $_POST['uid'], SQLITE3_INTEGER);
$result = $stmt->execute();
// Reallocate all chore items with identified ids
while ($res = $result->fetchArray(SQLITE3_ASSOC)){
    $stmt = $connection->prepare("UPDATE ChoreItem SET UserID=:id");

    // Calculate user to assign chore item to
    // Selects user with 0 chore items (if any)
    $substmt = $connection->prepare("SELECT ID FROM User EXCEPT SELECT ID FROM 
    (SELECT COUNT(User.ID), User.GroupID, User.ID FROM User INNER JOIN ChoreItem ON
    ChoreItem.UserID=User.ID GROUP BY ChoreItem.UserID ORDER BY COUNT(User.ID) ASC) 
    WHERE GroupID=:gid");
    $substmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
    $subresults = $substmt->execute();
    $subres = $subresults->fetchArray(SQLITE3_ASSOC);
    if ($subres == false){
        // Selects user with least chore items (must have at least 1)
        $substmt = $connection->prepare("SELECT ID FROM (SELECT COUNT(User.ID), User.GroupID, User.ID
        FROM User INNER JOIN ChoreItem ON ChoreItem.UserID=User.ID GROUP BY ChoreItem.UserID 
        ORDER BY COUNT(User.ID) ASC) 
        WHERE GroupID=:gid");
        $substmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
        $subresults = $substmt->execute();
        $subres = $subresults->fetchArray(SQLITE3_ASSOC);
        if ($subres == false){
            $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
        }
        else{
            $stmt->bindValue(':id', $subres['ID'], SQLITE3_INTEGER);
        }
    }
    else{
        $stmt->bindValue(':id', $subres['ID'], SQLITE3_INTEGER);
    }

    $stmt->execute();
}
echo "0";
?>