<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

// sprawdzenie czy wymagane parametry są ustawione
if (isset($_GET['editionNumber'])) 
{
    $editionNumber = htmlspecialchars(trim($_GET['editionNumber']));
    
   $query = "SELECT 
            wydanie.ID, 
            wydanie.ID_ksiazki, 
            wydanie.ID_wydawnictwa, 
            wydanie.ISBN, 
            wydanie.data_wydania, 
            wydanie.numer_wydania, 
            wydanie.jezyk, 
            wydanie.ilosc_stron, 
            wydanie.pdf, 
            ksiazka.tytul, 
            ksiazka.zdjecie,
            autor.imie AS autor_imie, 
            autor.nazwisko AS autor_nazwisko, 
            gatunek.nazwa AS gatunek,
            wypozyczenie.ID AS wypozyczenie_ID,
            wypozyczenie.ID_czytelnika,
            czytelnik.imie AS czytelnik_imie,
            czytelnik.nazwisko AS czytelnik_nazwisko,
            czytelnik.nr_karty AS czytelnik_nr_karty,
            czytelnik.email AS czytelnik_email,
            egzemplarz.ID AS egzemplarz_ID
        FROM wydanie
        LEFT JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
        LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
        LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID
        LEFT JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
        LEFT JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
        LEFT JOIN egzemplarz ON wydanie.ID = egzemplarz.ID_wydania
        LEFT JOIN wypozyczenie ON wydanie.ID = egzemplarz.ID_wydania
        LEFT JOIN czytelnik ON wypozyczenie.ID_czytelnika = czytelnik.ID
        WHERE wydanie.numer_wydania = ? LIMIT 1
    ";
    // Przygotowanie zapytania
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $editionNumber);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 1) {
        $data = $result->fetch_assoc();
        
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nie znaleziono danych dla podanego nr wydania']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Błąd, jeśli brakuje parametrów
echo json_encode(['success' => false, 'message' => 'Brak wymaganych parametrów']);
exit();
?>
