<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/signup.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
</head>
<body>
    <div class="signuppage">
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
                <a href="login.php" class="signupButton">
                    <div class="buttonWrapper">
                    <img src="assets/purpleButton.PNG" alt="green button" class="signupIcon">
                    <span class="buttonText" style="font-size: 1.05vw;">LOG IN</span>
                    </div>
                </a>
            </div>
            </div>

            <div class="loginbox">
                <div class="boxwrapper">
                    <img src="assets/box.PNG" alt="login box" class="boxIcon">
                    <span class="loginHeader" style="font-size: 4.5vw;">SIGN UP</span>

                    <form action="validation/createuser.php" method="POST" class="loginForm">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>

                        <label for="email">Username</label>
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