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
?>
