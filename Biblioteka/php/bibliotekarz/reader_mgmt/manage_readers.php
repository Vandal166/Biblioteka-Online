<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/helpers.php');

//TODO: nie ma opcji do dodawania autorow, gatunkow, wydawnictw.

// pobranie danych czytelnika dla wyswietlenia w tabeli

$query = "SELECT 
    czytelnik.ID AS czytelnik_ID,
    czytelnik.imie AS czytelnik_imie, 
    czytelnik.nazwisko AS czytelnik_nazwisko, 
    czytelnik.nr_karty AS czytelnik_nr_karty,
    czytelnik.email AS czytelnik_email,
    czytelnik.telefon AS czytelnik_telefon
FROM czytelnik
ORDER BY czytelnik.nazwisko ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html> 
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzaj Czytelnikami</title> 
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
                <li><a href="/Biblioteka/php/bibliotekarz/book_mgmt/manage_books.php"><button>Zarządzaj Książkami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/exemplar_mgmt/manage_exemplars.php"><button>Zarządzaj Egzemplarzami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/rent_mgmt/manage_rents.php"><button>Zarządzaj Wypożyczeniami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reader_mgmt/manage_readers.php"><button disabled>Zarządzaj Czytelnikami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reservation.php"><button>Rezerwacja Książek</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reports.php"><button>Raporty</button></a></li>
                <!-- TODO: ? <li><a href=""><button>Autorzy/Gatunek/Wydawnictwo</button></a></li> -->
            </ul>
    </section>   
        
      
    <section id="tabela">
        <table class="sortable">
            <thead>
