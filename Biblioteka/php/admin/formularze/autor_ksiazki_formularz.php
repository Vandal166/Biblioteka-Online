<section id="formularz">
        <div class="podsekcja">
            <h2>Dodawanie</h2>
            <form action="php/admin/formularze/main_formularz.php" method="POST">
                <input type="hidden" name="formularz" value="autor_ksiazki">
                <input type="hidden" name="action" value="add">
                <label for="ID_autora">ID autora:</label>
                <input type="text" id="ID_autora" name="ID_autora" value="<?php echo get_form_value('ID_autora');?>"><br>

                <label for="ID_ksiazki">ID ksiazki:</label>
                <input type="text" id="ID_ksiazki" name="ID_ksiazki" value="<?php echo get_form_value('ID_ksiazki');?>"><br>
                
                <button type="submit">Dodaj</button>
                <?php display_messages('add'); ?>
            </form>
        </div>
        
        <div class="podsekcja">
            <h2>Usuwanie</h2>
            <form action="php/admin/formularze/main_formularz.php" method="POST">
                <input type="hidden" name="formularz" value="autor_ksiazki">
                <input type="hidden" name="action" value="delete">
                <label for="ID">ID:</label>
                <input type="number" id="ID" name="ID"><br>
                
                <button type="submit">Usuń</button>
                <?php display_messages('delete'); ?>
            </form>
        </div>
    

        <div class="podsekcja">
            <h2>Edytowanie</h2>
            <form action="php/admin/formularze/main_formularz.php" method="POST">
                <input type="hidden" name="formularz" value="autor_ksiazki">
                <input type="hidden" name="action" value="edit">
                <label for="editID">ID:</label>
                <input type="number" id="editID" name="editID" value="<?php echo get_form_value('editID'); ?>"><br>
               
                <label for="new_ID_autora">Nowe ID autora:</label>
                <input type="text" id="new_ID_autora" name="new_ID_autora" value="<?php echo get_form_value('new_ID_autora'); ?>"><br>
                <label for="new_ID_ksiazki">Nowe ID ksiazki:</label>
                <input type="text" id="new_ID_ksiazki" name="new_ID_ksiazki" value="<?php echo get_form_value('new_ID_ksiazki'); ?>"><br>

                <button type="submit">Edytuj</button>
                <?php display_messages('edit'); ?> 
            </form>
        </div>
        <script>
            // nasłuchiwanie na zmiany w polu editID
            document.getElementById('editID').addEventListener('input', function() {
                const id = this.value; // Pobranie wpisanego ID
                
                if (id) { // Jeśli ID nie jest puste
                    const queryType = 'autor_ksiazki_edit'; // Typ zapytania

                    fetch(`php/admin/formularze/fetch_edit_data.php?editID=${id}&queryType=${queryType}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Ustawienie wartości pól formularza
                                document.getElementById('new_ID_autora').value = data.data.ID_autora; // dajemy  ~data.data.'nazwa kolumny'~ bo tak jest zwracane zapytanie
                                document.getElementById('new_ID_ksiazki').value = data.data.ID_ksiazki;                                
                            } else {
                                // czyszczenie pola formularza w przypadku błędu
                                document.getElementById('new_ID_autora').value = '';
                                document.getElementById('new_ID_ksiazki').value = '';                                
                                console.warn(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Błąd podczas pobierania danych:', error);
                        });
                } else {
                    // Jeśli ID jest puste, czyścimy pola formularza
                    document.getElementById('new_ID_autora').value = '';
                    document.getElementById('new_ID_ksiazki').value = '';                    
                }
            });
        </script>
    </section>