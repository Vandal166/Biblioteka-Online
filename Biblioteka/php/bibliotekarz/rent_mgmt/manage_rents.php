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
    wypozyczenie.ID AS wypozyczenie_ID,
    wypozyczenie.data_wypozyczenia,
    wypozyczenie.termin_oddania,
    wypozyczenie.data_oddania,
    czytelnik.ID AS czytelnik_ID,
    czytelnik.imie AS czytelnik_imie,
    czytelnik.nazwisko AS czytelnik_nazwisko,
    czytelnik.email AS czytelnik_email,
    czytelnik.nr_karty AS czytelnik_nr_karty,
    egzemplarz.ID AS egzemplarz_ID,
    egzemplarz.czy_dostepny,
    egzemplarz.stan,
    pracownik.ID AS pracownik_ID,
    pracownik.imie AS pracownik_imie,
    pracownik.nazwisko AS pracownik_nazwisko,
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
    wypozyczenie
    JOIN egzemplarz ON wypozyczenie.ID_egzemplarza = egzemplarz.ID
    JOIN czytelnik ON wypozyczenie.ID_czytelnika = czytelnik.ID
    JOIN pracownik ON wypozyczenie.ID_pracownika = pracownik.ID
    JOIN wydanie ON egzemplarz.ID_wydania = wydanie.ID
    JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
    JOIN wydawnictwo ON wydanie.ID_wydawnictwa = wydawnictwo.ID
    JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
    JOIN autor ON autor_ksiazki.ID_autora = autor.ID
    JOIN gatunek_ksiazki ON ksiazka.ID = gatunek_ksiazki.ID_ksiazki
    JOIN gatunek ON gatunek_ksiazki.ID_gatunku = gatunek.ID
    ORDER BY czytelnik.imie";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html> 
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzaj Wypożyczeniami</title> 
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
                <li><a href="php/books.php">Przeglądaj Książki</a></li>
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
                <li><a href="/Biblioteka/php/bibliotekarz/rent_mgmt/manage_rents.php"><button disabled>Zarządzaj Wypożyczeniami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reader_mgmt/manage_readers.php"><button>Zarządzaj Czytelnikami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reservation_mgmt/manage_reservations.php"><button>Rezerwacja Książek</button></a></li>
            </ul>
    </section>   
        
      
    <section id="tabela">
        <table class="sortable">
            <thead>
