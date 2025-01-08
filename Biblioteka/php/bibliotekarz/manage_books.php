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
                                <button type="button" class="import-image override-style" id="import-2" style="margin-left: 40px;">Importuj</button>             
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

                            <div class="checkbox-container">
                                <label for="edit_book_ebook">Czy elektronicznie:</label>            
                                <input type="hidden" name="edit_book_ebook" value="0">
                                <input type="checkbox" id="edit_book_ebook" name="edit_book_ebook" value="1">
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
                                <button type="button" class="import-image override-style" id="import-1" style="margin-left: 40px;">Importuj</button>
                            </div>
                                           
                            <div class="error-message" style="color: red; text-align: center"></div>
                            <button type="button" onclick="addNewBook()">Dodaj książkę</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    <script>            
    //TODO: dodac to do .js bo sie młyn robi
    //TODO: dodac to do .js bo sie młyn robi
    //TODO: dodac to do .js bo sie młyn robi
    //TODO: dodac to do .js bo sie młyn robi

    // Przyciski z klasy select-image mają działać jak otwieracze wybru plików
        document.querySelectorAll('.import-image').forEach(button => 
        {
            button.addEventListener('click', function () 
            {
                const fileInput = document.getElementById('file-input');

                // Określ target input na podstawie ID przycisku
                let targetInputId;
                if (this.id === 'import-1') 
                {
                    targetInputId = 'zdjecie'; // Pole tekstowe dla pierwszego przycisku
                } 
                else if (this.id === 'import-2') 
                {
                    targetInputId = 'new_zdjecie'; // Pole tekstowe dla drugiego przycisku
                }

                if (!targetInputId) 
                {
                    console.error('Nie znaleziono odpowiedniego pola docelowego dla tego przycisku.');
                    return;
                }

                // Otwórz okno wyboru pliku
                fileInput.click();

                // Nasłuchiwanie na wybór pliku
                fileInput.onchange = async function () 
                {
                    if (fileInput.files.length > 0) 
                    {
                        const file = fileInput.files[0];

                        // Sprawdzenie rozszerzenia pliku
                        const allowedExtensions = ['image/png', 'image/jpeg'];
                        if (!allowedExtensions.includes(file.type)) 
                        {
                            alert('Dozwolone są tylko pliki PNG, JPG lub JPEG.');
                            return;
                        }

                        // Prześlij plik na serwer
                        const formData = new FormData();
                        formData.append('file', file);

                        try 
                        {
                            const response = await fetch('/Biblioteka/php/upload.php', 
                            {
                                method: 'POST',
                                body: formData
                            });

                            const result = await response.json();

                            if (result.success) 
                            {
                                // Wstaw ścieżkę do pola
                                document.getElementById(targetInputId).value = result.path;
                                //alert('Plik przesłany pomyślnie!');
                            } 
                            else 
                            {
                                console.error('Błąd przesyłania pliku: ', result.message);
                                alert('Błąd podczas przesyłania pliku, sprawdź konsolę.');
                            }
                        } 
                        catch (error) 
                        {
                            console.error('Błąd przesyłania pliku:', error);
                            alert('Wystąpił błąd podczas przesyłania pliku.');
                        }

                        // Wyczyść wybór pliku
                        fileInput.value = '';
                    }
                };
            });
        });

        // Wypełnianie listy plików w <select>
        async function loadImageList(selectElementId) 
        {
            try 
            {
                const response = await fetch('/Biblioteka/php/get-images.php'); // Skrypt PHP zwracający listę plików
                const result = await response.json();

                if (result.success) 
                {
                    const selectElement = document.getElementById(selectElementId);
                    selectElement.innerHTML = '<option value="" disabled selected>-- Wybierz zdjęcie --</option>'; // Wyczyść istniejące opcje

                    result.files.forEach(file => 
                    {
                        const option = document.createElement('option');
                        option.value = `Biblioteka/images/${file}`;
                        option.textContent = file;
                        selectElement.appendChild(option);
                    });
                } 
                else 
                {
                    alert('Błąd podczas pobierania listy plików.');
                }
            } 
            catch (error) 
            {
                console.error('Błąd podczas pobierania listy plików:', error);
                alert('Wystąpił błąd podczas pobierania listy plików, sprawdź konsolę.');
            }
        }

        // Obsługa przypisywania ścieżki do odpowiedniego pola tekstowego
        document.querySelectorAll('.select-image').forEach(button => 
        {
            button.addEventListener('click', function () 
            {
                let targetInputId, selectElementId;

                if (this.id === 'select-1') 
                {
                    targetInputId = 'zdjecie';
                    selectElementId = 'image-select-add';
                } 
                else if (this.id === 'select-2') 
                {
                    targetInputId = 'new_zdjecie';
                    selectElementId = 'image-select-edit';
                }

                if (!targetInputId || !selectElementId) 
                {
                    console.error('Nie znaleziono odpowiedniego pola docelowego lub listy wyboru dla tego przycisku.');
                    return;
                }

                const selectedFile = document.getElementById(selectElementId).value;
                if (!selectedFile) 
                {
                    alert('Proszę wybrać zdjęcie z listy.');
                    return;
                }

                document.getElementById(targetInputId).value = selectedFile;
                //alert('Przypisano zdjęcie: ' + selectedFile);
            });
        });
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
                        document.getElementById('book_edition').innerText = data.wydanie_numer_wydania || 'Brak danych';
                        document.getElementById('book_language').innerText = data.wydanie_jezyk || 'Brak danych';
                        document.getElementById('book_pages').innerText = data.ilosc_stron || 'Brak danych';
                        document.getElementById('book_ebook').innerText = data.czy_elektronicznie ? 'Tak' : 'Nie';
                        document.getElementById('book_condition').innerText = data.stan || 'Brak danych';
                        document.getElementById('book_availability').innerText = data.czy_dostepny !== null ? (data.czy_dostepny ? 'Dostępna' : 'Niedostępna') : 'Brak danych'; // czyli nie ma infa o dostepnosci w egzemlarzu
                        const bookImage = document.getElementById('book_image');
                        if (data.ksiazka_zdjecie) 
                        {
                            let imagePath = data.ksiazka_zdjecie;

                            // Spr, czy ścieżka zaczyna się od "Biblioteka/"
                            if (!imagePath.startsWith('/')) {
                                imagePath = '/' + imagePath;
                            }

                            // Poprawiona ścieżka
                            bookImage.src = imagePath;
                            bookImage.style.display = 'block';
                        } 
                        else {
                            bookImage.style.display = 'none';
                        }
                        document.getElementById('infoBookModal').style.display = 'block';
                    })
                    .catch(error => console.error('Błąd:', error));
            }

            //modal dodawania
            function openAddBookModal() {
                document.getElementById('addBookModal').style.display = 'block';
            }            
    
            // modal edycji
            function openEditModal(bookID) {
                fetch(`/Biblioteka/php/bibliotekarz/get_book.php?id=${bookID}`)
                    .then(response => response.json())
                    .then(data => {
                        
                        document.getElementById('edit_book_id').value = data.wydanie_ID;
                        document.getElementById('edit_book_title').value = data.ksiazka_tytul;
                        document.getElementById('new_zdjecie').value = data.ksiazka_zdjecie;
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
                        document.getElementById('edit_book_edition').value = data.wydanie_numer_wydania;
                        document.getElementById('edit_book_language').value = data.wydanie_jezyk;
                        document.getElementById('edit_book_pages').value = data.ilosc_stron;
                        document.getElementById('edit_book_ebook').checked = data.wydanie_czy_elektronicznie ? 1 : 0;

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
                document.getElementById('successPopup').style.display = 'none';
                document.getElementById('addBookModal').style.display = 'none';
                document.getElementById('deleteBookModal').style.display = 'none';
                document.getElementById('editBookModal').style.display = 'none';
            }

            // nasłuchiwanie na załadowanie strony
            document.addEventListener('DOMContentLoaded', () => {
                <?php if (isset($_SESSION['success_message'])): ?>
                    showGlobalSuccessMessage("<?= htmlspecialchars($_SESSION['success_message']); ?>");
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                loadImageList('image-select-add');
                loadImageList('image-select-edit');
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
                //TODO: dodac edycje zdj
                const data = {
                    wydanie_ID: document.getElementById('edit_book_id').value,
                    ksiazka_tytul: document.getElementById('edit_book_title').value,
                    ksiazka_zdjecie: document.getElementById('new_zdjecie').value,
                    autor_imie: document.getElementById('edit_book_author_first').value,
                    autor_nazwisko: document.getElementById('edit_book_author_last').value,
                    gatunek_ID: document.getElementById('edit_book_genre').value,
                    wydanie_ISBN: document.getElementById('edit_book_isbn').value,
                    wydanie_data_wydania: document.getElementById('edit_book_release_date').value,
                    wydanie_numer_wydania: document.getElementById('edit_book_edition').value,
                    wydanie_jezyk: document.getElementById('edit_book_language').value,
                    wydanie_ilosc_stron: document.getElementById('edit_book_pages').value,
                    wydanie_czy_elektronicznie: document.getElementById('edit_book_ebook').checked ? 1 : 0
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

            //dodawnaie ksiazki
            function addNewBook() 
            {
                const data = {
                    ksiazka_tytul: document.getElementById('bookTitle').value,
                    autor_imie: document.getElementById('authorFirstName').value,
                    autor_nazwisko: document.getElementById('authorLastName').value,
                    gatunek: document.getElementById('genre').value,
                    wydawnictwo: document.getElementById('publisher').value,                    
                    wydanie_ISBN: document.getElementById('bookISBN').value,
                    wydanie_data_wydania: document.getElementById('releaseDate').value,
                    wydanie_numer_wydania: document.getElementById('editionNumber').value,
                    wydanie_jezyk: document.getElementById('language').value,
                    wydanie_ilosc_stron: document.getElementById('pages').value,
                    wydanie_czy_elektronicznie: document.getElementById('isElectronic').checked ? 1 : 0,
                    zdjecie: document.getElementById('zdjecie').value
                };

                fetch('/Biblioteka/php/bibliotekarz/add_book.php', {
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
                        const errorContainer = document.querySelector('#addBookForm .error-message');
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