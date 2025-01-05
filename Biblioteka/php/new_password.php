<?php
session_start();

require_once('helpers.php');
redirect_if_logged_in();
require_once('db_connection.php');
require_once('validation_funcs.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $token = $_POST['token'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_name = $_POST['user_name'];
    
    $params = [
        'value' => $new_password,
        'confirm_password' => $confirm_password
    ];
    $error = validate_password($params);
    if ($error) 
    {
        set_message('error', 'new_password', $error);
        header("Location: new_password.php?token=$token&name=" . urlencode($user_name));
        exit();
    }

    $sql = "SELECT ID_czytelnik, ID_pracownik, poziom_uprawnien, data_wygenerowania FROM reset_hasla WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 0) 
    {
        set_message('error', 'new_password', 'Nieprawidłowy token.');
        header("Location: reset_password.php");
        exit();
    }

    $reset_data = $result->fetch_assoc();
    $user_id = $reset_data['ID_czytelnik'] ?? $reset_data['ID_pracownik'];
    $user_type = $reset_data['poziom_uprawnien'];
    $token_creation_time = strtotime($reset_data['data_wygenerowania']);
    $current_time = time();

    // sprawdzenie czy token wygasł
    if (($current_time - $token_creation_time) > 3600) // 1 godz
    {
        // wygasł usuniecie tokena z bazy
        $sql = "DELETE FROM reset_hasla WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->close();

        set_message('error', 'new_password', 'Token wygasł. Spróbuj ponownie.');
        header("Location: reset_password.php");
        exit();
    }

    // zmiana hasła
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    if (!empty($reset_data['ID_pracownik'])) 
    {
        $sql = "UPDATE pracownik SET haslo = ? WHERE ID = ?";
    } 
    elseif (!empty($reset_data['ID_czytelnik'])) 
    {
        $sql = "UPDATE czytelnik SET haslo = ? WHERE ID = ?";
    } 
    else 
    {
        set_message('error', 'new_password', 'Nieprawidłowy typ użytkownika.');
        header("Location: reset_password.php");
        exit();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $hashed_password, $user_id);
    $stmt->execute();
    $stmt->close();

    // po zmianie hasła usuwamy token z bazy
    $sql = "DELETE FROM reset_hasla WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->close();

    set_message('success', 'new_password', 'Hasło zostało zmienione pomyślnie.');
    header("Location: login.php");
    exit();
}

$token = $_GET['token'] ?? '';
$user_name = $_GET['name'] ?? '';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetowanie hasła</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    

    <div class="login-box">
        <h2>Resetowanie hasła dla <?php echo htmlspecialchars($user_name);?></h2>

        <?php display_messages('new_password'); ?>

        <form action="new_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>">
            <input type="password" id="new_password" name="new_password" placeholder="Nowe hasło" minlength="6" maxlength="255" required>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Potwierdź hasło" minlength="6" maxlength="255" required>
            <button type="submit">Zresetuj hasło</button>
        </form>
        <a href="login.php">Powrót</a>
</div>
    
</body>
</html>