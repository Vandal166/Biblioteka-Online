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
                const hasActiveRenting = data.additionalData.some(item => !item.data_oddania); // jesli data oddania jest pusta to wypozyczenie jest aktywne
                document.getElementById('rentingDetailsButtonContainer').style.display = 'block';
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
            else
                document.getElementById('rentingDetailsButtonContainer').style.display = 'none';
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
                        <p><strong>Data wypożyczenia:</strong> ${item.data_wypozyczenia || 'Brak danych'}</p>
                        <p><strong>Termin oddania:</strong> ${item.termin_oddania || 'Brak danych'}</p>
                        <p><strong>Data oddania:</strong> ${item.data_oddania || 'Nie oddano'}</p>
                        <p><strong>Tytuł książki:</strong> ${item.ksiazka_tytul || 'Brak danych'}</p>
                        <p><strong>Autor:</strong> ${item.autor_imie} ${item.autor_nazwisko}</p>
                        <p><strong>Wydawnictwo:</strong> ${item.wydawnictwo}</p>
                        <p><strong>Stan egzemplarza:</strong> ${item.stan || 'Brak danych'}</p>
                        <hr style="border: 0; height: 1.5px; background: linear-gradient(to right, #fff, #000, #fff); margin: 20px 0;">
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

// modal edycji
function openEditModal(bookID) {
    fetch(`/Biblioteka/php/bibliotekarz/reader_mgmt/get_reader.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            
            document.getElementById('edit_reader_id').value = data.czytelnik.czytelnik_ID;
            document.getElementById('edit_reader_name').value = data.czytelnik.czytelnik_imie;
            document.getElementById('edit_reader_surname').value = data.czytelnik.czytelnik_nazwisko;
            document.getElementById('edit_card_number').value = data.czytelnik.czytelnik_nr_karty;
            document.getElementById('edit_reader_email').value = data.czytelnik.czytelnik_email;
            document.getElementById('edit_reader_phone').value = data.czytelnik.czytelnik_telefon;
            
            document.getElementById('editBookModal').style.display = 'block';
            document.getElementById('editBookForm').querySelector('.error-message').style.display = 'none';
            // wylaczenie scrolla na stronie ale dozwolone w modalu
            document.body.style.overflow = 'hidden';
        });
}

// modal usuwania
function openDeleteModal(bookID) {

    fetch(`/Biblioteka/php/bibliotekarz/reader_mgmt/get_reader.php?id=${bookID}`)
        .then(response => response.json())
        .then(data => {
            if (data.czytelnik) {
                const deleteModal = document.getElementById('deleteBookModal');
                document.getElementById('deleteBookReader').innerText = data.czytelnik.czytelnik_imie + ' ' + data.czytelnik.czytelnik_nazwisko;  
                
                deleteModal.style.display = 'flex';  
                window.currentBookID = bookID;  
            }
        })
        .catch(error => console.error('Błąd:', error));
}

    
function generate_card_number() {
    fetch('/Biblioteka/php/bibliotekarz/reader_mgmt/generate_card_number.php')
        .then(response => response.json())
        .then(data => {
            if (data.card_number) {
                document.getElementById('edit_card_number').value = data.card_number;
            } else {
                console.error('Błąd podczas generowania numeru karty:', data.error);
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
        czytelnik_ID: document.getElementById('edit_reader_id').value,
        czytelnik_imie: document.getElementById('edit_reader_name').value,
        czytelnik_nazwisko: document.getElementById('edit_reader_surname').value,
        czytelnik_nr_karty: document.getElementById('edit_card_number').value,
        czytelnik_email: document.getElementById('edit_reader_email').value,
        czytelnik_telefon: document.getElementById('edit_reader_phone').value
    };

    fetch('/Biblioteka/php/bibliotekarz/reader_mgmt/update_reader.php', {
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
    fetch(`/Biblioteka/php/bibliotekarz/reader_mgmt/delete_reader.php?id=${exemplarID}`, { 
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