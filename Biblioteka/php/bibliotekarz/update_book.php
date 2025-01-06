<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) 
{
    $id = intval($_POST['id']);
    $tytul = trim(htmlspecialchars($_POST['tytul']));
    $imie = trim(htmlspecialchars($_POST['imie']));
    $nazwisko = trim(htmlspecialchars($_POST['nazwisko']));

    $error = validate_book_data([
        'title' => $tytul,     
        'author_name' => $imie,
        'author_surname' => $nazwisko
    ]);

    if ($error) {
        echo json_encode(['error' => $error]);
        exit();
    }

    $query = "UPDATE ksiazka SET tytul = ?, imie = ?, nazwisko = ? WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $tytul, $imie, $nazwisko, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Błąd podczas aktualizacji']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Nieprawidłowe żądanie']);
}
?>
