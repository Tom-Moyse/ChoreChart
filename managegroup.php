<?php
include_once('main.php');
include(ROOT.'/php/utils.php');
require_login();
require_group();
require_no_joining_status();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ("./php/header.php");
        ?>
        <title>ChoreChart</title>
        <script src="js/managegroup-control.js"></script>
    </head>
    <body>
        <?php
        $active=3;
        if (is_mod()){
            include ("./php/navbar-internal-mod.php");
        }
        else{
            include ("./php/navbar-internal-standard.php");
        }
        ?>
        <div class="center-box">
            <div id="user-modal" class="modal hidden">
                <div class="modal-content fast-animate">
                    <div style="width:100%">
                        <img class="big-pic" src="img/usr/1.jpeg" alt="Profile Picture">
                    </div>
                    <table>
                        <tr>
                            <td>Name:</td>
                            <td>Their name</td>
                        </tr>
                        <tr>
                            <td>Moderator:</td>
                            <td>Yes/no</td>
                        </tr>
                    </table>
                    <div>
                        <a class="button">Make moderator</a>
                        <a class="button">Remove from group</a>
                    </div>
                </div>
            </div>
            <div id="join-code-modal" class="modal">
                <div class="modal-content fast-animate">
                    <h3>Join Code:</h3>
                    <div>
                        <h4>f5kd3</h4>
                    </div>
                </div>
            </div>
            <div class="other-left-panel">
                <h4>Group Members</h4>
                <div class="left-container">
                    <table class="members" id="mgroupmembers">
                        <colgroup>
                            <col style="width: 30%;">
                            <col style="width: 50%;">
                            <col style="width: 20%;">
                        </colgroup>
                        <tr>
                            <td><img src="img/usr/1.jpeg" alt="Profile Picture"></td>
                            <td>Name</td>
                            <td class="magnify"><embed src="img/magnify.svg" alt="Magnify"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="other-right-panel">
                <h4>Requesting Members</h4>
                <div class="right-container" style="text-align:right">
                    <table id="join-requests">
                        <tr>
                            <td><img src="img/usr/default.png" alt="Profile Picture"></td>
                            <td>Their Name</td>
                            <td class="circle-button"><a id="accept-button">✓</a></td>
                            <td class="circle-button"><a id="decline-button">✖</a></td>
                        </tr>
                    </table>
                    <a id="display-join">Display join code</a>
                </div>
            </div>
        </div>
    </body>
</html>