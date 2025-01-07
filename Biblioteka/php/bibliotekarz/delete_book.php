<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}


if (isset($_GET['id'])) {
    $wydanie_id = intval($_GET['id']);
    
    try
    {
        $conn->begin_transaction();

        $query = "SELECT ksiazka.tytul 
                  FROM ksiazka 
                  JOIN wydanie ON ksiazka.ID = wydanie.ID_ksiazki 
                  WHERE wydanie.ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $wydanie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'Nie znaleziono książki!']);
            exit();
        }
        $row = $result->fetch_assoc();
        $tytul = $row['tytul'];
        $stmt->close();

        //select
        $query = "SELECT COUNT(*) as count FROM wydanie WHERE ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $wydanie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if($row['count'] !== 1) 
        {
            echo json_encode(['success' => false, 'error' => 'Nie udałoo się usunąć książki!']);
            exit();
        }

        $conn->query("DELETE FROM wydanie WHERE ID=$wydanie_id LIMIT 1");
        
        $conn->commit();
        
        $_SESSION['success_message'] = 'Usunięto książkę: ' . $tytul;
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
