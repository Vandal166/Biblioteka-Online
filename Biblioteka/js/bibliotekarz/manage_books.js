// Otwieranie modala i ładowanie danych książki
function openInfoModal(bookID) {
    fetch(`/Biblioteka/php/bibliotekarz/book_mgmt/get_book.php?id=${bookID}`)
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
            const bookPdfElement = document.getElementById('book_pdf');
            if (data.wydanie_pdf) {
                bookPdfElement.innerText = 'Link do pliku PDF';

                // Spr, czy ścieżka zaczyna się od "Biblioteka/"
                if (!data.wydanie_pdf.startsWith('/'))
                {
                    data.wydanie_pdf = '/' + data.wydanie_pdf;
                }
                
                bookPdfElement.href = data.wydanie_pdf;
                bookPdfElement.style.pointerEvents = 'auto'; // Enable link functionality
            } else {
                bookPdfElement.innerText = 'Brak danych';
                bookPdfElement.href = '#';
                bookPdfElement.style.pointerEvents = 'none'; // Disable link functionality
            }
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
            // wylaczenie scrolla na stronie ale dozwolone w modalu
            document.body.style.overflow = 'hidden';
        })
        .catch(error => console.error('Błąd:', error));
}       

// modal edycji
function openEditModal(bookID) {
    fetch(`/Biblioteka/php/bibliotekarz/book_mgmt/get_book.php?id=${bookID}`)
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
            document.getElementById('new_pdf').value = data.wydanie_pdf;

            document.getElementById('editBookModal').style.display = 'block';
            document.getElementById('editBookForm').querySelector('.error-message').style.display = 'none';
            // wylaczenie scrolla na stronie ale dozwolone w modalu
            document.body.style.overflow = 'hidden';
        });
}

// modal usuwania
function openDeleteModal(bookID) {

    fetch(`/Biblioteka/php/bibliotekarz/book_mgmt/get_book.php?id=${bookID}`)
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

// zapisanie zmian po edycji
function saveBookChanges() 
{    
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
        wydanie_pdf: document.getElementById('new_pdf').value
    };

    fetch('/Biblioteka/php/bibliotekarz/book_mgmt/update_book.php', {
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
        wydanie_pdf: document.getElementById('pdf').value,
        zdjecie: document.getElementById('zdjecie').value
    };

    fetch('/Biblioteka/php/bibliotekarz/book_mgmt/add_book.php', {
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
    fetch(`/Biblioteka/php/bibliotekarz/book_mgmt/delete_book.php?id=${bookID}`, { 
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