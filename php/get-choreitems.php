<?php
// The file returns all the html for constructing a table chore item view and mirrors functionality
// located within chores but is instead called via a post request. This is helpful as it allows new
// tables to be generated dynamically via js/ajax when the user presses the left and right buttons
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: signout.php");
    exit();
}
if (!isset($_SESSION)){
    session_start();
}
include_once("../main.php");
include(ROOT."/php/database.php");

function shorten($input){
    if (strlen($input) < 15){
        return $input;
    }
    return substr($input, 0, 12).'...';
}

// Generate chore's for set date range
$finish_date = date('Y-m-d' ,strtotime("+ 10 days", strtotime($_POST['date'])));

$connection = new Database();
// Relevant information is queried for each choreitem, and all choreitems
// associated with the given group between the two given dates are selected
$query = 'SELECT ChoreItem.contents, ChoreItem.completed, ChoreItem.deadline,
    User.displayname FROM ChoreItem INNER JOIN User ON ChoreItem.UserID =
    User.ID WHERE User.GroupID = :gid AND ChoreItem.deadline BETWEEN :d1 AND :d2
    ORDER BY ChoreItem.deadline ASC';
$stmt = $connection->prepare($query);
$stmt->bindValue(':gid', $_SESSION['gid'], SQLITE3_INTEGER);
$stmt->bindValue(':d1', $_POST['date'], SQLITE3_TEXT);
$stmt->bindValue(':d2', $finish_date, SQLITE3_TEXT);
$page_results = $stmt->execute();

$curr_date = $_POST['date'];
$counter = 0;
$prev_counter = 0;
$date = date('d/m/Y', strtotime("+ ".$counter." days", strtotime($curr_date)));
$first = true;
$ran = false;
// Each chore item in the group is iterated over
while ($res= $page_results->fetchArray(SQLITE3_ASSOC)){
    $ran = true;
    // Check if current chore is on a new day to previous (keep track of days)
    // If so move onto next table cell until chore item found or ten cells
    // have been generated
    while (strtotime($res['deadline']) >= strtotime("+ ".$counter." days", strtotime($curr_date))){
        $date = date('d/m/Y', strtotime("+ ".$counter." days", strtotime($curr_date)));
        
        if ($first){
            echo ('<table><tr><td><p>'.$date.'</p><ul>');
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
        echo ('<li class="chore-element" '.$data_info.'>'.shorten($res['contents']).'</li>');
    }
    else{
        echo ('<li class="chore-element complete" '.$data_info.'>'.shorten($res['contents']).'</li>');
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
    echo ('<table><tr><td><p>'.$date.'</p><ul>');

    for ($i=1; $i < 10; $i++) { 
        $date = date('d/m/Y', strtotime("+ ".$i." days", strtotime($curr_date)));
        if ($i == 5){
            echo ('</ul></td></tr><tr><td><p>'.$date.'</p><ul>');
        }
        else{
            echo ('</ul></td><td><p>'.$date.'</p><ul>');
        }
        
    }
    echo ('</ul></td></tr></table>');
}
?>