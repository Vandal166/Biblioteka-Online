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
    $wydanie_numer_wydania = htmlspecialchars(trim($data['wydanie_numer_wydania']));
    $czytelnik_nr_karty = trim($data['czytelnik_nr_karty']);
    $rezerwacja_data_rezerwacji = $data['rezerwacja_data_rezerwacji'];
    $rezerwacja_czy_wydana = isset($data['rezerwacja_czy_wydana']) ? intval($data['rezerwacja_czy_wydana']) : 0;

    
    // walidajca
    $error = validate_book_data([        
        'release_date' => $rezerwacja_data_rezerwacji
    ]);
    if($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }
    
    $conn->begin_transaction();
    try
    {
        //spr czy czytelnik ma juz wypozyczone te same wydanie
        $query = "SELECT ID FROM rezerwacja WHERE ID_czytelnika = (SELECT ID FROM czytelnik WHERE nr_karty = ?) AND ID_wydania = (SELECT ID FROM wydanie WHERE numer_wydania = ?) LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $czytelnik_nr_karty, $wydanie_numer_wydania);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) 
            throw new Exception('Czytelnik ma już zarezerwowane to wydanie');

        // fetchowanie ID wydania  na podstawie numeru wydania
        $query_wydanie = "SELECT ID FROM wydanie WHERE numer_wydania = ? LIMIT 1";
        $stmt_wydanie = $conn->prepare($query_wydanie);
        $stmt_wydanie->bind_param("s", $wydanie_numer_wydania);
        $stmt_wydanie->execute();
        $result_wydanie = $stmt_wydanie->get_result();
        
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

            $insertQuery = "INSERT INTO rezerwacja (ID_czytelnika, ID_wydania, data_rezerwacji, czy_wydana) VALUES (?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("iisi", $czytelnik_ID, $wydanie_ID, $rezerwacja_data_rezerwacji, $rezerwacja_czy_wydana);
            if ($insertStmt->execute()) 
            {
                echo json_encode(['success' => true]);
                $_SESSION['success_message'] = 'Dodano rezerwacje!';
            } 
            else 
            {
                echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania rezerwacji']);
            }
            $insertStmt->close();
            $conn->commit();
        } 
        else 
        {
            if ($result_wydanie->num_rows === 0) {
                echo json_encode(['success' => false, 'error' => 'Nie znaleziono wydania o podanych danych']);
            } elseif ($result_czytelnik->num_rows === 0) {
                echo json_encode(['success' => false, 'error' => 'Nie znaleziono czytelnika o podanych danych']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Błąd podczas dodawania wypożyczenia']);
            }
        }
    } 
    catch (Exception $e) 
    {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} 
else {
    echo json_encode(['success' => false, 'error' => 'Brak wymaganych danych']);
}
$conn->close();
?>