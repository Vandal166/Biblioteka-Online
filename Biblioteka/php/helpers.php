<?php
// Funkcja zapisująca dane formularza w sesji
function remember_form_data() 
{
    $_SESSION['form_data'] = $_POST;
}

// Funkcja przywracająca dane formularza w inputach
function get_form_value($field_name) 
{
    return isset($_SESSION['form_data'][$field_name]) ? $_SESSION['form_data'][$field_name] : '';
}

function redirect_if_logged_in() 
{
    if (isset($_SESSION['user_id'])) 
    {
        header("Location: ../index.php");
        exit();
    }
}
?>
