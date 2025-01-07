<section id="formularz">
    <div class="podsekcja" id="C">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="ksiazka">
            <input type="hidden" name="action" value="add">
            <label for="tytul">Tytuł:</label>
            <input type="text" id="tytul" name="tytul" value="<?php echo get_form_value('tytul');?>" required><br>

            <label for="zdjecie">Zdjęcie:</label>
            <input type="text" id="zdjecie" name="zdjecie" value="<?php echo get_form_value('zdjecie');?>"><br>

            <div>
                <label for="image-select-add">Wybierz zdjęcie:</label>
                <select id="image-select-add">
                    <option value="">-- Wybierz zdjęcie --</option>
                </select>
            </div>

            <input type="file" id="file-input" name="file" accept=".png, .jpg, .jpeg"></input>
            <button type="button" class="select-image" id="select-1">Wybierz</button>

            <input type="file" id="file-input" name="file" accept=".png, .jpg, .jpeg"></input>
            <button type="button" class="import-image" id="import-1">Importuj</button>

            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>


    </div>
    
    <div class="podsekcja" id="D">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="ksiazka">
            <input type="hidden" name="action" value="delete">
            <label for="ID">ID:</label>
            <input type="number" id="ID" name="ID" required><br>

            <button type="submit">Usuń</button>
            <?php display_messages('delete'); ?>
        </form>


    </div>
    
    <div class="podsekcja" id="U">
        <h2>Edytowanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="ksiazka">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" required><br>

            <label for="new_tytul">Nowy tytuł:</label>
            <input type="text" id="new_tytul" name="new_tytul" value="<?php echo get_form_value('new_tytul');?>" required><br>

            <label for="new_zdjecie">Nowe zdjęcie:</label>
            <input type="text" id="new_zdjecie" name="new_zdjecie" value="<?php echo get_form_value('new_zdjecie');?>"><br>

            <div>
                <label for="image-select-edit">Wybierz zdjęcie:</label>
                <select id="image-select-edit">
                    <option value="">-- Wybierz zdjęcie --</option>
                </select>
            </div>

            <input type="file" id="file-input" name="file" accept=".png, .jpg, .jpeg"></input>
            <button type="button" class="select-image" id="select-2">Wybierz</button>

            <input type="file" id="file-input" name="file" accept=".png, .jpg, .jpeg"></input>
            <button type="button" class="import-image" id="import-2">Importuj</button>

            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?>
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID

            if (id) { // Jeśli ID nie jest puste
                const queryType = 'ksiazka_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('new_tytul').value = data.data.tytul;
                            document.getElementById('new_zdjecie').value = data.data.zdjecie;
                        } else {
                            document.getElementById('new_tytul').value = '';
                            document.getElementById('new_zdjecie').value = '';
                        }
                    });
            } else {
                document.getElementById('new_tytul').value = '';
                document.getElementById('new_zdjecie').value = '';
            }
        });


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
                            const response = await fetch('php/upload.php', 
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
                const response = await fetch('php/get-images.php'); // Skrypt PHP zwracający listę plików
                const result = await response.json();

                if (result.success) 
                {
                    const selectElement = document.getElementById(selectElementId);
                    selectElement.innerHTML = '<option value="">-- Wybierz zdjęcie --</option>'; // Wyczyść istniejące opcje

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

        // Ładowanie listy plików przy załadowaniu strony
        document.addEventListener('DOMContentLoaded', () => 
        {
            loadImageList('image-select-add'); // Lista dla sekcji Dodawanie
            loadImageList('image-select-edit'); // Lista dla sekcji Edycja
        });


    </script>
</section>