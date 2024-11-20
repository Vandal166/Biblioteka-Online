<?php

session_start();

if(isset($_SESSION['user_id']))  //TODO jeśli użytkownik jest zalogowany to do strony głównej
{
    header("Location: ../index.php");
    exit();
}
require_once('db_connection.php'); 


if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    
    $login = htmlspecialchars(trim($_POST['login']));
    $password = trim($_POST['password']);
    
    
    $sql = "SELECT * FROM czytelnik WHERE login = ?";
    $stmt = $conn->prepare($sql); 
    
    if ($stmt) 
    {
        
        $stmt->bind_param('s', $login);
        $stmt->execute(); 
        $result = $stmt->get_result(); // pobranie wyników
        
        if ($result->num_rows > 0) 
        {
            // znaleziono użytkowników o podanym loginie
            $user = $result->fetch_assoc();
            
            
            if (password_verify($password, $user['haslo'])) 
            {
                // OK logujemy
                            
                session_start();
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['login'] = $user['login'];
                
                
                header("Location: ../index.php");
                exit();
            } 
            else 
            {
                // błędne hasło
                $_SESSION['error'] = 'Nie udało się zalogować!';
                header("Location: login.php"); // przekierowanie do login.php
                exit();
            }
        } else 
        {
            // brak użytkownika w bazie danych
            $_SESSION['error'] = 'Nie udało się zalogować!';
            header("Location: login.php"); // przekierowanie do login.php
            exit();
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
?>
