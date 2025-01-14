<?php
session_start(); // Uruchomienie sesji

$conn = new mysqli("localhost", "root", "", "biblioteka");
$data = json_decode(file_get_contents("php://input"), true);

$editionID = intval($data['editionID']);
$userID = $_SESSION['user_id'] ?? null; // Pobranie ID użytkownika z sesji

if ($userID) {
    // Dodanie rezerwacji do bazy danych
    $sql = "INSERT INTO rezerwacja (ID_wydania, ID_czytelnika, data_rezerwacji, czy_wydana) VALUES (?, ?, NOW(), 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $editionID, $userID);
    if ($stmt->execute()) {
        echo json_encode(["message" => "Rezerwacja dodana pomyślnie."]);
    } else {
        echo json_encode(["message" => "Błąd podczas rezerwacji."]);
    }
} else {
    // Brak zalogowanego użytkownika
    echo json_encode(["message" => "Użytkownik nie jest zalogowany."]);
}
?>
