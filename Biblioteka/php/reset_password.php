<?php
session_start();
require_once('helpers.php');
redirect_if_logged_in();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zresetuj hasło</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-box">
        <h2>Zresetuj hasło</h2>

        <?php display_messages('reset_password'); ?>
        <?php display_messages('new_password'); ?>

        <form action="process_reset_password.php" method="POST">
            <input type="hidden" name="formularz" value="reset_password">
            <input type="text" name="login" placeholder="Login/Email" required>
            <button type="submit">Zresetuj hasło</button>
        </form>
        <a href="login.php">Powrót</a>
    </div>
</body>
</html>