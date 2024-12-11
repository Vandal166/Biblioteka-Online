<?php
session_start();
if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'administrator') 
{
    header("Location: ../../../index.php"); // Brak dostępu
    exit();
}
require_once('./../../db_connection.php');
require_once('../../validation_funcs.php');
require_once('../../helpers.php');


$messageADD = "";
$messageDELETE = "";
$messageEDIT = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // Sprawdzanie, który formularz został przesłany
    if (isset($_POST['action']) && $_POST['action'] === 'add') 
    {
        // Dodawanie danych
        $tytul = isset($_POST['tytul']) ? htmlspecialchars(trim($_POST['tytul'])) : null;
        $error = validate_book_title($tytul);
        remember_form_data();
        if($error)
        {
            $messageADD = $error;
        }
        else if (!empty($tytul)) 
        {
            $sql = "INSERT INTO ksiazka (tytul) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $tytul);

            if ($stmt->execute()) 
            {
                $messageADD = "Książka została dodana pomyślnie.";
                unset($_SESSION['form_data']);
            } 
            else 
            {
                $messageADD = "Błąd podczas dodawania: " . $stmt->error;
            }

            $stmt->close();
        } 
        else 
        {
            $messageADD = "Proszę wypełnić wszystkie pola.";
        }
    } 
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete') 
    {
        // Usuwanie danych
        $ID = intval($_POST['ID']);

        if ($ID > 0) 
        {
            // Sprawdzenie, czy rekord istnieje
            $checkSql = "SELECT COUNT(*) FROM ksiazka WHERE ID = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("i", $ID);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) 
            {
                $sql = "DELETE FROM ksiazka WHERE ID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $ID);

                if ($stmt->execute()) 
                {
                    $messageDELETE = "Rekord o ID $ID został usunięty.";
                } 
                else 
                {
                    $messageDELETE = "Błąd podczas usuwania: " . $stmt->error;
                }

                $stmt->close();
            } 
            else 
            {
                $messageDELETE = "Rekord o ID $ID nie istnieje.";
            }
        } 
        else 
        {
            $messageDELETE = "Nieprawidłowe ID.";
        }
    }
    elseif ($_POST['action'] === 'edit') 
    {
        // Edytowanie danych
        $ID = intval($_POST['ID']);
        $new_tytul = isset($_POST['new_tytul']) ? htmlspecialchars(trim($_POST['new_tytul'])) : null;
        $error = validate_book_title($new_tytul);
        remember_form_data();
        if($error)
        {
            $messageEDIT = $error;
        }
        else if ($ID > 0 && !empty($new_tytul)) 
        {
            // Sprawdzenie, czy rekord istnieje
            $checkSql = "SELECT COUNT(*) FROM ksiazka WHERE ID = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("i", $ID);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) 
            {
                $sql = "UPDATE ksiazka SET tytul = ? WHERE ID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $new_tytul, $ID);

                if ($stmt->execute()) 
                {
                    $messageEDIT = "Rekord o ID $ID został zaktualizowany.";
                    unset($_SESSION['form_data']);
                } 
                else 
                {
                    $messageEDIT = "Błąd podczas edytowania: " . $stmt->error;
                }

                $stmt->close();
            } 
            else 
            {
                $messageEDIT = "Rekord o ID $ID nie istnieje.";
            }
        } 
        else 
        {
            $messageEDIT = "Proszę wypełnić wszystkie pola.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administracyjny</title>
    <link rel="stylesheet" href="../../../css/style.css">
    <link rel="stylesheet" href="../../../css/admin.css">
</head>
<body>
    <header>
            <nav>
                <ul>
                    <li><a href="/Biblioteka/index.php">Strona Główna</a></li>
                    <li><a href="/Biblioteka/php/reservation.php">Rezerwacja Książek</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- if użytkownik jest zalogowany, wyświetl "Wyloguj" -->
                        <li><a href="/Biblioteka/php/logout.php" id="logoutBtn">Wyloguj się</a></li>
                    <?php else: ?>
                        <!-- if użytkownik nie jest zalogowany, wyświetl "Zaloguj się" -->
                        <li><a href="/Biblioteka/php/login.php">Zaloguj się</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
    </header>   

    <section id="pasek">
        <ul>
            <li><button disabled>Formularze</button></li>
            <li><a href="../tabele/ksiazka_tabela.php"><button>Tabele</button></a></li>
        </ul>
    </section>

    <section id="panel">
        <ul>
            <li><a href="autor_formularz.php"><button>Autor</button></a></li>
            <li><button style="color: grey;">Autor-Książka</button></li>
            <li><button style="color: grey;">Czytelnik</button></li>
            <li><button style="color: grey;">Egzemplarz</button></li>
            <li><button style="color: grey;">Gatunek</button></li>
            <li><button style="color: grey;">Gatunek-Książka</button></li>
            <li><button disabled>Książka</button></li>
            <li><button style="color: grey;">Pracownik</button></li>
            <li><button style="color: grey;">Rezerwacja</button></li>
            <li><button style="color: grey;">Wydanie</button></li>
            <li><button style="color: grey;">Wydawnictwo</button></li>
            <li><button style="color: grey;">Wypożyczenie</button></li>
        </ul>
    </section>
    
    <section id="formularz">
        <div class="podsekcja" id="C">
            <h2>Dodawanie</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="add">
                <label for="tytul">Tytuł:</label>
                <input type="text" id="tytul" name="tytul" value="<?php echo get_form_value('tytul');?>" required><br><br>

                <button type="submit">Dodaj</button>
                <?php if (!empty($messageADD)): ?>
                <p style="color: green; text-align:center"><?php echo htmlspecialchars($messageADD); ?></p>
                <?php endif; ?>
            </form>


        </div>
        
        <div class="podsekcja" id="D">
            <h2>Usuwanie</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="delete">
                <label for="ID">ID:</label>
                <input type="number" id="ID" name="ID" required><br><br>
                <button type="submit">Usuń</button>

                <?php if (!empty($messageDELETE)): ?>
                <p style="color: red; text-align:center"><?php echo htmlspecialchars($messageDELETE); ?></p>
                <?php endif; ?>
            </form>


        </div>
        
        <div class="podsekcja" id="U">
            <h2>Edytowanie</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <label for="ID">ID:</label>
                <input type="number" id="ID" name="ID" required><br><br>

                <label for="new_tytul">Nowy tytuł:</label>
                <input type="text" id="new_tytul" name="new_tytul" value="<?php echo get_form_value('new_tytul');?>" required><br><br>

                <button type="submit">Edytuj</button>
                <?php if (!empty($messageEDIT)): ?>
                <p style="color: green  ; text-align:center"><?php echo htmlspecialchars($messageEDIT); ?></p>
                <?php endif; ?>
            </form>


        </div>
    </section>


    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
    
    <script src="../../../js/admin.js"></script>
</body>
</html>
