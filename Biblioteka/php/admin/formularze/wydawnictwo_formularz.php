<section id="formularz">
    <div class="podsekcja" id="C">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="wydawnictwo">
            <input type="hidden" name="action" value="add">
            <label for="nazwa">Nazwa:</label>
            <input type="text" id="nazwa" name="nazwa" value="<?php echo get_form_value('nazwa');?>" required>

            <label for="kraj">Kraj:</label>
            <input type="text" id="kraj" name="kraj" value="<?php echo get_form_value('kraj');?>" required><br>

            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>


    </div>
    
    <div class="podsekcja" id="D">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="wydawnictwo">
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
            <input type="hidden" name="formularz" value="wydawnictwo">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" value="<?php echo get_form_value('editID'); ?>" required>

            <label for="new_nazwa">Nowa nazwa:</label>
            <input type="text" id="new_nazwa" name="new_nazwa" value="<?php echo get_form_value('new_nazwa'); ?>" required>

            <label for="new_kraj">Nowy kraj:</label>
            <input type="text" id="new_kraj" name="new_kraj" value="<?php echo get_form_value('new_kraj'); ?>" required><br>

            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?>
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID

            if (id) { // Jeśli ID nie jest puste
                const queryType = 'wydawnictwo_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('new_nazwa').value = data.data.nazwa;
                            document.getElementById('new_kraj').value = data.data.kraj;
                        } else {
                            document.getElementById('new_nazwa').value = '';
                            document.getElementById('new_kraj').value = '';
                        }
                    });
            } else {
                document.getElementById('new_nazwa').value = '';
                document.getElementById('new_kraj').value = '';
            }
        });
    </script>
</section>