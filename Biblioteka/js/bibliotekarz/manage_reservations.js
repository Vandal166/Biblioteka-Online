// Otwieranie modala i ładowanie danych książki
function openInfoModal(bookID) {
    fetch(`/Biblioteka/php/bibliotekarz/reservation_mgmt/get_reservation.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            
            document.getElementById('book_id').value = data.rezerwacja_ID || '';
            document.getElementById('reader_name').innerText = data.czytelnik_imie || 'Brak danych';
            document.getElementById('reader_surname').innerText = data.czytelnik_nazwisko || 'Brak danych';
            document.getElementById('reader_card_number').innerText = data.czytelnik_nr_karty || 'Brak danych';
            document.getElementById('reader_email').innerText = data.czytelnik_email || 'Brak danych';
            document.getElementById('reader_phone').innerText = data.czytelnik_telefon || 'Brak danych';

            document.getElementById('book_title').innerText = data.ksiazka_tytul || 'Brak danych';
            const bookImage = document.getElementById('book_image');
            if (data.ksiazka_zdjecie)
            {
                let imagePath = data.ksiazka_zdjecie;

                // Spr, czy ścieżka zaczyna się od "Biblioteka/"
                if (!imagePath.startsWith('/'))
                {
                    imagePath = '/' + imagePath;
                }

                // Poprawiona ścieżka
                bookImage.src = imagePath;
                bookImage.style.display = 'block';
            }
            else
            {
                bookImage.style.display = 'none';
            }
            document.getElementById('book_author').innerText = `${data.autor_imie || ''} ${data.autor_nazwisko || ''}`;
            document.getElementById('book_genre').innerText = data.gatunek || 'Brak danych';
            document.getElementById('book_publisher').innerText = data.wydawnictwo || 'Brak danych';
            document.getElementById('book_language').innerText = data.jezyk || 'Brak danych';
            document.getElementById('book_pages').innerText = data.ilosc_stron || 'Brak danych';
            document.getElementById('book_reservation_date').innerText = data.data_rezerwacji || 'Brak danych';
            document.getElementById('book_is_given').innerText = data.czy_wydana !== null ? (data.czy_wydana ? 'Wydana' : 'Niewydana') : 'Brak danych';
            
            document.getElementById('infoBookModal').style.display = 'block';
            // wylaczenie scrolla na stronie ale dozwolone w modalu
            document.body.style.overflow = 'hidden';
        })
        .catch(error => console.error('Błąd:', error));
}

// modal edycji
function openEditModal(bookID) {
    fetch(`/Biblioteka/php/bibliotekarz/reservation_mgmt/get_reservation.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_reservation_id').value = data.rezerwacja_ID || '';
            document.getElementById('edit_reader_name').innerText = data.czytelnik_imie || 'Brak danych';
            document.getElementById('edit_reader_surname').innerText = data.czytelnik_nazwisko || 'Brak danych';
            document.getElementById('edit_reader_card_number').innerText = data.czytelnik_nr_karty || 'Brak danych';
            document.getElementById('edit_book_title').innerText = data.ksiazka_tytul || 'Brak danych';
            const bookImage = document.getElementById('edit_book_image');
            if (data.ksiazka_zdjecie)
            {
                let imagePath = data.ksiazka_zdjecie;

                // Spr, czy ścieżka zaczyna się od "Biblioteka/"
                if (!imagePath.startsWith('/'))
                {
                    imagePath = '/' + imagePath;
                }

                // Poprawiona ścieżka
                bookImage.src = imagePath;
                bookImage.style.display = 'block';
            }
            else
            {
                bookImage.style.display = 'none';
            }
            document.getElementById('edit_book_author').innerText = `${data.autor_imie || ''} ${data.autor_nazwisko || ''}`;
            document.getElementById('edit_book_genre').innerText = data.gatunek || 'Brak danych';
            document.getElementById('edit_book_publisher').innerText = data.wydawnictwo || 'Brak danych';
            document.getElementById('edit_book_language').innerText = data.jezyk || 'Brak danych';
            document.getElementById('edit_book_pages').innerText = data.ilosc_stron || 'Brak danych';

            document.getElementById('edit_reservation_date').value = data.data_rezerwacji || '';
            document.getElementById('edit_is_given').checked = data.czy_wydana ? 1 : 0;

            document.getElementById('editBookModal').style.display = 'block';
            document.getElementById('editBookForm').querySelector('.error-message').style.display = 'none';
            // wylaczenie scrolla na stronie ale dozwolone w modalu
            document.body.style.overflow = 'hidden';
        });
}

