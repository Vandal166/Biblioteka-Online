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
    <title>Rejestracja</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
    <div class="register-box">
            <h2>Rejestracja</h2>

            <!-- komunikat gdy nie uda sie zarejestrowac -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" style="color: red; margin-bottom: 10px;">
                <?php echo $_SESSION['error']; // wyswietlanie komunikat ?>
            </div>
            <?php unset($_SESSION['error']); // usuwanie komunikatu ?>
        <?php endif; ?>


        <form action="../php/process_register.php" method="post">
            <!-- Przywraca ostatnie wpisane dane przy błędzie(bez hasła) -->
            <input type="text" name="first_name" placeholder="Imię" value="<?php echo get_form_value('first_name'); ?>" required>
            <input type="text" name="last_name" placeholder="Nazwisko" value="<?php echo get_form_value('last_name'); ?>" required>
            <input type="text" name="phone" placeholder="Telefon" value="<?php echo get_form_value('phone'); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo get_form_value('email'); ?>" required>
            <input type="text" name="username" placeholder="Login" value="<?php echo get_form_value('username'); ?>" required>

            <input type="password" name="password" placeholder="Hasło" required>
            <input type="password" name="confirm_password" placeholder="Potwierdź hasło" required>

            <!-- TODO dodac akceptacje jakiegos regulaminu or smth ? -->
            <button type="submit">Zarejestruj się</button>
            <a href="../php/login.php">Masz już konto? Zaloguj się</a>
        </form>
        <!-- TODO Reset password feature?? -->
        <a href="../index.php" class="btn-back">Powrót</a>
    </div>
</body>
</html>
