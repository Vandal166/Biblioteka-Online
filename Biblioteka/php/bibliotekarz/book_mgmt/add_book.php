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
    $tytul = htmlspecialchars(trim($data['ksiazka_tytul']));
    $autor_imie = ucfirst(htmlspecialchars(trim($data['autor_imie'])));
    $autor_nazwisko = ucfirst(htmlspecialchars(trim($data['autor_nazwisko'])));
    $gatunek = htmlspecialchars(trim($data['gatunek']));
    $wydawnictwo = htmlspecialchars(trim($data['wydawnictwo']));
    $ISBN = htmlspecialchars(trim($data['wydanie_ISBN']));
    $data_wydania = htmlspecialchars(trim($data['wydanie_data_wydania']));
    $numer_wydania = htmlspecialchars(trim($data['wydanie_numer_wydania']));
    $jezyk = htmlspecialchars(trim($data['wydanie_jezyk']));
    $ilosc_stron = intval($data['wydanie_ilosc_stron']);
    $czy_elektronicznie = isset($data['wydanie_czy_elektronicznie']) && $data['wydanie_czy_elektronicznie'] == 1 ? 1 : 0;
    $zdjecie = htmlspecialchars(trim($data['zdjecie']));

    // walidajca
    $error = validate_book_data([
        'title' => $tytul,
        'author_name' => $autor_imie,
        'author_surname' => $autor_nazwisko,
        'genre' => $gatunek,
        'publisher' => $wydawnictwo,
        'ISBN' => $ISBN,
        'release_date' => $data_wydania,
        'edition_number' => $numer_wydania,
        'language' => $jezyk,
        'page_count' => $ilosc_stron,
        'is_electronic' => $czy_elektronicznie,
        'image_path' => $zdjecie    
    ]);

    if ($error) 
    {
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }

    $conn->begin_transaction();
    try {
        // sprawdzanie czy autor istnieje, jesli tak to pobranie jego ID lub dodanie nowego
        $stmt = $conn->prepare("SELECT ID FROM autor WHERE imie = ? AND nazwisko = ?");
        $stmt->bind_param('ss', $autor_imie, $autor_nazwisko);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) 
        {
            $stmt = $conn->prepare("INSERT INTO autor (imie, nazwisko) VALUES (?, ?)");
            $stmt->bind_param('ss', $autor_imie, $autor_nazwisko);
            $stmt->execute();
            $autor_id = $conn->insert_id;
        } else {
            $stmt->bind_result($autor_id);
            $stmt->fetch();
        }
        
        // 2. spr gatunek
        $stmt = $conn->prepare("SELECT ID FROM gatunek WHERE ID = ?");
        $stmt->bind_param('i', $gatunek);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            throw new Exception('Gatunek nie istnieje!');
        }
        
        // 3. spr wydawnictwo
        $stmt = $conn->prepare("SELECT ID FROM wydawnictwo WHERE ID = ?");
        $stmt->bind_param('i', $wydawnictwo);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            throw new Exception('Wydawnictwo nie istnieje!');
        }
        
        // 4. sprawdzenie czy ksiazka o podanym tytule juz istnieje, jesli tak to pobranie jej ID else dodanie nowej
        $stmt = $conn->prepare("SELECT ID FROM ksiazka WHERE tytul = ?");
        $stmt->bind_param('s', $tytul);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) 
        {
            $stmt = $conn->prepare("INSERT INTO ksiazka (tytul, zdjecie) VALUES (?, ?)");
            $stmt->bind_param('ss', $tytul, $zdjecie);
            $stmt->execute();
            $ksiazka_id = $conn->insert_id;
        } 
        else 
        {
            $stmt->bind_result($ksiazka_id);
            $stmt->fetch();
        }

        
        // spr czy wydanie o isbn/nr_wydania juz istnieje
        $stmt = $conn->prepare("SELECT ID FROM wydanie WHERE ISBN = ? OR numer_wydania = ?");
        $stmt->bind_param('ss', $ISBN, $numer_wydania);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) 
        {
            throw new Exception('Wydanie o podanym ISBN lub numerze już istnieje!');
        }
        
        $stmt = $conn->prepare("
            INSERT INTO wydanie (ID_ksiazki, ID_wydawnictwa, ISBN, data_wydania, numer_wydania, jezyk, ilosc_stron, czy_elektronicznie)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE ID=LAST_INSERT_ID(ID)
        ");
        $stmt->bind_param(
            'iissssii',
            $ksiazka_id,
            $wydawnictwo,
            $ISBN,
            $data_wydania,
            $numer_wydania,
            $jezyk,
            $ilosc_stron,
            $czy_elektronicznie
        );
        $stmt->execute();
        $wydanie_id = $conn->insert_id;
        
        // spr czy ksiazka ma juz danego autora
        $stmt = $conn->prepare("SELECT 1 FROM autor_ksiazki WHERE ID_ksiazki = ? AND ID_autora = ?");
        $stmt->bind_param('ii', $ksiazka_id, $autor_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) 
        {
            $stmt = $conn->prepare("INSERT INTO autor_ksiazki (ID_ksiazki, ID_autora) VALUES (?, ?)");
            $stmt->bind_param('ii', $ksiazka_id, $autor_id);
            $stmt->execute();
        }

        // spr czy ksiazka ma juz dany gatunek
        $stmt = $conn->prepare("SELECT 1 FROM gatunek_ksiazki WHERE ID_ksiazki = ?");
        $stmt->bind_param('i', $ksiazka_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) 
        {
            $stmt = $conn->prepare("INSERT INTO gatunek_ksiazki (ID_ksiazki, ID_gatunku) VALUES (?, ?)");
            $stmt->bind_param('ii', $ksiazka_id, $gatunek);
            $stmt->execute();
        }


        $conn->commit();
        $_SESSION['success_message'] = 'Dodano książkę: ' . $tytul;
        echo json_encode(['success' => true]);
    } 
    catch (Exception $e) 
    {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    finally
    {
        $stmt->close();
    }
} 
else {
    echo json_encode(['success' => false, 'error' => 'Brak danych!']);
}
?>


