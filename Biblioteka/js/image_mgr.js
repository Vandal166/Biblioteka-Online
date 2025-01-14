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