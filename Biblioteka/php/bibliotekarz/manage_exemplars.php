<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/helpers.php');

// pobranie danych egzemplarza dla wyswietlenia w tabeli

$query = "SELECT 
    egzemplarz.ID AS egzemplarz_ID,
    egzemplarz.czy_dostepny,
    egzemplarz.stan,
    wydanie.ID AS wydanie_ID,
    wydanie.ISBN,
    wydanie.numer_wydania AS wydanie_nr_wydania,
    wydanie.jezyk AS jezyk,
    wydanie.ilosc_stron,
    ksiazka.tytul,
    wydawnictwo.nazwa AS wydawnictwo,
    autor.imie AS autor_imie,
    autor.nazwisko AS autor_nazwisko,
    gatunek.nazwa AS gatunek
FROM 
    egzemplarz
    JOIN wydanie ON egzemplarz.ID_wydania = wydanie.ID
    JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
    JOIN wydawnictwo ON wydanie.ID_wydawnictwa = wydawnictwo.ID
    JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
    JOIN autor ON autor_ksiazki.ID_autora = autor.ID
    JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
    JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html> 
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzaj Egzemplarzami</title> 
    <base href="/Biblioteka/"> <!-- bazowa sciezka dla odnośników -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bibliotekarz.css">
    <script src="js/sorttable.js" defer></script> <!-- skrypt do sortowania tabeli -->
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
                <li><a href="/Biblioteka/php/bibliotekarz/manage_books.php"><button>Zarządzaj Książkami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/manage_exemplars.php"><button disabled>Zarządzaj Egzemplarzami</button></a></li>
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
                <th>Język</th>
                <th>Wydawnictwo</th>
                <th>Numer wydania</th>
                <th>Ilość stron</th>
                <th>Stan</th>
                <th>Dostępny</th>
                <th class="sorttable_nosort">Akcje</th>
            </tr>
            </thead>
            <tbody>
                <?php while ($book = mysqli_fetch_assoc($result)) { ?>
                    <tr id="book_<?php echo $book['wydanie_ID']; ?>">                        
                    <td><?php echo htmlspecialchars($book['tytul']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['autor_imie'] . ' ' . $book['autor_nazwisko']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['gatunek']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['jezyk']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['wydawnictwo']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['wydanie_nr_wydania']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['ilosc_stron']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['stan']) ?: 'Brak'; ?></td>
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
        <div class="add-book-container">
            <button class="add-book-button" onclick="openAddBookModal()">+</button>
        </div>

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
                            <p><strong>Zdjęcie:</strong> <img id="book_image" src="" alt="Zdjęcie książki" style="max-width: 200px; max-height: 200px; display: none;"></p>
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
                        <h2>Edytuj Egzemplarz</h2>
                        <form id="editBookForm">
                            <input type="hidden" name="id" id="edit_book_id">
                            <label id="edit_book_title" style="text-align: center; font-size: 30px;"></label>
                        <div class="book-details-container">
                            <p><strong>Zdjęcie:</strong> <img id="edit_book_image" src="" alt="Zdjęcie książki" style="max-width: 200px; max-height: 200px; display: none;"></p>
                            <p><strong>Autor:</strong> <span id="edit_book_author"></span></p>
                            <p><strong>Gatunek:</strong> <span id="edit_book_genre"></span></p>                        
                            <p><strong>ISBN:</strong> <span id="edit_book_isbn"></span></p>
                            <p><strong>Data wydania:</strong> <span id="edit_book_release_date"></span></p>
                            <p><strong>Język:</strong> <span id="edit_book_language"></span></p>
                            <p><strong>Ilość stron:</strong> <span id="edit_book_pages"></span></p>
                        </div>
                            
                            <hr style="border: 0; height: 1.5px; background: linear-gradient(to right, #fff, #000, #fff); margin: 20px 0;">

                            <div class="checkbox-container">
                                <label for="edit_isAvailable">Czy dostępny:</label>            
                                <input type="hidden" name="edit_isAvailable" value="0">
                                <input type="checkbox" id="edit_isAvailable" name="edit_isAvailable" value="1">
                            </div>

                            <label for="edit_book_condition">Stan:</label>
                            <input type="text" id="edit_book_condition" name="edit_book_condition" required>

                            <label>Nowy Numer wydania: <input type="text" id="edit_book_edition" maxlength="20" /></label>
                            <!-- po wpisaniu powiazac z tym wpisanym wydaniem(o ile istnieje) -->

                            <div class="error-message" style="color: red; text-align: center"></div>
                            <button type="button" onclick="saveBookChanges()">Zapisz</button>
                        </form>
                    </div>
                </div>

                <!-- Modal do dodawania książki -->
                <div id="addBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Dodaj nową książkę</h2>
                        <form id="addBookForm">
                            <label for="bookTitle">Tytuł:</label>
                            <input type="text" id="bookTitle" name="bookTitle" required>

                            <div style="display: flex; gap: 10%;">
                                <div>
                                    <label for="authorFirstName">Imię autora:</label>
                                    <input type="text" id="authorFirstName" name="authorFirstName" required>
                                </div>
                                <div>
                                    <label for="authorLastName">Nazwisko autora:</label>
                                    <input type="text" id="authorLastName" name="authorLastName" required>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10%;">
                                <div>
                                    <label for="genre">Gatunek:</label>
                                    <select id="genre" name="genre" class="override-style" required>
                                        <option value="" disabled selected>--Wybierz gatunek--</option>
                                        <?php 
                                        $genre_query = "SELECT ID, nazwa FROM gatunek";
                                        $genre_result = mysqli_query($conn, $genre_query);
                                        while ($genre = mysqli_fetch_assoc($genre_result)) {
                                            echo '<option value="' . $genre['ID'] . '">' . htmlspecialchars($genre['nazwa']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="publisher">Wydawnictwo:</label>
                                    <select id="publisher" name="publisher" class="override-style" required>
                                        <!-- <input type="hidden" id="publisherCountry" name="publisherCountry"> -->
                                        <option value="" disabled selected>--Wybierz wydawnictwo--</option>
                                        <?php 
                                        $publisher_query = "SELECT ID, nazwa, kraj FROM wydawnictwo";
                                        $publisher_result = mysqli_query($conn, $publisher_query);
                                        while ($publisher = mysqli_fetch_assoc($publisher_result)) {
                                            echo '<option value="' . $publisher['ID'] . '">' . htmlspecialchars($publisher['nazwa']) . ' - ' . htmlspecialchars($publisher['kraj']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            

                            <label for="bookISBN">ISBN:</label>
                            <input type="text" id="bookISBN" name="bookISBN" maxlength="13" required>

                            <label for="editionNumber">Numer wydania:</label>
                            <input type="text" id="editionNumber" name="editionNumber" maxlength="20" required>

                            <label for="releaseDate">Data wydania:</label>
                            <input type="date" id="releaseDate" name="releaseDate" required>

                            <label for="language">Język:</label>
                            <input type="text" id="language" name="language" required>

                            <label for="pages">Ilość stron:</label>
                            <input type="text" id="pages" name="pages" maxlength="4" required>
                            
                            <div class="checkbox-container">
                                <label for="isElectronic">Czy elektronicznie:</label>            
                                <input type="hidden" name="isElectronic" value="0">
                                <input type="checkbox" id="isElectronic" name="isElectronic" value="1">
                            </div>
                            
                            <label for="zdjecie">Zdjęcie:</label>
                            <input type="text" id="zdjecie" name="zdjecie"><br>

                            <div>
                                <label for="image-select-add">Wybierz zdjęcie:</label>
                                <select class="override-style" id="image-select-add">
                                    <option value="" disabled selected>-- Wybierz zdjęcie --</option>
                                </select>

                                <input type="file" id="file-input" name="file" accept=".png, .jpg, .jpeg"></input>
                                <button type="button" class="select-image override-style" id="select-1">Wybierz</button>
                                
                                <input type="file" id="file-input" name="file" accept=".png, .jpg, .jpeg"></input>
                                <button type="button" class="import-image override-style" id="import-1">Importuj</button>
                            </div>
                                           
                            <div class="error-message" style="color: red; text-align: center"></div>
                            <button type="button" onclick="addNewBook()">Dodaj książkę</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    <script>      
    // nasłuchiwanie na załadowanie strony
    document.addEventListener('DOMContentLoaded', () => {
        <?php if (isset($_SESSION['success_message'])): ?>
            showGlobalSuccessMessage("<?= htmlspecialchars($_SESSION['success_message']); ?>");
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        loadImageList('image-select-add');
       //loadImageList('image-select-edit');
    });            
    </script>

    <!-- skrypt do modali(pop-up) -->
    
    <script src="js/bibliotekarz/manage_exemplars.js" defer></script>    

    <!-- skrypt do ladowania zdjec -->
    <script src="js/image_mgr.js" defer></script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>