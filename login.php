<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
</head>
<body>
    <div class="loginpage">
        <div class="wrapper">
            <div class="title">
                <h1>
                    <a href="homepage.html">
                        <img src="assets/bitlog.png" alt="BitLog" class="bitlogLogo">
                    </a>
                </h1>
                <p>
                    Embark on a pixel-powered money quest where you manage your gold,
                    <br>defeat overspending, and unlock savings milestones!
                </p>

                <div class="buttons">
                <a href="signup.php" class="signupButton">
                    <div class="buttonWrapper">
                    <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                    <span class="buttonText" style="font-size: 1.05vw;">SIGN UP</span>
                    </div>
                </a>
            </div>
            </div>

            <div class="loginbox">
                <div class="boxwrapper">
                    <img src="assets/box.PNG" alt="login box" class="boxIcon">
                    <span class="loginHeader" style="font-size: 4.5vw;">LOG IN</span>

                    <form action="validation/users.php" method="POST" class="loginForm">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>

                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>

                        <button type="submit" class="enterButton">enter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>