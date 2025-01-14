<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/validation_funcs.php');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) 
{
    $czytelnik_nr_karty = $data['czytelnik_nr_karty'];
    $wydanie_numer_wydania = $data['wydanie_numer_wydania'];
    $pracownik_ID = $data['pracownik_ID'];
    $data_wypozyczenia = $data['data_wypozyczenia'];
    $termin_zwrotu = $data['termin_oddania'];

    // walidajca
    $error = validate_book_data([        
        'release_date' => $data_wypozyczenia,
        'release_date' => $termin_zwrotu 
    ]);

    if ($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }

    // fetchowanie ID egzemplarza  na podstawie numeru wydania z wydaie
    $query = "SELECT ID FROM egzemplarz WHERE ID_wydania = (SELECT ID FROM wydanie WHERE numer_wydania = ?) LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $wydanie_numer_wydania);
    $stmt->execute();
    $result_wydanie = $stmt->get_result();
    
    // fetchowanie ID czytelnika na podstawie numeru karty
    $query_czytelnik = "SELECT ID FROM czytelnik WHERE nr_karty = ? LIMIT 1";
    $stmt_czytelnik = $conn->prepare($query_czytelnik);
    $stmt_czytelnik->bind_param("s", $czytelnik_nr_karty);
    $stmt_czytelnik->execute();
    $result_czytelnik = $stmt_czytelnik->get_result();


    if ($result_wydanie->num_rows === 1 && $result_czytelnik->num_rows === 1) 
    {
        $row_wydanie = $result_wydanie->fetch_assoc();
        $row_czytelnik = $result_czytelnik->fetch_assoc();
        $wydanie_ID = $row_wydanie['ID'];
        $czytelnik_ID = $row_czytelnik['ID'];

        $insertQuery = "INSERT INTO wypozyczenie (ID_czytelnika, ID_egzemplarza, ID_pracownika, data_wypozyczenia, termin_oddania) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iiiss", $czytelnik_ID, $wydanie_ID, $pracownik_ID, $data_wypozyczenia, $termin_zwrotu);

        if ($insertStmt->execute()) 
        {
            echo json_encode(['success' => true]);
            $_SESSION['success_message'] = 'Dodano wypożyczenie!';
        } 
        else 
        {
            echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania egzemplarza']);
        }
        $insertStmt->close();
    } 
    else 
    {
        if ($result_wydanie->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'Nie znaleziono egzemplarza o podanych danych']);
        } elseif ($result_czytelnik->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'Nie znaleziono czytelnika o podanych danych']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania wypożyczenia']);
        }
    }
    $stmt->close();
} 
else {
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych']);
}
$conn->close();
?>