<?php
include_once('main.php');
include(ROOT.'/php/utils.php');
require_login();
require_group();
require_no_joining_status();

if (!is_mod()){
    header("Location: chore.php");
    exit();
}

$connection = new Database();

$stmt = $connection->prepare("SELECT gname FROM ChoreGroup WHERE ID=:gid");
$stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
$results = $stmt->execute();
$gname = $results->fetchArray(SQLITE3_ASSOC)['gname'];
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
        include ("./php/navbar-internal-mod.php");
        ?>
        <div class="center-box" style="padding:0;">
            <div id="user-modal" class="modal hidden">
                <div class="modal-content fast-animate">
                    <div style="width:100%">
                        <img id="user-img" class="big-pic" src="img/usr/default.png" alt="Profile Picture">
                    </div>
                    <table id="user-details">
                        <tr>
                            <td>Name:</td>
                            <td id="user-name"></td>
                        </tr>
                        <tr>
                            <td>Moderator:</td>
                            <td id="user-modstatus"></td>
                        </tr>
                    </table>
                    <div id="user-button-group">
                        <a class="button disabled" id="toggle-mod">Toggle Moderator</a>
                        <a class="button disabled" id="kick">Remove from group</a>
                    </div>
                </div>
            </div>
            <div id="join-code-modal" class="modal hidden">
                <div class="modal-content fast-animate">
                    <h3>Join Code:</h3>
                    <div class="text-box">
                        <h4><?php
                            $stmt = $connection->prepare("SELECT code FROM ChoreGroup WHERE ID=:gid");
                            $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                            $results = $stmt->execute();

                            echo ($results->fetchArray(SQLITE3_ASSOC)['code']);
                        ?></h4>
                    </div>
                </div>
            </div>
            <div id="edit-name-modal" class="modal hidden">
                <div class="modal-content animate">
                    <h3>Rename Group</h3>
                    <form id="name-form">
                        <label for="group" class="hide-element"><?php echo $gname;?></label>
                        <input type="text" name="group" value="<?php echo $gname;?>">
                        <input type="hidden" name="gid" value="<?php echo $_SESSION['gid'];?>">
                        <p id="nmissing" class="warning hidden">Name Required</p><br>
                        <p id="ninvalid" class="warning hidden">Name contains invalid characters.</p><br>

                        <input type="submit" value="Submit">
                    </form>
                </div>
            </div>
            <div id="title-container">
                <h3><?php echo $gname;?></h3>
                <a id="edit-name">✏️</a>
            </div>
            <div class="other-left-panel">
                <h4>Group Members</h4>
                <div class="left-container">
                    <table class="members" id="mgroupmembers">
                        <colgroup>
                            <col style="width: 20%;">
                            <col style="width: 20%;">
                            <col style="width: 40%;">
                            <col style="width: 20%;">
                        </colgroup>
                        <tbody>
                        <?php
                        $stmt = $connection->prepare("SELECT ID, displayname FROM User Where GroupID=:gid");
                        $stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
                        $result = $stmt->execute();
                        while ($res = $result->fetchArray(SQLITE3_ASSOC)){
                            echo ('<tr>');
                            if (is_user_mod($res['ID'])){
                                echo ('<td><embed src="img/crown.svg" alt="Mod Icon"></td>');
                            }
                            else{
                                echo ('<td></td>');
                            }
                            if (file_exists(ROOT."/img/usr/".$res['ID'].".jpeg")){
                                echo ('<td><img src="img/usr/'.$res['ID'].'.jpeg" alt="Profile Picture"></td>');
                            }
                            else{
                                echo ('<td><img src="img/usr/default.png" alt="Profile Picture"></td>');
                            }
                            echo ('<td>'.$res['displayname'].'</td>');
                            echo ('<td class="magnify" data-uid="'.$res['ID'].'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 490 490">
                                    <style>svg {cursor:pointer;}</style>
                                    <path fill="none" stroke="#ffffff" stroke-width="36" stroke-linecap="round"
                                    d="m280,278a153,153 0 1,0-2,2l170,170m-91-117 110,110-26,26-110-110"/>
                                </svg> 
                            </td></tr>');
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="other-right-panel">
                <h4>Requesting Members</h4>
                <div class="right-container" style="text-align:right">
                    <table id="join-requests">
                        <?php
                        $stmt = $connection->prepare("SELECT JoinRequest.ID AS JoinID, User.ID AS UserID, User.displayname
                            FROM JoinRequest INNER JOIN User ON JoinRequest.UserID = User.ID 
                            WHERE JoinRequest.GroupID=:gid");
                        $stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
                        $results = $stmt->execute();

                        while ($res = $results->fetchArray(SQLITE3_ASSOC)){
                            echo ('<tr data-joinid="'.$res['JoinID'].'">');
                            if (file_exists(ROOT."/img/usr/".$res['UserID'].".jpeg")){
                                echo ('<td><img src="img/usr/'.$res['UserID'].'.jpeg" alt="Profile Picture"></td>');
                            }
                            else{
                                echo ('<td><img src="img/usr/default.png" alt="Profile Picture"></td>');
                            }
                            echo ('<td>'.$res['displayname'].'</td>
                                <td class="circle-button" id="accept-button"><a>✓</a></td>
                                <td class="circle-button" id="decline-button"><a>✖</a></td>
                            </tr>');
                        }
                        ?>
                    </table>
                    <a id="display-join">Display join code</a>
                </div>
            </div>
        </div>
    </body>
</html>