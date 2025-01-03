<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'administrator') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

$tabela = isset($_GET['tabela']) ? $_GET['tabela'] : null; // nazwa tabeli do wyświetlenia

if(!$tabela)
    $tabela = 'autor';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administracyjny</title>
    <base href="/Biblioteka/"> <!-- bazowa sciezka dla odnośników -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
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
            <li><a href="php/admin/formularze/main_formularz.php?formularz=<?php echo $tabela; ?>"><button>Formularze</button></a></li>
            <li><button disabled>Tabele</button></li>
        </ul>
    </section>

    <section id="panel">
        <ul>         
            <?php
            $tabele = [
                'autor' => 'Autor',
                'autor_ksiazki' => 'Autor-Książka',
                'czytelnik' => 'Czytelnik',
                'egzemplarz' => 'Egzemplarz',
                'gatunek' => 'Gatunek',
                'gatunek_ksiazki' => 'Gatunek-Książka',
                'ksiazka' => 'Książka',
                'pracownik' => 'Pracownik',
                'rezerwacja' => 'Rezerwacja',
                'wydanie' => 'Wydanie',
                'wydawnictwo' => 'Wydawnictwo',
                'wypozyczenie' => 'Wypożyczenie'
            ];

            foreach ($tabele as $key => $value) {
                if ($tabela === $key) {
                    echo "<li><button disabled>$value</button></li>";
                } else {
                    echo "<li><a href='php/admin/tabele/main_tabela.php?tabela=$key'><button>$value</button></a></li>";
                }
            }
            ?>
        </ul>
    </section>

    <?php
        //zaladowanie tabeli
        if ($tabela && array_key_exists($tabela, $tabele)){
            include(BASE_PATH . 'php/admin/tabele/' . $tabela . '_tabela.php');           
        }
        else {
            echo "<p>Brak tabeli</p>";
        }
    ?>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>