<!-- czytelnik:imie nazwisko nr_karty; -->
            <tr>                
                <th>Czytelnik</th>
                <th>Numer karty</th>
                <th>Email</th>
                <th>Telefon</th>
                <th class="sorttable_nosort">Akcje</th>
            </tr>
            </thead>
            <tbody>
                <?php while ($book = mysqli_fetch_assoc($result)) { ?>
                    <tr id="book_<?php echo $book['czytelnik_ID']; ?>">   
                    <td><?php echo htmlspecialchars($book['czytelnik_imie'] . ' ' . $book['czytelnik_nazwisko']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['czytelnik_nr_karty']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['czytelnik_email']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['czytelnik_telefon']) ?: 'Brak'; ?></td>                  
                    <td class="actions"> 
                        <button onclick="openInfoModal(<?php echo $book['czytelnik_ID']; ?>)">Szczegóły</button>
                        <button onclick="openEditModal(<?php echo $book['czytelnik_ID']; ?>)">Edytuj</button>
                        <button onclick="openDeleteModal(<?php echo $book['czytelnik_ID']; ?>)">Usuń</button>                        
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
                <h2 style="font-size: 18px;">Czy na pewno chcesz usunąć wypożyczenie czytelnika:</h2>
                <p id="deleteBookReader" style="font-size: 16px; display: inline;"></p>
                <i><h2 id="deleteBookTitle" style="font-size: 18px; display: inline;"></h2></i>

                <div class="popup-buttons">
                    <button onclick="deleteBook()">Tak</button>
                    <button onclick="closeModal()">Nie</button>
                </div>
            </div>
        </div>


        <section class="formularz">
            <div class="podsekcja">
            <!-- pop up do infa wyp -->                
                <div id="infoBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Szczegóły czytelnika</h2>
                        <form id="infoBookForm">
                            <input type="hidden" name="id" id="book_id">
                            <p><strong>Imię:</strong> <span id="reader_name"></span></p>
                            <p><strong>Nazwisko:</strong> <span id="reader_surname"></span></p>
                            <p><strong>Numer karty:</strong> <span id="reader_card_number"></span></p>
                            <p><strong>Email:</strong> <span id="reader_email"></span></p>
                            <p><strong>Telefon:</strong> <span id="reader_phone"></span></p>
                            <!-- TODO: przycisk do zobaczenia wypożyczeń/rezerwacji? -->
                            <div id="rentingDetailsButtonContainer"></div> 
                                                        
                            <!-- Modal to display reader renting details -->
                            <div id="readerRentingDetailsModal" class="modal">
                                <div class="modal-content">
                                    <span class="close-btn" onclick="closeModal()">&times;</span>
                                    <h2>Szczegóły wypożyczeń</h2>
                                    <div id="readerRentingDetailsContent"></div>
                                    <button type="button" onclick="closeModal()">Zamknij</button>
                                </div>
                            </div>
                            
                            //TODO: fix
                            <button type="button" onclick="closeModal()">Zamknij</button>
                        </form>
                    </div>
                </div>
                 
                <div id="editBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Edytuj Wypożyczenie</h2>
                        <form id="editBookForm">
                        <input type="hidden" name="id" id="edit_renting_id">
                        
                        <div class="reader-details-container" id="editModalReaderDetails">
                            <p><strong>Imię:</strong> <span id="edit_reader_name"></span></p>
                            <p><strong>Nazwisko:</strong> <span id="edit_reader_surname"></span></p>
                            <p><strong>Numer karty:</strong> <span id="edit_reader_card_number"></span></p>
                            <p><strong>Email:</strong> <span id="edit_reader_email"></span></p>
                        </div>

                        <i style="text-align: center; font-size: 30px;"><label id="edit_book_title" ></label></i>
                        <div class="book-details-container" id="editModalDetails">
                            <p><strong>Zdjęcie:</strong> <img id="edit_book_image" src="" alt="Zdjęcie książki" style="max-width: 200px; max-height: 200px; display: none;"></p>
                            <p><strong>Autor:</strong> <span id="edit_book_author"></span></p>
                            <p><strong>Gatunek:</strong> <span id="edit_book_genre"></span></p>                        
                            <p><strong>ISBN:</strong> <span id="edit_book_isbn"></span></p>
                            <p><strong>Data wydania:</strong> <span id="edit_book_release_date"></span></p>
                            <p><strong>Język:</strong> <span id="edit_book_language"></span></p>
                            <p><strong>Ilość stron:</strong> <span id="edit_book_pages"></span></p>
                        </div>
                            
                            <hr style="border: 0; height: 1.5px; background: linear-gradient(to right, #fff, #000, #fff); margin: 20px 0;">

                            <label for="edit_due_date">Termin zwrotu:</label>
                            <input type="date" id="edit_due_date" name="edit_due_date" required>
                            <label for="edit_return_date">Data zwrotu:</label>
                            <input type="date" id="edit_return_date" name="edit_return_date">

                            <div class="error-message" style="color: red; text-align: center"></div>
                            <button type="button" onclick="saveBookChanges()">Zapisz</button>
                        </form>
                    </div>
                </div>

                <!-- Modal do dodawania egz -->
                <div id="addBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Dodaj nowe wypożyczenie</h2>
                        <form id="addBookForm">
                            
                            <!-- dynamiczne fetchowanie danych po podaniu nr_karty(bibliotekarz moze zobaczyc nr_karty w 'Zarzadzaniu czytelnikiem')-->
                            <label for="cardNumber" style="padding-left: 10px;font-size: 20px;">Czytelnik wypożyczający:</label>
                            <select id="cardNumSelect" name="cardNumber" style="display: none;" required>
                                <option value="" selected disabled>-- Wybierz czytelnika --</option>
                            </select>
                            <input type="text" id="cardNumInput" name="cardNumber" placeholder="Wpisz numer karty czytelnika" maxlength="10" required>
                            <div class="reader-details-container" id="addModalReaderDetails" style="display: none;">
                                <p><strong>Imię:</strong> <span id="add_reader_name"></span></p>
                                <p><strong>Nazwisko:</strong> <span id="add_reader_surname"></span></p>
                                <p><strong>Numer karty:</strong> <span id="add_reader_card_number"></span></p>
                                <p><strong>Email:</strong> <span id="add_reader_email"></span></p>
                            </div>

                            <label for="exemplarID" style="padding-left: 10px;font-size: 20px;">Egzemplarz wypożyczany:</label>
                            <select id="exemplarSelect" name="exemplarID" style="display: none;" required>
                                <option value="" selected disabled>-- Wybierz egzemplarz --</option>
                            </select>
                            <input type="text" id="exemplarInput" name="exemplarID" placeholder="Wpisz numer wydania" maxlength="20" required>

                            <i style="text-align: center; font-size: 30px;"><label id="add_book_title" ></label></i>
                            <div class="book-details-container" id="addModalDetails" style="display: none;">
                                <p><strong>Zdjęcie:</strong> <img id="add_book_image" src="" alt="Zdjęcie książki" style="max-width: 200px; max-height: 200px; display: none;"></p>
                                <p><strong>Autor:</strong> <span id="add_book_author"></span></p>
                                <p><strong>Gatunek:</strong> <span id="add_book_genre"></span></p>                        
                                <p><strong>ISBN:</strong> <span id="add_book_isbn"></span></p>
                                <p><strong>Data wydania:</strong> <span id="add_book_release_date"></span></p>
                                <p><strong>Język:</strong> <span id="add_book_language"></span></p>
                                <p><strong>Ilość stron:</strong> <span id="add_book_pages"></span></p>
                            </div>

                            <!-- dodawanie -->
                            <hr style="border: 0; height: 1.5px; background: linear-gradient(to right, #fff, #000, #fff); margin: 20px 0;">
                            <!-- current ID of pracownik(bibliotekarz) ktory nadaje wypozyczenie -->
                            <input type="hidden" name="add_librarian_ID" id="add_librarian_ID" value="<?php echo $_SESSION['user_id']; ?>">

                            <label for="add_rent_date">Data wypożyczenia:</label>
                            <input type="date" id="add_rent_date" name="add_rent_date" required>
                            <label for="add_due_date">Termin oddania:</label>
                            <input type="date" id="add_due_date" name="add_due_date" required>

                            <div class="error-message" style="color: red; text-align: center"></div>
                            <button type="button" onclick="addNewRenting()">Dodaj wypożyczenie</button>
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
        // smooth scroll danych<td> po sortowaniu
        const table = document.querySelector('.sortable'); 
        table.addEventListener('click', function() 
        {
            table.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        
    });     
    </script>

    <!-- skrypt do modali(pop-up) -->
    <script src="js/bibliotekarz/manage_readers.js" defer></script>    

    <!-- skrypt do ladowania zdjec -->
    <script src="js/image_mgr.js" defer></script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>