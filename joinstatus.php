<?php
include_once('main.php');
include(ROOT.'/php/utils.php');
require_login();
require_no_group();
require_joining_status();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ("./php/header.php");
        ?>
        <title>ChoreChart</title>
        <script src="js/cancel-control.js"></script>
    </head>
    <body>
        <?php
        include ("./php/navbar-entry.php")
        ?>
        <div class="center-box">
            <h2>ChoreChart App</h2>
            <p id="join-info"></p>
            <a id="cancel" class="button">Cancel Request</a>
        </div>

        <div id="join-modal" class="modal hidden">
            <div class="modal-content animate">
                <h3>Join Group</h3>
                <form id="join-form">
                    <label for="code" class="hide-element">Join Code</label>
                    <input type="text" name="code" placeholder="Join Code">
                    <p id="cinvalid" class="warning hidden">Code invalid</p><br>

                    <input type="submit" value="Submit">
                </form>
            </div>
        </div>
        <div id="create-modal" class="modal hidden">
            <div class="modal-content animate">
                <h3>Create Group</h3>
                <form id="create-form">
                    <label for="group" class="hide-element">Group Name</label>
                    <input type="text" name="group" placeholder="Group Name">
                    <p id="nmissing" class="warning hidden">Name Required</p><br>
                    <p id="ninvalid" class="warning hidden">Name contains invalid characters.</p><br>

                    <input type="submit" value="Submit">
                </form>
            </div>
        </div>
    </body>
</html>