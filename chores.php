<?php
include_once('main.php');
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
                                <td>Name</td>
                            </tr>
                            <tr>
                                <td>Chore:</td>
                                <td>chore description</td>
                            </tr>
                            <tr>
                                <td>Deadline:</td>
                                <td>relative date</td>
                            </tr>
                            <tr>
                                <td>Completed:</td>
                                <td>tick/cross</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
                                        <li class="chore-element">C1</li>
                                        <li class="chore-element complete">C2</li>
                                        <li class="chore-element">C3</li>
                                    </ul>
                                </td>
                                <td>
                                    <p>01/03/2021</p>
                                    <ul>
                                        <li class="chore-element">D1</li>
                                        <li class="chore-element">D2</li>
                                    </ul>
                                </td>
                                <td>
                                    <p>02/03/2021</p>
                                    <ul>
                                        <li class="chore-element">E1</li>
                                        <li class="chore-element complete">E2</li>
                                        <li class="chore-element">E3</li>
                                        <li class="chore-element complete">E4</li>
                                    </ul>
                                </td>
                                <td>
                                    <p>03/03/2021</p>
                                    <ul>
                                        <li class="chore-element">F1</li>
                                        <li class="chore-element">F2</li>
                                    </ul>
                                </td>
                                <td>
                                    <p>04/03/2021</p>
                                    <ul>
                                        <li class="chore-element complete"><a>G1</a></li>
                                        <li class="chore-element">G2</li>
                                        <li class="chore-element">G3</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                            <td>
                                    <p>05/03/2021</p>
                                    <ul>
                                        <li class="chore-element">C1</li>
                                        <li class="chore-element">C2</li>
                                        <li class="chore-element">C3</li>
                                    </ul>
                                </td>
                                <td>
                                    <p>06/03/2021</p>
                                    <ul>
                                        <li class="chore-element">D1</li>
                                        <li class="chore-element">D2</li>
                                    </ul>
                                </td>
                                <td>
                                    <p>07/03/2021</p>
                                    <ul>
                                        <li class="chore-element">E1</li>
                                        <li class="chore-element">E2</li>
                                        <li class="chore-element">E3</li>
                                        <li class="chore-element">E4</li>
                                    </ul>
                                </td>
                                <td>
                                    <p>08/03/2021</p>
                                    <ul>
                                        <li class="chore-element complete">F1</li>
                                        <li class="chore-element complete">F2</li>
                                    </ul>
                                </td>
                                <td>
                                    <p>09/03/2021</p>
                                    <ul>
                                        <li class="chore-element">G1</li>
                                        <li class="chore-element">G2</li>
                                        <li class="chore-element">G3</li>
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