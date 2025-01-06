<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

// pobranie danych książek
$query = "
    SELECT         
        ksiazka.ID,
        ksiazka.tytul,
        autor.imie,
        autor.nazwisko,
        gatunek.nazwa AS gatunek,
        wydanie.ilosc_stron,
        egzemplarz.czy_dostepny
    FROM ksiazka
    LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
    LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID
    LEFT JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
    LEFT JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
    LEFT JOIN wydanie ON ksiazka.ID = wydanie.ID_ksiazki
    LEFT JOIN egzemplarz ON wydanie.ID = egzemplarz.ID_wydania
    ORDER BY ksiazka.tytul";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html> 
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzaj Książkami</title> 
    <base href="/Biblioteka/"> <!-- bazowa sciezka dla odnośników -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bibliotekarz.css">
    <script src="js/sorttable.js" defer></script>
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

    <section id="panel">              
            <ul>          
                <li><a href="/Biblioteka/php/bibliotekarz/manage_books.php"><button disabled>Zarządzaj Książkami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/manage_exemplars.php"><button>Zarządzaj Egzemplarzami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/manage_borrowings.php"><button>Zarządzaj Wypożyczeniami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/manage_users.php"><button>Zarządzaj Czytelnikami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reservation.php"><button>Rezerwacja Książek</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reports.php"><button>Raporty</button></a></li>
            </ul>
    </section>   
        
      
    <section id="tabela">
        <table class="sortable">
            <thead>
            <tr>
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Gatunek</th>
                <th>Ilość stron</th>
                <th>Dostępność</th>
                <th class="sorttable_nosort">Akcje</th>
            </tr>
            </thead>
            <tbody>
                <?php while ($book = mysqli_fetch_assoc($result)) { ?>
                    <tr id="book_<?php echo $book['ID']; ?>">                        
                    <td><?php echo htmlspecialchars($book['tytul']); ?></td>
                    <td><?php echo htmlspecialchars($book['imie'] . ' ' . $book['nazwisko']); ?></td>
                    <td><?php echo htmlspecialchars($book['gatunek']); ?></td>
                    <td><?php echo htmlspecialchars($book['ilosc_stron']); ?></td>
                    <td><?php echo $book['czy_dostepny'] ? 'Tak' : 'Nie'; ?></td>
                    <td class="actions"> 
                        <button onclick="openEditModal(<?php echo $book['ID']; ?>)">Szczegóły</button>
                        <button onclick="editBook(<?php echo $book['ID']; ?>)">Edytuj</button>
                        <a href="/Biblioteka/php/bibliotekarz/delete_book.php?id=<?php echo $book['ID']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę książkę?');"><button>Usuń</button></a>
                    </td>                    
                </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        
        <section class="formularz">
            <div class="podsekcja">
            <!-- pop up do edycji książki -->                
                <div id="editBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Szczegóły książki</h2>
                        <form id="editBookForm">
                            <input type="hidden" name="id" id="book_id">
                            <p><strong>Tytuł:</strong> <span id="book_title"></span></p>
                            <p><strong>Autor:</strong> <span id="book_author"></span></p>
                            <p><strong>Gatunek:</strong> <span id="book_genre"></span></p>
                            <p><strong>Wydawnictwo:</strong> <span id="book_publisher"></span></p>
                            <p><strong>Kraj wydawnictwa:</strong> <span id="book_publisher_country"></span></p>
                            <p><strong>ISBN:</strong> <span id="book_isbn"></span></p>
                            <p><strong>Data wydania:</strong> <span id="book_release_date"></span></p>
                            <p><strong>Numer wydania:</strong> <span id="book_edition"></span></p>
                            <p><strong>Język:</strong> <span id="book_language"></span></p>
                            <p><strong>Ilość stron:</strong> <span id="book_pages"></span></p>
                            <p><strong>Elektroniczna:</strong> <span id="book_ebook"></span></p>
                            <p><strong>Stan egzemplarza:</strong> <span id="book_condition"></span></p>
                            <p><strong>Dostępność:</strong> <span id="book_availability"></span></p>
                            <button type="button" onclick="closeModal()">Zamknij</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    <script>
        // Otwieranie modala i ładowanie danych książki
            function openEditModal(bookId) {
                fetch(`/Biblioteka/php/bibliotekarz/get_book.php?id=${bookId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('book_id').value = data.ksiazka_id || '';
                        document.getElementById('book_title').innerText = data.tytul || 'Brak danych';
                        document.getElementById('book_author').innerText = `${data.autor_imie || ''} ${data.autor_nazwisko || ''}`;
                        document.getElementById('book_genre').innerText = data.gatunek || 'Brak danych';
                        document.getElementById('book_publisher').innerText = data.wydawnictwo || 'Brak danych';
                        document.getElementById('book_publisher_country').innerText = data.wydawnictwo_kraj || 'Brak danych';
                        document.getElementById('book_isbn').innerText = data.ISBN || 'Brak danych';
                        document.getElementById('book_release_date').innerText = data.data_wydania || 'Brak danych';
                        document.getElementById('book_edition').innerText = data.numer_wydania || 'Brak danych';
                        document.getElementById('book_language').innerText = data.jezyk || 'Brak danych';
                        document.getElementById('book_pages').innerText = data.ilosc_stron || 'Brak danych';
                        document.getElementById('book_ebook').innerText = data.czy_elektronicznie ? 'Tak' : 'Nie';
                        document.getElementById('book_condition').innerText = data.stan || 'Brak danych';
                        document.getElementById('book_availability').innerText = data.czy_dostepny ? 'Dostępna' : 'Niedostępna';

                        document.getElementById('editBookModal').style.display = 'block';
                    })
                    .catch(error => console.error('Błąd:', error));
            }

        function closeModal() {
            document.getElementById('editBookModal').style.display = 'none';
        }

        // zachowanie po zapisaniu zmian
        function saveBook() {
            const formData = new FormData(document.getElementById('editBookForm'));

            fetch('/Biblioteka/php/bibliotekarz/update_book.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Książka została zaktualizowana!');
                    closeModal();
                    location.reload();
                } else {
                    alert('Błąd: ' + data.error);
                }
            })
            .catch(error => console.error('Błąd:', error));
        }
    </script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>