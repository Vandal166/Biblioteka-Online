<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');
require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/validation_funcs.php');
require_once(BASE_PATH . 'php/helpers.php');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $wydanie_id = intval($data['wydanie_ID']);
    $gatunek_id = intval($data['gatunek_ID']);
    $tytul = htmlspecialchars(trim($data['ksiazka_tytul']));
    $zdjecie = htmlspecialchars(trim($data['ksiazka_zdjecie']));
    $autor_imie = ucfirst(htmlspecialchars(trim($data['autor_imie'])));
    $autor_nazwisko = ucfirst(htmlspecialchars(trim($data['autor_nazwisko'])));
    $ISBN = htmlspecialchars(trim($data['wydanie_ISBN']));
    $data_wydania = htmlspecialchars(trim($data['wydanie_data_wydania']));
    $numer_wydania = htmlspecialchars(trim($data['wydanie_numer_wydania']));
    $jezyk = htmlspecialchars(trim($data['wydanie_jezyk']));
    $ilosc_stron = intval($data['wydanie_ilosc_stron']);
    $czy_elektronicznie = isset($data['wydanie_czy_elektronicznie']) && $data['wydanie_czy_elektronicznie'] == 1 ? 1 : 0;
    
    // walidajca
    $error = validate_book_data([
        'title' => $tytul,      
        'image_path' => $zdjecie,
        'author_name' => $autor_imie,
        'author_surname' => $autor_nazwisko,
        'ISBN' => $ISBN,
        'release_date' => $data_wydania,
        'edition_number' => $numer_wydania,
        'language' => $jezyk,
        'page_count' => $ilosc_stron
    ]);
    
    if ($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }
    //TODO: fix, po updacie danych ale nie zmienienie np. imiona/nazwiska autora wstawia nowego autora o tych samych danych

    $conn->begin_transaction();
    try {
        // Aktualizacja lub wstawienie tytułu książki        
        $stmt = $conn->prepare("
            INSERT INTO ksiazka (ID, tytul, zdjecie)
            VALUES ((SELECT ID_ksiazki FROM wydanie WHERE ID = ?), ?, ?)
            ON DUPLICATE KEY UPDATE tytul = VALUES(tytul), zdjecie = VALUES(zdjecie)
        ");
        $stmt->bind_param("iss", $wydanie_id, $tytul, $zdjecie);
        $stmt->execute();

        // Aktualizacja lub wstawienie autora
        $stmt = $conn->prepare("
            INSERT INTO autor (imie, nazwisko)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE imie = VALUES(imie), nazwisko = VALUES(nazwisko)
        ");
        $stmt->bind_param("ss", $autor_imie, $autor_nazwisko);
        $stmt->execute();
        $autor_id = $conn->insert_id;

        // Aktualizacja lub wstawienie autora książki
        $stmt = $conn->prepare("
            INSERT INTO autor_ksiazki (ID_ksiazki, ID_autora)
            VALUES ((SELECT ID_ksiazki FROM wydanie WHERE ID = ?), ?)
            ON DUPLICATE KEY UPDATE ID_autora = VALUES(ID_autora)
        ");
        $stmt->bind_param("ii", $wydanie_id, $autor_id);
        $stmt->execute();        

        // Aktualizacja gatunku
        $stmt = $conn->prepare("
            UPDATE gatunek_ksiazki
            SET ID_gatunku = ?
            WHERE ID_ksiazki = (SELECT ID_ksiazki FROM wydanie WHERE ID = ?)
        ");
        $stmt->bind_param("ii", $gatunek_id, $wydanie_id);
        $stmt->execute();

        // Aktualizacja wydania
        $stmt = $conn->prepare("
            UPDATE wydanie
            SET ISBN = ?, data_wydania = ?, numer_wydania = ?, jezyk = ?, ilosc_stron = ?, czy_elektronicznie = ?
            WHERE ID = ?
        ");
        $stmt->bind_param("sssssii", $ISBN, $data_wydania, $numer_wydania, $jezyk, $ilosc_stron, $czy_elektronicznie, $wydanie_id);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success_message'] = 'Dane książki zostały pomyślnie zaktualizowane!';
        echo json_encode(['success' => true]);
    } 
    catch (Exception $e)
    {
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    }
} 
else {
    echo json_encode(['error' => 'Brak danych wejściowych']);
}
?>
