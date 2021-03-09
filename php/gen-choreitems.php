<?php
// This file mirrors functionality of php at top of chores pages but is configured for a post request
// such that additional choreitems can be generated when required

// File given a start date generates all relevant chore items for the group for the next 10 days
// and stores these choreitems withing the ChoreItem db table
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

// Select all repeating sessions that require new chore items to be generated
$stmt = $connection->prepare("SELECT * FROM Chore WHERE GroupID=:gid AND repeats=1 AND
                            julianday(:gendate) > julianday(lastchoreitemdate)");
$stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
$stmt->bindValue(':gendate', $_POST['date'], SQLITE3_TEXT);
$results = $stmt->execute();
$inserts_occured = false;

$end_datestamp = strtotime($_POST['date']." +10 days");
// Loop through each chore that needs new chore items generating
while($res = $results->fetchArray(SQLITE3_ASSOC)){
    // For each chore, add chore items until required number of items added
    $new_datestamp = strtotime($res['lastchoreitemdate'] . $res['interval']);
    while ($new_datestamp < $end_datestamp){
        $inserts_occured = true;
        // Insert new chore item
        $stmt = $connection->prepare("INSERT INTO ChoreItem VALUES (NULL, :con, 0, :dea, :cid, :id)");
        $stmt->bindValue(':con', $res['contents'], SQLITE3_TEXT);
        $stmt->bindValue(':dea', date('Y-m-d H:i:s', $new_datestamp), SQLITE3_TEXT);
        $stmt->bindValue(':cid', $res['ID'], SQLITE3_INTEGER);
        // If chore has a 'fixed' user (meaning not auto choreholder) set choreitem uid to match 
        if ($res['fixed'] == 1){
            $stmt->bindValue(':id', $res['UserID'], SQLITE3_INTEGER);
        }
        // Otherwise calculate the user which the choreitem will be allocated to
        else{
            // First selects any users with 0 other chore items
            $substmt = $connection->prepare("SELECT ID FROM User WHERE GroupID=:gid EXCEPT SELECT ID
            FROM (SELECT COUNT(User.ID), User.GroupID, User.ID FROM User INNER JOIN ChoreItem ON
            ChoreItem.UserID=User.ID GROUP BY ChoreItem.UserID ORDER BY COUNT(User.ID) ASC) 
            WHERE GroupID=:gid");
            $substmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
            $subresults = $substmt->execute();
            $subres = $subresults->fetchArray(SQLITE3_ASSOC);
            // If no users had zero chore items
            if ($subres == false){
                // Selects user with least chore items (must have at least 1)
                $substmt = $connection->prepare("SELECT ID FROM (SELECT COUNT(User.ID), User.GroupID, User.ID
                FROM User INNER JOIN ChoreItem ON ChoreItem.UserID=User.ID GROUP BY ChoreItem.UserID 
                ORDER BY COUNT(User.ID) ASC) 
                WHERE GroupID=:gid");
                $substmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                $subresults = $substmt->execute();
                $subres = $subresults->fetchArray(SQLITE3_ASSOC);
                // If query fails to produce a result assign chore item to logged in user
                if ($subres == false){
                    $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
                }
                // Otherwise assign chore item to group member with least current amount of chore items
                else{
                    $stmt->bindValue(':id', $subres['ID'], SQLITE3_INTEGER);
                }
            }
            // If their was at least one user with zero chore items, assign choreitem to said user
            else{
                $stmt->bindValue(':id', $subres['ID'], SQLITE3_INTEGER);
            }
            
        }
        // Execute insert
        $stmt->execute();

        // Update date stamp
        $prev_datestamp = $new_datestamp;
        $new_datestamp = strtotime($res['interval'], $prev_datestamp);
    }
    if ($inserts_occured){
        // Update value of lastchoreitem for the chore
        $stmt = $connection->prepare("UPDATE Chore SET lastchoreitemdate=:newdate WHERE ID=:id");
        $stmt->bindValue(":newdate", date('Y-m-d H:i:s', $prev_datestamp));
        $stmt->bindValue(":id", $res['ID']);
        $stmt->execute();
    }
}
echo 0;
?>
