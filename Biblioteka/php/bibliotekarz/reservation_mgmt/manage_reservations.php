<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/helpers.php');

// pobranie danych rezerwacji dla wyswietlenia w tabeli

$query = "SELECT 
    rezerwacja.ID AS rezerwacja_ID,
    rezerwacja.ID_wydania AS wydanie_ID,
    rezerwacja.ID_czytelnika AS czytelnik_ID,
    rezerwacja.data_rezerwacji AS data_rezerwacji,
    rezerwacja.czy_wydana AS czy_wydana,
    czytelnik.imie AS czytelnik_imie,
    czytelnik.nazwisko AS czytelnik_nazwisko,
    czytelnik.nr_karty AS czytelnik_nr_karty,
    czytelnik.email AS czytelnik_email,
    czytelnik.telefon AS czytelnik_telefon,
    wydanie.ID_ksiazki AS ksiazka_ID,
    ksiazka.tytul AS ksiazka_tytul,
    autor.imie AS autor_imie,
    autor.nazwisko AS autor_nazwisko,
    autor_ksiazki.ID AS autor_ID
FROM rezerwacja
JOIN czytelnik ON rezerwacja.ID_czytelnika = czytelnik.ID
JOIN wydanie ON rezerwacja.ID_wydania = wydanie.ID
JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.ID
JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
JOIN autor ON autor_ksiazki.ID_autora = autor.ID
ORDER BY rezerwacja.data_rezerwacji DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html> 
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzaj Rezerwacjami</title> 
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
                <li><a href="/Biblioteka/php/bibliotekarz/rent_mgmt/manage_rents.php"><button>Zarządzaj Wypożyczeniami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reader_mgmt/manage_readers.php"><button>Zarządzaj Czytelnikami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reservation_mgmt/manage_reservations.php"><button disabled>Rezerwacja Książek</button></a></li>
            </ul>
    </section>   
        
      
    <section id="tabela">
        <table class="sortable">
            <thead>
