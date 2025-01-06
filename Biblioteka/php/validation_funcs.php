<?php

// main funkcja walidująca dane użytkownika i zwracajaca od razu gdy znajdzie błąd
// uzycie: $userdata = ['name' => 'Jan', 'surname' => $nazwisko, 'telefon' => '123456789'] itd. mozna dodac wiecej/mniej danych
function validate_user_data($user_data) 
{
    $error = validate_name(['value' => $user_data['name']]);
    if ($error) 
        return $error;

    $error = validate_name(['value' => $user_data['surname']]);
    if ($error) 
        return $error;

    $error = validate_phone(['value' => $user_data['telefon']]);
    if ($error) 
        return $error;

    $error = validate_email(['value' => $user_data['email']]);
    if ($error) 
        return $error;

    $error = validate_login(['value' => $user_data['login']]);
    if ($error) 
        return $error;

    $error = validate_password(['value' => $user_data['password']]);
    if ($error) 
        return $error;

    return null; // Brak błędów
}

function check_user_data($user_data, $conn)
{
    $error = check_if_exists(['conn' => $conn, 'table' => 'czytelnik', 'column' => 'telefon', 'value' => $user_data['telefon'], 'no_log' => true]);
    if ($error) 
        return 'Podany numer telefonu jest już zajęty!';

    $error = check_if_exists(['conn' => $conn, 'table' => 'czytelnik', 'column' => 'login', 'value' => $user_data['login'], 'no_log' => true]);
    if ($error) 
        return 'Podany login jest już zajęty!';

    $error = check_if_exists(['conn' => $conn, 'table' => 'czytelnik', 'column' => 'email', 'value' => $user_data['email'], 'no_log' => true]);
    if ($error) 
        return 'Podany email jest już zajęty!';

    return null; // Brak błędów
}

//Funckja waliduajca poprawnosc imienia/nazwiska
function validate_name($params) 
{
    if (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/', $params['value'])) 
    {
        return 'Imię i nazwisko mogą zawierać tylko litery!';
    }
    return null;
}

function validate_email($params) 
{
    if (!filter_var($params['value'], FILTER_VALIDATE_EMAIL)) 
    {
        return 'Podaj poprawny adres email!';
    }
    return null;
}

function validate_login($params) 
{
    if (!preg_match('/^[a-zA-Z0-9]+$/', $params['value'])) 
    {
        return 'Login może zawierać tylko litery i cyfry!';
    }

    return null;
}

// Funkcja generująca unikalny numer karty, 
// nie trzeba tworzyc funckji osobnej do walidacji nr_karty bo to nie jest input od uzytkownika
function generate_card_number($conn)
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
function validate_phone($params) 
{
    if (!preg_match('/^\d{9}$/', $params['value'])) 
    {
        return 'Numer telefonu musi zawierać 9 cyfr!';
    }    

    return null;
}

// Funkcja walidująca długość hasła
function validate_password($params) 
{
    // Sprawdzenie długości hasła
    if (!preg_match('/^.{6,}$/', $params['value'])) 
    {
        return 'Hasło musi zawierać co najmniej 6 znaków!';
    }

    // Sprawdzenie potwierdzenia hasła (jeśli jest przekazane)
    if (isset($_POST['confirm_password']) && $params['value'] !== $_POST['confirm_password']) 
    {
        return 'Hasła nie są takie same!';
    }

    return null;
}
function validate_date($params) 
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $params['value'])) 
    {
        return 'Podaj poprawną datę!';
    }

    return null;
}

// Funkcja walidująca, czy hasła są takie same
// function validate_password_confirmation($password, $confirm_password) : bool 
// {
//     return $password === $confirm_password; // === -> sprawdza wartość i typ
// }


// Funkcja sprawdzająca, czy użytkownik o podanym telefonie/logine/emailu już istnieje w tabeli 'table'
// params - tablica z parametrami do walidacji np $params['table'] = 'pracownik', $params['column'] = 'login'
function check_if_exists($params) 
{
    if(!isset($params['table']) || !isset($params['column'])) 
        return 'Nieprawidłowe parametry funkcji check_if_exists!';
    
    $sql_check = "SELECT * FROM " . $params['table'] . " WHERE " . $params['column'] . " = ?";
    if (isset($params['owning_ID'])) 
    {
        $sql_check .= " AND ID != ?";
    }
    $stmt_check = $params['conn']->prepare($sql_check);
    if (isset($params['owning_ID'])) 
    {
        $stmt_check->bind_param('si', $params['value'], $params['owning_ID']);
    } 
    else 
    {
        $stmt_check->bind_param('s', $params['value']);
    }
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) 
    {
        if(!isset($params['no_log'])) // nie wyswietlamy gdy ustawiony jest 'no_log'
            return 'Rekord o wartości ' . $params['value'] . ' już istnieje w tabeli ' . $params['table'] . '!';
        
        return 'Błąd w formularzu!';
    }
    return null;
}

function check_ID_exists($params) 
{
    $sql_check = "SELECT * FROM " . $params['table'] . " WHERE ID = ?";
    $stmt_check = $params['conn']->prepare($sql_check);
    $stmt_check->bind_param('i', $params['ID']);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) 
    {
        return 'Rekord o podanym ID nie istnieje w tabeli ' . $params['table'] . '!';
    }
    return null;
}

function validate_book_data($params)
{
    $error = validate_book_title(['value' => $params['title']]);
    if ($error) 
        return $error;

    $error = validate_ISBN(['value' => $params['ISBN']]);
    if ($error) 
        return $error;

    $error = validate_edition_number(['value' => $params['edition_number']]);
    if ($error) 
        return $error;

    $error = validate_language(['value' => $params['language']]);
    if ($error) 
        return $error;

    $error = validate_page_count(['value' => $params['page_count']]);
    if ($error) 
        return $error;

    $error = validate_name(['value' => $params['author_name']]);
    if ($error) 
        return $error;

    $error = validate_name(['value' => $params['author_surname']]);
    if ($error) 
        return $error;

    return null; // Brak błędów
}

function validate_book_title($title) {
    
    if (empty($title)) {
        return "Tytuł książki nie może być pusty.";
    }

    if (strlen($title) > 255) {
        return "Tytuł książki nie może przekraczać 255 znaków.";
    }

    // pozwalaj na litery, cyfry, spacej, i niektore znaki spec
    if (!preg_match('/^[a-zA-Z0-9\s\p{L}\p{P}]+$/u', $title)) {
        return "Tytuł książki zawiera niedozwolone znaki.";
    }

    // null no error
    return null;
}

function validate_ISBN($params)
{
    if (!preg_match('/^\d{13}$/', $params['value']))
        return "ISBN musi składać się z 13 cyfr.";
    
    return null;
}

function validate_edition_number($params)
{
    if (!preg_match('/^\d{20}$/', $params['value']))
        return "Numer wydania musi składać się z 20 cyfr.";
    
    return null;
}
function validate_language($params)
{
    if (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/', $params['value']))
        return "Język musi składać się z liter.";
    
    return null;
}
function validate_page_count($params)
{
    // min. 1, max. 9999
    if (!preg_match('/^[1-9]\d{0,3}$/', $params['value']))
        return "Ilość stron musi składać się z 1-4 cyfr";
    
    return null;
}
?>
