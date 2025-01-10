<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) 
{
    $wydanie_numer_wydania = htmlspecialchars(trim($data['wydanie_numer_wydania']));
    $egzemplarz_stan = htmlspecialchars(trim($data['egzemplarz_stan']));
    $egzemplarz_czy_dostepny = isset($data['egzemplarz_czy_dostepny']) ? intval($data['egzemplarz_czy_dostepny']) : 0;

    // fetchowanie ID wydania na podstawie numeru wydania
    $query = "SELECT ID FROM wydanie WHERE numer_wydania = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $wydanie_numer_wydania);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) 
    {
        $wydanie = $result->fetch_assoc();
        $wydanie_id = $wydanie['ID'];

        $insertQuery = "INSERT INTO egzemplarz (ID_wydania, czy_dostepny, stan) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iis", $wydanie_id, $egzemplarz_czy_dostepny, $egzemplarz_stan);

        if ($insertStmt->execute()) {
            echo json_encode(['success' => true]);
            $_SESSION['success_message'] = 'Dodano egzemplarz!';
        } 
        else {
            echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania egzemplarza']);
        }
        $insertStmt->close();
    } 
    else 
    {
        echo json_encode(['success' => false, 'error' => 'Nie znaleziono wydania o podanym numerze']);
    }
    $stmt->close();
} 
else {
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych']);
}
$conn->close();
?>