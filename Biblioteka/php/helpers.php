<?php
// Funkcja zapisująca dane formularza w sesji
function remember_form_data() 
{
    $_SESSION['form_data'] = $_POST;
}
function clear_form_data() : void 
{
    $_SESSION['form_data'] = [];
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

// Funkcja ustawiająca komunikaty o błędach i sukcesach dla formularzy
// $type - error/success
// $form_type - add/delete/edit
function set_message($type, $form_type, $message) {
    $_SESSION['error'] = [];
    $_SESSION['success'] = [];
    $_SESSION['error'][$form_type] = null;
    $_SESSION['success'][$form_type] = null;
    
    $_SESSION[$type][$form_type] = htmlspecialchars($message);
}

// Funkcja wyświetlająca komunikaty o błędach i sukcesach gdy są ustawione
function display_messages($form_type) {
    if (isset($_SESSION['error'][$form_type])) {
        echo '<div class="error-message" style="color: red; margin-bottom: 10px;">';
        echo $_SESSION['error'][$form_type]; 
        echo '</div>';
        unset($_SESSION['error'][$form_type]); 
    }

    if (isset($_SESSION['success'][$form_type])) {
        echo '<div class="success-message" style="color: green; margin-bottom: 10px;">';
        echo $_SESSION['success'][$form_type];
        echo '</div>';
        unset($_SESSION['success'][$form_type]);
    }
}
?>
