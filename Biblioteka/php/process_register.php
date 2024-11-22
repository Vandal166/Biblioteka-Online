<?php

session_start();

require_once('helpers.php');
redirect_if_logged_in();

require_once('db_connection.php');
require_once('validation_funcs.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{ 
    // htmlspecialchars - zapobiega atakom XSS
    // np. <script>alert('Hello');</script>
    // -> &lt;script&gt;alert('Hello');&lt;/script&gt; (jako zwykly tekst zamist kodu)
    
    $first_name = ucfirst(strtolower(htmlspecialchars(trim($_POST['first_name'])))); // ucfirst - pierwsza litera duza, reszta mala
    $last_name = ucfirst(strtolower(htmlspecialchars(trim($_POST['last_name']))));  
    $phone = htmlspecialchars(trim($_POST['phone'])); 
    $email = htmlspecialchars(trim($_POST['email']));
    $username = htmlspecialchars(trim($_POST['username']));
    
    remember_form_data(); // zapamietanie danych z formularza

    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);


    // WALIDACJA DANYCH
    $error = validate_user_data($first_name, $last_name, $phone, $email, $username, $password, $confirm_password, $conn);

    if ($error) 
    {
        $_SESSION['error'] = $error;
        header("Location: register.php");
        exit();
    }
    // KONIEC WALIDACJI


    
    // Haszowanie hasła bcrypt
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT); 

    $card_number = check_card_number($conn);
    // Przygotowanie zapytania SQL
    $sql = "INSERT INTO czytelnik (imie, nazwisko, nr_karty, telefon, email, login, haslo) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // zapobieganie SQL Injection poprzez prepare statement
    $stmt = $conn->prepare($sql); // statement

    if($stmt) 
    {
        
        // Przypisanie parametrów do zapytania gdzie 's' to string, 'i' to integer etc
        $stmt->bind_param('sssssss', $first_name, $last_name, $card_number, $phone, $email, $username, $hashed_pass);

        if($stmt->execute()) 
        {
            $_SESSION['success'] = 'Rejestracja zakończona sukcesem! Zaloguj się, aby kontynuować.';
            unset($_SESSION['form_data']); // usuwanie danych z formularza po udanej rejestracji
            header("Location: login.php");
            exit();
        } 
        else 
        {
            $_SESSION['error'] = 'Błąd podczas rejestracji. Spróbuj ponownie.';
            header("Location: register.php");
            exit();
        }

        $stmt->close();
    } 
    else 
    {
        $_SESSION['error'] = 'Wystąpił nieoczekiwany błąd. Spróbuj ponownie.';
        header("Location: register.php");
        exit();
    }

    $conn->close();
}
