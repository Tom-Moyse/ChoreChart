<?php
// File that handles the user uploading a new profile avatar to the server
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: signout.php");
    exit();
}

if (!isset($_SESSION)){
    session_start();
}

// The file is first given an appropriate name which is simply their user id
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

// Check the file is not over the file size limit
if ($_FILES["file"]["size"] > 500000) {
    $uploadOk = 2;
}

// Check the file extension
if($imageFileType != "jpg" && $imageFileType != "jpeg") {
    $uploadOk = 3;
}

// Assuming no errors upload file to the server inton the img/usr/ directory
if ($uploadOk == 0) {
    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $uploadOk = 4;
    } 
}

// Return JSON response with html image src address
echo json_encode([$uploadOk, "img/usr/" . $_SESSION['uid'].'.jpeg']);
?>