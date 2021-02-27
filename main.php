<?php
define('ROOT', dirname(__FILE__));

function end_session(){
    if (!isset($_SESSION)){
        session_start();
    }
    session_destroy();
}
?>
