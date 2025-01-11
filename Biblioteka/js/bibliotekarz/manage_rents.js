// Otwieranie modala i ładowanie danych książki
function openInfoModal(bookID) {
    fetch(`/Biblioteka/php/bibliotekarz/rent_mgmt/get_rented.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            
            document.getElementById('book_id').value = data.wypozyczenie_ID || '';
            document.getElementById('book_title').innerText = data.ksiazka_tytul || 'Brak danych';
            document.getElementById('book_author').innerText = `${data.autor_imie || ''} ${data.autor_nazwisko || ''}`;
            document.getElementById('book_isbn').innerText = data.wydanie_ISBN || 'Brak danych';
            document.getElementById('book_edition').innerText = data.wydanie_nr_wydania || 'Brak danych';
            
            
            document.getElementById('book_language').innerText = data.wydanie_jezyk || 'Brak danych';
            document.getElementById('book_pages').innerText = data.wydanie_ilosc_stron || 'Brak danych';
            document.getElementById('book_reader').innerText = `${data.czytelnik_imie || ''} ${data.czytelnik_nazwisko || ''}`;
            document.getElementById('book_card_number').innerText = data.czytelnik_nr_karty || 'Brak danych';
            document.getElementById('book_librarian').innerText = `${data.pracownik_imie || ''} ${data.pracownik_nazwisko || ''}`;
            document.getElementById('book_rent_date').innerText = data.data_wypozyczenia || 'Brak danych';
            document.getElementById('book_due_date').innerText = data.termin_oddania || 'Brak danych';
            document.getElementById('book_return_date').innerText = data.data_oddania || 'Brak danych';
            
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

//modal dodawania
function openAddBookModal() {
    document.getElementById('addBookModal').style.display = 'block';
    // wylaczenie scrolla na stronie ale dozwolone w modalu
    document.body.style.overflow = 'hidden';
}            

// modal edycji
function openEditModal(bookID) {
    fetch(`/Biblioteka/php/bibliotekarz/rent_mgmt/get_rented.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            
            document.getElementById('edit_renting_id').value = data.wypozyczenie_ID;
            document.getElementById('edit_reader_name').innerText = data.czytelnik_imie || 'Brak danych';
            document.getElementById('edit_reader_surname').innerText = data.czytelnik_nazwisko || 'Brak danych';
            document.getElementById('edit_reader_card_number').innerText = data.czytelnik_nr_karty || 'Brak danych';
            document.getElementById('edit_reader_email').innerText = data.czytelnik_email || 'Brak danych';

            document.getElementById('edit_book_title').innerText = data.ksiazka_tytul;
            const bookImage = document.getElementById('edit_book_image');
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
            document.getElementById('edit_book_author').innerText = `${data.autor_imie || ''} ${data.autor_nazwisko || ''}`;
            document.getElementById('edit_book_genre').innerText = data.gatunek || 'Brak danych';
            document.getElementById('edit_book_isbn').innerText = data.wydanie_ISBN || 'Brak danych';
            document.getElementById('edit_book_release_date').innerText = data.wydanie_data_wydania || 'Brak danych';
            document.getElementById('edit_book_language').innerText = data.wydanie_jezyk || 'Brak danych';
            document.getElementById('edit_book_pages').innerText = data.wydanie_ilosc_stron || 'Brak danych';
            
            document.getElementById('edit_due_date').value = data.termin_oddania || '';
            document.getElementById('edit_return_date').value = data.data_oddania || '';
            
            
            document.getElementById('editBookModal').style.display = 'block';
            document.getElementById('editBookForm').querySelector('.error-message').style.display = 'none';
            // wylaczenie scrolla na stronie ale dozwolone w modalu
            document.body.style.overflow = 'hidden';
        });
}

