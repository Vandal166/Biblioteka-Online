<?php
/**
 * Skrypt obsługujący żądanie edycji danych w panelu administracyjnym.
 * 
 * Wymaga aktywnej sesji oraz poziomu uprawnień 'administrator'.
 * 
 * Parametry:
 * - $_GET['editID'] (int) - ID rekordu do edycji.
 * - $_GET['queryType'] (string) - Typ zapytania, określający tabelę i kolumny do edycji. np. 'autor_edit', 'ksiazka_edit'.
 * 
 * Obsługiwane typy zapytań:
 * - wszystkie dla tabel w bazie danych(12)
 * 
 * Zwraca:
 * - JSON z kluczem 'success' (bool true) oraz 'data' (array) w przypadku sukcesu.
 * - JSON z kluczem 'success' (bool false) oraz 'message' (string) w przypadku błędu.
 * 
 */
session_start();
require_once('../../db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'administrator') {
    header("Location: ../../../index.php"); // Brak dostępu
    exit();
}

// sprawdzenie czy wymagane parametry są ustawione
if (isset($_GET['editID']) && is_numeric($_GET['editID']) && isset($_GET['queryType'])) 
{
    $ID = intval($_GET['editID']);
    $queryType = htmlspecialchars($_GET['queryType']);

    // Predefiniowane zapytania SQL
    $queries = [
        'autor_edit' => "SELECT imie, nazwisko FROM autor WHERE ID = ?",

        'ksiazka_edit' => "SELECT tytul, zdjecie FROM ksiazka WHERE ID = ?",

        'autor_ksiazki_edit' => "SELECT ID_autora, ID_ksiazki FROM autor_ksiazki WHERE ID = ?",

        'wydawnictwo_edit' => "SELECT nazwa, kraj FROM wydawnictwo WHERE ID = ?",

        'czytelnik_edit' => "SELECT imie, nazwisko, telefon, email, login FROM czytelnik WHERE ID = ?",

        'wypozyczenie_edit' => "SELECT ID_czytelnika, ID_egzemplarza, ID_pracownika, data_wypozyczenia, termin_oddania, data_oddania FROM wypozyczenie WHERE ID = ?",

        'egzemplarz_edit' => "SELECT ID_wydania, czy_dostepny, stan FROM egzemplarz WHERE ID = ?",

        'wydanie_edit' => "SELECT ID_ksiazki, ID_wydawnictwa, ISBN, data_wydania, numer_wydania, jezyk, ilosc_stron, czy_elektronicznie FROM wydanie WHERE ID = ?",

        'rezerwacja_edit' => "SELECT ID_wydania, ID_czytelnika, data_rezerwacji, czy_wydana FROM rezerwacja WHERE ID = ?",

        'pracownik_edit' => "SELECT imie, nazwisko, poziom_uprawnien, email, login FROM pracownik WHERE ID = ?",

        'gatunek_ksiazki_edit' => "SELECT ID_ksiazki, ID_gatunku FROM gatunek_ksiazki WHERE ID = ?",
    
        'gatunek_edit' => "SELECT nazwa FROM gatunek WHERE ID = ?"
    ];

    // sprawdzenie, czy podany typ zapytania istnieje
    if (!array_key_exists($queryType, $queries)) {
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowy typ zapytania']);
        exit();
    }

    // Przygotowanie zapytania
    $stmt = $conn->prepare($queries[$queryType]);
    $stmt->bind_param('i', $ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nie znaleziono danych dla podanego ID']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Błąd, jeśli brakuje parametrów
echo json_encode(['success' => false, 'message' => 'Brak wymaganych parametrów']);
exit();
?>
