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
        <a href="../index.php" class="btn-back">Powrót</a>
        <h2>Logowanie</h2>
        <form action="process_login.php" method="POST">
            <input type="text" name="login" placeholder="Login" required>
            <input type="password" name="password" placeholder="Hasło" required>
            <button type="submit">Zaloguj się</button>
        </form>
        <a href="register.php">Nie masz konta? Zarejestruj się</a>
    </div>
</body>
</html>
