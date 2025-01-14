document.querySelectorAll('.select-pdf').forEach(button => {
    button.addEventListener('click', function () {
        let selectElementId, targetInputId;

        // Określenie odpowiednich pól na podstawie ID przycisku
        if (this.id === 'select-pdf') {
            selectElementId = 'pdf-select';
            targetInputId = 'pdf';
        } else if (this.id === 'new_select-pdf') {
            selectElementId = 'new_pdf-select';
            targetInputId = 'new_pdf';
        } else {
            console.error('Nie znaleziono odpowiednich pól dla tego przycisku.');
            return;
        }

        const selectElement = document.getElementById(selectElementId);
        const targetInput = document.getElementById(targetInputId);

        // Pobranie wybranej wartości z <select>
        const selectedFile = selectElement.value;

        if (!selectedFile) {
            //alert('Proszę wybrać plik PDF z listy.');
            console.error('Proszę wybrać plik PDF z listy.');
            return;
        }

        // Wstawienie wybranej ścieżki do pola tekstowego
        targetInput.value = selectedFile;
        //alert(`Wybrano plik: ${selectedFile}`);
        console.log(`Wybrano plik: ${selectedFile}`);
    });
});

document.querySelectorAll('.import-pdf').forEach(button => {
    button.addEventListener('click', function () {
        let fileInputId, targetInputId;

        // Określenie odpowiednich pól na podstawie ID przycisku
        if (this.id === 'import-pdf') {
            fileInputId = 'file-input-pdf';
            targetInputId = 'pdf';
        } else if (this.id === 'new_import-pdf') {
            fileInputId = 'file-input-pdf';
            targetInputId = 'new_pdf';
        } else {
            console.error('Nie znaleziono odpowiednich pól dla tego przycisku.');
            return;
        }

        const fileInput = document.getElementById(fileInputId);
        const targetInput = document.getElementById(targetInputId);

        // Symulacja kliknięcia na input typu "file"
        fileInput.click();

        // Nasłuchiwanie zmiany na polu input
        fileInput.onchange = async function () {
            if (fileInput.files.length === 0) {
                //alert('Proszę wybrać plik PDF do importu.');
                console.error('Proszę wybrać plik PDF do importu.');
                return;
            }

            const file = fileInput.files[0];

            if (file.type !== 'application/pdf') {
                //alert('Wybrany plik musi być w formacie PDF.');
                console.error('Wybrany plik musi być w formacie PDF.');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('/Biblioteka/php/upload-pdf.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                if (result.success) {
                    targetInput.value = result.path;
                    //alert(`Plik PDF został przesłany pomyślnie: ${result.path}`);
                    console.log(`Plik PDF został przesłany pomyślnie: ${result.path}`);
                } else {
                    // alert('Błąd podczas przesyłania pliku PDF: ' + result.message);
                    console.error('Błąd podczas przesyłania pliku PDF:', result.message);
                }
            } catch (error) {
                console.error('Błąd podczas przesyłania pliku PDF:', error);
                //alert('Wystąpił błąd podczas przesyłania pliku PDF.');
                
            }
        };
    });
});

document.getElementById('editID')?.addEventListener('input', function () 
{
    const id = this.value; // Pobranie wpisanego ID

    if (id) { // Jeśli ID nie jest puste
        const queryType = 'wydanie_edit'; // Typ zapytania dla tabeli wydanie

        fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('new_pdf').value = data.data.pdf || ''; // Pobiera ścieżkę pliku PDF
                } else {
                    document.getElementById('new_pdf').value = ''; // Czyści pole, jeśli brak danych
                }
            })
            .catch(error => {
                console.error('Błąd podczas pobierania danych:', error);
            });
    } else {
        document.getElementById('new_pdf').value = ''; // Czyści pole, jeśli ID jest puste
    }
});

document.addEventListener('DOMContentLoaded', () => {
    loadPdfList('pdf-select'); // Lista dla sekcji Dodawanie
    loadPdfList('new_pdf-select'); // Lista dla sekcji Edycja
});

// Funkcja do ładowania listy plików PDF
async function loadPdfList(selectElementId) {
    try {
        const response = await fetch('/Biblioteka/php/get-pdfs.php'); // Skrypt PHP zwracający listę plików PDF
        const result = await response.json();

        if (result.success) {
            const selectElement = document.getElementById(selectElementId);
            selectElement.innerHTML = '<option value="" disabled selected>-- Wybierz plik PDF --</option>'; // Wyczyść istniejące opcje

            result.files.forEach(file => {
                const option = document.createElement('option');
                option.value = `Biblioteka/books/${file}`; // Ścieżka do pliku PDF
                option.textContent = file; // Wyświetlana nazwa pliku
                selectElement.appendChild(option);
            });
        } else {
            //alert('Błąd podczas pobierania listy plików PDF.');
            console.error('Błąd podczas pobierania listy plików PDF:', result.message);
        }
    } catch (error) {
        console.error('Błąd podczas pobierania listy plików PDF:', error);
       // alert('Wystąpił błąd podczas pobierania listy plików PDF, sprawdź konsolę.');
    }
}

