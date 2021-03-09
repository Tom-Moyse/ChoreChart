<?php
// Helper php file with some definitions to assist in setting correct filepaths
// in certain areas of the program
define('ROOT', dirname(__FILE__));

function end_session(){
    if (!isset($_SESSION)){
        session_start();
    }
    session_destroy();
}
?>
