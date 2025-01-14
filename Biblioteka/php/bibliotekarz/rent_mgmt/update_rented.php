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
    
    $wypozyczenie_ID = $data['wypozyczenie_ID'];
    $termin_zwrotu = $data['termin_zwrotu'];
    $data_oddania = $data['data_oddania'];
    
    // walidajca
    $params = [
        'value' => $termin_zwrotu
    ];
    $error = validate_date($params);
    if ($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }

    $conn->begin_transaction();
    try {

        //spr czy istnieje wyp o podanym ID
        $stmt = $conn->prepare("SELECT * FROM wypozyczenie WHERE ID = ?");
        $stmt->bind_param("i", $wypozyczenie_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Wypożyczenie o podanym ID nie istnieje');
        }
        
        // Aktualizacja wypozyczenia
        if (empty($data_oddania)) 
            $data_oddania = null;   
        
        $stmt = $conn->prepare("UPDATE wypozyczenie SET termin_oddania = ?, data_oddania = ? WHERE ID = ?");
        $stmt->bind_param("ssi", $termin_zwrotu, $data_oddania, $wypozyczenie_ID);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success_message'] = 'Wypożyczenie zostało pomyślnie zaktualizowane!';
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
