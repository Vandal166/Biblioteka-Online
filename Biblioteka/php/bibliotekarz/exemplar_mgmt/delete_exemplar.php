<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}


if (isset($_GET['id'])) {
    $egzemplarzID = intval($_GET['id']);
    
    try
    {
        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT ID, ID_wydania, stan FROM egzemplarz WHERE ID = ?");
        $stmt->bind_param("i", $egzemplarzID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            throw new Exception('Nie znaleziono egzemplarza o podanym ID');
        }
        $row = $result->fetch_assoc();
        $stan = $row['stan'];
        $wydanieID = $row['ID_wydania'];
        $stmt->close();

        // zapisywanie tytul ksiazki
        $stmt = $conn->prepare("SELECT tytul FROM ksiazka WHERE ID = (SELECT ID_ksiazki FROM wydanie WHERE ID = ?)");
        $stmt->bind_param("i", $wydanieID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $tytul = $row['tytul'];
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM egzemplarz WHERE ID = ? LIMIT 1");
        $stmt->bind_param("i", $egzemplarzID);
        $stmt->execute();
        $stmt->close();
        
        
        $conn->commit();
        // spr czy $stan != null
        $_SESSION['success_message'] = 'Usunięto egzemplarz: ' . $tytul . ($stan != null ? ' ' . $stan : '');
        echo json_encode(['success' => true]);
    } 
    catch (Exception $e) 
    {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Nie udało się usunąć książki!']);
    }
}
else
{
    echo json_encode(['success' => false, 'error' => 'Brak danych!']);
}
?>
