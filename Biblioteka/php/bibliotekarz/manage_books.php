<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

// pobranie danych książek
// $query = "
//     SELECT         
//         ksiazka.ID,
//         ksiazka.tytul,
//         autor.imie,
//         autor.nazwisko,
//         gatunek.nazwa AS gatunek,
//         wydanie.ilosc_stron,
//         egzemplarz.czy_dostepny
//     FROM ksiazka
//     LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
//     LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID
//     LEFT JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
//     LEFT JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
//     LEFT JOIN wydanie ON ksiazka.ID = wydanie.ID_ksiazki
//     LEFT JOIN egzemplarz ON wydanie.ID = egzemplarz.ID_wydania
//     ORDER BY ksiazka.tytul";

$query = "SELECT
        ksiazka.ID AS ksiazka_ID,
        ksiazka.tytul,
        autor.imie AS autor_imie,
        autor.nazwisko AS autor_nazwisko,
        gatunek.nazwa AS gatunek,
        wydanie.ISBN,
        wydanie.data_wydania,
        wydanie.jezyk,
        wydanie.ilosc_stron,
        wydanie.czy_elektronicznie,
        egzemplarz.ID AS egzemplarz_ID,
        egzemplarz.czy_dostepny,
        egzemplarz.stan
    FROM egzemplarz
    LEFT JOIN wydanie ON egzemplarz.ID_wydania = wydanie.ID
    LEFT JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
    LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
    LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID
    LEFT JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
    LEFT JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
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
                    <tr id="book_<?php echo $book['egzemplarz_ID']; ?>">                        
                    <td><?php echo htmlspecialchars($book['tytul']); ?></td>
                    <td><?php echo htmlspecialchars($book['autor_imie'] . ' ' . $book['autor_nazwisko']); ?></td>
                    <td><?php echo htmlspecialchars($book['gatunek']); ?></td>
                    <td><?php echo htmlspecialchars($book['ilosc_stron']); ?></td>
                    <td><?php echo $book['czy_dostepny'] ? 'Tak' : 'Nie'; ?></td>
                    <!-- TODO: czy_dostepny zle wyswietla tak/nie -->
                    <td class="actions"> 
                        <button onclick="openInfoModal(<?php echo $book['egzemplarz_ID']; ?>)">Szczegóły</button>
                        <button onclick="openEditModal(<?php echo $book['egzemplarz_ID']; ?>)">Edytuj</button>
                        <a href="/Biblioteka/php/bibliotekarz/delete_book.php?id=<?php echo $book['egzemplarz_ID']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę książkę?');"><button>Usuń</button></a>
                    </td>                    
                </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        
        <section class="formularz">
            <div class="podsekcja">
            <!-- pop up do infa książki -->                
                <div id="infoBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Szczegóły książki</h2>
                        <form id="infoBookForm">
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

                <div id="editBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Edytuj Książkę</h2>
                        <form id="editBookForm">
                            
                            <label>Tytuł: <input id="edit_book_title" /></label>
                            <label>Imię autora: <input id="edit_book_author_first" /></label>
                            <label>Nazwisko autora: <input id="edit_book_author_last" /></label>
                            <label>Gatunek: 
                                <select id="edit_book_genre">
                                    <?php
                                    $genre_query = "SELECT ID, nazwa FROM gatunek";
                                    $genre_result = mysqli_query($conn, $genre_query);
                                    while ($genre = mysqli_fetch_assoc($genre_result)) {
                                        echo '<option value="' . $genre['ID'] . '">' . htmlspecialchars($genre['nazwa']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </label>                          
                            <label>ISBN: <input id="edit_book_isbn" /></label>
                            <label>Data wydania: <input id="edit_book_release_date" type="date" /></label>
                            <label>Język: <input id="edit_book_language" /></label>
                            <button type="button" onclick="saveBookChanges()">Zapisz</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    <script>
        // Otwieranie modala i ładowanie danych książki
            function openInfoModal(bookId) {
                fetch(`/Biblioteka/php/bibliotekarz/get_book.php?id=${bookId}`)
                    .then(response => response.json())
                    .then(data => {
                        // TODO: nie wysiwetla ISBN, date etc, sprawdzic get_book.php
                        document.getElementById('book_id').value = data.egzemplarz_ID || '';
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

                        document.getElementById('infoBookModal').style.display = 'block';
                    })
                    .catch(error => console.error('Błąd:', error));
            }

            function openEditModal(bookId) {
                fetch(`/Biblioteka/php/bibliotekarz/get_book.php?id=${bookId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('edit_book_id').value = data.egzemplarz_ID;
                        document.getElementById('edit_book_title').value = data.tytul;
                        document.getElementById('edit_book_author_first').value = data.autor_imie;
                        document.getElementById('edit_book_author_last').value = data.autor_nazwisko;
                        document.getElementById('edit_book_genre').value = data.gatunek;
                        document.getElementById('edit_book_isbn').value = data.ISBN;
                        document.getElementById('edit_book_release_date').value = data.data_wydania;
                        document.getElementById('edit_book_language').value = data.jezyk;
                        document.getElementById('editBookModal').style.display = 'block';
                    });
            }

        function closeModal() {
            //zamkniecie i wyczyszczenie formularza
            document.getElementById('infoBookModal').style.display = 'none';
            document.getElementById('editBookModal').style.display = 'none';
        }

        // zachowanie po zapisaniu zmian
        function saveBookChanges() {
            // TODO: nie dziala ;C, wydanie_id jest aktualizowane dla pierwszego rekordu w bazie, a nie dla wybranej ksiazki
        const data = {
            ksiazka_id: document.getElementById('edit_book_id').value,
            tytul: document.getElementById('edit_book_title').value,
            autor_imie: document.getElementById('edit_book_author_first').value,
            autor_nazwisko: document.getElementById('edit_book_author_last').value,
            gatunek_id: document.getElementById('edit_book_genre').value,
            ISBN: document.getElementById('edit_book_isbn').value,
            data_wydania: document.getElementById('edit_book_release_date').value,
            jezyk: document.getElementById('edit_book_language').value
        };

        fetch('/Biblioteka/php/bibliotekarz/update_book.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Książka została zaktualizowana.');
                  location.reload();
              } else {
                  alert('Wystąpił błąd: ' + data.error);
              }
          });
    }
    </script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>