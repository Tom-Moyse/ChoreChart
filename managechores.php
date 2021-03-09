<?php
include_once('main.php');
include(ROOT.'/php/utils.php');
require_login();
require_group();
require_no_joining_status();

if (!is_mod()){
    header("Location: mychores.php");
    exit();
}

$connection = new Database();

$stmt = $connection->prepare("SELECT gname FROM ChoreGroup WHERE ID=:gid");
$stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
$results = $stmt->execute();
$gname = h($results->fetchArray(SQLITE3_ASSOC)['gname']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ("./php/header.php");
        ?>
        <title>ChoreChart</title>
        <script src="js/managechores-control.js"></script>
    </head>
    <body>
        <?php
        $active=4;
        include ("./php/navbar-internal-mod.php");
        ?>
        <div class="center-box" style="padding:0;">
            <div id="title-container">
                <h3><?php echo $gname;?></h3>
            </div>
            <div id="edit-repeating-modal" class="modal hidden">
                <div class="modal-content fast-animate">
                    <form class="modal-form" id="edit-repeating">
                        <table>
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td><label for="contents">Chore:</label></td>
                                    <td><input id="er-chore" type="text" name="contents" value="Current chore contents"></td>
                                </tr>
                                <tr>
                                    <td><label for="fixed">Auto Choreholder:</label></td>
                                    <td><input id="er-check" type="checkbox" name="fixed"></td>
                                </tr>
                                <tr id="er-chorehold">
                                    <td><label for="choreholder">Choreholder:</label></td>
                                    <td>
                                        <select id="er-choreholder" name="choreholder">
                                            <?php
                                            // Dropdown displays all users currently in group, list
                                            // of users is obtained from db table
                                            $stmt = $connection->prepare("SELECT ID,displayname FROM User WHERE GroupID=:gid");
                                            $stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
                                            $results = $stmt->execute();
                                            while ($res = $results->fetchArray(SQLITE3_ASSOC)){
                                                echo ('<option value="'.$res['ID'].'">'.h($res['displayname']).'</option>');
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="startdate">Start date:</label></td>
                                    <td><input id="er-date" type="datetime-local" name="startdate"></td>
                                </tr>
                                <tr id="er-frequency">
                                    <td><label for="frequency">Repeat every:</label></td>
                                    <td>
                                        <input id="er-fnum" type="number" name="frequency" class="half-width">
                                        <select id="er-fval" name="interval" class="half-width">
                                            <option value="days">Day(s)</option>
                                            <option value="weeks">Week(s)</option>
                                            <option value="month">Month(s)</option>
                                            <option value="years">Year(s)</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>   
                    </form>
                    <p class="warning hidden chore-error">Chore description must not be empty</p>
                    <p class="warning hidden date-error">Invalid date</p>
                    <p class="warning hidden frequency-error">Invalid repeat value</p>
                    <a class="button" id="edit-repeating-chore">Save Changes</a>
                </div>
            </div>
            <div id="create-repeating-modal" class="modal hidden">
                <div class="modal-content fast-animate">
                    <form class="modal-form" id="create-repeating">
                        <table>
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td><label for="contents">Chore:</label></td>
                                    <td><input type="text" name="contents" placeholder="Clean the dishes..." id="cr-chore"></td>
                                </tr>
                                <tr>
                                    <td><label for="fixed">Auto Choreholder:</label></td>
                                    <td><input id="cr-check" type="checkbox" name="fixed" checked></td>
                                </tr>
                                <tr id="cr-chorehold" class="hidden">
                                    <td><label for="choreholder">Choreholder:</label></td>
                                    <td>
                                        <select name="choreholder">
                                            <?php
                                            // Dropdown displays all users currently in group, list
                                            // of users is obtained from db table
                                            $stmt = $connection->prepare("SELECT ID,displayname FROM User WHERE GroupID=:gid");
                                            $stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
                                            $results = $stmt->execute();
                                            while ($res = $results->fetchArray(SQLITE3_ASSOC)){
                                                echo ('<option value="'.$res['ID'].'">'.h($res['displayname']).'</option>');
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="startdate">Start date:</label></td>
                                    <td><input id="cr-date" type="datetime-local" name="startdate"></td>
                                </tr>
                                <tr>
                                    <td><label for="frequency">Repeat every:</label></td>
                                    <td>
                                        <input id="cr-fnum" type="number" name="frequency" class="half-width">
                                        <select id="cr-fval" name="interval" class="half-width">
                                            <option value="days">Day(s)</option>
                                            <option value="weeks">Week(s)</option>
                                            <option value="months">Month(s)</option>
                                            <option value="years">Year(s)</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>   
                    </form>
                    <p class="warning hidden chore-error">Chore description must not be empty</p>
                    <p class="warning hidden date-error">Invalid date</p>
                    <p class="warning hidden frequency-error">Invalid repeat value</p>
                    <a class="button" id="add-repeating-chore">Add Chore</a>
                </div>
            </div>
            <div id="edit-single-modal" class="modal hidden">
                <div class="modal-content fast-animate">
                    <form class="modal-form" id="edit-single">
                        <table>
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td><label for="contents">Chore:</label></td>
                                    <td><input id="es-chore" type="text" name="contents" value=""></td>
                                </tr>
                                <tr>
                                    <td><label for="fixed">Auto Choreholder:</label></td>
                                    <td><input id="es-check" type="checkbox" name="fixed"></td>
                                </tr>
                                <tr id="es-chorehold">
                                    <td><label for="choreholder">Choreholder:</label></td>
                                    <td>
                                        <select id="es-choreholder" name="choreholder">
                                            <?php
                                            // Dropdown displays all users currently in group, list
                                            // of users is obtained from db table
                                            $stmt = $connection->prepare("SELECT ID,displayname FROM User WHERE GroupID=:gid");
                                            $stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
                                            $results = $stmt->execute();
                                            while ($res = $results->fetchArray(SQLITE3_ASSOC)){
                                                echo ('<option value="'.$res['ID'].'">'.h($res['displayname']).'</option>');
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="date">Date:</label></td>
                                    <td><input id="es-date" type="datetime-local" name="date"></td>
                                </tr>
                                <tr>
                                    <td><label for="repeats">Repeats: </label></td>
                                    <td><input id="es-repeats" type="checkbox" name="repeats"></td>
                                </tr>
                                <tr id="es-frequency" class="hidden">
                                    <td><label for="frequency">Repeat every:</label></td>
                                    <td>
                                        <input id="es-fnum" type="number" name="frequency" class="half-width">
                                        <select name="interval" class="half-width">
                                            <option value="days">Day(s)</option>
                                            <option value="weeks">Week(s)</option>
                                            <option value="months">Month(s)</option>
                                            <option value="years">Year(s)</option>
                                        </select>
                                    </td>
                                </tr>       
                            </tbody>
                        </table>
                    </form>
                    <p class="warning hidden chore-error">Chore description must not be empty</p>
                    <p class="warning hidden date-error">Invalid date</p>
                    <p class="warning hidden frequency-error">Invalid repeat value</p>
                    <a class="button" id="edit-single-chore">Save Changes</a>
                </div>
            </div>
            <div id="create-single-modal" class="modal hidden">
                <div class="modal-content fast-animate">
                    <form class="modal-form" id="create-single">
                        <table>
                            <colgroup>
                                <col style="width:30%;">
                                <col style="width:70%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td><label for="contents">Chore:</label></td>
                                    <td><input type="text" name="contents" placeholder="Clean the dishes..." id="cs-chore"></td>
                                </tr>
                                <tr>
                                    <td><label for="fixed">Auto Choreholder:</label></td>
                                    <td><input id="cs-check" type="checkbox" name="fixed" checked></td>
                                </tr>
                                <tr id="cs-chorehold" class="hidden">
                                    <td><label for="choreholder">Choreholder:</label></td>
                                    <td>
                                        <select name="choreholder">
                                            <?php
                                            // Dropdown displays all users currently in group, list
                                            // of users is obtained from db table
                                            $stmt = $connection->prepare("SELECT ID,displayname FROM User WHERE GroupID=:gid");
                                            $stmt->bindValue(":gid", $_SESSION['gid'], SQLITE3_INTEGER);
                                            $results = $stmt->execute();
                                            while ($res = $results->fetchArray(SQLITE3_ASSOC)){
                                                echo ('<option value="'.$res['ID'].'">'.h($res['displayname']).'</option>');
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="date">Date:</label></td>
                                    <td><input type="datetime-local" name="date" id="cs-date" placeholder="mm/dd/yyyy --:--"></td>
                                </tr>      
                            </tbody>
                        </table>
                    </form>
                    <p class="warning hidden chore-error">Chore description must not be empty</p>
                    <p class="warning hidden date-error">Invalid date</p>
                    <a class="button" id="add-single-chore">Create Chore</a>
                </div>
            </div>
            <div class="left-div">
                <h4>Past Chores</h4>
                <div class="div-content">
                    <p id="no-past-chores">No past chores found</p>
                    <table>
                        <colgroup>
                            <col>
                            <col style="width: 35px;">
                        </colgroup>
                        <tbody id="prev-chores">
                            <?php
                            // Display list of all chores that were 'ono-off' and have now passed the
                            // completion deadline. Retrieved from the db chore table
                            $stmt = $connection->prepare("SELECT ID, contents FROM Chore WHERE repeats=0 
                                AND GroupID=:gid AND julianday(date('now')) > julianday(lastchoreitemdate)");
                            $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                            $results = $stmt->execute();
                            while ($res = $results->fetchArray(SQLITE3_ASSOC)){
                                echo ('<tr data-choreid="'.$res['ID'].'">
                                    <td>'.h($res['contents']).'</td>
                                    <td class="other-circle-button delete-button"><a>🗑️</a></td>
                                </tr>');
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mid-div">
                <h4>Repeating Chores</h4>
                <div class="div-content">
                    <p id="no-repeating-chores">No repeating chores found</p>
                    <table>
                        <colgroup>
                            <col>
                            <col style="width: 35px;">
                            <col style="width: 35px;">
                        </colgroup>
                        <tbody id="repeating-chores">
                            <?php
                            // Displays a list of all the groups repeating chores. Obtained from
                            // the db chore table
                            $stmt = $connection->prepare("SELECT ID, contents FROM Chore WHERE repeats=1 
                                AND GroupID=:gid");
                            $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                            $results = $stmt->execute();
                            while ($res = $results->fetchArray(SQLITE3_ASSOC)){
                                echo ('<tr data-choreid="'.$res['ID'].'">
                                    <td>'.h($res['contents']).'</td>
                                    <td class="other-circle-button edit-button"><a>🖊️</a></td>
                                    <td class="other-circle-button delete-button"><a>🗑️</a></td>
                                </tr>');
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <a class="button" id="create-repeating-button">Create New</a> 
            </div>
            <div class="right-div">
                <h4>One-off Chores</h4>
                <div class="div-content">
                    <p id="no-single-chores">No one-off chores found</p>
                    <table>
                        <colgroup>
                            <col>
                            <col style="width: 35px;">
                            <col style="width: 35px;">
                        </colgroup>
                        <tbody id="single-chores">
                            <?php
                            // Display a list of all the one off chores currently set that have yet
                            // to reach the deadline date 'upcoming'. Chores fetched from db table
                            $stmt = $connection->prepare("SELECT ID, contents FROM Chore WHERE repeats=0 
                                AND GroupID=:gid AND julianday(date('now')) <= julianday(lastchoreitemdate)");
                            $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                            $results = $stmt->execute();
                            while ($res = $results->fetchArray(SQLITE3_ASSOC)){
                                echo ('<tr data-choreid="'.$res['ID'].'">
                                    <td>'.h($res['contents']).'</td>
                                    <td class="other-circle-button edit-button"><a>🖊️</a></td>
                                    <td class="other-circle-button delete-button"><a>🗑️</a></td>
                                </tr>');
                            }
                            ?>
                        </tbody>
                    </table>
                </div> 
                <a class="button" id="create-single-button">Create New</a>  
            </div>
        </div>
    </body>
</html>