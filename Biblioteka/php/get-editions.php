<?php
session_start();
$conn = new mysqli("localhost", "root", "", "biblioteka");

$bookID = intval($_GET['bookID']);
$userID = $_SESSION['user_id'] ?? null;
$poziomUprawnien = $_SESSION['poziom_uprawnien'] ?? null; // Domyślnie null dla czytelników

$sql = "
    SELECT 
        wydanie.id,
        wydanie.numer_wydania,
        wydanie.jezyk,
        wydanie.data_wydania,
        wydanie.ilosc_stron,
        wydanie.pdf,
        wydawnictwo.nazwa,
        (SELECT id FROM rezerwacja WHERE rezerwacja.ID_wydania = wydanie.id AND rezerwacja.ID_czytelnika = ?) AS reservation_id
    FROM wydanie
    JOIN wydawnictwo ON wydanie.ID_wydawnictwa = wydawnictwo.id
    WHERE wydanie.ID_ksiazki = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userID, $bookID);
$stmt->execute();
$result = $stmt->get_result();

$baseUrl = 'http://localhost/';
$editions = [];
while ($row = $result->fetch_assoc()) {
    if (!empty($row['pdf'])) {
        $row['pdf'] = $baseUrl . ltrim($row['pdf'], '/');
    }
    $row['reserved'] = $row['reservation_id'] !== null;
    $editions[] = $row;
}

header('Content-Type: application/json');
echo json_encode([
    'editions' => $editions,
    'isLoggedIn' => $userID !== null, // Informacja o zalogowaniu użytkownika
    'poziomUprawnien' => $poziomUprawnien // Poziom uprawnień użytkownika
]);
?>
