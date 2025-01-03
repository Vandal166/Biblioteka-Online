<section id="formularz">
    <div class="podsekcja">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="autor">
            <input type="hidden" name="action" value="add">
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" value="<?php echo get_form_value('imie'); ?>" required><br>

            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" value="<?php echo get_form_value('nazwisko'); ?>" required><br>
            
            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>
    </div>
    
    <div class="podsekcja">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="autor">
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
            <input type="hidden" name="formularz" value="autor">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" value="<?php echo get_form_value('editID'); ?>"><br>
            
            <label for="new_imie">Nowe imię:</label>
            <input type="text" id="new_imie" name="new_imie" value="<?php echo get_form_value('new_imie'); ?>"><br>

            <label for="new_nazwisko">Nowe nazwisko:</label>
            <input type="text" id="new_nazwisko" name="new_nazwisko" value="<?php echo get_form_value('new_nazwisko'); ?>"><br>

            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?> 
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID
            
            if (id) { // Jeśli ID nie jest puste
                const queryType = 'autor_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Ustawienie wartości pól formularza
                            document.getElementById('new_imie').value = data.data.imie || ''; // dajemy  ~data.data.'nazwa kolumny'~ bo tak jest zwracane zapytanie
                            document.getElementById('new_nazwisko').value = data.data.nazwisko || '';                                
                        } else {
                            // czyszczenie pola formularza w przypadku błędu
                            document.getElementById('new_imie').value = '';
                            document.getElementById('new_nazwisko').value = '';                                
                            console.warn(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Błąd podczas pobierania danych:', error);
                    });
            } else {
                // Jeśli ID jest puste, czyścimy pola formularza
                document.getElementById('new_imie').value = '';
                document.getElementById('new_nazwisko').value = '';                    
            }
        });
    </script>
</section>