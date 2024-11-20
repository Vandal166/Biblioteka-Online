<?php

//TODO sprawdzic czy nr_karty istnieje

// main funkcja walidująca dane użytkownika i zwracajaca od razu gdy znajdzie błąd
function validate_user_data($email, $phone, $password, $confirm_password, $conn, $username) 
{
    // Sprawdzanie emaila
    $error = validate_email($email);
    if ($error) 
        return $error;

    // Sprawdzanie telefonu
    $error = validate_phone($phone);
    if ($error) 
        return $error;

    // Sprawdzanie hasła
    $error = validate_password($password);
    if ($error) 
        return $error;

    // Sprawdzanie potwierdzenia hasła
    $error = validate_password_confirmation($password, $confirm_password);
    if ($error) 
        return $error;

    // Sprawdzanie, czy użytkownik już istnieje
    $error = check_user_exists($conn, $email, $username);
    if ($error) 
        return $error;

    return null; // Brak błędów
}

// Funkcja walidująca poprawność adresu email
function validate_email($email) 
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
        return 'Podaj poprawny adres email!';
    }
    return null;
}

// Funkcja walidująca numer telefonu (9 cyfr)
function validate_phone($phone) 
{
    if (!preg_match('/^\d{9}$/', $phone)) 
    {
        return 'Numer telefonu musi zawierać 9 cyfr!';
    }
    return null;
}

// Funkcja walidująca długość hasła
function validate_password($password) 
{
    if (strlen($password) < 6) 
    {
        return 'Hasło musi mieć co najmniej 6 znaków!';
    }
    return null;
}

// Funkcja walidująca, czy hasła są takie same
function validate_password_confirmation($password, $confirm_password) 
{
    if ($password !== $confirm_password) // !== -> sprawdza wartość i typ
    {
        return 'Hasła nie są takie same!';
    }
    return null;
}

// Funkcja do sprawdzenia, czy użytkownik z danym emailem lub loginem już istnieje
function check_user_exists($conn, $email, $username) 
{
    $sql_check = "SELECT * FROM czytelnik WHERE email = ? OR login = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('ss', $email, $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) 
    {
        return 'Użytkownik o podanym emailu lub loginie już istnieje!';
    }
    return null;
}
?>
