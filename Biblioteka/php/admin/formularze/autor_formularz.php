<?php
session_start();
if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'administrator') 
{
    header("Location: ../../../index.php"); // Brak dostępu
    exit();
}
require_once('./../../db_connection.php');
require_once('../../../php/validation_funcs.php');
require_once('../../../php/helpers.php');


$messageADD = "";
$messageDELETE = "";
$messageEDIT = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    // Sprawdzanie, który formularz został przesłany
    if (isset($_POST['action']) && $_POST['action'] === 'add') 
    {
        // Dodawanie danych
        $imie = isset($_POST['imie']) ? ucfirst(strtolower(htmlspecialchars(trim($_POST['imie'])))) : null;
        $imieError = validate_name($imie);

        $nazwisko = isset($_POST['nazwisko']) ? ucfirst(strtolower(htmlspecialchars(trim($_POST['nazwisko'])))) : null;
        $nazwError = validate_name($nazwisko);
        remember_form_data();
        if ($imieError || $nazwError) 
        {
            $messageADD = $imieError ? $imieError : $nazwError;   
        }
        else if (!empty($imie) && !empty($nazwisko)) 
        {
            $sql = "INSERT INTO autor (imie, nazwisko) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $imie, $nazwisko);

            if ($stmt->execute()) 
            {
                $messageADD = "Autor został dodany pomyślnie.";
                // clear form data
                $_SESSION['form_data'] = [];
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
        $ID = intval($_POST['ID']); // intval() zapewnia, że ID jest liczbą całkowitą
        
        if ($ID > 0) 
        {
            // Sprawdzenie, czy rekord istnieje
            $checkSql = "SELECT COUNT(*) FROM autor WHERE ID = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("i", $ID);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) 
            {
                $sql = "DELETE FROM autor WHERE ID = ?";
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
        $new_imie = isset($_POST['new_imie']) ? ucfirst(strtolower(htmlspecialchars((trim($_POST['new_imie']))))) : null;
        $new_imieError = validate_name($new_imie);
        $new_nazwisko = isset($_POST['new_nazwisko']) ? ucfirst(strtolower(htmlspecialchars((trim($_POST['new_nazwisko']))))) : null;
        $new_nazwiskoError = validate_name($new_nazwisko);
        remember_form_data();

        if ($new_imieError || $new_nazwiskoError) 
        {
            $messageEDIT = $new_imieError ? $new_imieError : $new_nazwiskoError;   
        }
        else if ($ID > 0 && !empty($new_imie) && !empty($new_nazwisko)) 
        {
            // Sprawdzenie, czy rekord istnieje
            $checkSql = "SELECT COUNT(*) FROM autor WHERE ID = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("i", $ID);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) 
            {
                $sql = "UPDATE autor SET imie = ?, nazwisko = ? WHERE ID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $new_imie, $new_nazwisko, $ID);

                if ($stmt->execute()) 
                {
                    $messageEDIT = "Rekord o ID $ID został zaktualizowany.";
                    $_SESSION['form_data'] = [];
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
            <li><a href="../tabele/autor_tabela.php"><button>Tabele</button></a></li>
        </ul>
    </section>

    <section id="panel">
        <ul>
            <li><button disabled>Autor</button></li>
            <li><button style="color: grey;">Autor-Książka</button></li>
            <li><button style="color: grey;">Czytelnik</button></li>
            <li><button style="color: grey;">Egzemplarz</button></li>
            <li><button style="color: grey;">Gatunek</button></li>
            <li><button style="color: grey;">Gatunek-Książka</button></li>
            <li><a href="ksiazka_formularz.php"><button>Książka</button></a></li>
            <li><button style="color: grey;">Pracownik</button></li>
            <li><button style="color: grey;">Rezerwacja</button></li>
            <li><button style="color: grey;">Wydanie</button></li>
            <li><button style="color: grey;">Wydawnictwo</button></li>
            <li><button style="color: grey;">Wypożyczenie</button></li>
        </ul>
    </section>
    
    <section id="formularz">
        <div class="podsekcja">
            <h2>Dodawanie</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="add">
                <label for="imie">Imię:</label>
                <input type="text" id="imie" name="imie" value="<?php echo get_form_value('imie'); ?>" required><br><br>

                <label for="nazwisko">Nazwisko:</label>
                <input type="text" id="nazwisko" name="nazwisko" value="<?php echo get_form_value('nazwisko'); ?>" required><br><br>

                <button type="submit">Dodaj</button>
                <?php if (!empty($messageADD)): ?>
                <p style="color: green; text-align:center"><?php echo htmlspecialchars($messageADD); ?></p>
                <?php endif; ?>
            </form>


        </div>
        
        <div class="podsekcja">
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
        
        <div class="podsekcja">
            <h2>Edytowanie</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <label for="ID">ID:</label>
                <input type="number" id="ID" name="ID" required><br><br>

                <label for="new_imie">Nowe imię:</label>
                <input type="text" id="new_imie" name="new_imie" value="<?php echo get_form_value('new_imie'); ?>" required><br><br>

                <label for="new_nazwisko">Nowe nazwisko:</label>
                <input type="text" id="new_nazwisko" name="new_nazwisko" value="<?php echo get_form_value('new_nazwisko'); ?>" required><br><br>

                <button type="submit">Edytuj</button>
                <?php if (!empty($messageEDIT)): ?>
                <p style="color: green; text-align:center"><?php echo htmlspecialchars($messageEDIT); ?></p>
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
