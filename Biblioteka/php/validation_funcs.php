<?php

// main funkcja walidująca dane użytkownika i zwracajaca od razu gdy znajdzie błąd
function validate_user_data($first_name, $last_name, $phone, $email, $username, $password, $confirm_password, $conn) 
{
    // Sprawdzanie imienia
    $error = validate_name($first_name);
    if ($error) 
        return $error;

    // Sprawdzanie nazwiska
    $error = validate_name($last_name);
    if ($error) 
        return $error;
    
    // Sprawdzanie telefonu
    $error = validate_phone($phone, $conn);
    if ($error) 
        return $error;
    
    // Sprawdzanie emaila
    $error = validate_email($email);
    if ($error) 
        return $error;

    // Sprawdzanie, czy użytkownik już istnieje
    $error = check_user_exists($conn, $email, $username);
    if ($error) 
        return $error;
    
    // Sprawdzanie hasła
    $error = validate_password($password, $confirm_password);
    if ($error) 
        return $error;

    return null; // Brak błędów
}

//Funckja waliduajca poprawnosc imienia/nazwiska
function validate_name($name) 
{
    if (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/', $name))  // sprawdza czy imie/nazwisko zawiera tylko litery
    {
        return 'Imię i nazwisko mogą zawierać tylko litery!';
    }
    return null;
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

// Funkcja generująca unikalny numer karty
function check_card_number($conn)
{
    do 
    {
        $card_number = rand(1000000000, 9999999999); // losowanie 10 cyfrowego numeru karty
        $sql_check = "SELECT * FROM czytelnik WHERE nr_karty = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param('s', $card_number);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
    } 
    while ($result_check->num_rows > 0); // losowanie nr_karty dopóki nie znajdzie unikalnego

    return $card_number;
}


// Funkcja walidująca numer telefonu (9 cyfr)
function validate_phone($phone, $conn) 
{
    if (!preg_match('/^\d{9}$/', $phone)) 
    {
        return 'Numer telefonu musi zawierać 9 cyfr!';
    }

    $sql_check = "SELECT * FROM czytelnik WHERE telefon = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('s', $phone);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if($result_check->num_rows > 0) // sprawdzanie, czy numer telefonu juz istnieje
    {
        return 'Użytkownik o podanym numerze telefonu już istnieje!';
    }

    return null;
}

// Funkcja walidująca długość hasła
function validate_password($password, $confirm_password) 
{
    if (strlen($password) < 6) 
    {
        return 'Hasło musi mieć co najmniej 6 znaków!';
    }

    if(!validate_password_confirmation($password, $confirm_password))
    {
        return 'Hasła nie są takie same!'; 
    }
    return null;
}

// Funkcja walidująca, czy hasła są takie same
function validate_password_confirmation($password, $confirm_password) : bool 
{
    return $password === $confirm_password; // === -> sprawdza wartość i typ
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
