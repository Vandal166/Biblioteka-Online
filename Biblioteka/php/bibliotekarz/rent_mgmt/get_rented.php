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
        wypozyczenie.ID AS wypozyczenie_ID,
        wypozyczenie.data_wypozyczenia,
        wypozyczenie.termin_oddania,
        wypozyczenie.data_oddania,
        czytelnik.ID AS czytelnik_ID,
        czytelnik.imie AS czytelnik_imie,
        czytelnik.nazwisko AS czytelnik_nazwisko,
        czytelnik.email AS czytelnik_email,
        czytelnik.nr_karty AS czytelnik_nr_karty,
        egzemplarz.ID AS egzemplarz_ID,
        egzemplarz.czy_dostepny,
        egzemplarz.stan,
        pracownik.ID AS pracownik_ID,
        pracownik.imie AS pracownik_imie,
        pracownik.nazwisko AS pracownik_nazwisko,
        wydanie.ID AS wydanie_ID,
        wydanie.ISBN AS wydanie_ISBN,
        wydanie.numer_wydania AS wydanie_nr_wydania,
        wydanie.jezyk AS wydanie_jezyk,
        wydanie.ilosc_stron AS wydanie_ilosc_stron,
        wydanie.data_wydania AS wydanie_data_wydania,
        ksiazka.tytul AS ksiazka_tytul,
        ksiazka.zdjecie AS ksiazka_zdjecie,
        wydawnictwo.nazwa AS wydawnictwo,
        autor.imie AS autor_imie,
        autor.nazwisko AS autor_nazwisko,
        gatunek.nazwa AS gatunek
    FROM 
        wypozyczenie
        JOIN egzemplarz ON wypozyczenie.ID_egzemplarza = egzemplarz.ID
        JOIN czytelnik ON wypozyczenie.ID_czytelnika = czytelnik.ID
        JOIN pracownik ON wypozyczenie.ID_pracownika = pracownik.ID
        JOIN wydanie ON egzemplarz.ID_wydania = wydanie.ID
        JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
        JOIN wydawnictwo ON wydanie.ID_wydawnictwa = wydawnictwo.ID
        JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
        JOIN autor ON autor_ksiazki.ID_autora = autor.ID
        JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
        JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
    WHERE wypozyczenie.ID = ?";
        
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Wypożyczenie o ID: ' . $id . ' nie istnieje']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Brak ID wypożyczenia']);
}
?>