<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

$query = "SELECT
    czytelnik.imie AS czytelnik_imie,
    czytelnik.nazwisko AS czytelnik_nazwisko,
    czytelnik.nr_karty AS czytelnik_nr_karty
    FROM czytelnik";

$result = $conn->query($query);
$wypozyczenieList = [];
while ($row = $result->fetch_assoc()) {
    $wypozyczenieList[] = $row;
}
echo json_encode($wypozyczenieList);
?>