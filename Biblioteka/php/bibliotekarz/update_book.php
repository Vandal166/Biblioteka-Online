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
    $autor_imie = ucfirst(htmlspecialchars(trim($data['autor_imie'])));
    $autor_nazwisko = ucfirst(htmlspecialchars(trim($data['autor_nazwisko'])));
    $ISBN = htmlspecialchars(trim($data['wydanie_ISBN']));
    $data_wydania = htmlspecialchars(trim($data['wydanie_data_wydania']));
    $jezyk = htmlspecialchars(trim($data['wydanie_jezyk']));
    
    // walidajca
    $error = validate_book_data([
        'title' => $tytul,        
        'author_name' => $autor_imie,
        'author_surname' => $autor_nazwisko,
        'ISBN' => $ISBN,
        'release_date' => $data_wydania,
        'language' => $jezyk,
    ]);
    // 'edition_number' => $edition_number,

    if ($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }
    //TODO: spr czy istnieje wydanie o takich danych
    //TODO: spr czy istnieje wydanie o takich danych
    //TODO: spr czy istnieje wydanie o takich danych
    //TODO: spr czy istnieje wydanie o takich danych
    //TODO: spr czy istnieje wydanie o takich danych


    $conn->begin_transaction();
    try {
        // Aktualizacja tytułu książki
        $conn->query("UPDATE ksiazka 
                      SET tytul='$tytul' 
                      WHERE ID=(SELECT ID_ksiazki FROM wydanie WHERE ID=$wydanie_id)");

        // Aktualizacja autora
        $conn->query("UPDATE autor 
                      JOIN autor_ksiazki ON autor.ID = autor_ksiazki.ID_autora 
                      JOIN wydanie ON autor_ksiazki.ID_ksiazki = wydanie.ID_ksiazki 
                      SET autor.imie='$autor_imie', autor.nazwisko='$autor_nazwisko' 
                      WHERE wydanie.ID=$wydanie_id");


        // Aktualizacja lub wstawienie gatunku
        $result = $conn->query("SELECT COUNT(*) as count FROM gatunek_ksiazki 
                                WHERE ID_ksiazki=(SELECT ID_ksiazki FROM wydanie WHERE ID=$wydanie_id)");

        $row = $result->fetch_assoc();
        if ($row['count'] > 0)
        {
            // Aktualizacja istniejącego powiązania
            $conn->query("UPDATE gatunek_ksiazki 
                        SET ID_gatunku=$gatunek_id 
                        WHERE ID_ksiazki=(SELECT ID_ksiazki FROM wydanie WHERE ID=$wydanie_id)");
        } 
        else 
        {
            // Wstawienie nowego powiązania, jeśli nie istnieje inaczej nie zaktualizuje sie
            $conn->query("INSERT INTO gatunek_ksiazki (ID_ksiazki, ID_gatunku) 
                        VALUES ((SELECT ID_ksiazki FROM wydanie WHERE ID=$wydanie_id), $gatunek_id)");
        }

        // Aktualizacja wydania
        $conn->query("UPDATE wydanie 
                      SET ISBN='$ISBN', data_wydania='$data_wydania', jezyk='$jezyk' 
                      WHERE ID=$wydanie_id");

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
