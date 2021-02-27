<?php
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

function idToCode($id){
    $code = "";
    for ($i=0; $i < 5; $i++) { 
        $nextVal = $id % 36;
        if ($nextVal <= 25){
            $code .= chr($nextVal + 97);
        }
        else{
            $code .= strval(nextVal-26);
        }
        $id = intdiv($id, 36);
    }
    return $code;
}

$result = $connection->query('SELECT MAX(ID) FROM ChoreGroup');
$res = $result->fetchArray(SQLITE3_ASSOC)['MAX(ID)'];
if ($res == null){
    $code = idToCode(0);
}
else{
    $code = idToCode($res);
}


$stmt = $connection->prepare('INSERT INTO ChoreGroup VALUES (NULL, :code, :gname)');
$stmt->bindValue(':code', $code, SQLITE3_TEXT);
$stmt->bindValue(':gname', $_POST['name'], SQLITE3_TEXT);
$stmt->execute();

$result = $connection->query('SELECT last_insert_rowid()');
$insertID = $result->fetchArray(SQLITE3_ASSOC)['last_insert_rowid()'];

$stmt = $connection->prepare('UPDATE User SET moderator=1, GroupID=:gid WHERE ID=:id');
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->bindValue(':gid', $insertID, SQLITE3_INTEGER);
$stmt->execute();

echo 0;
?>