<!-- ksiazka.tytul, czytelnik.imie, czytelnik.nazwisko, rezerwacja.data_rezerwacji, rezerwacja.czy_wydana -->
            <tr>                
                <th>Tytuł Książki</th>
                <th>Czytelnik</th>
                <th>Data Rezerwacji</th>
                <th>Czy Wydana</th>
                <th class="sorttable_nosort">Akcje</th>
            </tr>
            </thead>
            <tbody>
                <?php while ($book = mysqli_fetch_assoc($result)) { ?>
                    <tr id="book_<?php echo $book['rezerwacja_ID']; ?>">   
                    <td><?php echo htmlspecialchars($book['ksiazka_tytul']); ?></td>
                    <td><?php echo htmlspecialchars($book['czytelnik_imie'] . ' ' . $book['czytelnik_nazwisko']) ?: 'Brak'; ?></td>
                    <td><?php echo htmlspecialchars($book['data_rezerwacji']); ?></td>
                    <td><?php echo $book['czy_wydana'] ? 'Tak' : 'Nie'; ?></td>                 
                    <td class="actions"> 
                        <button onclick="openInfoModal(<?php echo $book['rezerwacja_ID']; ?>)">Szczegóły</button>
                        <button onclick="openEditModal(<?php echo $book['rezerwacja_ID']; ?>)">Edytuj</button>
                        <button onclick="openDeleteModal(<?php echo $book['rezerwacja_ID']; ?>)">Usuń</button>                        
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
                <h2 style="font-size: 18px;">Czy na pewno chcesz usunąć rezerwacje</h2>
                <i><h2 id="deleteBookTitle" style="font-size: 18px; display: inline;"></h2></i>
                <h2 style="font-size: 18px; display: inline;"> dla czytelnika: </h2>
                <p id="deleteBookReader" style="font-size: 18px; display: inline;"></p>

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
                        <h2>Szczegóły rezerwacji</h2>
                        <form id="infoBookForm">
                            <input type="hidden" name="id" id="book_id">
                            <p><strong>Imię:</strong> <span id="reader_name"></span></p>
                            <p><strong>Nazwisko:</strong> <span id="reader_surname"></span></p>
                            <p><strong>Numer karty:</strong> <span id="reader_card_number"></span></p>
                            <p><strong>Email:</strong> <span id="reader_email"></span></p>
                            <p><strong>Telefon:</strong> <span id="reader_phone"></span></p>
                            
                            <p><strong>Tytuł:</strong> <span id="book_title"></span></p>
                            <p><strong>Zdjęcie:</strong> <img id="book_image" src="" alt="Zdjęcie książki" style="max-width: 200px; max-height: 200px; display: none;"></p>
                            <p><strong>Autor:</strong> <span id="book_author"></span></p>
                            <p><strong>Gatunek:</strong> <span id="book_genre"></span></p>
                            <p><strong>Wydawnictwo:</strong> <span id="book_publisher"></span></p>
                            <p><strong>Język:</strong> <span id="book_language"></span></p>
                            <p><strong>Ilość stron:</strong> <span id="book_pages"></span></p>
                            <p><strong>Data rezerwacji:</strong> <span id="book_reservation_date"></span></p>
                            <p><strong>Czy wydana:</strong> <span id="book_is_given"></span></p>                           
                            
                            <button type="button" onclick="closeModal()">Zamknij</button>
                        </form>
                    </div>
                </div>
                 
                <div id="editBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Edytuj Rezerwacje</h2>
                        <form id="editBookForm">
                            <input type="hidden" name="id" id="edit_reservation_id">

                            <p><strong>Imię:</strong> <span id="edit_reader_name"></span></p>
                            <p><strong>Nazwisko:</strong> <span id="edit_reader_surname"></span></p>
                            <p><strong>Numer karty:</strong> <span id="edit_reader_card_number"></span></p>
                            
                            <p><strong>Tytuł:</strong> <span id="edit_book_title"></span></p>
                            <p><strong>Zdjęcie:</strong> <img id="edit_book_image" src="" alt="Zdjęcie książki" style="max-width: 200px; max-height: 200px; display: none;"></p>
                            <p><strong>Autor:</strong> <span id="edit_book_author"></span></p>
                            <p><strong>Gatunek:</strong> <span id="edit_book_genre"></span></p>
                            <p><strong>Wydawnictwo:</strong> <span id="edit_book_publisher"></span></p>
                            <p><strong>Język:</strong> <span id="edit_book_language"></span></p>
                            <p><strong>Ilość stron:</strong> <span id="edit_book_pages"></span></p>

                            <hr style="border: 0; height: 1.5px; background: linear-gradient(to right, #fff, #000, #fff); margin: 20px 0;">
                            <label for="edit_reservation_date">Data rezerwacji:</label>
                            <input type="date" id="edit_reservation_date" name="edit_reservation_date" required>

                            <div class="checkbox-container">
                                <label for="edit_is_given">Czy wydana:</label>            
                                <input type="hidden" name="edit_is_given" value="0">
                                <input type="checkbox" id="edit_is_given" name="edit_is_given" value="1">
                            </div>
                        
                            <div class="error-message" style="color: red; text-align: center"></div>
                            <button type="button" onclick="saveBookChanges()">Zapisz</button>
                        </form>
                    </div>
                </div>

                <div id="addBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Dodaj nowa rezerwacje</h2>
                        <form id="addBookForm">
                            
                            <!-- dynamiczne fetchowanie danych po podaniu nr_wydania -->
                            <label for="editionNumber" style="padding-left: 10px;font-size: 20px;">Istniejace wydanie:</label>
                            <select id="editionNumberSelect" name="editionNumber" style="display: none;" required>
                                <option value="" selected disabled>-- Wybierz wydanie --</option>
                            </select>
                            <input type="text" id="editionNumberInput" name="editionNumber" placeholder="Wpisz istniejący numer wydania" maxlength="20" required>

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


                            <label for="cardNumber" style="padding-left: 10px;font-size: 20px;">Czytelnik rezerwujący:</label>
                            <select id="cardNumSelect" name="cardNumber" style="display: none;" required>
                                <option value="" selected disabled>-- Wybierz czytelnika --</option>
                            </select>
                            <input type="text" id="cardNumInput" name="cardNumber" placeholder="Wpisz numer karty czytelnika" maxlength="10" required>
                            <div class="reader-details-container" id="addModalReaderDetails" style="display: none;">
                                <p><strong>Imię:</strong> <span id="add_reader_name"></span></p>
                                <p><strong>Nazwisko:</strong> <span id="add_reader_surname"></span></p>
                                <p><strong>Numer karty:</strong> <span id="add_reader_card_number"></span></p>
                                <p><strong>Email:</strong> <span id="add_reader_email"></span></p>
                                <p><strong>Telefon:</strong> <span id="add_reader_phone"></span></p>                                
                            </div>


                            <!-- dodawanie -->
                            <hr style="border: 0; height: 1.5px; background: linear-gradient(to right, #fff, #000, #fff); margin: 20px 0;">

                            <label for="add_reservation_date">Data rezerwacji:</label>
                            <input type="date" id="add_reservation_date" name="add_reservation_date" required>

                            <div class="checkbox-container">
                                <label for="add_is_given">Czy wydana:</label>            
                                <input type="hidden" name="add_is_given" value="0">
                                <input type="checkbox" id="add_is_given" name="add_is_given" value="1">
                            </div>

                            <div class="error-message" style="color: red; text-align: center"></div>
                            <button type="button" onclick="addNewExemplar()">Dodaj rezerwacje</button>
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
                        const selectElement = document.getElementById('editionNumberSelect');
                        listData.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.numer_wydania;
                            option.textContent = `${item.tytul} - ${item.autor_imie} ${item.autor_nazwisko}`;
                            selectElement.appendChild(option);
                        });
                        selectElement.style.display = 'block';
                        document.getElementById('editionNumberInput').style.display = 'none';
                        
                        //  listener dla selecta
                        selectElement.addEventListener('change', function() {
                            const editionNumber = this.value;
                            fetchAdditionData(editionNumber);
                        });
                    });
            } else {
                document.getElementById('editionNumberSelect').style.display = 'none';
                document.getElementById('editionNumberInput').style.display = 'block';

                //  listener dla inputa
                document.getElementById('editionNumberInput').addEventListener('input', function() {
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
    <script src="js/bibliotekarz/manage_reservations.js" defer></script>    

    <!-- skrypt do ladowania zdjec -->
    <script src="js/image_mgr.js" defer></script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>