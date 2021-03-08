<?php
include_once("../main.php");

if (!isset($_SESSION)){
    session_start();
}

$target_file = "../img/usr/" . $_SESSION['uid'].'.jpeg';
$uploadOk = 0;
$imageFileType = strtolower(pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if($check === false) {
        $uploadOk = 1;
    }
}

if ($_FILES["file"]["size"] > 500000) {
    $uploadOk = 2;
}

if($imageFileType != "jpg" && $imageFileType != "jpeg") {
    $uploadOk = 3;
}

if ($uploadOk == 0) {
    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $uploadOk = 4;
    } 
}

echo json_encode([$uploadOk, "img/usr/" . $_SESSION['uid'].'.jpeg']);
?>