<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/validation_funcs.php');
require_once(BASE_PATH . 'php/helpers.php');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    
    $czytelnik_ID = intval($data['czytelnik_ID']);
    $czytelnik_imie = ucfirst(htmlspecialchars(trim($data['czytelnik_imie'])));
    $czytelnik_nazwisko = ucfirst(htmlspecialchars(trim($data['czytelnik_nazwisko'])));
    $czytelnik_nr_karty = htmlspecialchars(trim($data['czytelnik_nr_karty']));
    $czytelnik_email = htmlspecialchars(trim($data['czytelnik_email']));
    $czytelnik_telefon = htmlspecialchars(trim($data['czytelnik_telefon']));
    
    // walidajca
    $error = validate_user_data([
        'name' => $czytelnik_imie,
        'surname' => $czytelnik_nazwisko,
        'email' => $czytelnik_email,
        'telefon' => $czytelnik_telefon
    ]);
    if ($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }

    $check_phone = check_if_exists([
        'conn' => $conn, 
        'table' => 'czytelnik', 
        'column' => 'telefon', 
        'value' => $czytelnik_telefon, 
        'owning_ID' => $czytelnik_ID,
        'no_log' => false
    ]);
    if ($check_phone) {
        echo json_encode(['success' => false, 'error' => 'Podany telefon jest już zajęty!']);
        exit();
    }
    $check_email = check_if_exists([
        'conn' => $conn, 
        'table' => 'czytelnik', 
        'column' => 'email', 
        'value' => $czytelnik_email, 
        'owning_ID' => $czytelnik_ID,
        'no_log' => false
    ]);
    if ($check_email) {
        echo json_encode(['success' => false, 'error' => 'Podany email jest już zajęty!']);
        exit();
    }

    $conn->begin_transaction();
    try {
        
        $stmt = $conn->prepare('UPDATE czytelnik SET imie = ?, nazwisko = ?, nr_karty = ?, email = ?, telefon = ? WHERE ID = ?');
        $stmt->bind_param('sssssi', $czytelnik_imie, $czytelnik_nazwisko, $czytelnik_nr_karty, $czytelnik_email, $czytelnik_telefon, $czytelnik_ID);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $_SESSION['success_message'] = 'Czytelnik został pomyślnie zaktualizowany!';
        echo json_encode(['success' => true]);
    } 
    catch (Exception $e)
    {
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    }
} 
else {
    echo json_encode(['error' => 'Brak danych wejściowych']);
}
?>
