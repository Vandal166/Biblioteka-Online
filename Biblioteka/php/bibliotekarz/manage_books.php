<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/helpers.php');

// pobranie danych książek
$query = "SELECT
        wydanie.ID AS wydanie_ID,
        ksiazka.ID AS ksiazka_ID,
        ksiazka.tytul,
        autor.imie AS autor_imie,
        autor.nazwisko AS autor_nazwisko,
        gatunek.nazwa AS gatunek,
        wydawnictwo.nazwa AS wydawnictwo,
        wydawnictwo.kraj AS wydawnictwo_kraj,
        wydanie.ISBN,
        wydanie.data_wydania,
        wydanie.numer_wydania,
        wydanie.jezyk,
        wydanie.ilosc_stron,
        wydanie.czy_elektronicznie,
        egzemplarz.czy_dostepny,
        egzemplarz.stan
    FROM wydanie
    LEFT JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
    LEFT JOIN egzemplarz ON egzemplarz.ID_wydania = wydanie.ID
    LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
    LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID
    LEFT JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
    LEFT JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
    LEFT JOIN wydawnictwo ON wydanie.ID_wydawnictwa = wydawnictwo.ID
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
                    <tr id="book_<?php echo $book['wydanie_ID']; ?>">                        
                    <td><?php echo htmlspecialchars($book['tytul']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['autor_imie'] . ' ' . $book['autor_nazwisko']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['gatunek']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['ilosc_stron']) ?: 'Brak'; ?></td>
                    <td><?php echo $book['czy_dostepny'] ? 'Tak' : 'Nie'; ?></td>
                    
                    <td class="actions"> 
                        <button onclick="openInfoModal(<?php echo $book['wydanie_ID']; ?>)">Szczegóły</button>
                        <button onclick="openEditModal(<?php echo $book['wydanie_ID']; ?>)">Edytuj</button>
                        <button onclick="openDeleteModal(<?php echo $book['wydanie_ID']; ?>)">Usuń</button>                        
                    </td>                    
                </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <!-- Pop-up Sukcesu -->       
        <div id="successPopup" class="popup" style="display: none;">
            <div class="popup-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <p id="successPopupMessage"></p>
            </div>
        </div>

        <!-- popup usuwania książki -->
        <div id="deleteBookModal" class="popup" style="display: none;">
            <div class="popup-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2 style="font-size: 18px;">Czy na pewno chcesz usunąć książkę:</h2>
                <h2 id="deleteBookTitle" style="font-size: 18px; display: inline;"></h2>
                [<p id="deleteBookPages" style="font-size: 16px; display: inline;"></p> stron]

                <div class="popup-buttons">
                    <button onclick="deleteBook()">Tak</button>
                    <button onclick="closeModal()">Nie</button>
                </div>
            </div>
        </div>


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
                            <input type="hidden" name="id" id="edit_book_id">
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
                            <label>ISBN: <input type="text" id="edit_book_isbn" maxlength="13"/></label>
                            <label>Data wydania: <input id="edit_book_release_date" type="date" /></label>
                            <label>Język: <input id="edit_book_language" /></label>
                            <div class="error-message" style="color: red; text-align: center"></div>
                            
                            <button type="button" onclick="saveBookChanges()">Zapisz</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    <script>            
        // Otwieranie modala i ładowanie danych książki
            function openInfoModal(bookID) {
                fetch(`/Biblioteka/php/bibliotekarz/get_book.php?id=${bookID}`)
                    .then(response => response.json())
                    .then(data => {
                        
                        document.getElementById('book_id').value = data.wydanie_ID || '';
                        document.getElementById('book_title').innerText = data.ksiazka_tytul || 'Brak danych';
                        document.getElementById('book_author').innerText = `${data.autor_imie || ''} ${data.autor_nazwisko || ''}`;
                        document.getElementById('book_genre').innerText = data.gatunek || 'Brak danych';
                        document.getElementById('book_publisher').innerText = data.wydawnictwo || 'Brak danych';
                        document.getElementById('book_publisher_country').innerText = data.wydawnictwo_kraj || 'Brak danych';
                        document.getElementById('book_isbn').innerText = data.wydanie_ISBN || 'Brak danych';
                        document.getElementById('book_release_date').innerText = data.wydanie_data_wydania || 'Brak danych';
                        document.getElementById('book_edition').innerText = data.numer_wydania || 'Brak danych';
                        document.getElementById('book_language').innerText = data.wydanie_jezyk || 'Brak danych';
                        document.getElementById('book_pages').innerText = data.ilosc_stron || 'Brak danych';
                        document.getElementById('book_ebook').innerText = data.czy_elektronicznie ? 'Tak' : 'Nie';
                        document.getElementById('book_condition').innerText = data.stan || 'Brak danych';
                        document.getElementById('book_availability').innerText = data.czy_dostepny !== null ? (data.czy_dostepny ? 'Dostępna' : 'Niedostępna') : 'Brak danych'; // czyli nie ma infa o dostepnosci w egzemlarzu

                        document.getElementById('infoBookModal').style.display = 'block';
                    })
                    .catch(error => console.error('Błąd:', error));
            }
            // modal edycji
            function openEditModal(bookID) {
                fetch(`/Biblioteka/php/bibliotekarz/get_book.php?id=${bookID}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('edit_book_id').value = data.wydanie_ID;
                        document.getElementById('edit_book_title').value = data.ksiazka_tytul;
                        document.getElementById('edit_book_author_first').value = data.autor_imie;
                        document.getElementById('edit_book_author_last').value = data.autor_nazwisko;
                        const genreDropdown = document.getElementById('edit_book_genre');
                        const selectedGenre = data.gatunek_ID;
                        if(selectedGenre) {
                            Array.from(genreDropdown.options).forEach(option => {
                                option.selected = option.value === selectedGenre.toString();
                            });
                        }
                        document.getElementById('edit_book_isbn').value = data.wydanie_ISBN;
                        document.getElementById('edit_book_release_date').value = data.wydanie_data_wydania;
                        document.getElementById('edit_book_language').value = data.wydanie_jezyk;
                        document.getElementById('editBookModal').style.display = 'block';
                        document.getElementById('editBookForm').querySelector('.error-message').style.display = 'none';
                    });
            }

            // modal usuwania
            function openDeleteModal(bookID) {
               
                fetch(`/Biblioteka/php/bibliotekarz/get_book.php?id=${bookID}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.ksiazka_tytul != null) {
                            const deleteModal = document.getElementById('deleteBookModal');
                            document.getElementById('deleteBookTitle').innerText = data.ksiazka_tytul;  
                            document.getElementById('deleteBookPages').innerText = data.ilosc_stron; 
                            deleteModal.style.display = 'flex';  
                            window.currentBookID = bookID;  
                        }
                    })
                    .catch(error => console.error('Błąd:', error));
            }


            function closeModal() {
                //zamkniecie i wyczyszczenie formularza
                document.getElementById('infoBookModal').style.display = 'none';
                document.getElementById('editBookModal').style.display = 'none';
                document.getElementById('successPopup').style.display = 'none';
                document.getElementById('deleteBookModal').style.display = 'none';
            }

            // nasłuchiwanie na załadowanie strony
            document.addEventListener('DOMContentLoaded', () => {
                <?php if (isset($_SESSION['success_message'])): ?>
                    showGlobalSuccessMessage("<?= htmlspecialchars($_SESSION['success_message']); ?>");
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
            });

            // Funkcja wyświetlająca globalny pop-up sukcesu
            function showGlobalSuccessMessage(message) {
                const popup = document.getElementById('successPopup');
                const messageContainer = document.getElementById('successPopupMessage');
                messageContainer.textContent = message;
                popup.style.display = 'flex';
            }

            // zapisanie zmian po edycji
            function saveBookChanges() 
            {
                const data = {
                    wydanie_ID: document.getElementById('edit_book_id').value,
                    ksiazka_tytul: document.getElementById('edit_book_title').value,
                    autor_imie: document.getElementById('edit_book_author_first').value,
                    autor_nazwisko: document.getElementById('edit_book_author_last').value,
                    gatunek_ID: document.getElementById('edit_book_genre').value,
                    wydanie_ISBN: document.getElementById('edit_book_isbn').value,
                    // where edition_number :C ?
                    wydanie_data_wydania: document.getElementById('edit_book_release_date').value,
                    wydanie_jezyk: document.getElementById('edit_book_language').value
                };

                fetch('/Biblioteka/php/bibliotekarz/update_book.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        location.reload();
                    } 
                    else 
                    {                 
                        const errorContainer = document.querySelector('#editBookForm .error-message');
                        if (errorContainer) {
                            errorContainer.textContent = data.error;
                            errorContainer.style.display = 'block';
                        }
                    }
                });  
            }
            // usuawanie książki
            function deleteBook() 
            {
                const bookID = window.currentBookID;
                fetch(`/Biblioteka/php/bibliotekarz/delete_book.php?id=${bookID}`, { 
                    method: 'POST' 
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {                            
                            location.reload();
                        } else {                            
                            console.error(data.error);
                        }
                    });
            }
        
    </script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>