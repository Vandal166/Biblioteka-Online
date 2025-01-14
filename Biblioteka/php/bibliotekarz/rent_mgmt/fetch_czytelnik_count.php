<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

$query = "SELECT COUNT(*) AS count FROM czytelnik";
$result = $conn->query($query);
$row = $result->fetch_assoc();
echo json_encode(['count' => $row['count']]);
?>