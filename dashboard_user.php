<?php
session_start();
require "validation/connect.php";

$user_id = $_SESSION["user_id"] ?? null;
$username = $_SESSION["username"] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
</head>
<body>
    <div class="dashboardpage">
        <div class="sidebar">
            <div class="icons">
                <img src="assets/logo.png" alt="logo" class="logo" style="width: 4vw;">

                <a href="dashboard_user.php">
                    <img src="assets/homeicon.png" alt="home" class="home">
                </a>

                <a href="wallet.php">
                    <img src="assets/walleticon.png" alt="wallet" class="wallet" style="width: 3vw;">
                </a>

                <a href="logout.php">
                    <img src="assets/logouticon.png" alt="logout" class="logout" style="width: 3vw; margin-top: 42vh;">
                </a>
            </div>
        </div>

        <div class="backgroundimg">
            <div class="character">
                <div class="characterwrapper">
                    <img src="assets/characbg.png" alt="background">
                    <span class="welcometext" style="font-size: 3vw;">Welcome,<br><?php echo $username; ?>!</span>
                </div>
            </div>

            <div class="overview">
                    <ul class="list">
                    <li>Allowance</li>
                    <li>Budget</li>
                    <li>Expenses</li>
                    </ul>
            </div>

            <div class="expenses">
                
            </div>
        </div>

    </div>
</body>
</html>