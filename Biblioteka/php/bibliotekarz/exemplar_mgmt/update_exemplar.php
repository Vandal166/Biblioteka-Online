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
    
    $egzemplarz_ID = intval($data['egzemplarz_ID']);
    $stan = htmlspecialchars(trim($data['stan']));
    $numer_wydania = htmlspecialchars(trim($data['wydanie_numer_wydania']));
    $czy_dostepny = isset($data['egzemplarz_czy_dostepny']) && $data['egzemplarz_czy_dostepny'] == 1 ? 1 : 0;


    
    $conn->begin_transaction();
    try {

        //spr czy istnieje wydanie o podanym ID
        $stmt = $conn->prepare("SELECT ID_wydania FROM egzemplarz WHERE ID = ?");
        $stmt->bind_param("i", $egzemplarz_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Egzemplarz o podanym ID nie istnieje');
        }
        $row = $result->fetch_assoc();
        $wydanie_ID = $row['ID_wydania'];
        

        // bierzemy ID wydania na podstawie numeru wydania
        $stmt = $conn->prepare("SELECT ID FROM wydanie WHERE numer_wydania = ?");
        $stmt->bind_param("s", $numer_wydania);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Wydanie o podanym numerze nie istnieje');
        }
        $row = $result->fetch_assoc();
        $wydanie_ID = $row['ID'];
        
        // Aktualizacja egzemplarza
        $stmt = $conn->prepare("
            UPDATE egzemplarz
            SET ID_wydania = ?, czy_dostepny = ?, stan = ?
            WHERE ID = ?
        ");
        $stmt->bind_param("iisi", $wydanie_ID, $czy_dostepny, $stan, $egzemplarz_ID);
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
