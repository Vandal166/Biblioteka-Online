<section id="formularz">
    <div class="podsekcja">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="gatunek_ksiazki">
            <input type="hidden" name="action" value="add">
            <label for="ID_ksiazki">ID ksiazki:</label>
            <input type="text" id="ID_ksiazki" name="ID_ksiazki" value="<?php echo get_form_value('ID_ksiazki'); ?>"><br>

            <label for="ID_gatunku">ID gatunku:</label>
            <input type="text" id="ID_gatunku" name="ID_gatunku" value="<?php echo get_form_value('ID_gatunku'); ?>"><br>
            
            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>
    </div>
    
    <div class="podsekcja">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="gatunek_ksiazki">
            <input type="hidden" name="action" value="delete">
            <label for="ID">ID:</label>
            <input type="number" id="ID" name="ID" value=""><br>
            
            <button type="submit">Usuń</button>
            <?php display_messages('delete'); ?>
        </form>
    </div>


    <div class="podsekcja">
        <h2>Edytowanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="gatunek_ksiazki">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" value="<?php echo get_form_value('editID'); ?>"><br>
        
            <label for="new_ID_ksiazki">Nowe ID ksiazki:</label>
            <input type="text" id="new_ID_ksiazki" name="new_ID_ksiazki" value="<?php echo get_form_value('new_ID_ksiazki'); ?>"><br>
            <label for="new_ID_gatunku">Nowe ID gatunku:</label>
            <input type="text" id="new_ID_gatunku" name="new_ID_gatunku" value="<?php echo get_form_value('new_ID_gatunku'); ?>"><br>
            
            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?> 
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID
            
            if (id) { // Jeśli ID nie jest puste
                const queryType = 'gatunek_ksiazki_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Wypełnienie pól formularza danymi z bazy
                            document.getElementById('new_ID_ksiazki').value = data.data.ID_ksiazki;
                            document.getElementById('new_ID_gatunku').value = data.data.ID_gatunku;
                        } else {
                            // Jeśli nie znaleziono rekordu, czyścimy pola formularza
                            document.getElementById('new_ID_ksiazki').value = '';
                            document.getElementById('new_ID_gatunku').value = '';
                        }
                    });
            } else {
                // Jeśli ID jest puste, czyścimy pola formularza
                document.getElementById('new_ID_ksiazki').value = '';
                document.getElementById('new_ID_gatunku').value = '';
            }
        });
    </script>
</section>