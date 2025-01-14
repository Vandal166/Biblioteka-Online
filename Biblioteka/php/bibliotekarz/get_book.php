<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) 
{
    $id = intval($_GET['id']);   

    $query = "SELECT
        wydanie.ID AS wydanie_ID,
        ksiazka.ID AS ksiazka_ID,
        ksiazka.tytul AS ksiazka_tytul,
        ksiazka.zdjecie AS ksiazka_zdjecie,
        autor.imie AS autor_imie,
        autor.nazwisko AS autor_nazwisko,
        gatunek.ID AS gatunek_ID,
        gatunek.nazwa AS gatunek,
        wydawnictwo.nazwa AS wydawnictwo,
        wydawnictwo.kraj AS wydawnictwo_kraj,
        wydanie.ISBN AS wydanie_ISBN,
        wydanie.data_wydania AS wydanie_data_wydania,
        wydanie.numer_wydania AS wydanie_numer_wydania,
        wydanie.jezyk AS wydanie_jezyk,
        wydanie.ilosc_stron,
        wydanie.pdf,
        egzemplarz.czy_dostepny,
        egzemplarz.stan
    FROM wydanie
    LEFT JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
    LEFT JOIN egzemplarz ON egzemplarz.ID_wydania = wydanie.ID
    LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
    LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID
    LEFT JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
    LEFT JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
    LEFT JOIN wydawnictwo ON wydanie.ID_wydawnictwa = wydawnictwo.ID
    WHERE wydanie.ID = ?
    LIMIT 1";
        
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Książka nie istnieje']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Brak ID książki']);
}
?>
