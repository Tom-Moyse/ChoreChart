<?php
include_once('main.php');
include(ROOT.'/php/utils.php');
require_login();
require_group();
require_no_joining_status();

$connection = new Database();
$stmt = $connection->prepare("SELECT ID, email, username, displayname FROM User WHERE ID=:id");
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$result = $stmt->execute();
$res = $result->fetchArray(SQLITE3_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ("./php/header.php");
        ?>
        <title>ChoreChart</title>
        <script src="js/account-control.js"></script>
    </head>
    <body>
        <?php
        $active=2;
        include ("./php/navbar-internal-mod.php");
        ?>
        <div class="center-box">
            <div id="edit-displayname-modal" class="modal hidden">
                <div class="modal-content animate">
                    <h4 class="modal-title">Change displayname</h4>
                    <form id="displayname-form">
                        <label for="displayname" class="hide-element">New Displayname:</label>
                        <input id="displayname-input" type="text" name="displayname" value="<?php echo $res['displayname']?>">
                        <p id="nmissing" class="warning hidden">Name Required</p>
                        <br><br>
                        <input type="submit" value="Submit">
                    </form>
                </div>
            </div>
            <div id="edit-email-modal" class="modal hidden">
                <div class="modal-content animate">
                    <h4 class="modal-title">Change email</h4>
                    <form id="email-form">
                        <label for="email" class="hide-element">New Displayname:</label>
                        <input id="email-input" type="email" name="email" value="<?php echo $res['email']?>">
                        <p id="emissing" class="warning hidden">Email Required</p>
                        <p id="einvalid" class="warning hidden">Email invalid</p>
                        <p id="etaken" class="warning hidden">Email already taken</p>
                        <br><br>
                        <input type="submit" value="Submit">
                    </form>
                </div>
            </div>
            <div id="edit-password-modal" class="modal hidden">
                <div class="modal-content animate">
                    <h4 class="modal-title">Change Password</h4>
                    <form id="password-form">
                        <label for="password" class="hide-element">New Password:</label>
                        <input id="password-input" type="password" name="password" placeholder="Password">
                        <label for="password-confirm" class="hide-element">Confirm Password:</label>
                        <input id="password-confirm-input" type="password" name="password-confirm" placeholder="Confirm password">
                        <p id="pmissing" class="warning hidden">Password Required</p>
                        <p id="pshort" class="warning hidden">Password must be at least 8 characters</p>
                        <p id="pinvalid" class="warning hidden">Password contains invalid characters</p>
                        <p id="pmatch" class="warning hidden">Passwords do not match</p>
                        <br><br>
                        <input type="submit" value="Submit">
                    </form>
                </div>
            </div>
            <div id="leave-confirm-modal" class="modal hidden">
                <div class="modal-content animate">
                    <h4 class="modal-title">Leave Group</h4>
                    <p style="padding:20px;">
                    Are you sure you want to leave the group? This action is irreversible, and if
                    you wish to rejoin you will once again have to be accepted.
                    </p>
                    <a class="button" id="confirm-leave">Confirm</a>
                </div>
            </div>
            <h3>Account Details</h3>
            <div class="focus-container">
                <div style="width:100%">
                    <?php
                    if (file_exists(ROOT."/img/usr/".$res['ID'].".jpeg")){
                        echo ('<td><img id="user-img" class="big-pic" src="img/usr/'.$res['ID'].'.jpeg" alt="Profile Picture"></td>');
                    }
                    else{
                        echo ('<td><img id="user-img" class="big-pic" src="img/usr/default.png" alt="Profile Picture"></td>');
                    }
                    ?>
                </div>
                <table id="user-details">
                    <tr>
                        <td>Username:</td>
                        <td><?php echo $res['username'] ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Display Name:</td>
                        <td id="user-displayname"><?php echo $res['displayname'] ?></td>
                        <td class="magnify" id="edit-displayname">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="18" height="18px" viewBox="0 0 512 512" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 512 512">
                                <path style="fill:#ffffff;" d="m453.7,133l-286,289.6-71.6-75 284.8-288.4c9-9.1 24.8-9 33.7,0.1l39.2,40.3c9,9.2 8.9,24.2-0.1,33.4zm-375.7,254.4l49.1,51.5-66.9,14.5 17.8-66zm404.8-316.3l-39.2-40.3c-13.3-14.8-54.9-35.8-91.5-0.4l-298.7,302.5c-2.5,2.5-4.3,5.6-5.2,9l-36,133.1c-4.6,17 4.2,27.5 19.6,25.7 1.4-0.2 140.9-30.1 140.9-30.1 3.8-0.8 7.3-2.8 10.1-5.6l299.6-303.4c24.6-24.8 24.8-65.4 0.4-90.5z"/>
                            </svg>
                        </td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td id="user-email"><?php echo $res['email'] ?></td>
                        <td class="magnify" id="edit-email">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="18" height="18px" viewBox="0 0 512 512" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 512 512">
                                <path style="fill:#ffffff;" d="m453.7,133l-286,289.6-71.6-75 284.8-288.4c9-9.1 24.8-9 33.7,0.1l39.2,40.3c9,9.2 8.9,24.2-0.1,33.4zm-375.7,254.4l49.1,51.5-66.9,14.5 17.8-66zm404.8-316.3l-39.2-40.3c-13.3-14.8-54.9-35.8-91.5-0.4l-298.7,302.5c-2.5,2.5-4.3,5.6-5.2,9l-36,133.1c-4.6,17 4.2,27.5 19.6,25.7 1.4-0.2 140.9-30.1 140.9-30.1 3.8-0.8 7.3-2.8 10.1-5.6l299.6-303.4c24.6-24.8 24.8-65.4 0.4-90.5z"/>
                            </svg>
                        </td>
                    </tr>
                </table>
                <div id="user-button-group">
                    <a class="button" id="change-pass">Change password</a>
                    <a class="button" id="leave-group">Leave group</a>
                </div>
            </div>
        </div>
    </body>
</html>