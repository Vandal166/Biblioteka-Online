<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

// sprawdzenie czy wymagane parametry są ustawione
if (isset($_GET['cardNumber'])) 
{
    $cardNumber = htmlspecialchars(trim($_GET['cardNumber']));
    
    $query = "SELECT 
            czytelnik.imie AS czytelnik_imie,
            czytelnik.nazwisko AS czytelnik_nazwisko,
            czytelnik.nr_karty AS czytelnik_nr_karty,
            czytelnik.email AS czytelnik_email
        FROM czytelnik
        WHERE czytelnik.nr_karty = ? LIMIT 1";
    
    // Przygotowanie zapytania
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $cardNumber);
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
