<?php
// File handles creation of a new repeating chore for a given group based upon user's form parameters
// and returns the corresponding html for the chore item such that it can be dynamically added to
// the page via js/ajax
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

$date = date("Y-m-d H:i:s", strtotime($_POST['startdate']));
$interval = " +".$_POST['frequency']." ".$_POST['interval'];

if (isset($_POST['fixed'])){
    // Create chore with 'random' choreholders
    $stmt = $connection->prepare("INSERT INTO Chore VALUES (NULL, :con, 1, :ite, 0, :dat, :dat, :gid, NULL)");
    $stmt->bindValue(':con', $_POST['contents'], SQLITE3_TEXT);
    $stmt->bindValue(':ite', $interval, SQLITE3_TEXT);
    $stmt->bindValue(':dat', $date, SQLITE3_TEXT);
    $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
    $stmt->execute();

    $choreid = $connection->querySingle("SELECT last_insert_rowid()");
    $choreid = $choreid['last_insert_rowid()'];

    // Insert chore item
    $stmt = $connection->prepare("INSERT INTO ChoreItem VALUES (NULL, :con, 0, :dat, :cid, :id)");
    $stmt->bindValue(':con', $_POST['contents'], SQLITE3_TEXT);
    $stmt->bindValue(':cid', $choreid, SQLITE3_INTEGER);
    $stmt->bindValue(':dat', $date, SQLITE3_TEXT);
    
    // Calculate user to assign chore item to
    // Selects user with 0 chore items (if any)
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
        // If query fails to produce a result assign chore item to logged in user
        if ($res == false){
            $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
        }
        // Otherwise assign chore item to group member with least current amount of chore items
        else{
            $stmt->bindValue(':id', $res['ID'], SQLITE3_INTEGER);
        }
    }
    // If their was at least one user with zero chore items, assign choreitem to said user
    else{
        $stmt->bindValue(':id', $res['ID'], SQLITE3_INTEGER);
    }

    $stmt->execute();
}
else{
    // Create chore with given choreholder
    $stmt = $connection->prepare("INSERT INTO Chore VALUES (NULL, :con, 1, :ite, 1, :dat, :dat, :gid, :id)");
    $stmt->bindValue(':con', $_POST['contents'], SQLITE3_TEXT);
    $stmt->bindValue(':ite', $interval, SQLITE3_TEXT);
    $stmt->bindValue(':dat', $date, SQLITE3_TEXT);
    $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
    $stmt->bindValue(':id', $_POST['choreholder'], SQLITE3_INTEGER);
    $stmt->execute();

    $choreid = $connection->querySingle("SELECT last_insert_rowid()");
    $choreid = $choreid['last_insert_rowid()'];

    // Insert chore item
    $stmt = $connection->prepare("INSERT INTO ChoreItem VALUES (NULL, :con, 0, :dat, :cid, :id)");
    $stmt->bindValue(':con', $_POST['contents'], SQLITE3_TEXT);
    $stmt->bindValue(':dat', $date, SQLITE3_TEXT);
    $stmt->bindValue(':cid', $choreid, SQLITE3_INTEGER);
    $stmt->bindValue(':id', $_POST['choreholder'], SQLITE3_INTEGER);
    $stmt->execute();
}


$html = '<tr><td>'.$_POST['contents'].'</td><td class="other-circle-button edit-button"><a>🖊️</a>
    </td><td class="other-circle-button delete-button"><a>🗑️</a></td></tr>';

echo (json_encode(["0", $choreid, $html]));
?>