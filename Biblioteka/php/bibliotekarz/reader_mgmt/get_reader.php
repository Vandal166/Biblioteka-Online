<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // First query to fetch the czytelnik data
    $query1 = "SELECT 
        ID AS czytelnik_ID,
        imie AS czytelnik_imie,
        nazwisko AS czytelnik_nazwisko,
        nr_karty AS czytelnik_nr_karty,
        email AS czytelnik_email,
        telefon AS czytelnik_telefon
    FROM 
        czytelnik
    WHERE ID = ?";
    
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    if ($result1->num_rows > 0) {
        $czytelnikData = $result1->fetch_assoc();
    } else {
        echo json_encode(['error' => 'Czytelnik o ID: ' . $id . ' nie istnieje']);
        exit();
    }
    $stmt1->close();

    // Second query to fetch the rest of the data
    $query2 = "SELECT 
        wypozyczenie.ID AS wypozyczenie_ID,
        wypozyczenie.data_wypozyczenia,
        wypozyczenie.termin_oddania,
        wypozyczenie.data_oddania,
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
        JOIN pracownik ON wypozyczenie.ID_pracownika = pracownik.ID
        JOIN wydanie ON egzemplarz.ID_wydania = wydanie.ID
        JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
        JOIN wydawnictwo ON wydanie.ID_wydawnictwa = wydawnictwo.ID
        JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
        JOIN autor ON autor_ksiazki.ID_autora = autor.ID
        JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
        JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
    WHERE wypozyczenie.ID_czytelnika = ?";
    
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $additionalData = [];
    while ($row = $result2->fetch_assoc()) {
        $additionalData[] = $row;
    }
    $stmt2->close();

    // Combine the data
    $response = [
        'czytelnik' => $czytelnikData,
        'additionalData' => $additionalData
    ];

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Brak ID czytelnika']);
}
?>