<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/helpers.php');

// pobranie danych książek dla wyswietlenia w tabeli
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
        wydanie.pdf,
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
                <li><a href="/Biblioteka/php/bibliotekarz/book_mgmt/manage_books.php"><button disabled>Zarządzaj Książkami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/exemplar_mgmt/manage_exemplars.php"><button>Zarządzaj Egzemplarzami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/rent_mgmt/manage_rents.php"><button>Zarządzaj Wypożyczeniami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reader_mgmt/manage_readers.php"><button>Zarządzaj Czytelnikami</button></a></li>
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
                    <td><?php echo htmlspecialchars($book['jezyk']) ?: 'Brak'; ?></td>
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
                <i><h2 id="deleteBookTitle" style="font-size: 18px; display: inline;"></h2></i>
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
                            <p><strong>PDF:</strong> <a id="book_pdf" href="#" target="_blank"></a></p>
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
                            <label>Nowy Tytuł: <input id="edit_book_title" /></label>

                            <label for="new_zdjecie">Nowe zdjęcie:</label>
                            <input type="text" id="new_zdjecie" name="new_zdjecie"><br>

                            <div>
                                <label for="image-select-edit">Wybierz zdjęcie:</label>
                                <select class="override-style" id="image-select-edit">
                                    <option value="" disabled selected>-- Wybierz zdjęcie --</option>
                                </select>

                                <input type="file" id="file-input" name="file" accept=".png, .jpg, .jpeg"></input>
                                <button type="button" class="select-image override-style" id="select-2">Wybierz</button>

                                <input type="file" id="file-input" name="file" accept=".png, .jpg, .jpeg"></input>
                                <button type="button" class="import-image override-style" id="import-2">Importuj</button>             
                            </div>

                            <label>Nowe Imię autora: <input id="edit_book_author_first" /></label>
                            <label>Nowe Nazwisko autora: <input id="edit_book_author_last" /></label>
                            <label>Nowy Gatunek: 
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
                            <label>Nowy ISBN: <input type="text" id="edit_book_isbn" maxlength="13"/></label>
                            <label>Nowa Data wydania: <input id="edit_book_release_date" type="date" /></label>
                            <label>Nowy Numer wydania: <input type="text" id="edit_book_edition" maxlength="20" /></label>
                            <label>Nowy Język: <input id="edit_book_language" /></label>
                            <label>Nowa Ilość stron: <input id="edit_book_pages" maxlength="4"/></label>

                            <label for="new_pdf">Nowy PDF:</label>
                            <input type="text" id="new_pdf" name="new_pdf">
                            <div>
                                <label for="new_pdf-select">Wybierz PDF:</label>
                                <select class="override-style" id="new_pdf-select">
                                    <option value="" disabled selected>-- Wybierz plik PDF --</option>
                                </select>
                            
                                <input type="file" id="file-input-pdf" name="file" accept=".pdf" style="display: none;"></input>
                                <button type="button" class="select-pdf override-style" id="new_select-pdf">Wybierz</button>
                            
                                <input type="file" id="file-input-pdf" name="file" accept=".pdf" style="display: none;"></input>
                                <button type="button" class="import-pdf override-style" id="new_import-pdf">Importuj</button>
                            </div>

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

                            <label for="pdf">PDF:</label>
                            <input type="text" id="pdf" name="pdf"><br>

                            <div>
                                <label for="pdf-select">Wybierz PDF:</label>
                                <select class="override-style" id="pdf-select">
                                    <option value="" disabled selected>-- Wybierz plik PDF --</option>
                                </select>
                            
                                <input type="file" id="file-input-pdf" name="file" accept=".pdf" style="display: none;"></input>
                                <button type="button" class="select-pdf override-style" id="select-pdf">Wybierz</button>
                            
                                <input type="file" id="file-input-pdf" name="file" accept=".pdf" style="display: none;"></input>
                                <button type="button" class="import-pdf override-style" id="import-pdf">Importuj</button>
                                           
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
       
        loadImageList('image-select-add');
        loadImageList('image-select-edit');
        
    });            
    </script>

    <!-- default skrypt -->
    <script src="js/bibliotekarz/global.js" defer></script>
    
    <!-- skrypt do modali(pop-up) -->
    <script src="js/bibliotekarz/manage_books.js" defer></script>    

    <!-- skrypt do ladowania zdjec -->
    <script src="js/image_mgr.js" defer></script>

    <!-- skrypt do pdf -->
    <script src="js/pdf_mgr.js" defer></script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>