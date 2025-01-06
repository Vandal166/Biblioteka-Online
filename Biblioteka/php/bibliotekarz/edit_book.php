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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) 
{
    $id = intval($_GET['id']);

    // pobranie danych książki
    $query = "SELECT * FROM ksiazka WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        set_message('error', 'edit_book', 'Książka o podanym ID nie istnieje!');
        exit();
    }
    $book = $result->fetch_assoc();
    $stmt->close();
    
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update']))
{
    $id = intval($_POST['id']);
    $tytul = trim(htmlspecialchars($_POST['tytul']));
    $imie = trim(htmlspecialchars($_POST['imie']));
    $nazwisko = trim(htmlspecialchars($_POST['nazwisko']));

    $error = validate_book_data([
        'title' => $tytul,        
        'edition_number' => $edition_number,
        'author_name' => $imie,
        'author_surname' => $nazwisko
    ]);

    if ($error) {
        set_message('error', 'edit_book', $error);
        exit();
    }

    $query = "UPDATE ksiazka SET tytul = ?, imie = ?, nazwisko = ? WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $tytul, $imie, $nazwisko, $id);
    $stmt->execute();
    $stmt->close();   
    header('Location: manage_books.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Książke</title> 
    <base href="/Biblioteka/"> <!-- bazowa sciezka dla odnośników -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bibliotekarz.css">
</head>
<body>
    <section id="formularz">
        <div class="podsekcja">
            <h2>Edytuj książkę</h2>
            <form action="edit_book.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $book['ID']; ?>">
                <label for="tytul">Tytuł:</label>
                <input type="text" name="tytul" value="<?php echo $book['tytul']; ?>"><br>
                <label for="imie">Imię:</label>
                <input type="text" name="imie" value="<?php echo $book['imie']; ?>"><br>
                <label for="nazwisko">Nazwisko:</label>
                <input type="text" name="nazwisko" value="<?php echo $book['nazwisko']; ?>"><br>
                
                <button type="submit" name="update">Zaktualizuj</button>
                <?php display_messages('edit_book'); ?>
            </form>
        </div>
    </section>
</body>
</html>