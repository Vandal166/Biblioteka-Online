<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    echo json_encode(['error' => 'Brak dostępu']);
    exit();
}
require_once(BASE_PATH . 'php/validation_funcs.php');

$card_number = generate_card_number($conn);
echo json_encode(['card_number' => $card_number]);
?>