// modal usuwania
function openDeleteModal(bookID) {

    fetch(`/Biblioteka/php/bibliotekarz/rent_mgmt/get_rented.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            if (data.ksiazka_tytul != null) {
                const deleteModal = document.getElementById('deleteBookModal');
                document.getElementById('deleteBookReader').innerText = data.czytelnik_imie + ' ' + data.czytelnik_nazwisko;
                document.getElementById('deleteBookTitle').innerText = data.ksiazka_tytul;  
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
    document.body.style.overflow = 'auto';
}

// Funkcja wyświetlająca globalny pop-up sukcesu
function showGlobalSuccessMessage(message) {
    const popup = document.getElementById('successPopup');
    const messageContainer = document.getElementById('successPopupMessage');
    messageContainer.textContent = message;
    popup.style.display = 'flex';
}

// zapisanie zmian po edycji
function saveBookChanges() 
{   // zmienne z lewej to nazwy z 'data' z fetcha, czyli z bazy danych
    // console.log(data) zeby zobaczyc jak dane sa zapisane w zmiennej 
    // i uniknac bledu 'undefined array key'
    const data = {        
        wypozyczenie_ID: document.getElementById('edit_renting_id').value,
        termin_zwrotu: document.getElementById('edit_due_date').value,
        data_oddania: document.getElementById('edit_return_date').value
    };

    fetch('/Biblioteka/php/bibliotekarz/rent_mgmt/update_rented.php', {
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
function addNewRenting() {
    const data = {
        czytelnik_nr_karty: document.getElementById('cardNumSelect').value || document.getElementById('cardNumInput').value,
        wydanie_numer_wydania: document.getElementById('exemplarSelect').value || document.getElementById('exemplarInput').value,
        pracownik_ID: document.getElementById('add_librarian_ID').value,
        data_wypozyczenia: document.getElementById('add_rent_date').value,
        termin_oddania: document.getElementById('add_due_date').value
    };

    fetch('/Biblioteka/php/bibliotekarz/rent_mgmt/add_renting.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            location.reload();
        } else {
            const errorContainer = document.querySelector('#addBookForm .error-message');
            if (errorContainer) {
                errorContainer.textContent = data.error;
                errorContainer.style.display = 'block';
            }
        }
    }).catch(error => console.error('Błąd:', error));
}
// usuawanie książki
function deleteBook() 
{
    const rentedID = window.currentBookID;
    fetch(`/Biblioteka/php/bibliotekarz/rent_mgmt/delete_rented.php?id=${rentedID}`, { 
        method: 'POST' 
    }).then(response => response.json())
        .then(data => {
            if (data.success) 
            {                            
                location.reload();
            }
            else
            {                            
                console.error(data.error);
            }
        });
}

function fetchEditionData(editionNumber) {
    if (editionNumber) 
    {
        fetch(`php/bibliotekarz/rent_mgmt/fetch_wydanie.php?editionNumber=${editionNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editModalReaderDetails').style.display = 'block';
                    document.getElementById('editModalDetails').style.display = 'block';
                    // Ustawienie wartości pól formularza
                    document.getElementById('edit_reader_name').textContent = data.data.czytelnik_imie;
                    document.getElementById('edit_reader_surname').textContent = data.data.czytelnik_nazwisko;
                    document.getElementById('edit_reader_card_number').textContent = data.data.czytelnik_nr_karty;
                    document.getElementById('edit_reader_email').textContent = data.data.czytelnik_email;

                    document.getElementById('edit_book_title').textContent = data.data.tytul;
                    document.getElementById('edit_book_author').textContent = data.data.autor_imie + ' ' + data.data.autor_nazwisko;
                    document.getElementById('edit_book_genre').textContent = data.data.gatunek;
                    document.getElementById('edit_book_isbn').textContent = data.data.ISBN;
                    document.getElementById('edit_book_release_date').textContent = data.data.data_wydania;
                    document.getElementById('edit_book_language').textContent = data.data.jezyk;
                    document.getElementById('edit_book_pages').textContent = data.data.ilosc_stron;
                    const bookImage = document.getElementById('edit_book_image');
                    if (data.data.zdjecie) {
                        let imagePath = data.data.zdjecie;

                        // Spr, czy ścieżka zaczyna się od "Biblioteka/"
                        if (!imagePath.startsWith('/')) {
                            imagePath = '/' + imagePath;
                        }

                        // Poprawiona ścieżka
                        bookImage.src = imagePath;
                        bookImage.style.display = 'block';
                    } else {
                        bookImage.style.display = 'none';
                    }

                    
                } else {
                    // czyszczenie pola formularza w przypadku błędu
                    document.getElementById('edit_reader_name').textContent = '';
                    document.getElementById('edit_reader_surname').textContent = '';
                    document.getElementById('edit_reader_card_number').textContent = '';
                    document.getElementById('edit_reader_email').textContent = '';
                    document.getElementById('edit_book_title').textContent = '';
                    document.getElementById('edit_book_author').textContent = '';
                    document.getElementById('edit_book_genre').textContent = '';
                    document.getElementById('edit_book_isbn').textContent = '';
                    document.getElementById('edit_book_release_date').textContent = '';
                    document.getElementById('edit_book_language').textContent = '';
                    document.getElementById('edit_book_pages').textContent = '';
                    document.getElementById('edit_book_image').style.display = 'none';
                    document.getElementById('editModalDetails').style.display = 'none';

                    console.warn(data.message);
                }
            })
            .catch(error => {
                console.error('Błąd podczas pobierania danych:', error);
            });
    } else {
        // Jeśli ID jest puste, czyścimy pola formularza
        document.getElementById('edit_reader_name').textContent = '';
        document.getElementById('edit_reader_surname').textContent = '';
        document.getElementById('edit_reader_card_number').textContent = '';
        document.getElementById('edit_reader_email').textContent = '';
        document.getElementById('edit_book_title').textContent = '';
        document.getElementById('edit_book_author').textContent = '';
        document.getElementById('edit_book_genre').textContent = '';
        document.getElementById('edit_book_isbn').textContent = '';
        document.getElementById('edit_book_release_date').textContent = '';
        document.getElementById('edit_book_language').textContent = '';
        document.getElementById('edit_book_pages').textContent = '';
        document.getElementById('edit_book_image').style.display = 'none';    
        document.getElementById('editModalDetails').style.display = 'none';
    }
}  

