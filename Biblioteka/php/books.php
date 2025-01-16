<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Książki</title>
    <link rel="stylesheet" href="/Biblioteka/css/style.css">
    <link rel="stylesheet" href="/Biblioteka/css/books.css">
    <link rel="stylesheet" href="/Biblioteka/css/modal.css">
</head>
<body>
    <!-- Nagłówek -->
    <header>
        <nav>
            <ul>
                <?php if(isset($_SESSION['poziom_uprawnien']) && $_SESSION['poziom_uprawnien'] === 'administrator'): ?>
                    <!-- if administrator, wyświetl guzik do panelu admina -->
                    <li><a href="../php/admin/tabele/main_tabela.php?tabela=autor">Panel administracyjny</a></li>
                <?php elseif(isset($_SESSION['poziom_uprawnien']) && $_SESSION['poziom_uprawnien'] === 'bibliotekarz'): ?>
                    <!-- if bibliotekarz, wyświetl guzik do panelu bibliotekarza -->
                    <li><a href="../php/bibliotekarz/panel_bibliotekarski.php" >Panel bibliotekarski</a></li>
                <?php endif; ?>

                <li><a href="../index.php">Strona Główna</a></li>
        
                <?php if(isset($_SESSION['user_id']) && !isset($_SESSION['poziom_uprawnien'])): ?>
                    <li><a href="../php/user.php">Wyświetl profil</a></li>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- if użytkownik jest zalogowany, wyświetl "Wyloguj" -->
                    <li><a href="../php/logout.php" id="logoutBtn">Wyloguj się</a></li>
                <?php else: ?>
                    <!-- if użytkownik nie jest zalogowany, wyświetl "Zaloguj się" -->
                    <li><a href="../php/login.php">Zaloguj się</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>   

    <header>
        <nav>
            <ul>
                <!-- wyszukiwanie po tutule -->
                <section id="search">
                    <form action="" method="GET">
                        <input type="text" name="search" id="searchInput" placeholder="Wyszukaj książkę po tytule">
                        <button type="submit">Szukaj</button>
                    </form>
                </section>
            </ul>
        </nav>
    </header>

    <div id="books" class="grid-container">
        <!-- Książki będą tutaj wczytywane -->
    </div>

    <div id="editionModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeEditionModal()">&times;</span>
            <h2 id="modalBookTitle">Dostępne wydania</h2>
            <ul id="editionList" class="edition-list">
            <!-- Lista wydań książki zostanie załadowana dynamicznie -->
            </ul>
        </div>
    </div>


    <section id="pages">
        <div class="pagination" id="pagination">
            <!-- Przyciski paginacji będą generowane dynamicznie -->
        </div>
    </section>

    <!-- Stopka -->
    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const urlParams = new URLSearchParams(window.location.search);
            const bookID = urlParams.get('bookID');
            const bookTitle = urlParams.get('bookTitle');

            if (bookID && bookTitle) {
                openEditionModal(bookID, bookTitle);
            }
        });

        // Event listener dla ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeEditionModal();
            }
        });

        // Event listener dla klikania poza modal
        document.addEventListener('click', function(event) {
            closeEditionModal();
        
        });
    </script>
    
    <script src="../js/script.js"></script>
    <script src="../js/books.js"></script>
</body>
</html>
