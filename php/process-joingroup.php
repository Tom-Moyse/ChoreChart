<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: register.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();

$connection = new Database();

// Check if code given was valid
$stmt = $connection->prepare('SELECT ID FROM ChoreGroup WHERE code = :code');
$stmt->bindValue(':code', $_POST['code'], SQLITE3_TEXT);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);
if ($res['ID'] == null){
    echo 1;
    exit();
}

// Create a new join request
$stmt = $connection->prepare('INSERT INTO JoinRequest VALUES (NULL, :gid, :id)');
$stmt->bindValue(':gid', $res['ID'], SQLITE3_INTEGER);
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->execute();

// Send email to group moderators to inform them of new join request
// Get info required for email
$stmt = $connection->prepare("SELECT displayname FROM User WHERE ID=:id");
$stmt->bindValue(':id', $_SESSION['uid']);
$result = $stmt->execute();
$name = $result->fetchArray(SQLITE3_ASSOC)['displayname'];
$stmt = $connection->prepare("SELECT email FROM User WHERE GroupID=:id AND moderator=1");
$stmt->bindValue(':id', $res['ID']);
$result = $stmt->execute();

$header = 'From: noreply@chorechart.com';
while ($res = $result->fetchArray(SQLITE3_ASSOC)){
    $message = "The user: ".$name." has requested to join the chore group. You should accept/decline the request so they aren't kept waiting!";
    $message = wordwrap($message, 70, "\r\n");
    mail($res['email'], "New join request", $message, $header);
}

echo 0;
?>