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
    $rezerwacja_ID = intval($_GET['id']);

    try
    {
        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT * FROM rezerwacja WHERE ID = ?");
        $stmt->bind_param("i", $rezerwacja_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0)
            throw new Exception('Rezerwacja o podanym ID nie istnieje');
        

        // zapisywanie tytul ksiazki i imie/nazwisko czytelnika rezrującego
        $stmt = $conn->prepare("SELECT 
            ksiazka.tytul,
            czytelnik.imie AS czytelnik_imie,
            czytelnik.nazwisko AS czytelnik_nazwisko
            FROM rezerwacja
            JOIN wydanie ON rezerwacja.ID_wydania = wydanie.ID
            JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
            JOIN czytelnik ON rezerwacja.ID_czytelnika = czytelnik.ID
            WHERE rezerwacja.ID = ?");

        $stmt->bind_param("i", $rezerwacja_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $tytul = $row['tytul'];
        $czyt_imie = $row['czytelnik_imie'];
        $czyt_nazwisko = $row['czytelnik_nazwisko'];
        

        $stmt = $conn->prepare("DELETE FROM rezerwacja WHERE ID = ? LIMIT 1");
        $stmt->bind_param("i", $rezerwacja_ID);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['success_message'] = "Rezerwacja książki {$tytul} przez czytelnika {$czyt_imie} {$czyt_nazwisko} zostało pomyślnie usunięte.";
        echo json_encode(['success' => true]);
    } 
    catch (Exception $e) 
    {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
else
{
    echo json_encode(['success' => false, 'error' => 'Brak danych!']);
}
?>
