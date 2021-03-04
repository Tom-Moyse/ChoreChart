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

$stmt = $connection->prepare("SELECT displayname FROM User Where ID=:id");
$stmt->bindValue(":id", $_POST['uid'], SQLITE3_INTEGER);
$result = $stmt->execute();
$name = $result->fetchArray(SQLITE3_ASSOC)['displayname'];

echo ("<tr><td></td>");
if (file_exists(ROOT."/img/usr/".$_POST['uid'].".jpeg")){
    echo ('<td><img src="img/usr/'.$_POST['uid'].'.jpeg" alt="Profile Picture"></td>');
}
else{
    echo ('<td><img src="img/usr/default.png" alt="Profile Picture"></td>');
}
echo ('<td>'.$name.'</td>');
echo ('<td class="magnify" data-uid="'.$_POST['uid'].'">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 490 490">
        <style>svg {cursor:pointer;}</style>
        <path fill="none" stroke="#ffffff" stroke-width="36" stroke-linecap="round"
        d="m280,278a153,153 0 1,0-2,2l170,170m-91-117 110,110-26,26-110-110"/>
    </svg> 
</td></tr>');
?>