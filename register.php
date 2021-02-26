<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ("./php/header.php");
        ?>
        <title>ChoreChart: Register</title>
        <script src="js/register-validate.js"></script>
    </head>
    <body>
        <?php
        $active=2;
        include ("./php/navbar-external.php");
        ?>
        <div class="center-box">
            <div class="focus-container">
                <h3>Register</h3>
                <form>
                    <label for="username" class="hide-element">Username</label>
                    <input type="text" name="username" placeholder="Username">
                    <p id="utaken" class="warning hidden">Username already taken</p>
                    <p id="uinvalid" class="warning hidden">Username contains invalid characters</p>
                    <p id="umissing" class="warning hidden">Username required</p><br>

                    <label for="email" class="hide-element">Email</label>
                    <input type="text" name="email" placeholder="Email">
                    <p id="etaken" class="warning hidden">Email already in use</p>
                    <p id="einvalid" class="warning hidden">Email is invalid</p>
                    <p id="emissing" class="warning hidden">Email required</p><br><br>

                    <label for="password" class="hide-element">Password</label>
                    <input type="password" name="password" placeholder="Password">
                    <p id="pmissing" class="warning hidden">Password required</p><br>

                    <label for="password-confirm" class="hide-element">Password Confirmation</label>
                    <input type="password" name="password-confirm" placeholder="Confirm Password">
                    <p id="pmatch" class="warning hidden">Passwords do not match</p>
                    <p id="pinvalid" class="warning hidden">Password contains spaces: </p>
                    <p id="pshort" class="warning hidden">Password below 8 characters</p><br>

                    <input type="submit" value="Register">
                </form>
                <p>Already have an account, <a href="login.php">login here instead</a></p>
            </div>
        </div>
    </body>
</html>