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
        rezerwacja.ID AS rezerwacja_ID,
        rezerwacja.ID_wydania AS wydanie_ID,
        rezerwacja.ID_czytelnika AS czytelnik_ID,
        rezerwacja.data_rezerwacji,
        rezerwacja.czy_wydana,
        czytelnik.imie AS czytelnik_imie,
        czytelnik.nazwisko AS czytelnik_nazwisko,
        czytelnik.email AS czytelnik_email,
        czytelnik.telefon AS czytelnik_telefon,
        czytelnik.nr_karty AS czytelnik_nr_karty,
        ksiazka.tytul AS ksiazka_tytul,
        ksiazka.zdjecie AS ksiazka_zdjecie,
        autor.imie AS autor_imie,
        autor.nazwisko AS autor_nazwisko,
        gatunek.nazwa AS gatunek,
        wydawnictwo.nazwa AS wydawnictwo,
        wydanie.jezyk,
        wydanie.ilosc_stron
        FROM rezerwacja
        LEFT JOIN czytelnik ON rezerwacja.ID_czytelnika = czytelnik.ID
        LEFT JOIN wydanie ON rezerwacja.ID_wydania = wydanie.ID
        LEFT JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
        LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
        LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID
        LEFT JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
        LEFT JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
        LEFT JOIN wydawnictwo ON wydanie.ID_wydawnictwa = wydawnictwo.ID
        WHERE rezerwacja.ID = ?";
        
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Egzemplarz o ID: ' . $id . ' nie istnieje']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Brak ID egzemplarzu']);
}
?>