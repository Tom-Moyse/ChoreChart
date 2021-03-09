<?php
// File handles moderator renaming their group
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: register.php");
    exit();
}

include_once("../main.php");
include(ROOT."/php/utils.php");

require_login();

$connection = new Database();

// Verify data server-side to avoid issues from bypassing client verification
$nameExpr = "/^[a-z0-9_!?,'. -]+$/i";

if (!preg_match($nameExpr, $_POST['name'])){
    echo 1;
    exit();
}

// Group name is updated in the db table
$stmt = $connection->prepare("UPDATE ChoreGroup SET gname=:nam WHERE ID=:id");
$stmt->bindValue(":nam", $_POST['name'], SQLITE3_TEXT);
$stmt->bindValue(":id", $_POST['gid'], SQLITE3_INTEGER);
$stmt->execute();

echo 0;
?>