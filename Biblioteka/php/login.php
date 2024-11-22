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

         <!-- komunikat gdy nie uda sie zalogowac -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" style="color: red; margin-bottom: 10px;">
                <?php echo $_SESSION['error']; // wyswietlanie komunikat ?>
            </div>
            <?php unset($_SESSION['error']); // usuwanie komunikatu ?>
        <?php endif; ?>

        <!-- kominakt gdy uda sie zarejestrowac -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message" style="color: green; margin-bottom: 10px;">
                <?php echo $_SESSION['success']; // wyswietlanie komunikat ?>
            </div>
            <?php unset($_SESSION['success']); // usuwanie komunikatu ?>
        <?php endif; ?>
        
        <form action="process_login.php" method="POST">
            <input type="text" name="login" placeholder="Login" required>
            <input type="password" name="password" placeholder="Hasło" required>
            <button type="submit">Zaloguj się</button>
        </form>
        <a href="register.php">Nie masz konta? Zarejestruj się</a>
        <a href="../index.php" class="btn-back">Powrót</a>
    </div>
</body>
</html>
