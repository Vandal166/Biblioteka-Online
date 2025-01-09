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
    $wydanie_id = intval($data['wydanie_ID']);
    $gatunek_id = intval($data['gatunek_ID']);
    
    $stan = htmlspecialchars(trim($data['egzemplarz_stan']));
    $numer_wydania = htmlspecialchars(trim($data['wydanie_numer_wydania']));
    $czy_dostepny = isset($data['egzemplarz_czy_dostepny']) && $data['egzemplarz_czy_dostepny'] == 1 ? 1 : 0;


    // walidajca
    $error = validate_book_data([        
        'edition_number' => $numer_wydania
    ]);
    
    if ($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }
    
    $conn->begin_transaction();
    try {

        //spr czy istnieje wydanie o podanym ID
        $stmt = $conn->prepare("SELECT ID FROM wydanie WHERE ID = ?");
        $stmt->bind_param("i", $wydanie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Wydanie o podanym ID nie istnieje');
        }

        //TODO: check manage_exemplar.php line 206
        // Aktualizacja numeru wydania
        $stmt = $conn->prepare("
            UPDATE wydanie
            SET numer_wydania = ?
            WHERE ID = ?
        ");
        $stmt->bind_param("si", $numer_wydania, $wydanie_id);
        $stmt->execute();


        // Aktualizacja egzemplarza
        $stmt = $conn->prepare("
            UPDATE egzemplarz
            SET czy_dostepny = ?, stan = ?
            WHERE ID_wydania = ?
        ");
        $stmt->bind_param("isi", $czy_dostepny, $stan, $wydanie_id);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success_message'] = 'Dane egzemplarza zostały pomyślnie zaktualizowane!';
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
