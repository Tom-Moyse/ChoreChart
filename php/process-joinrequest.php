<?php
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

$stmt = $connection->prepare("SELECT gname FROM ChoreGroup WHERE ID=:gid");
$stmt->bindValue(':gid', $_SESSION['gid']);
$result = $stmt->execute();
$group = $result->fetchArray(SQLITE3_ASSOC)['gname'];

$header = 'From: noreply@chorechart.com';
$message = "Unfortunately, you're request to join the chore group: ".$group."has been declined. They must not want you!";

$stmt = $connection->prepare("SELECT User.ID, User.email FROM JoinRequest INNER JOIN User ON User.ID=JoinRequest.UserID WHERE JoinRequest.ID=:rid");
$stmt->bindValue(":rid", $_POST['reqID'], SQLITE3_INTEGER);
$results = $stmt->execute();
$res = $results->fetchArray(SQLITE3_ASSOC);
$id = $res['ID'];
$email = $res['email'];

if ($_POST['add'] == 1){
    $stmt = $connection->prepare("UPDATE User SET GroupID=:gid WHERE ID=:id");
    $stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
    $stmt->bindValue(":id", $id, SQLITE3_INTEGER);
    
    $result = $stmt->execute();

    $message = "Congratulations, you have been accepted into the chore group: ".$group."! Now you can start contributing to their chores.";
}

$stmt = $connection->prepare("DELETE FROM JoinRequest WHERE ID=:id");
$stmt->bindValue(":id", $_POST['reqID'], SQLITE3_INTEGER);
$result = $stmt->execute();
$message = wordwrap($message, 70, "\r\n");

mail($email, "ChoreChart Request Response", $message, $header);

echo json_encode(["0", $id]);
?>