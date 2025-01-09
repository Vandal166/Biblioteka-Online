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

        // Ładowanie listy plików przy załadowaniu strony
        document.addEventListener('DOMContentLoaded', () => 
        {
            loadImageList('image-select-add'); // Lista dla sekcji Dodawanie
            loadImageList('image-select-edit'); // Lista dla sekcji Edycja
        });
    </script>

    <!-- skrypt do ladowania zdjec -->
    <script src="/Biblioteka/js/image_mgr.js" defer></script>

</section>