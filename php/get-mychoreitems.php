<?php
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
$query = 'SELECT ChoreItem.contents, ChoreItem.completed, ChoreItem.deadline,
    ChoreItem.ID FROM ChoreItem INNER JOIN User ON ChoreItem.UserID =
    User.ID WHERE User.ID = :id AND ChoreItem.deadline BETWEEN :d1 AND :d2
    ORDER BY ChoreItem.deadline ASC';
$stmt = $connection->prepare($query);
$stmt->bindValue(':id', $_SESSION['uid'], SQLITE3_INTEGER);
$stmt->bindValue(':d1', $_POST['date'], SQLITE3_TEXT);
$stmt->bindValue(':d2', $finish_date, SQLITE3_TEXT);
$page_results = $stmt->execute();

$curr_date = $_POST['date'];
$counter = 0;
$prev_counter = 0;
$date = date('d/m/Y', strtotime("+ ".$counter." days", strtotime($curr_date)));
$first = true;
$ran = false;
while ($res= $page_results->fetchArray(SQLITE3_ASSOC)){
    $ran = true;
    // Check if current chore is on a new day to previous (keep track of days)
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
                $date_no_seconds.'" data-choreid="'.$res['ID'].'"';
    if ($res['completed'] == 0){
        echo ('<li class="chore-element" '.$data_info.'>'.shorten($res['contents']).'</li>');
    }
    else{
        echo ('<li class="chore-element complete" '.$data_info.'>'.shorten($res['contents']).'</li>');
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