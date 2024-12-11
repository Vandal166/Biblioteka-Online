<?php
session_start();
if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'administrator') {
    header("Location: ../../../index.php"); // Brak dostępu
    exit();
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
            <li><a href="../formularze/ksiazka_formularz.php"><button>Formularze</button></a></li>
            <li><button disabled>Tabele</button></li>
        </ul>
    </section>

    <section id="panel">
        <ul>
            <li><a href="autor_tabela.php"><button>Autor</button></a></li>
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
    
    <section id="tabela">
        <?php
        require_once('./../../db_connection.php');

        // Zapytanie SQL
        $sql = "SELECT * FROM ksiazka";
        $result = $conn->query($sql);

        echo "<table>";
        // Nagłówki tabeli
        echo "<thead>
                <tr>
                    <th>ID</th>
                    <th>Tytuł</th>
                </tr>
            </thead>";
        echo "<tbody>";

        // Sprawdzenie, czy są dane
        if ($result->num_rows > 0) 
        {
            // Wyświetlanie danych
            while ($row = $result->fetch_assoc()) 
            {
                echo "<tr>
                        <td>" . $row["ID"] . "</td>
                        <td>" . $row["tytul"] . "</td>
                    </tr>";
            }
        } 
        else 
        {
            // Pusty wiersz z wiadomością
            echo "<tr>
                    <td colspan='3' class='no-data'>Brak danych</td>
                </tr>";
        }
        echo "</tbody>";
        echo "</table>";

        // Zamknięcie połączenia
        $conn->close();
        ?>
    </section>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
    
    <script src="../../../js/admin.js"></script>
</body>
</html>
