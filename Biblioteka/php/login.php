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
    <title>Logowanie</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-box">
        <h2>Logowanie</h2>

        <?php display_messages('login'); ?>
        <?php display_messages('register'); ?>
        <?php display_messages('new_password'); ?>
        
        <form action="process_login.php" method="POST">
            <input type="hidden" name="formularz" value="login">
            <input type="text" name="login" placeholder="Login/Email" value="<?php echo get_form_value('login'); ?>" required>
            <input type="password" name="password" placeholder="Hasło" minlength="6" maxlength="255" required>
            <button type="submit">Zaloguj się</button>
        </form>
        <a href="register.php">Nie masz konta? Zarejestruj się</a>
        <a href="../php/reset_password.php">Zapomniałeś hasła?</a>
        <a href="../index.php" class="btn-back">Powrót</a>
    </div>
</body>
</html>
