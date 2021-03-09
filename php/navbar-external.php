<?php
// Template navbar for the index, login and register page - takes a $active variable which indicates
// the current page such that active id can be set to correct link
    switch ($active){
        case 0:
            echo '<nav>
                <a href="index.php"><img id="nav-img" src="img/icon.png" alt="Logo"></a>
                <ul>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>';
            break;
        case 1:
            echo '<nav>
            <a href="index.php"><img id="nav-img" src="img/icon.png" alt="Logo"></a>
            <ul>
                <li><a id="active" href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>';
            break;
        case 2:
            echo '<nav>
            <a href="index.php"><img id="nav-img" src="img/icon.png" alt="Logo"></a>
            <ul>
                <li><a href="login.php">Login</a></li>
                <li><a id="active" href="register.php">Register</a></li>
            </ul>
        </nav>';
    }
?>