<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ("./php/header.php");
        ?>
        <title>ChoreChart: Login</title>
        <script src="js/login-validate.js"></script>
    </head>
    <body>
        <?php
        $active=1;
        include ("./php/navbar-external.php");
        ?>
        <div class="center-box">
            <div class="focus-container">
                <h3>Login</h3>
                <form>
                    <label for="uname-email" class="hide-element">Username/Email</label>
                    <input type="text" name="uname-email" placeholder="Username/Email">
                    <p id="uemissing" class="warning hidden">Username/Email required</p><br>

                    <label for="password" class="hide-element">Password</label>
                    <input type="password" name="password" placeholder="Password">
                    <p id="pmissing" class="warning hidden">Password required</p><br>
                    <p id="match" class="warning hidden">Username/Email & Password do not match</p><br>

                    <input type="submit" value="Login">
                </form>
                <p>Don't have an account, <a href="register.php">create one here instead</a></p>
            </div>
        </div>
    </body>
</html>