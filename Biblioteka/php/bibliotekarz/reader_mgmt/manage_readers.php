<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/helpers.php');

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
                <h2 style="font-size: 18px;">Czy na pewno chcesz usunąć czytelnika:</h2>
                <h2 id="deleteBookReader" style="font-size: 18px; display: inline;"></h2>

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
                            
                            <div id="rentingDetailsButtonContainer"></div> <!-- przycisk Szczegoly wpozyczenia/rezerwacji -->
                                                                                    
                            <!-- Modal to display reader renting details -->
                            <div id="readerRentingDetailsModal" class="modal">
                                <div class="modal-content">
                                    <span class="close-btn" onclick="closeModal()">&times;</span>
                                    <h2>Szczegóły wypożyczeń</h2>
                                    <div id="readerRentingDetailsContent"></div>
                                    <button type="button" onclick="closeModal()">Zamknij</button>
                                </div>
                            </div>
                            
                            <button type="button" onclick="closeModal()">Zamknij</button>
                        </form>
                    </div>
                </div>
                 
                <div id="editBookModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <h2>Edytuj Czytelnika</h2>
                        <form id="editBookForm">
                            <input type="hidden" name="id" id="edit_reader_id">
                        
                            <label for="edit_reader_name">Imię:</label>
                            <input type="text" id="edit_reader_name" name="edit_reader_name" required>
                            <label for="edit_reader_surname">Nazwisko:</label>
                            <input type="text" id="edit_reader_surname" name="edit_reader_surname" required>
                            <label for="edit_card_number">Numer karty:</label>
                            <input type="text" id="edit_card_number" name="edit_card_number" required readonly>
                            <!-- Przycisk do wygenerowania nowego nr_karty -->
                            <button type="button" onclick="generate_card_number()">Generuj nowy numer karty</button>
                            <label for="edit_reader_email">Email:</label>
                            <input type="email" id="edit_reader_email" name="edit_reader_email" required>
                            <label for="edit_reader_phone">Telefon:</label>
                            <input type="text" id="edit_reader_phone" name="edit_reader_phone" required>
                        
                            <div class="error-message" style="color: red; text-align: center"></div>
                            <button type="button" onclick="saveBookChanges()">Zapisz</button>
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
    });     
    </script>
    <!-- default skrypt -->
    <script src="js/bibliotekarz/global.js" defer></script>
    
    <!-- skrypt do modali(pop-up) -->
    <script src="js/bibliotekarz/manage_readers.js" defer></script>    

    <!-- skrypt do ladowania zdjec -->
    <script src="js/image_mgr.js" defer></script>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>