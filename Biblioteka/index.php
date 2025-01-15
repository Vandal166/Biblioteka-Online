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
    </section>

<section id="featured-books">
    <h2>Polecane książki</h2>
    <div class="book-carousel">
        <div class="carousel-track">
            <?php
            require_once('php/db_connection.php');
            $query = "SELECT ID, tytul, zdjecie FROM ksiazka ORDER BY RAND() LIMIT 10";
            $result = $conn->query($query);
            while ($book = $result->fetch_assoc()) {
                if ($book['zdjecie'] && !str_starts_with($book['zdjecie'], '/')) {
                    $book['zdjecie'] = '/' . $book['zdjecie'];
                }
                if ($book['zdjecie']) {
                    echo "<div class='book-item'>
                        <img src='{$book['zdjecie']}' alt='{$book['tytul']}' loading='lazy' onclick=\"handleBookClick({$book['ID']}, '{$book['tytul']}')\">
                        <h3>{$book['tytul']}</h3>
                    </div>";
                }
            }
            ?>
        </div>
    </div>
</section>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.carousel-track');
    const items = document.querySelectorAll('.book-item');
    const itemWidth = items[0].offsetWidth + 20; // width + margin
    const totalItems = items.length;
    let currentIndex = 0;

    const updateCarousel = () => {
        track.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    };

    const autoSlide = () => {
        currentIndex = (currentIndex + 1) % 4; // Zapętlenie indeksu po osiągnięciu 3
        updateCarousel();
    };

    // automatyczne przesuwanie co 3 sekundy
    setInterval(autoSlide, 3000);
});

    </script>
    
    <!-- Sekcja o nas -->
    <section id="about">
        <h2>O nas</h2>
        <p>Biblioteka online, która umożliwia wygodne wypożyczanie książek.<br> Sprawdź naszą bazę książek i zarezerwuj interesujący Cię egzemplarz!</p>
    </section>

    <!-- Stopka -->
    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
    
    <script src="js/slideshow.js"></script>
    <script src="js/books.js"></script>
</body>
</html>
