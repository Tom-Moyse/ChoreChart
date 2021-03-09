<?php
include_once('main.php');
include(ROOT.'/php/utils.php');
require_login();
require_group();
require_no_joining_status();

// On page load request all required choreitems for the next 20 days are computed
// and added to the chore item database.

// Send first item generation request via internal php (as to subsequently ajax)
$date = date('Y-m-d H:i:s' ,strtotime("+ 20 days", strtotime(date('Y-m-d H:i:s'))));
$connection = new Database();

// Select all repeating sessions that require new chore items to be generated
$stmt = $connection->prepare("SELECT * FROM Chore WHERE GroupID=:gid AND repeats=1 AND
                            julianday(:gendate) > julianday(lastchoreitemdate)");
$stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
$stmt->bindValue(':gendate', $date, SQLITE3_TEXT);
$results = $stmt->execute();
$inserts_occured = false;

// Loop through each chore that needs new chore items generating
while($res = $results->fetchArray(SQLITE3_ASSOC)){
    // For each chore, add chore items until required number of items added
    $new_datestamp = strtotime($res['lastchoreitemdate'] . $res['interval']);
    while ($new_datestamp < strtotime($date)){
        $inserts_occured = true;
        // Insert new chore item
        $stmt = $connection->prepare("INSERT INTO ChoreItem VALUES (NULL, :con, 0, :dea, :cid, :id)");
        $stmt->bindValue(':con', $res['contents'], SQLITE3_TEXT);
        $stmt->bindValue(':dea', date('Y-m-d H:i:s', $new_datestamp), SQLITE3_TEXT);
        $stmt->bindValue(':cid', $res['ID'], SQLITE3_INTEGER);
        // If chore has a 'fixed' user (meaning not auto choreholder) set choreitem uid to match 
        if ($res['fixed'] == 1){
            $stmt->bindValue(':id', $res['UserID'], SQLITE3_INTEGER);
        }
        // Otherwise calculate the user which the choreitem will be allocated to
        else{
            // First selects any users with 0 other chore items
            $substmt = $connection->prepare("SELECT ID FROM User WHERE GroupID=:gid EXCEPT SELECT ID
            FROM (SELECT COUNT(User.ID), User.GroupID, User.ID FROM User INNER JOIN ChoreItem ON
            ChoreItem.UserID=User.ID GROUP BY ChoreItem.UserID ORDER BY COUNT(User.ID) ASC) 
            WHERE GroupID=:gid");
            $substmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
            $subresults = $substmt->execute();
            $subres = $subresults->fetchArray(SQLITE3_ASSOC);
            // If no users had zero chore items
            if ($subres == false){
                // Selects user with least chore items (must have at least 1)
                $substmt = $connection->prepare("SELECT ID FROM (SELECT COUNT(User.ID), User.GroupID, User.ID
                FROM User INNER JOIN ChoreItem ON ChoreItem.UserID=User.ID GROUP BY ChoreItem.UserID 
                ORDER BY COUNT(User.ID) ASC) 
                WHERE GroupID=:gid");
                $substmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                $subresults = $substmt->execute();
                $subres = $subresults->fetchArray(SQLITE3_ASSOC);
                // If query fails to produce a result assign chore item to logged in user
                if ($subres == false){
                    $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
                }
                // Otherwise assign chore item to group member with least current amount of chore items
                else{
                    $stmt->bindValue(':id', $subres['ID'], SQLITE3_INTEGER);
                }
            }
            // If their was at least one user with zero chore items, assign choreitem to said user
            else{
                $stmt->bindValue(':id', $subres['ID'], SQLITE3_INTEGER);
            }
            
        }
        // Execute insert
        $stmt->execute();

        // Update date stamp
        $prev_datestamp = $new_datestamp;
        $new_datestamp = strtotime($res['interval'], $prev_datestamp);
    }
    if ($inserts_occured){
        // Update value of lastchoreitem for the chore
        $stmt = $connection->prepare("UPDATE Chore SET lastchoreitemdate=:newdate WHERE ID=:id");
        $stmt->bindValue(":newdate", date('Y-m-d H:i:s', $prev_datestamp));
        $stmt->bindValue(":id", $res['ID']);
        $stmt->execute();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ("./php/header.php");
        ?>
        <title>ChoreChart</title>
        <script src="js/chores-control.js"></script>
    </head>
    <body>
        <?php
        $active=0;
        if (is_mod()){
            include ("./php/navbar-internal-mod.php");
        }
        else{
            include ("./php/navbar-internal-standard.php");
        }
        ?>
        <div class="center-box" style="padding:0;">
            <div id="info-popup" class="popup hidden">
                <div class="popup-content fast-animate">
                    <table id="chore-popup" style="table-layout:fixed;width:100%;">
                        <colgroup>
                            <col style="width: 30%;">
                            <col style="width: 70%;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td>Who:</td>
                                <td id="cname">Name</td>
                            </tr>
                            <tr>
                                <td>Chore:</td>
                                <td id="cdesc">chore description</td>
                            </tr>
                            <tr>
                                <td>Deadline:</td>
                                <td id="cdate">relative date</td>
                            </tr>
                            <tr>
                                <td>Completed:</td>
                                <td id="ccom">tick/cross</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="title-container">
                <h3>
                <?php
                // Retrieve group name from database using session groupid
                $stmt = $connection->prepare("SELECT gname FROM ChoreGroup WHERE ID=:gid");
                $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                $results = $stmt->execute();
                    echo h($results->fetchArray(SQLITE3_ASSOC)['gname']);
                ?>
                </h3>
            </div> 
            <div class="left-panel">
                <h4>Chores</h4>
                <div id="chore-container">
                    <a class="scroll-button" id='left-scroll'>🡠</a>
                    <a class="scroll-button" id='right-scroll'>🡢</a>
                    <?php
                    function shorten($input){
                        if (strlen($input) < 15){
                            return $input;
                        }
                        return substr($input, 0, 12).'...';
                    }
                    // Function generates the table for all chore items for a given group between 
                    // two given dates, also takes a $show parameter to set whether table is hidden
                    // or not
                    function genTable($date1, $date2, $show, $id){
                        $connection = new Database();
                        // Relevant information is queried for each choreitem, and all choreitems
                        // associated with the given group between the two given dates are selected
                        $query = 'SELECT ChoreItem.contents, ChoreItem.completed, ChoreItem.deadline,
                            User.displayname FROM ChoreItem INNER JOIN User ON ChoreItem.UserID =
                            User.ID WHERE User.GroupID = :gid AND ChoreItem.deadline BETWEEN :d1 AND :d2
                            ORDER BY ChoreItem.deadline ASC';
                        $stmt = $connection->prepare($query);
                        $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                        $stmt->bindValue(':d1', $date1, SQLITE3_TEXT);
                        $stmt->bindValue(':d2', $date2, SQLITE3_TEXT);
                        $page_prev_results = $stmt->execute();
                        
                        $curr_date = $date1;
                        $counter = 0;
                        $prev_counter = 0;
                        $date = date('d/m/Y', strtotime("+ ".$counter." days", strtotime($curr_date)));
                        $first = true;
                        $ran = false;
                        // Each chore item in the group is iterated over
                        while ($res= $page_prev_results->fetchArray(SQLITE3_ASSOC)){
                            $ran = true;
                            // Check if current chore is on a new day to previous (keep track of days)
                            // If so move onto next table cell until chore item found or ten cells
                            // have been generated
                            while (strtotime($res['deadline']) >= strtotime("+ ".$counter." days", strtotime($curr_date))){
                                $date = date('d/m/Y', strtotime("+ ".$counter." days", strtotime($curr_date)));
                                
                                if ($first){
                                    if ($show){
                                        echo ('<div id="'.$id.'" class="chores"><table><tr><td><p>'.$date.'</p><ul>');
                                    }
                                    else{
                                        echo ('<div id="'.$id.'" class="chores hidden"><table><tr><td><p>'.$date.'</p><ul>');
                                    }
                                    $first = false;
                                }
                                else{
                                    // Move onto a new row after 5 days
                                    if ($counter == 5){
                                        echo('</ul></td></tr><tr><td><p>'.$date.'</p><ul>');
                                    }
                                    else{
                                        echo('</ul></td><td><p>'.$date.'</p><ul>');
                                    }
                                }
                                $counter++;
                            }

                            $date_no_seconds = date("d/m/Y H:i", strtotime($res['deadline']));
                            // Output html relevant to current chore
                            $data_info = 'data-contents="'.$res['contents'].'" data-deadline="'.
                                $date_no_seconds.'" data-choreholder="'.$res['displayname'].'"';
                            // Chore item is added to current table cell
                            if ($res['completed'] == 0){
                                echo ('<li class="chore-element" '.$data_info.'>'.h(shorten($res['contents'])).'</li>');
                            }
                            else{
                                echo ('<li class="chore-element complete" '.$data_info.'>'.h(shorten($res['contents'])).'</li>');
                            }
                            
                        }
                        // If all choreitems were added before last table cell added, add remaining
                        // required table cells
                        if ($ran){
                            for ($i=$counter; $i<10; $i++) { 
                                $date = date('d/m/Y', strtotime("+ ".$i." days", strtotime($curr_date)));
                                if ($i == 5){
                                    echo ('</ul></td></tr><tr><td><p>'.$date.'</p><ul>');
                                }
                                else{
                                    echo ('</ul></td><td><p>'.$date.'</p><ul>');
                                }
                            }
                            echo ('</ul></td></tr></table></div>');
                        }
                        
                        // If there are no chores make sure empty table is displayed
                        else{
                            $date = date('d/m/Y', strtotime("+ ".$counter." days", strtotime($curr_date)));
                            if ($show){
                                echo ('<div id="'.$id.'" class="chores"><table><tr><td><p>'.$date.'</p><ul>');
                            }
                            else{
                                echo ('<div id="'.$id.'" class="chores hidden"><table><tr><td><p>'.$date.'</p><ul>');
                            }
                            for ($i=1; $i < 10; $i++) { 
                                $date = date('d/m/Y', strtotime("+ ".$i." days", strtotime($curr_date)));
                                if ($i == 5){
                                    echo ('</ul></td></tr><tr><td><p>'.$date.'</p><ul>');
                                }
                                else{
                                    echo ('</ul></td><td><p>'.$date.'</p><ul>');
                                }
                                
                            }
                            echo ('</ul></td></tr></table></div>');
                        }
                    }

                    // Generate chore's for previous page, current page and next page
                    // Only current page will be visible (each page shows range of 10 days)
                    $prev_date = date('Y-m-d' ,strtotime("- 10 days", strtotime(date('Y-m-d'))));
                    $day0_date = date('Y-m-d');
                    $day10_date = date('Y-m-d' ,strtotime("+ 10 days", strtotime(date('Y-m-d'))));
                    $day20_date = date('Y-m-d' ,strtotime("+ 20 days", strtotime(date('Y-m-d'))));

                    genTable($prev_date, $day0_date, false, "left-chores");
                    genTable($day0_date, $day10_date, true, "mid-chores");
                    genTable($day10_date, $day20_date, false, "right-chores");
                    ?>
                </div>
            </div>
            <div class="right-panel">
                <h4>Members</h4>
                <div class="right-container">
                    <table class="members">
                        <?php
                        // Generate a list of all memebers currently in the group and display in
                        // this side panel
                        $connection = new Database();
                        $query = 'SELECT User.ID, User.displayname, User.moderator FROM User INNER JOIN
                            ChoreGroup ON User.GroupID = ChoreGroup.ID WHERE ChoreGroup.ID = :gid';
                        $stmt = $connection->prepare($query);
                        $stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
                        $members = $stmt->execute();
                        
                        while ($member = $members->fetchArray(SQLITE3_ASSOC)){
                            echo ('<tr>');
                            if ($member['moderator'] == 1){
                                echo ('<td><embed src="img/crown.svg" alt="Mod Icon"></td>');
                            }
                            else{
                                echo ('<td></td>');
                            }
                            if (file_exists(ROOT."/img/usr/".$member['ID'].".jpeg")){
                                echo ('<td><img src="img/usr/'.$member['ID'].'.jpeg" alt="Profile Picture" class="thumbnail"></td>');
                            }
                            else{
                                echo ('<td><img src="img/usr/default.png" alt="Profile Picture"></td>');
                            }
                            echo('<td>'.h($member['displayname']).'</td></tr>');
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>