<?php
session_start(); // Uruchomienie sesji

$conn = new mysqli("localhost", "root", "", "biblioteka");
$data = json_decode(file_get_contents("php://input"), true);

$reservationID = intval($data['reservationID']);
$userID = $_SESSION['user_id'] ?? null; // Pobranie ID użytkownika z sesji

if ($userID) {
    // Sprawdź, czy rezerwacja należy do zalogowanego użytkownika
    $sql = "SELECT ID FROM rezerwacja WHERE id = ? AND ID_czytelnika = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reservationID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Rezerwacja należy do użytkownika, można ją anulować
        $sqlDelete = "DELETE FROM rezerwacja WHERE id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $reservationID);
        if ($stmtDelete->execute()) {
            echo json_encode(["message" => "Rezerwacja anulowana pomyślnie."]);
        } else {
            echo json_encode(["message" => "Błąd podczas anulowania rezerwacji."]);
        }
    } else {
        // Rezerwacja nie należy do zalogowanego użytkownika
        echo json_encode(["message" => "Brak uprawnień do anulowania tej rezerwacji."]);
    }
} else {
    // Użytkownik nie jest zalogowany
    echo json_encode(["message" => "Użytkownik nie jest zalogowany."]);
}
?>
