<section id="formularz">
    <div class="podsekcja" id="C">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="wydanie">
            <input type="hidden" name="action" value="add">
            <label for="ID_ksiazki">ID książki:</label>
            <input type="number" id="ID_ksiazki" name="ID_ksiazki" value="<?php echo get_form_value('ID_ksiazki');?>" required>
            <label for="ID_wydawnictwa">ID wydawnictwa:</label>
            <input type="number" id="ID_wydawnictwa" name="ID_wydawnictwa" value="<?php echo get_form_value('ID_wydawnictwa');?>" required>
            <label for="ISBN">ISBN:</label>
            <input type="text" id="ISBN" name="ISBN" value="<?php echo get_form_value('ISBN');?>" maxlength="13" required>
            <label for="data_wydania">Data wydania:</label>
            <input type="date" id="data_wydania" name="data_wydania" value="<?php echo get_form_value('data_wydania');?>" required>
            <label for="numer_wydania">Numer wydania:</label>
            <input type="text" id="numer_wydania" name="numer_wydania" value="<?php echo get_form_value('numer_wydania');?>" maxlength="20" required>
            <!--TODO dodac jakis lepszy placeholder bo wpisywanie 20 cyfr na slepo to !cool -->
            <label for="jezyk">Język:</label>
            <input type="text" id="jezyk" name="jezyk" value="<?php echo get_form_value('jezyk');?>" required>     
            <label for="ilosc_stron">Ilość stron:</label>
            <input type="number" id="ilosc_stron" name="ilosc_stron" value="<?php echo get_form_value('ilosc_stron');?>" required>       
            <label for="pdf">Plik PDF:</label>
            <input type="text" id="pdf" name="pdf" value="<?php echo get_form_value('pdf'); ?>"><br>

            <div>
                <label for="pdf-select">Wybierz plik PDF:</label>
                <select id="pdf-select">
                    <option value="">-- Wybierz plik PDF --</option>
                </select>
            </div>

            <input type="file" id="file-input-pdf" name="file" accept=".pdf"></input>
            <button type="button" class="select-pdf" id="select-pdf">Wybierz</button>
            <button type="button" class="import-pdf" id="import-pdf">Importuj</button>

            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>
    </div>
    
    <div class="podsekcja" id="D">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="wydanie">
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
            <input type="hidden" name="formularz" value="wydanie">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" value="<?php echo get_form_value('editID');?>" required>
            
            <label for="new_ID_ksiazki">Nowe ID książki:</label>
            <input type="number" id="new_ID_ksiazki" name="new_ID_ksiazki" value="<?php echo get_form_value('new_ID_ksiazki');?>" required>
            <label for="new_ID_wydawnictwa">Nowe ID wydawnictwa:</label>
            <input type="number" id="new_ID_wydawnictwa" name="new_ID_wydawnictwa" value="<?php echo get_form_value('new_ID_wydawnictwa');?>" required>
            <label for="new_ISBN">Nowe ISBN:</label>
            <input type="text" id="new_ISBN" name="new_ISBN" maxlength="13" value="<?php echo get_form_value('new_ISBN');?>" required>
            <label for="new_data_wydania">Nowa data wydania:</label>
            <input type="date" id="new_data_wydania" name="new_data_wydania" value="<?php echo get_form_value('new_data_wydania');?>" required>
            <label for="new_numer_wydania">Nowy numer wydania:</label>
            <input type="text" id="new_numer_wydania" name="new_numer_wydania" maxlength="20" value="<?php echo get_form_value('new_numer_wydania');?>" required>
            <label for="new_jezyk">Nowy język:</label>
            <input type="text" id="new_jezyk" name="new_jezyk" value="<?php echo get_form_value('new_jezyk');?>" required>
            <label for="new_ilosc_stron">Ilość stron:</label>
            <input type="number" id="new_ilosc_stron" name="new_ilosc_stron" value="<?php echo get_form_value('new_ilosc_stron');?>" required>   
            <label for="new_pdf">Plik PDF:</label>
            <input type="text" id="new_pdf" name="new_pdf" value="<?php echo get_form_value('new_pdf'); ?>"><br>
            <div>
                <label for="new_pdf-select">Wybierz plik PDF:</label>
                <select id="new_pdf-select">
                    <option value="">-- Wybierz plik PDF --</option>
                </select>
            </div>

            <input type="file" id="file-input-pdf" name="file" accept=".pdf"></input>
            <button type="button" class="select-pdf" id="new_select-pdf">Wybierz</button>
            <button type="button" class="import-pdf" id="new_import-pdf">Importuj</button>
            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?>
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID

            if (id) { // Jeśli ID nie jest puste
                const queryType = 'wydanie_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('new_ID_ksiazki').value = data.data.ID_ksiazki;
                            document.getElementById('new_ID_wydawnictwa').value = data.data.ID_wydawnictwa;
                            document.getElementById('new_ISBN').value = data.data.ISBN;
                            document.getElementById('new_data_wydania').value = data.data.data_wydania;
                            document.getElementById('new_numer_wydania').value = data.data.numer_wydania;
                            document.getElementById('new_jezyk').value = data.data.jezyk;
                            document.getElementById('new_ilosc_stron').value = data.data.ilosc_stron;
                            document.getElementById('new_pdf').value = data.data.pdf;
                        } else {
                            document.getElementById('new_ID_ksiazki').value = '';
                            document.getElementById('new_ID_wydawnictwa').value = '';
                            document.getElementById('new_ISBN').value = '';
                            document.getElementById('new_data_wydania').value = '';
                            document.getElementById('new_numer_wydania').value = '';
                            document.getElementById('new_jezyk').value = '';
                            document.getElementById('new_ilosc_stron').value = '';
                            document.getElementById('new_pdf').value = '';
                        }
                    });
            } else {
                document.getElementById('new_ID_ksiazki').value = '';
                document.getElementById('new_ID_wydawnictwa').value = '';
                document.getElementById('new_ISBN').value = '';
                document.getElementById('new_data_wydania').value = '';
                document.getElementById('new_numer_wydania').value = '';
                document.getElementById('new_jezyk').value = '';
                document.getElementById('new_ilosc_stron').value = '';
                document.getElementById('new_pdf').value = '';
            }
        });
    </script>

    <script src="/Biblioteka/js/pdf_mgr.js" defer></script>
</section>