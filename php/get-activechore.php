<?php
// File returns all html to display the active chore of the user, functionality mirrors that of
// internal mychores but is designed to be called via an js/ajax get request such that when a user
// marks a task complete the active chore can be updated to reflect this.
if ($_SERVER['REQUEST_METHOD'] != 'GET'){
    header("Location: signout.php");
    exit();
}
if (!isset($_SESSION)){
    session_start();
}

include_once("../main.php");
include(ROOT."/php/database.php");

$connection = new Database();

// Choreitem table is queried for the choreitem that is relevant to the user with the closest deadline
// date that has not already passed. If no such chore items exist the relevant message is displayed.
$stmt = $connection->prepare("SELECT contents, deadline FROM ChoreItem WHERE
    UserID=:id AND julianday(deadline) > julianday(date('now')) AND completed=0
    ORDER BY deadline ASC");
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$results = $stmt->execute();
$res = $results->fetchArray(SQLITE3_ASSOC);

echo ('<h4>Active Chore</h4><br>');

if ($res == false){
    echo ('<p>You have completed all your chores!</p>');
}
else{
    echo('<table id="chore-info" style="margin:auto"><tr>
            <td>Chore: </td>
            <td>'.$res['contents'].'</td>
        </tr><tr>
            <td>Deadline: </td>
            <td>'.substr($res['deadline'],0,-3).'</td>
        </tr></table>');
}
?>