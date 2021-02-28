<?php
switch($active){
    case 0:
        echo '<nav>
            <a href="php/signout.php"><img id="nav-img" src="img/icon.png" alt="Logo"></a>
            <ul>
                <li><a href="" id="active">Group Chores</a></li>
                <li><a href="mychores.php">My Chores</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="php/signout.php">Sign-out</a></li>
            </ul>
        </nav>';
        break;
    case 1:
        echo '<nav>
            <a href="php/signout.php"><img id="nav-img" src="img/icon.png" alt="Logo"></a>
            <ul>
                <li><a href="chores.php">Group Chores</a></li>
                <li><a href="" id="active">My Chores</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="php/signout.php">Sign-out</a></li>
            </ul>
        </nav>';
        break;
    case 2:
        echo '<nav>
            <a href="php/signout.php"><img id="nav-img" src="img/icon.png" alt="Logo"></a>
            <ul>
                <li><a href="chores.php">Group Chores</a></li>
                <li><a href="mychores.php">My Chores</a></li>
                <li><a href="" id="active">My Account</a></li>
                <li><a href="php/signout.php">Sign-out</a></li>    
            </ul>
        </nav>';
}
?>