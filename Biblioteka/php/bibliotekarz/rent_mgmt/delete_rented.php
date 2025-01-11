<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}


if (isset($_GET['id'])) {
    $wypozyczenieID = intval($_GET['id']);
    
    try
    {
        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT * FROM wypozyczenie WHERE ID = ?");
        $stmt->bind_param("i", $wypozyczenieID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Wypożyczenie o podanym ID nie istnieje');
        }

        // zapisywanie tytul ksiazki i imie/nazwisko czytelnika wypożyczającego
        $stmt = $conn->prepare("SELECT 
            ksiazka.tytul, 
            czytelnik.imie AS czytelnik_imie, 
            czytelnik.nazwisko AS czytelnik_nazwisko
            FROM wypozyczenie
            LEFT JOIN egzemplarz ON wypozyczenie.ID_egzemplarza = egzemplarz.ID
            LEFT JOIN wydanie ON egzemplarz.ID_wydania = wydanie.ID
            LEFT JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
            LEFT JOIN czytelnik ON wypozyczenie.ID_czytelnika = czytelnik.ID
            WHERE wypozyczenie.ID = ?");

        $stmt->bind_param("i", $wypozyczenieID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $tytul = $row['tytul'];
        $czyt_imie = $row['czytelnik_imie'];
        $czyt_nazwisko = $row['czytelnik_nazwisko'];

        $stmt = $conn->prepare("DELETE FROM wypozyczenie WHERE ID = ? LIMIT 1");
        $stmt->bind_param("i", $wypozyczenieID);
        $stmt->execute();

        
        $conn->commit();
        $_SESSION['success_message'] = "Wypożyczenie książki {$tytul} przez czytelnika {$czyt_imie} {$czyt_nazwisko} zostało pomyślnie usunięte.";
        echo json_encode(['success' => true]);
    } 
    catch (Exception $e) 
    {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Nie udało się usunąć wypożyczenia!']);
    }
}
else
{
    echo json_encode(['success' => false, 'error' => 'Brak danych!']);
}
?>
