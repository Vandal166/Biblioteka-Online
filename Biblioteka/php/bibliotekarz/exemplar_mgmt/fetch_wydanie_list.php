<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

$query = "
    SELECT 
        wydanie.numer_wydania, 
        ksiazka.tytul, 
        autor.imie AS autor_imie, 
        autor.nazwisko AS autor_nazwisko
    FROM wydanie
    LEFT JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
    LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
    LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID
";
$result = $conn->query($query);
$wydanieList = [];
while ($row = $result->fetch_assoc()) {
    $wydanieList[] = $row;
}
echo json_encode($wydanieList);
?>