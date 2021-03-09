<?php
// Basic php file that 'signs out' the user by destroying 
// session and redirecting to the index page
if (!isset($_SESSION)){
    session_start();
}
session_destroy();

header("Location: ../index.php");
exit();
?>