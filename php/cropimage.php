<?php
include_once("../main.php");

if (!isset($_SESSION)){
    session_start();
}

$src_image = imagecreatefromjpeg($_POST['path']);
$width = imagesx($src_image);
$height = imagesy($src_image);
$size = min($width, $height);

if ($width < $height){
    $diff = intdiv($height - $width, 2);
    $dest_image = imagecrop($src_image, ['x' => 0, 'y' => $diff, 'width' => $size, 'height' => $size]);
}
else if($width > $height){
    $diff = intdiv($height - $width, 2);
    $dest_image = imagecrop($src_image, ['x' => $diff, 'y' => 0, 'width' => $size, 'height' => $size]);
}
else{
    $dest_image = $src_image;
}

$dest_image = imagescale($dest_image, 160);

if ($dest_image !== FALSE) {
    imagejpeg($dest_image, $_POST['path']);
    imagedestroy($dest_image);
}
imagedestroy($src_image);
echo "0";
?>