<section id="formularz">
    <div class="podsekcja" id="C">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="egzemplarz">
            <input type="hidden" name="action" value="add">
            <label for="ID_wydania">ID wydania:</label>
            <input type="number" id="ID_wydania" name="ID_wydania" value="<?php echo get_form_value('ID_wydania');?>" required>
            <div class="checkbox-container">
            <label for="czy_dostepny">Czy dostępny:</label>
            <input type="hidden" name="czy_dostepny" value="0">
            <input type="checkbox" id="czy_dostepny" name="czy_dostepny" value="1" <?php echo get_form_value('czy_dostepny') ? 'checked' : ''; ?>>
            </div>
            <label for="stan">Stan:</label>
            <input type="text" id="stan" name="stan" value="<?php echo get_form_value('stan');?>" required>
            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>


    </div>
    
    <div class="podsekcja" id="D">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="egzemplarz">
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
            <input type="hidden" name="formularz" value="egzemplarz">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" required>

            <label for="new_ID_wydania">Nowe ID wydania:</label>
            <input type="number" id="new_ID_wydania" name="new_ID_wydania" value="<?php echo get_form_value('new_ID_wydania');?>" required>
            <div class="checkbox-container">
            <label for="new_czy_dostepny">Czy dostępny:</label>
            <input type="hidden" name="new_czy_dostepny" value="0">
            <input type="checkbox" id="new_czy_dostepny" name="new_czy_dostepny" value="1" <?php echo get_form_value('new_czy_dostepny') ? 'checked' : ''; ?>>
            </div>
            <label for="new_stan">Stan:</label>
            <input type="text" id="new_stan" name="new_stan" value="<?php echo get_form_value('new_stan');?>" required>
            

            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?>
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID

            if (id) { // Jeśli ID nie jest puste
                const queryType = 'egzemplarz_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('new_ID_wydania').value = data.data.ID_wydania;
                            document.getElementById('new_czy_dostepny').checked = data.data.czy_dostepny == 1;
                            document.getElementById('new_stan').value = data.data.stan;
                        } else {
                            document.getElementById('new_ID_wydania').value = '';
                            document.getElementById('new_czy_dostepny').checked = false;
                            document.getElementById('new_stan').value = '';
                        }
                    });
            } else {
                document.getElementById('new_ID_wydania').value = '';
                document.getElementById('new_czy_dostepny').checked = false;
                document.getElementById('new_stan').value = '';
            }
        });
    </script>
</section>