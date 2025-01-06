<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $ksiazka_id = intval($data['ksiazka_id']);
    $tytul = $data['tytul'];
    $autor_id = intval($data['autor_id']);
    $imie = $data['autor_imie'];
    $nazwisko = $data['autor_nazwisko'];
    $gatunek_id = intval($data['gatunek_id']);
    $wydawnictwo_id = intval($data['wydawnictwo_id']);
    $ISBN = $data['ISBN'];
    $data_wydania = $data['data_wydania'];
    $jezyk = $data['jezyk'];
    $ilosc_stron = $data['ilosc_stron'];
    $czy_elektronicznie = intval($data['czy_elektronicznie']);
    $stan = $data['stan'];
    $czy_dostepny = intval($data['czy_dostepny']);

    $conn->begin_transaction();
    try {
        $conn->query("UPDATE ksiazka SET tytul='$tytul' WHERE ID=$ksiazka_id");
        $conn->query("UPDATE autor SET imie='$imie', nazwisko='$nazwisko' WHERE ID=$autor_id");
        $conn->query("UPDATE gatunek SET nazwa='$gatunek' WHERE ID=$gatunek_id");
        $conn->query("UPDATE wydanie SET ISBN='$ISBN', data_wydania='$data_wydania', jezyk='$jezyk', ilosc_stron='$ilosc_stron', czy_elektronicznie=$czy_elektronicznie WHERE ID=$wydawnictwo_id");
        $conn->query("UPDATE egzemplarz SET stan='$stan', czy_dostepny=$czy_dostepny WHERE ID=$egzemplarz_id");

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Brak danych wejściowych']);
}
?>
