<?php
// Połączenie z bazą danych
$conn = new mysqli("localhost", "root", "", "biblioteka");

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Parametry paginacji
$itemsPerPage = 12; // Ilość książek na stronę (3x4)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $itemsPerPage;

// Parametr wyszukiwania
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// URL bazowy dla obrazów
$baseUrl = 'http://localhost/';

// Pobranie danych książek
$sql = "
    SELECT 
        ksiazka.id AS id_ksiazki, 
        ksiazka.tytul, 
        ksiazka.zdjecie, 
        IFNULL(GROUP_CONCAT(CONCAT(autor.imie, ' ', autor.nazwisko) SEPARATOR ', '), 'Brak') AS autorzy
    FROM ksiazka
    LEFT JOIN autor_ksiazki ON ksiazka.id = autor_ksiazki.ID_ksiazki
    LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.id
    WHERE ksiazka.tytul LIKE '%$search%'
    GROUP BY ksiazka.id
    LIMIT $itemsPerPage OFFSET $offset
";

$result = $conn->query($sql);

// Pobranie liczby wszystkich książek
$totalBooks = $conn->query("SELECT COUNT(*) as total FROM ksiazka WHERE tytul LIKE '%$search%'")->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $itemsPerPage);

$books = [];
while ($row = $result->fetch_assoc()) 
{
    $row['zdjecie'] = !empty($row['zdjecie']) ? $baseUrl . $row['zdjecie'] : $baseUrl . 'Biblioteka/images/placeholder.jpg';
    $books[] = [
        'id' => $row['id_ksiazki'],
        'tytul' => $row['tytul'],
        'zdjecie' => $row['zdjecie'],
        'autorzy' => $row['autorzy'], // Lista autorów oddzielona przecinkami
    ];
}

// Zwracanie danych w formacie JSON
header('Content-Type: application/json');
echo json_encode([
    'books' => $books,
    'totalPages' => $totalPages,
]);

$conn->close();