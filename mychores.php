<?php
include_once('main.php');
include(ROOT.'/php/utils.php');
require_login();
require_group();
require_no_joining_status();

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
        <script src="js/mychores-control.js"></script>
    </head>
    <body>
        <?php
        $active=1;
        if (is_mod()){
            include ("./php/navbar-internal-mod.php");
        }
        else{
            include ("./php/navbar-internal-standard.php");
        }
        ?>
        <div class="center-box">
            <div id="chore-modal" class="modal hidden">
                <div class="modal-content fast-animate">
                    <h3>Chore Info</h3>
                    <table id="chore-info" style="table-layout:fixed;width:100%;margin:auto;">
                        <tr>
                            <td>Chore Description:</td>
                            <td id="cdesc">chore description</td>
                        </tr>
                        <tr>
                            <td>Chore Deadline:</td>
                            <td id="cdate">relative date</td>
                        </tr>
                    </table>
                    <a class="button" id="toggle-complete" style="color:white">Mark as Complete</a>
                </div>
            </div>
            <h3>Your Chores</h3>
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

                function genTable($date1, $date2, $show, $id){
                    $connection = new Database();
                    $query = 'SELECT ChoreItem.contents, ChoreItem.completed, ChoreItem.deadline,
                        ChoreItem.ID FROM ChoreItem INNER JOIN User ON ChoreItem.UserID =
                        User.ID WHERE User.ID = :id AND ChoreItem.deadline BETWEEN :d1 AND :d2
                        ORDER BY ChoreItem.deadline ASC';
                    $stmt = $connection->prepare($query);
                    $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
                    $stmt->bindValue(':d1', $date1, SQLITE3_TEXT);
                    $stmt->bindValue(':d2', $date2, SQLITE3_TEXT);
                    $page_prev_results = $stmt->execute();
                    
                    $curr_date = $date1;
                    $counter = 0;
                    $prev_counter = 0;
                    $date = date('d/m/Y', strtotime("+ ".$counter." days", strtotime($curr_date)));
                    $first = true;
                    $ran = false;
                    while ($res= $page_prev_results->fetchArray(SQLITE3_ASSOC)){
                        $ran = true;
                        // Check if current chore is on a new day to previous (keep track of days)
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
                                    $date_no_seconds.'" data-choreid="'.$res['ID'].'"';
                        if ($res['completed'] == 0){
                            echo ('<li class="chore-element" '.$data_info.'>'.h(shorten($res['contents'])).'</li>');
                        }
                        else{
                            echo ('<li class="chore-element complete" '.$data_info.'>'.h(shorten($res['contents'])).'</li>');
                        }
                        
                    }
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
                // Only current page will be visible
                $prev_date = date('Y-m-d' ,strtotime("- 10 days", strtotime(date('Y-m-d'))));
                $day0_date = date('Y-m-d');
                $day10_date = date('Y-m-d' ,strtotime("+ 10 days", strtotime(date('Y-m-d'))));
                $day20_date = date('Y-m-d' ,strtotime("+ 20 days", strtotime(date('Y-m-d'))));

                genTable($prev_date, $day0_date, false, "left-chores");
                genTable($day0_date, $day10_date, true, "mid-chores");
                genTable($day10_date, $day20_date, false, "right-chores");
                ?>
            </div>
            <div id="chore-notif">
                <h4>Active Chore</h4>
                <br>
                <?php
                $stmt = $connection->prepare("SELECT contents, deadline FROM ChoreItem WHERE
                    UserID=:id AND julianday(deadline) > julianday(date('now')) AND completed=0
                    ORDER BY deadline ASC");
                $stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
                $results = $stmt->execute();
                $res = $results->fetchArray(SQLITE3_ASSOC);

                if ($res == false){
                    echo ('<p>You have completed all your chores!</p>');
                }
                else{
                    echo('<table id="chore-info" style="margin:auto"><tr>
                            <td>Chore: </td>
                            <td>'.h($res['contents']).'</td>
                        </tr><tr>
                            <td>Deadline: </td>
                            <td>'.h(substr($res['deadline'],0,-3)).'</td>
                        </tr></table>');
                }
                ?>
            </div>
        </div>
    </body>
</html>