function fetchAdditionData(editionNumber) {
    if (editionNumber) 
    { 
        fetch(`php/bibliotekarz/rent_mgmt/fetch_wydanie.php?editionNumber=${editionNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    document.getElementById('addModalDetails').style.display = 'block';
                    // Ustawienie wartości pól formularza
                    document.getElementById('add_book_title').textContent = data.data.tytul;
                    document.getElementById('add_book_author').textContent = data.data.autor_imie + ' ' + data.data.autor_nazwisko;
                    document.getElementById('add_book_genre').textContent = data.data.gatunek;
                    document.getElementById('add_book_isbn').textContent = data.data.ISBN;
                    document.getElementById('add_book_release_date').textContent = data.data.data_wydania;
                    document.getElementById('add_book_language').textContent = data.data.jezyk;
                    document.getElementById('add_book_pages').textContent = data.data.ilosc_stron;
                    const bookImage = document.getElementById('add_book_image');
                    if (data.data.zdjecie) {
                        let imagePath = data.data.zdjecie;

                        // Spr, czy ścieżka zaczyna się od "Biblioteka/"
                        if (!imagePath.startsWith('/')) {
                            imagePath = '/' + imagePath;
                        }

                        // Poprawiona ścieżka
                        bookImage.src = imagePath;
                        bookImage.style.display = 'block';
                    } else {
                        bookImage.style.display = 'none';
                    }
                } else {
                    // czyszczenie pola formularza w przypadku błędu
                    document.getElementById('add_book_title').textContent = '';
                    document.getElementById('add_book_author').textContent = '';
                    document.getElementById('add_book_genre').textContent = '';
                    document.getElementById('add_book_isbn').textContent = '';
                    document.getElementById('add_book_release_date').textContent = '';
                    document.getElementById('add_book_language').textContent = '';
                    document.getElementById('add_book_pages').textContent = '';
                    document.getElementById('add_book_image').style.display = 'none';
                    document.getElementById('addModalDetails').style.display = 'none';

                    console.warn(data.message);
                }
            })
            .catch(error => {
                console.error('Błąd podczas pobierania danych:', error);
            });
    } else {
        // Jeśli ID jest puste, czyścimy pola formularza
        document.getElementById('add_book_title').textContent = '';
        document.getElementById('add_book_author').textContent = '';
        document.getElementById('add_book_genre').textContent = '';
        document.getElementById('add_book_isbn').textContent = '';
        document.getElementById('add_book_release_date').textContent = '';
        document.getElementById('add_book_language').textContent = '';
        document.getElementById('add_book_pages').textContent = '';
        document.getElementById('add_book_image').style.display = 'none';    
        document.getElementById('addModalDetails').style.display = 'none';
    }
}  

function fetchAdditionReaderData(cardNumber) {
    if (cardNumber) 
    { 
        fetch(`php/bibliotekarz/rent_mgmt/fetch_czytelnik.php?cardNumber=${cardNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    document.getElementById('addModalReaderDetails').style.display = 'block';
                    // Ustawienie wartości pól formularza
                    document.getElementById('add_reader_name').textContent = data.data.czytelnik_imie;
                    document.getElementById('add_reader_surname').textContent = data.data.czytelnik_nazwisko;
                    document.getElementById('add_reader_card_number').textContent = data.data.czytelnik_nr_karty;
                    document.getElementById('add_reader_email').textContent = data.data.czytelnik_email;
                } else {
                    // czyszczenie pola formularza w przypadku błędu
                    document.getElementById('add_reader_name').textContent = '';
                    document.getElementById('add_reader_surname').textContent = '';
                    document.getElementById('add_reader_card_number').textContent = '';
                    document.getElementById('add_reader_email').textContent = '';
                    document.getElementById('addModalReaderDetails').style.display = 'none';                    

                    console.warn(data.message);
                }
            })
            .catch(error => {
                console.error('Błąd podczas pobierania danych:', error);
            });
    } else {
        // Jeśli ID jest puste, czyścimy pola formularza
        document.getElementById('add_reader_name').textContent = '';
        document.getElementById('add_reader_surname').textContent = '';
        document.getElementById('add_reader_card_number').textContent = '';
        document.getElementById('add_reader_email').textContent = '';
        document.getElementById('addModalReaderDetails').style.display = 'none';                    

    }
}  