<?php
session_start();
require_once('helpers.php');
redirect_if_logged_in();
require_once('db_connection.php');

require_once('validation_funcs.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{ 
    $login = htmlspecialchars(trim($_POST['login'])); //lub email
    remember_form_data(); // zapamietanie danych z formularza

    $sql = "SELECT ID AS user_id, 'czytelnik' AS user_type, imie AS user_name, email FROM czytelnik WHERE login = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if($result->num_rows == 0)
    {
        // Check in 'pracownik' table
        $sql = "SELECT ID AS user_id, 'pracownik' AS user_type, imie AS user_name, email FROM pracownik WHERE login = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }

    if($result->num_rows == 0)
    {
        set_message('error', 'reset_password', 'Nie znaleziono podanego użytkownika');
        header("Location: reset_password.php");
        exit();
    }

    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];
    $user_type = $user['user_type'];
    $user_name = $user['user_name'];
    $user_email = $user['email'];

    // Validate email address
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        set_message('error', 'reset_password', 'Nieprawidłowy adres email.');
        header("Location: reset_password.php");
        exit();
    }
    
    $token = bin2hex(random_bytes(32)); // token do resetowania hasła
    $sql = "INSERT INTO reset_hasla (ID_uzytkownika, poziom_uprawnien, token) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $user_id, $user_type, $token);
    $stmt->execute();
    $stmt->close();

    $link = "http://localhost/Biblioteka/php/new_password.php?token=$token&name=" . urlencode($user_name);
    
    $mail = new PHPMailer(true);
    try {
       
        $mail->isSMTP();
        $mail->Host = 'smtp.sendgrid.net'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'apikey'; 
        $mail->Password = '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';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('bibliotekaonline00@gmail.com', 'Biblioteka');
        $mail->addAddress($user_email, $user_name);

        $mail->isHTML(true);
        $mail->Subject = 'Resetowanie hasla';
        $mail->Body    = "Cześć $user_name,<br><br>Kliknij w poniższy link, aby zresetować swoje hasło:<br><a href='$link'>$link</a><br><br>Jeśli nie prosiłeś o zresetowanie hasła, zignoruj tę wiadomość.";
        $mail->AltBody = "Cześć $user_name,\n\nKliknij w poniższy link, aby zresetować swoje hasło:\n$link\n\nJeśli nie prosiłeś o zresetowanie hasła, zignoruj tę wiadomość.";

        $mail->send();
        set_message('success', 'reset_password', 'Link do resetowania hasła został wysłany na Twój adres email.');
    } catch (Exception $e) {
        set_message('error', 'reset_password', "Wystąpił problem z wysłaniem emaila. Mailer Error: {$mail->ErrorInfo}");
    }

    header("Location: reset_password.php");
    exit();
}