// modal usuwania
function openDeleteModal(bookID) {

    fetch(`/Biblioteka/php/bibliotekarz/reservation_mgmt/get_reservation.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            if (data.ksiazka_tytul != null) {
                const deleteModal = document.getElementById('deleteBookModal');
                document.getElementById('deleteBookTitle').textContent = data.ksiazka_tytul;
                document.getElementById('deleteBookReader').textContent = `${data.czytelnik_imie} ${data.czytelnik_nazwisko}`;
                deleteModal.style.display = 'flex';
                window.currentBookID = bookID;
            }
        })
        .catch(error => console.error('Błąd:', error));
}

// zapisanie zmian po edycji
function saveBookChanges() 
{   // zmienne z lewej to nazwy z 'data' z fetcha, czyli z bazy danych
    // console.log(data) zeby zobaczyc jak dane sa zapisane w zmiennej 
    // i uniknac bledu 'undefined array key'
    const data = {        
        rezerwacja_ID: document.getElementById('edit_reservation_id').value,
        rezerwacja_data_rezerwacji: document.getElementById('edit_reservation_date').value,
        rezerwacja_czy_wydana: document.getElementById('edit_is_given').checked ? 1 : 0
    };

    fetch('/Biblioteka/php/bibliotekarz/reservation_mgmt/update_reservation.php', {
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
function addNewExemplar() {
    const data = {
        wydanie_numer_wydania: document.getElementById('editionNumberSelect').value || document.getElementById('editionNumberInput').value,
        czytelnik_nr_karty: document.getElementById('cardNumSelect').value || document.getElementById('cardNumInput').value,
        rezerwacja_data_rezerwacji: document.getElementById('add_reservation_date').value,
        rezerwacja_czy_wydana: document.getElementById('add_is_given').checked ? 1 : 0
    };
    
    fetch('/Biblioteka/php/bibliotekarz/reservation_mgmt/add_reservation.php', {
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
    const exemplarID = window.currentBookID;
    fetch(`/Biblioteka/php/bibliotekarz/reservation_mgmt/delete_reservation.php?id=${exemplarID}`, { 
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

function fetchEditionData(editionNumber) {
    if (editionNumber) { // Jeśli ID nie jest puste
        fetch(`php/bibliotekarz/exemplar_mgmt/fetch_wydanie.php?editionNumber=${editionNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editModalDetails').style.display = 'block';
                    // Ustawienie wartości pól formularza
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
    if (editionNumber) { // Jeśli ID nie jest puste
        fetch(`php/bibliotekarz/exemplar_mgmt/fetch_wydanie.php?editionNumber=${editionNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
                    document.getElementById('add_reader_phone').textContent = data.data.czytelnik_telefon;
                } else {
                    // czyszczenie pola formularza w przypadku błędu
                    document.getElementById('add_reader_name').textContent = '';
                    document.getElementById('add_reader_surname').textContent = '';
                    document.getElementById('add_reader_card_number').textContent = '';
                    document.getElementById('add_reader_email').textContent = '';
                    document.getElementById('add_reader_phone').textContent = '';
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
        document.getElementById('add_reader_phone').textContent = '';
        document.getElementById('addModalReaderDetails').style.display = 'none';                    

    }
} 