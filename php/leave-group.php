<?php
if (!isset($_SESSION)){
    session_start();
}

include_once("../main.php");
include(ROOT."/php/database.php");

function redirect(){
    $_SESSION['gid'] = null;
    header("Location: ../joingroup.php");
    exit();
}

$connection = new Database();

$stmt = $connection->prepare("UPDATE User SET GroupID=NULL, moderator=0 WHERE ID=:id");
$stmt->bindValue(":id", $_SESSION['uid'], SQLITE3_INTEGER);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);

// Remove all their associated chore items wheriein the chore was assigned to this user (remove chore as well)
// Or user had choreitem from 'mixed chore' (reallocate choreitem to random other group member)
// Or user is last user in group in which case scrap everything including choregroup
// if user is not last user but last moderator in group give other user in group moderator

// Get number of users in group
$stmt = $connection->prepare("SELECT count(ID) FROM User WHERE GroupID = :gid");
$stmt->bindValue(':gid', $_SESSION['gid']);
$result = $stmt->execute();
$count = $result->fetchArray(SQLITE3_ASSOC)['count(ID)'];

if ($count == 0){
    // Delete all chores, choreitems, choregroup associated
    $stmt = $connection->prepare("DELETE FROM ChoreItem WHERE UserID = :id");
    $stmt->bindValue(':id', $_SESSION['uid']);
    $stmt->execute();
    $stmt = $connection->prepare("DELETE FROM Chore WHERE GroupID = :gid");
    $stmt->bindValue(':gid', $_SESSION['gid']);
    $stmt->execute();
    $stmt = $connection->prepare("DELETE FROM ChoreGroup WHERE ID = :gid");
    $stmt->bindValue(':gid', $_SESSION['gid']);
    $stmt->execute();
    redirect();
}
// Get number of other moderators in the group to see if new user will be set as moderator
$stmt = $connection->prepare("SELECT count(ID) FROM User WHERE GroupID = :gid AND moderator=1");
$stmt->bindValue(':gid', $_SESSION['gid']);
$result = $stmt->execute();
$count = $result->fetchArray(SQLITE3_ASSOC)['count(ID)'];

if ($count == 0){
    // If no moderators remaining set one 'random' user in group to moderator
    $stmt = $connection->prepare("UPDATE User SET moderator=1 WHERE GroupID IN 
        (SELECT GroupID FROM User WHERE GroupID = :gid LIMIT 1)");
    $stmt->bindValue(':gid', $_SESSION['gid']);
    $result = $stmt->execute();
}

// Remove all fixed chores
// First select all chore items id's that should be deleted
$stmt = $connection->prepare("SELECT ChoreItem.ID FROM ChoreItem INNER JOIN Chore ON Chore.ID = ChoreItem.ChoreID
    WHERE Chore.fixed = 1 AND Chore.UserID = :id");
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$result = $stmt->execute();
// Remove all chore items with identified ids
while ($res = $result->fetchArray(SQLITE3_ASSOC)){
    $stmt = $connection->prepare("DELETE FROM ChoreItem WHERE ID=:id");
    $stmt->bindValue(':id', $res['ID'], SQLITE3_INTEGER);
    $stmt->execute();
}
// Remove all the associated chores
$stmt = $connection->prepare("DELETE FROM Chore WHERE fixed = 1 AND UserID = :id");
$stmt->bindValue(':id', $_SESSION['uid']);
$stmt->execute();

// Reallocate all chore items that aren't fixed
// First select all chore items to reallocate
$stmt = $connection->prepare("SELECT ChoreItem.ID FROM ChoreItem INNER JOIN Chore ON Chore.ID = ChoreItem.ChoreID
    WHERE Chore.fixed = 0 AND ChoreItem.UserID = :id");
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
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
redirect();
?>