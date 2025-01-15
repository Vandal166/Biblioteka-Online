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
    
    $rezerwacja_ID = intval($data['rezerwacja_ID']);
    $rezerwacja_data_rezerwacji = $data['rezerwacja_data_rezerwacji'];
    $rezerwacja_czy_wydana = isset($data['rezerwacja_czy_wydana']) ? intval($data['rezerwacja_czy_wydana']) : 0;
    
    // walidajca
    $error = validate_book_data([
        'release_date' => $rezerwacja_data_rezerwacji
    ]);
    if ($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }

    $conn->begin_transaction();
    try {

        //spr czy istnieje rezrw o podanym ID
        $stmt = $conn->prepare("SELECT * FROM rezerwacja WHERE ID = ?");
        $stmt->bind_param("i", $rezerwacja_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0)
            throw new Exception('Rezerwacja o podanym ID nie istnieje');
        
        
        
        $stmt = $conn->prepare("UPDATE rezerwacja SET data_rezerwacji = ?, czy_wydana = ? WHERE ID = ?");
        $stmt->bind_param("sii", $rezerwacja_data_rezerwacji, $rezerwacja_czy_wydana, $rezerwacja_ID);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success_message'] = 'Rezerwacja została pomyślnie zaktualizowana!';
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