<!-- czytelnik:imie nazwisko; egzemplarz:wydanie->ksiazka.tytul,czy_dostepny,stan; pracownik:imie,nazwisko; wypozyczenie:data_wypozyczenia,termin_oddania,data_oddania; -->
            <tr>                
                <th>Czytelnik</th>
                <th>Tytul</th>
                <th>Czy dostępny</th>
                <th>Stan</th>
                <th>Pracownik</th>
                <th>Data wypożyczenia</th>
                <th>Termin oddania</th>
                <th>Data oddania</th>
                <th class="sorttable_nosort">Akcje</th>
            </tr>
            </thead>
            <tbody>
                <?php while ($book = mysqli_fetch_assoc($result)) { ?>
                    <tr id="book_<?php echo $book['wypozyczenie_ID']; ?>">   
                    <td><?php echo htmlspecialchars($book['czytelnik_imie'] . ' ' . $book['czytelnik_nazwisko']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['tytul']) ?: 'Brak'; ?></td>
                    <td><?php echo $book['czy_dostepny'] ? 'Tak' : 'Nie'; ?></td>
                    <td><?php echo htmlspecialchars($book['stan']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['pracownik_imie'] . ' ' . $book['pracownik_nazwisko']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['data_wypozyczenia']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['termin_oddania']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['data_oddania']) ?: 'Brak'; ?></td>                    
                    <td class="actions"> 
                        <button onclick="openInfoModal(<?php echo $book['wypozyczenie_ID']; ?>)">Szczegóły</button>
                        <button onclick="openEditModal(<?php echo $book['wypozyczenie_ID']; ?>)">Edytuj</button>
                        <button onclick="openDeleteModal(<?php echo $book['wypozyczenie_ID']; ?>)">Usuń</button>                        
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
                        <h2>Szczegóły wypożyczenia</h2>
                        <form id="infoBookForm">
                            <input type="hidden" name="id" id="book_id">
                            <p><strong>Tytuł:</strong> <span id="book_title"></span></p>
                            <p><strong>Zdjęcie:</strong> <img id="book_image" src="" alt="Zdjęcie książki" style="max-width: 200px; max-height: 200px; display: none;"></p>
                            <p><strong>Autor:</strong> <span id="book_author"></span></p>                            
                            <p><strong>ISBN:</strong> <span id="book_isbn"></span></p>                            
                            <p><strong>Numer wydania:</strong> <span id="book_edition"></span></p>
                            <p><strong>Język:</strong> <span id="book_language"></span></p>
                            <p><strong>Ilość stron:</strong> <span id="book_pages"></span></p>
                            <p><strong>Wypożyczone przez:</strong> <span id="book_reader"></span></p> <!-- czytelnik.imie/nazwisko -->
                            <p><strong>Numer karty:</strong> <span id="book_card_number"></span></p>
                            <p><strong>Udzielone przez:</strong> <span id="book_librarian"></span></p> <!-- pracownik.imie/nazwisko -->
                            <p><strong>Data wypożyczenia:</strong> <span id="book_rent_date"></span></p>
                            <p><strong>Termin oddania:</strong> <span id="book_due_date"></span></p>
                            <p><strong>Data oddania:</strong> <span id="book_return_date"></span></p> <!-- can be null -->
                            <p><strong>Stan egzemplarza:</strong> <span id="book_condition"></span></p>
                            <p><strong>Dostępność:</strong> <span id="book_availability"></span></p>
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
        
        fetch('php/bibliotekarz/rent_mgmt/fetch_czytelnik_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.count <= 15) // jesli jest mniej niz 15 wyp. w bazie to wyswietlamy selecta, inaczej input  
            {
            // fetchowanie listy wydań i wypełnienie selecta
                fetch('php/bibliotekarz/rent_mgmt/fetch_czytelnik_list.php')
                    .then(response => response.json())
                    .then(listData => {
                        const selectElement = document.getElementById('cardNumSelect');
                        listData.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.czytelnik_nr_karty;
                            option.textContent = `${item.czytelnik_imie} ${item.czytelnik_nazwisko} - ${item.czytelnik_nr_karty}`;
                            selectElement.appendChild(option);
                        });
                        selectElement.style.display = 'block';
                        document.getElementById('cardNumInput').style.display = 'none';
                        
                        //  listener dla selecta
                        selectElement.addEventListener('change', function() {
                            const cardNumber = this.value;
                            fetchAdditionReaderData(cardNumber);
                        });
                    });
            } else {
                document.getElementById('cardNumSelect').style.display = 'none';
                document.getElementById('cardNumInput').style.display = 'block';

                //  listener dla inputa
                document.getElementById('cardNumInput').addEventListener('input', function() {
                    const cardNumber = this.value;
                    fetchAdditionReaderData(cardNumber);
                });
            }
        })
        .catch(error => console.error('Błąd:', error));

        fetch('php/bibliotekarz/rent_mgmt/fetch_wydanie_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.count <= 15) // jesli jest mniej niz 15 wydan w bazie to wyswietlamy selecta, inaczej input  
            {
            // fetchowanie listy wydań i wypełnienie selecta
                fetch('php/bibliotekarz/rent_mgmt/fetch_wydanie_list.php')
                    .then(response => response.json())
                    .then(listData => {
                        const selectElement = document.getElementById('exemplarSelect');
                        listData.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.numer_wydania;
                            option.textContent = `${item.tytul} - ${item.autor_imie} ${item.autor_nazwisko}`;
                            selectElement.appendChild(option);
                        });
                        selectElement.style.display = 'block';
                        document.getElementById('exemplarInput').style.display = 'none';
                        
                        //  listener dla selecta
                        selectElement.addEventListener('change', function() {
                            const editionNumber = this.value;
                            fetchAdditionData(editionNumber);
                        });
                    });
            } else {
                document.getElementById('exemplarSelect').style.display = 'none';
                document.getElementById('exemplarInput').style.display = 'block';

                //  listener dla inputa
                document.getElementById('exemplarInput').addEventListener('input', function() {
                    const editionNumber = this.value;
                    fetchAdditionData(editionNumber);
                });
            }
        })
        .catch(error => console.error('Błąd:', error));
    });     
    </script>

    <!-- default skrypt -->
    <script src="js/bibliotekarz/global.js" defer></script>

    <!-- skrypt do modali(pop-up) -->
    
    <script src="js/bibliotekarz/manage_rents.js" defer></script>    

    <!-- skrypt do ladowania zdjec -->
    <script src="js/image_mgr.js" defer></script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>