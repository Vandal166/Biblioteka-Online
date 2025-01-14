<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteka Online</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Nagłówek -->
    <header>
        <nav>
            <ul>
                <?php if(isset($_SESSION['poziom_uprawnien']) && $_SESSION['poziom_uprawnien'] === 'administrator'): ?>
                    <!-- if administrator, wyświetl guzik do panelu admina -->
                    <li><a href="php/admin/tabele/main_tabela.php?tabela=autor">Panel administracyjny</a></li>
                <?php elseif(isset($_SESSION['poziom_uprawnien']) && $_SESSION['poziom_uprawnien'] === 'bibliotekarz'): ?>
                    <!-- if bibliotekarz, wyświetl guzik do panelu bibliotekarza -->
                    <li><a href="php/bibliotekarz/panel_bibliotekarski.php" >Panel bibliotekarski</a></li>
                <?php endif; ?>

                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="php/reservation.php">Rezerwacja Książek</a></li>

                <?php if(isset($_SESSION['user_id']) && !isset($_SESSION['poziom_uprawnien'])): ?>
                    <li><a href="php/user.php">Wyświetl profil</a></li>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- if użytkownik jest zalogowany, wyświetl "Wyloguj" -->
                    <li><a href="php/logout.php" id="logoutBtn">Wyloguj się</a></li>
                <?php else: ?>
                    <!-- if użytkownik nie jest zalogowany, wyświetl "Zaloguj się" -->
                    <li><a href="php/login.php">Zaloguj się</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>   

    <!-- Sekcja powitalna -->
    <section id="welcome">
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <h1>Witaj <?php echo $_SESSION['login'];?><br> w Bibliotece Online!</h1>
        <?php else: ?>
        <h1>Witaj w Bibliotece Online!</h1> 
        <?php endif; ?>

        <p>Znajdź i wypożycz książki online z naszej szerokiej oferty!</p>
    </section>

    <!-- Sekcja książek -->
    <section id="books">
        <h2>Nasze książki</h2>
        <p>Przeglądaj naszą bazę książek i zarezerwuj interesujący Cię egzemplarz!</p>
        <a href="php/books.php" class="btn">Przeglądaj książki</a>
        <!--TODO: dodac cos w stylu slide-show książek z bazy danych, wyswietlaloby zdjecie do klinkiecia z tytulem NICE -->
    </section>

    <!-- Sekcja rezerwacji -->
    <section id="reservation">
        <h2>Rezerwacja Książek</h2>
        <p>Rezerwuj książki, które chcesz wypożyczyć, a my zajmiemy się resztą!</p>
        <a href="php/reservation.php" class="btn">Zarezerwuj teraz</a>
    </section>
    
    <!-- Sekcja o nas -->
    <section id="about">
        <h2>O nas</h2>
        <p>Biblioteka online, która umożliwia wygodne wypożyczanie książek.<br> Sprawdź naszą bazę książek i zarezerwuj interesujący Cię egzemplarz!</p>
    </section>

    <!-- Stopka -->
    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
    
    <script src="js/script.js"></script>
</body>
</html>
