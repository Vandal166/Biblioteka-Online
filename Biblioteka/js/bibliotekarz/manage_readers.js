// Otwieranie modala i ładowanie danych książki
function openInfoModal(bookID) {
    fetch(`/Biblioteka/php/bibliotekarz/reader_mgmt/get_reader.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            
            document.getElementById('book_id').value = data.czytelnik.czytelnik_ID || '';
            document.getElementById('reader_name').innerText = data.czytelnik.czytelnik_imie || 'Brak danych';
            document.getElementById('reader_surname').innerText = data.czytelnik.czytelnik_nazwisko || 'Brak danych';
            document.getElementById('reader_card_number').innerText = data.czytelnik.czytelnik_nr_karty || 'Brak danych';
            document.getElementById('reader_email').innerText = data.czytelnik.czytelnik_email || 'Brak danych';
            document.getElementById('reader_phone').innerText = data.czytelnik.czytelnik_telefon || 'Brak danych';
            // Handle additional data if needed
            if (data.additionalData && data.additionalData.length > 0) {
                // Check if there are any active wypozyczenie
                const hasActiveRenting = data.additionalData.some(item => !item.data_oddania);
                const rentingDetailsButtonContainer = document.getElementById('rentingDetailsButtonContainer');
                rentingDetailsButtonContainer.innerHTML = ''; // Clear previous content

                if (hasActiveRenting) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.innerText = 'Pokaż wypożyczenia/rezerwacje';
                    button.onclick = () => showReaderRentingDetails(bookID);
                    rentingDetailsButtonContainer.appendChild(button);
                }
            }
            document.getElementById('infoBookModal').style.display = 'block';
            // wylaczenie scrolla na stronie ale dozwolone w modalu
            document.body.style.overflow = 'hidden';
        })
        .catch(error => console.error('Błąd:', error));
}

function showReaderRentingDetails(readerID) {
    fetch(`/Biblioteka/php/bibliotekarz/reader_mgmt/get_reader.php?id=${readerID}`)
        .then(response => response.json())
        .then(data => {
            if (data.additionalData && data.additionalData.length > 0) {
                const content = document.getElementById('readerRentingDetailsContent');
                content.innerHTML = ''; // Clear previous content

                data.additionalData.forEach(item => {
                    const rentingDetail = document.createElement('div');
                    rentingDetail.classList.add('renting-detail');
                    rentingDetail.innerHTML = `
                        <p><strong>Data wypożyczenia:</strong> ${item.data_wypozyczenia}</p>
                        <p><strong>Termin oddania:</strong> ${item.termin_oddania}</p>
                        <p><strong>Data oddania:</strong> ${item.data_oddania || 'Nie oddano'}</p>
                        <p><strong>Tytuł książki:</strong> ${item.ksiazka_tytul}</p>
                        <p><strong>Autor:</strong> ${item.autor_imie} ${item.autor_nazwisko}</p>
                        <p><strong>Wydawnictwo:</strong> ${item.wydawnictwo}</p>
                        <p><strong>Stan egzemplarza:</strong> ${item.stan}</p>
                    `;
                    content.appendChild(rentingDetail);
                });

                document.getElementById('readerRentingDetailsModal').style.display = 'block';
                // Disable page scroll but allow in modal
                document.body.style.overflow = 'hidden';
            } else {
                console.log('Brak wypożyczeń dla tego czytelnika');
            }
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
    fetch(`/Biblioteka/php/bibliotekarz/exemplar_mgmt/get_exemplar.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            
            document.getElementById('edit_book_id').value = data.egzemplarz_ID;
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
            document.getElementById('edit_book_pages').innerText = data.ilosc_stron || 'Brak danych';
            document.getElementById('edit_isAvailable').checked = data.czy_dostepny ? 1 : 0;
            document.getElementById('edit_book_condition').value = data.stan;
            //document.getElementById('edit_book_edition').value = data.wydanie_numer_wydania;
            // Fetch the list of wydanie records and populate the select element
            fetch('php/bibliotekarz/exemplar_mgmt/fetch_wydanie_list.php')
                .then(response => response.json())
                .then(listData => {
                    const selectElement = document.getElementById('edit_editionNumberSelect');
                    selectElement.innerHTML = ''; // Clear existing options
                    listData.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.numer_wydania;
                        option.textContent = `${item.tytul} - ${item.autor_imie} ${item.autor_nazwisko}`;
                        selectElement.appendChild(option);
                    });

                    // Set the selected option
                    const options = selectElement.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value === data.wydanie_numer_wydania) {
                            options[i].selected = true;
                            break;
                        }
                    }

                    selectElement.style.display = 'block';
                    document.getElementById('edit_editionNumberInput').style.display = 'none';
                });
            document.getElementById('editBookModal').style.display = 'block';
            document.getElementById('editBookForm').querySelector('.error-message').style.display = 'none';
            // wylaczenie scrolla na stronie ale dozwolone w modalu
            document.body.style.overflow = 'hidden';
        });
}

// modal usuwania
function openDeleteModal(bookID) {

    fetch(`/Biblioteka/php/bibliotekarz/exemplar_mgmt/get_exemplar.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            if (data.ksiazka_tytul != null) {
                const deleteModal = document.getElementById('deleteBookModal');
                document.getElementById('deleteBookTitle').innerText = data.ksiazka_tytul;  
                document.getElementById('deleteBookCondition').innerText = data.stan; 
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
    document.getElementById('readerRentingDetailsModal').style.display = 'none';

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
        egzemplarz_ID: document.getElementById('edit_book_id').value,
        wydanie_numer_wydania: document.getElementById('edit_editionNumberSelect').value || document.getElementById('edit_editionNumberInput').value,
        stan: document.getElementById('edit_book_condition').value,
        egzemplarz_czy_dostepny: document.getElementById('edit_isAvailable').checked ? 1 : 0
    };

    fetch('/Biblioteka/php/bibliotekarz/exemplar_mgmt/update_exemplar.php', {
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
        egzemplarz_stan: document.getElementById('add_book_condition').value,
        egzemplarz_czy_dostepny: document.getElementById('add_isAvailable').checked ? 1 : 0
    };

    fetch('/Biblioteka/php/bibliotekarz/exemplar_mgmt/add_exemplar.php', {
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
    fetch(`/Biblioteka/php/bibliotekarz/exemplar_mgmt/delete_exemplar.php?id=${exemplarID}`, { 
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