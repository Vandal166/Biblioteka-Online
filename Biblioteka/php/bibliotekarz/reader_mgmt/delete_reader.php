<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}


if (isset($_GET['id'])) 
{
    $czytelnik_ID = intval($_GET['id']);
    
    try
    {
        $conn->begin_transaction();

        //spr czy czytelnik istnieje
        $stmt = $conn->prepare('SELECT imie, nazwisko FROM czytelnik WHERE ID = ?');
        $stmt->bind_param('i', $czytelnik_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) 
        {
            echo json_encode(['success' => false, 'error' => 'Nie znaleziono czytelnika!']);
            exit();
        }

        $czytelnik = $result->fetch_assoc();
        $czytelnik_imie = $czytelnik['imie'];
        $czytelnik_nazwisko = $czytelnik['nazwisko'];

        $stmt = $conn->prepare('DELETE FROM czytelnik WHERE ID = ? LIMIT 1');
        $stmt->bind_param('i', $czytelnik_ID);
        $stmt->execute();
        
        $conn->commit();
        
        $_SESSION['success_message'] = 'Usunięto czytelnika: ' . $czytelnik_imie . ' ' . $czytelnik_nazwisko;
        echo json_encode(['success' => true]);
    } 
    catch (Exception $e) 
    {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Nie udało się usunąć czytelnika!']);
    }
}
else
{
    echo json_encode(['success' => false, 'error' => 'Brak danych!']);
}
?>
