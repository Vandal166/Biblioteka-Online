<section id="formularz">
    <div class="podsekcja" id="C">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="wypozyczenie">
            <input type="hidden" name="action" value="add">
            <label for="ID_czytelnika">ID czytelnika:</label>
            <input type="number" id="ID_czytelnika" name="ID_czytelnika" value="<?php echo get_form_value('ID_czytelnika');?>" required>
            <label for="ID_egzemplarza">ID egzemplarza:</label>
            <input type="number" id="ID_egzemplarza" name="ID_egzemplarza" value="<?php echo get_form_value('ID_egzemplarza');?>" required>
            <label for="ID_pracownika">ID pracownika:</label>
            <input type="number" id="ID_pracownika" name="ID_pracownika" value="<?php echo get_form_value('ID_pracownika');?>" required>
            <label for="data_wypozyczenia">Data wypożyczenia:</label>
            <input type="date" id="data_wypozyczenia" name="data_wypozyczenia" value="<?php echo get_form_value('data_wypozyczenia');?>" required>
            <label for="termin_oddania">Termin oddania:</label>
            <input type="date" id="termin_oddania" name="termin_oddania" value="<?php echo get_form_value('termin_oddania');?>" required>
            <label for="data_oddania">Data oddania:</label>
            <input type="date" id="data_oddania" name="data_oddania" value="<?php echo get_form_value('data_oddania');?>">

            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>
    </div>
    
    <div class="podsekcja" id="D">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="wypozyczenie">
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
            <input type="hidden" name="formularz" value="wypozyczenie">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" required>

            <label for="new_ID_czytelnika">Nowe ID czytelnika:</label>
            <input type="number" id="new_ID_czytelnika" name="new_ID_czytelnika" value="<?php echo get_form_value('new_ID_czytelnika');?>">
            <label for="new_ID_egzemplarza">Nowe ID egzemplarza:</label>
            <input type="number" id="new_ID_egzemplarza" name="new_ID_egzemplarza" value="<?php echo get_form_value('new_ID_egzemplarza');?>">
            <label for="new_ID_pracownika">Nowe ID pracownika:</label>
            <input type="number" id="new_ID_pracownika" name="new_ID_pracownika" value="<?php echo get_form_value('new_ID_pracownika');?>">
            <label for="new_data_wypozyczenia">Nowa data wypożyczenia:</label>
            <input type="date" id="new_data_wypozyczenia" name="new_data_wypozyczenia" value="<?php echo get_form_value('new_data_wypozyczenia');?>">
            <label for="new_termin_oddania">Nowy termin oddania:</label>
            <input type="date" id="new_termin_oddania" name="new_termin_oddania" value="<?php echo get_form_value('new_termin_oddania');?>">
            <label for="new_data_oddania">Nowa data oddania:</label>
            <input type="date" id="new_data_oddania" name="new_data_oddania" value="<?php echo get_form_value('new_data_oddania');?>">  

            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?>
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID

            if (id) { // Jeśli ID nie jest puste
                const queryType = 'wypozyczenie_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            
                            document.getElementById('new_ID_czytelnika').value = data.data.ID_czytelnika;
                            document.getElementById('new_ID_egzemplarza').value = data.data.ID_egzemplarza;
                            document.getElementById('new_ID_pracownika').value = data.data.ID_pracownika;
                            document.getElementById('new_data_wypozyczenia').value = data.data.data_wypozyczenia;
                            document.getElementById('new_termin_oddania').value = data.data.termin_oddania;
                            document.getElementById('new_data_oddania').value = data.data.data_oddania;
                        } else {
                            document.getElementById('new_ID_czytelnika').value = '';
                            document.getElementById('new_ID_egzemplarza').value = '';
                            document.getElementById('new_ID_pracownika').value = '';
                            document.getElementById('new_data_wypozyczenia').value = '';
                            document.getElementById('new_termin_oddania').value = '';
                            document.getElementById('new_data_oddania').value = '';
                        }
                    });
            } else {
                document.getElementById('new_ID_czytelnika').value = '';
                document.getElementById('new_ID_egzemplarza').value = '';
                document.getElementById('new_ID_pracownika').value = '';
                document.getElementById('new_data_wypozyczenia').value = '';
                document.getElementById('new_termin_oddania').value = '';
                document.getElementById('new_data_oddania').value = '';
            }
        });
    </script>
</section>