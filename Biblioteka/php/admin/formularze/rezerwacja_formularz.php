<section id="formularz">
    <div class="podsekcja" id="C">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="rezerwacja">
            <input type="hidden" name="action" value="add">
            
            <label for="ID_wydania">ID wydania:</label>
            <input type="number" id="ID_wydania" name="ID_wydania" value="<?php echo get_form_value('ID_wydania');?>" required>
            <label for="ID_czytelnika">ID czytelnika:</label>
            <input type="number" id="ID_czytelnika" name="ID_czytelnika" value="<?php echo get_form_value('ID_czytelnika');?>" required>
            <label for="data_rezerwacji">Data rezerwacji:</label>
            <input type="date" id="data_rezerwacji" name="data_rezerwacji" value="<?php echo get_form_value('data_rezerwacji');?>" required>
            <div class="checkbox-container">
            <label for="czy_wydana">Czy wydana:</label>
            <input type="hidden" name="czy_wydana" value="0">
            <input type="checkbox" id="czy_wydana" name="czy_wydana" value="1" <?php echo get_form_value('czy_wydana') ? 'checked' : ''; ?>>
            </div>
            
            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>
    </div>
    
    <div class="podsekcja" id="D">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="rezerwacja">
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
            <input type="hidden" name="formularz" value="rezerwacja">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" value="<?php echo get_form_value('editID'); ?>" required>

            <label for="new_ID_wydania">Nowe ID wydania:</label>
            <input type="number" id="new_ID_wydania" name="new_ID_wydania" value="<?php echo get_form_value('new_ID_wydania');?>" required>
            <label for="new_ID_czytelnika">Nowe ID czytelnika:</label>
            <input type="number" id="new_ID_czytelnika" name="new_ID_czytelnika" value="<?php echo get_form_value('new_ID_czytelnika');?>" required>
            <label for="new_data_rezerwacji">Nowa data rezerwacji:</label>
            <input type="date" id="new_data_rezerwacji" name="new_data_rezerwacji" value="<?php echo get_form_value('new_data_rezerwacji');?>" required>
            <div class="checkbox-container">
            <label for="new_czy_wydana">Nowa czy wydana:</label>
            <input type="hidden" name="new_czy_wydana" value="0">
            <input type="checkbox" id="new_czy_wydana" name="new_czy_wydana" value="1" <?php echo get_form_value('new_czy_wydana') ? 'checked' : ''; ?>>
            </div>

            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?>
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID

            if (id) { // Jeśli ID nie jest puste
                const queryType = 'rezerwacja_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('new_ID_wydania').value = data.data.ID_wydania;
                            document.getElementById('new_ID_czytelnika').value = data.data.ID_czytelnika;
                            document.getElementById('new_data_rezerwacji').value = data.data.data_rezerwacji;
                            document.getElementById('new_czy_wydana').checked = data.data.czy_wydana == 1;
                        } else {
                            document.getElementById('new_ID_wydania').value = '';
                            document.getElementById('new_ID_czytelnika').value = '';
                            document.getElementById('new_data_rezerwacji').value = '';
                            document.getElementById('new_czy_wydana').checked = false;
                        }
                    });
            } else {
                document.getElementById('new_ID_wydania').value = '';
                document.getElementById('new_ID_czytelnika').value = '';
                document.getElementById('new_data_rezerwacji').value = '';
                document.getElementById('new_czy_wydana').checked = false;
            }
        });
    </script>
</section>