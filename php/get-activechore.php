<?php
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