<?php
include('main.php');
include(ROOT.'/php/utils.php');
require_login();
require_group();
require_no_joining_status();
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
        <div class="center-box">
            <div class="left-panel">
                <h3>Chores</h3>
                <div id="chore-container">
                    <a class="scroll-button">🡠</a>
                    <a class="scroll-button">🡢</a>
                    <div id="chores">
                        <table>
                            <tr>
                                <td>
                                    <p>28/02/2021</p>
                                    <ul>
                                        <li><a>C1</a></li>
                                        <li id="complete"><a>C2</a></li>
                                        <li><a>C3</a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p>01/03/2021</p>
                                    <ul>
                                        <li><a>D1</a></li>
                                        <li><a>D2</a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p>02/03/2021</p>
                                    <ul>
                                        <li><a>E1</a></li>
                                        <li id="complete"><a>E2</a></li>
                                        <li><a>E3</a></li>
                                        <li id="complete"><a>E4</a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p>03/03/2021</p>
                                    <ul>
                                        <li><a>F1</a></li>
                                        <li><a>F2</a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p>04/03/2021</p>
                                    <ul>
                                        <li id="complete"><a>G1</a></li>
                                        <li><a>G2</a></li>
                                        <li><a>G3</a></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                            <td>
                                    <p>05/03/2021</p>
                                    <ul>
                                        <li><a>C1</a></li>
                                        <li><a>C2</a></li>
                                        <li><a>C3</a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p>06/03/2021</p>
                                    <ul>
                                        <li><a>D1</a></li>
                                        <li><a>D2</a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p>07/03/2021</p>
                                    <ul>
                                        <li><a>E1</a></li>
                                        <li><a>E2</a></li>
                                        <li><a>E3</a></li>
                                        <li><a>E4</a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p>08/03/2021</p>
                                    <ul>
                                        <li id="complete"><a>F1</a></li>
                                        <li id="complete"><a>F2</a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p>09/03/2021</p>
                                    <ul>
                                        <li><a>G1</a></li>
                                        <li><a>G2</a></li>
                                        <li><a>G3</a></li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="right-panel">
                <h3>Members</h3>
                <div class="right-container">
                    <table id="members">
                        <tr>
                            <td><embed src="img/crown.svg" alt="Profile Picture"></td>
                            <td><img src="img/usr/default.png" alt="Profile Picture"></td>
                            <td>Billy Wonder but actually a stupidly long name</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><img src="img/usr/default.png" alt="Profile Picture"></td>
                            <td>Bob Marley</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>