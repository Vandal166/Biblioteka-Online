<section id="formularz">
    <div class="podsekcja" id="C">
        <h2>Dodawanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="czytelnik">
            <input type="hidden" name="action" value="add">
            <!-- imie, nazwisko, nr_karty, telefon, email, login -->
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" value="<?php echo get_form_value('imie');?>" required>
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" value="<?php echo get_form_value('nazwisko');?>" required>            
            <label for="telefon">Telefon:</label>
            <input type="text" id="telefon" name="telefon" value="<?php echo get_form_value('telefon');?>" maxlength="9" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo get_form_value('email');?>" required>
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" value="<?php echo get_form_value('login');?>" required>
            <label for="haslo">Hasło:</label>
            <input type="password" id="haslo" name="haslo" minlength="6" maxlength="255" required>
            <label for="confirm_password">Potwierdź hasło:</label>
            <input type="password" id="confirm_password" name="confirm_password" minlength="6" maxlength="255" required>

            <button type="submit">Dodaj</button>
            <?php display_messages('add'); ?>
        </form>


    </div>
    
    <div class="podsekcja" id="D">
        <h2>Usuwanie</h2>
        <form action="php/admin/formularze/main_formularz.php" method="POST">
            <input type="hidden" name="formularz" value="czytelnik">
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
            <input type="hidden" name="formularz" value="czytelnik">
            <input type="hidden" name="action" value="edit">
            <label for="editID">ID:</label>
            <input type="number" id="editID" name="editID" value="<?php echo get_form_value('editID');?>" required>

            <label for="new_imie">Nowe imię:</label>
            <input type="text" id="new_imie" name="new_imie" value="<?php echo get_form_value('new_imie');?>" required>
            <label for="new_nazwisko">Nowe nazwisko:</label>
            <input type="text" id="new_nazwisko" name="new_nazwisko" value="<?php echo get_form_value('new_nazwisko');?>" required>            
            <label for="new_telefon">Nowy telefon:</label>
            <input type="text" id="new_telefon" name="new_telefon" value="<?php echo get_form_value('new_telefon');?>" maxlength="9" required>
            <label for="new_email">Nowy email:</label>
            <input type="email" id="new_email" name="new_email" value="<?php echo get_form_value('new_email');?>" required>
            <label for="new_login">Nowy login:</label>
            <input type="text" id="new_login" name="new_login" value="<?php echo get_form_value('new_login');?>" required>

            <button type="submit">Edytuj</button>
            <?php display_messages('edit'); ?>
        </form>
    </div>
    <script>
        // nasłuchiwanie na zmiany w polu editID
        document.getElementById('editID').addEventListener('input', function() {
            const id = this.value; // Pobranie wpisanego ID

            if (id) { // Jeśli ID nie jest puste
                const queryType = 'czytelnik_edit'; // Typ zapytania

                fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('new_imie').value = data.data.imie;
                            document.getElementById('new_nazwisko').value = data.data.nazwisko;                            
                            document.getElementById('new_telefon').value = data.data.telefon;
                            document.getElementById('new_email').value = data.data.email;
                            document.getElementById('new_login').value = data.data.login;
                        }
                        else 
                        {
                            document.getElementById('new_imie').value = '';
                            document.getElementById('new_nazwisko').value = '';                            
                            document.getElementById('new_telefon').value = '';
                            document.getElementById('new_email').value = '';
                            document.getElementById('new_login').value = '';
                        }
                    });
            } else {
                document.getElementById('new_imie').value = '';
                document.getElementById('new_nazwisko').value = '';                
                document.getElementById('new_telefon').value = '';
                document.getElementById('new_email').value = '';
                document.getElementById('new_login').value = '';
            }
        });
    </script>
</section>