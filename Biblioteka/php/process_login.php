<?php
session_start();

require_once('helpers.php');
redirect_if_logged_in();

require_once('db_connection.php'); 


if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $login = htmlspecialchars(trim($_POST['login']));
    remember_form_data();
    $password = trim($_POST['password']);
    $user = null;
    
    
    $sql = "SELECT * FROM czytelnik WHERE login = ? OR email = ?";
    $stmt = $conn->prepare($sql); 
    
    if ($stmt) 
    {        
        $stmt->bind_param('ss', $login, $login);
        $stmt->execute(); 
        $result = $stmt->get_result(); // pobranie wyników
        
        if ($result->num_rows > 0) 
        {
            // znaleziono użytkowników o podanym loginie/emailu
            $user = $result->fetch_assoc();
            $role = 'czytelnik';
        }
        
        $stmt->close();
    }

    // Jeśli użytkownik nie został znaleziony w tabeli 'czytelnik', sprawdzamy tabelę 'pracownicy'
    if (!$user) 
    {
        $sql_pracownik = "SELECT * FROM pracownik WHERE login = ? OR email = ?";
        $stmt = $conn->prepare($sql_pracownik);

        if ($stmt) 
        {
            $stmt->bind_param('ss', $login, $login);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) 
            {
                $user = $result->fetch_assoc();
                $role = 'pracownik';
            }
            $stmt->close();
        }
    }

    // Jeśli użytkownik został znaleziony w jednej z tabel
    if ($user) 
    {
        if (password_verify($password, $user['haslo'])) 
        {
            // Logowanie poprawne
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['role'] = $role; //Przechowanie informacji, czy to 'czytelnik', czy pracownik 
            $_SESSION['poziom_uprawnien'] = $user['poziom_uprawnien']; //('bibliotekarz', lub 'administrator')

            // Przekierowanie na strone glowna
            header("Location: \Biblioteka\index.php");

            exit();
        } 
        else 
        {
            // Błędne hasło            
            set_message('error', 'login', 'Nie udało się zalogować! Błędne dane.');
            header("Location: login.php");
            exit();
        }
    } 
    else 
    {
        // Brak użytkownika w bazie danych       
        set_message('error', 'login', 'Nie udało się zalogować! Użytkownik nie istnieje.');
        header("Location: login.php");
        exit();
    }

    $conn->close();
}
?>
