<?php 
require_once('../../validation_funcs.php');


/**
* Głowna funkcja obsługująca CRUD dla podanej tabeli.
* Wywoływana przez funkcje obsługujące akcje dla poszczególnych formularzy. np. handle_autor_actions, etc
*
* @param string $table: Nazwa tabeli w bazie danych. np 'autor', 'ksiazka', 'czytelnik', etc.
* @param array $fields: Array z nazwami pól formularza. np ['imie', 'nazwisko', 'telefon', 'email', 'login', 'haslo']
* @param array $validations: Array z funkcjami walidacyjnymi dla pól. np ['imie' => 'validate_name', 'nazwisko' => 'validate_name']
* @param string $action: Akcja do wykonania ('add', 'delete', 'edit').
* @param string $primaryKey: Nazwa klucza głównego tabeli (domyślnie 'ID'). Tylko do usuwania i edycji.
* @param array $add_custom_fields: Array z niestandardowymi polami i funkcjami generującymi ich wartości. np. ['nr_karty' => 'generate_card_number']
*
* @global mysqli $conn Połączenie z bazą danych.
*
* @return void
*/
function handle_crud_actions($table, $fields, $validations, $action, $primaryKey = 'ID', $add_custom_fields = []) : void
{
    global $conn;

    remember_form_data();

    $formAction = $_POST['action'] ?? '';

    if ($formAction !== $action) {
        return; // Przerwij, jeśli akcja nie pasuje do bieżącego formularza
    }

    switch ($action) {
        case 'add':
            // Przechwytywanie i walidacja pól
            $data = [];
            foreach ($fields as $field) {
                if (strpos($field, 'czy_') === 0) // sprawdzanie czy pole jest checkboxem zaczynajacym sie od 'czy_'
                {
                    $data[$field] = isset($_POST[$field]) && $_POST[$field] == '1' ? 1 : 0;                    
                } else 
                {
                    // std
                    $data[$field] = isset($_POST[$field]) ? htmlspecialchars(trim($_POST[$field])) : null;
                }           
                                    
                // imiona i nazwiska z dużej litery
                if (in_array($field, ['imie', 'nazwisko'])) {
                    $data[$field] = ucfirst(strtolower($data[$field]));
                }
                if (isset($validations[$field]) && $validations[$field] !== null) {
                    $params = [
                        'value' => $data[$field],
                        'conn' => $conn,
                        'owning_ID' => null,
                        'check_if_exists' => true
                    ];                   
                    
                    // custom dla hasla
                    if ($field === 'haslo') 
                    {
                        $params['confirm_password'] = $_POST['confirm_password'] ?? null;
                        if ($data[$field] === $params['confirm_password']) 
                        {
                            $data[$field] = password_hash($data[$field], PASSWORD_DEFAULT);
                        } 
                        else 
                        {
                            set_message('error', 'add', 'Hasła nie są zgodne.');
                            return;
                        }
                    }

                    $error = $validations[$field]($params);
                    if ($error) {
                        set_message('error', 'add', $error);
                        return;
                    }
                }                
            }
            // **Obsługa pól niestandardowych (add_custom_fields)**
            // np. generowanie numeru karty dla czytelnika
            foreach ($add_custom_fields as $custom_field => $generator_function) 
            {
                if (function_exists($generator_function))
                    $data[$custom_field] = $generator_function($conn);                
            }
            // Dodawanie rekordu
            $all_fields = array_merge($fields, array_keys($add_custom_fields)); // Łączymy standardowe i customowe pola
            $placeholders = implode(', ', array_fill(0, count($all_fields), '?'));
            $sql = "INSERT INTO $table (" . implode(', ', $all_fields) . ") VALUES ($placeholders)";
            $stmt = $conn->prepare($sql);
            $types = str_repeat('s', count($all_fields)); // Wszystkie pola jako string
            $stmt->bind_param($types, ...array_values($data));
            if ($stmt->execute()) {
                set_message('success', 'add', ucfirst($table) . ' został dodany pomyślnie.');
                clear_form_data();
            } else {
                set_message('error', 'add', "Błąd: " . $stmt->error);
            }
            $stmt->close();
            break;

        case 'delete':
            // Walidacja ID
            $ID = intval($_POST[$primaryKey] ?? 0);
            if ($ID > 0) {
                $checkSql = "SELECT COUNT(*) FROM $table WHERE $primaryKey = ?";
                $stmt = $conn->prepare($checkSql);
                $stmt->bind_param("i", $ID);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count === 1) {
                    $deleteSql = "DELETE FROM $table WHERE $primaryKey = ?";
                    $stmt = $conn->prepare($deleteSql);
                    $stmt->bind_param("i", $ID);
                    if ($stmt->execute()) {
                        set_message('success', 'delete', ucfirst($table) . " o $primaryKey $ID został usunięty.");
                    } else {
                        set_message('error', 'delete', "Błąd: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    set_message('error', 'delete', "Rekord o $primaryKey $ID nie istnieje.");
                }
            } else {
                set_message('error', 'delete', "Nieprawidłowe $primaryKey.");
            }
            break;

        case 'edit':
            // Przechwytywanie i walidacja pól
            $ID = intval($_POST['edit' . $primaryKey] ?? 0);
            if ($ID > 0) {
                $data = [];
                foreach ($fields as $field) {                    
                    if (strpos($field, 'new_czy_') === 0) // sprawdzanie czy pole jest checkboxem zaczynajacym sie od 'czy_'
                    {
                        $data[$field] = isset($_POST[$field]) && $_POST[$field] == '1' ? 1 : 0;                    
                    } 
                    else 
                    {
                        // std
                        $data[$field] = isset($_POST['new_' . $field]) ? htmlspecialchars(trim($_POST['new_' . $field])) : null;
                    }  

                    // imiona i nazwiska z dużej litery
                    if (in_array($field, ['imie', 'nazwisko'])) {
                        $data[$field] = ucfirst(strtolower($data[$field]));
                    }

                    if (isset($validations[$field])) {
                        $params = [
                            'value' => $data[$field],
                            'conn' => $conn,
                            'owning_ID' => $ID,
                            'check_if_exists' => true
                        ];
                        $error = $validations[$field]($params);
                        if ($error) {
                            set_message('error', 'edit', $error);
                            return;
                        }
                    }
                }
                //sprawdzenie czy rekord o podanym ID istnieje
                $checkSql = "SELECT COUNT(*) FROM $table WHERE $primaryKey = ?";
                $stmt = $conn->prepare($checkSql);
                $stmt->bind_param("i", $ID);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count !== 1) {
                    set_message('error', 'edit', "Rekord o $primaryKey $ID nie istnieje.");
                    return;
                }

                $placeholders = implode(' = ?, ', $fields) . ' = ?';
                $sql = "UPDATE $table SET $placeholders WHERE $primaryKey = ?";
                $stmt = $conn->prepare($sql);
                $types = str_repeat('s', count($fields)) . 'i';
                $stmt->bind_param($types, ...array_merge(array_values($data), [$ID]));
                if ($stmt->execute()) {
                    set_message('success', 'edit', ucfirst($table) . " został zaktualizowany.");
                    clear_form_data();
                } else {
                    set_message('error', 'edit', "Błąd: " . $stmt->error);
                }
                $stmt->close();
            } else {
                set_message('error', 'edit', "Nieprawidłowe $primaryKey.");
            }
            break;

        default:
            set_message('error', $action, "Nieznana akcja.");
    }
}

/**
 * Wywoływana przez 'main_formularz.php' 
 * Obsługuje akcje CRUD dla tabeli 'table_name'.
 *
 * Ta funkcja przetwarza akcje tworzenia, usuwania i edycji dla encji 'autor'.
 * Używa funkcji `handle_crud_actions` do wykonania tych operacji.
 *
 * @param string $action Akcja do wykonania. Np: 'add', 'delete' i 'edit'.
 *
 * Funkcja wykonuje następujące akcje:
 * - 'add': Dodaje nowego 'autora' z polami 'imie' i 'nazwisko', walidowanymi przez 'validate_name'.
 * - 'delete': Usuwa istniejącego 'autora'.
 * - 'edit': Edytuje istniejącego 'autora' z polami 'imie' i 'nazwisko', walidowanymi przez 'validate_name'.
 * 
 *  Opcjonalnie moze dodac pole 'nr_karty' z generowanym numerem karty: po 'add' ...['nr_karty' => 'generate_card_number']
 *  Walidajca moze byc rowniez funkcja np. 'imie' => function($params) { ... } 
 */
function handle_autor_actions($action)
{
    handle_crud_actions(
        'autor',
        ['imie', 'nazwisko'],
        [
            'imie' => 'validate_name',
            'nazwisko' => 'validate_name'
        ],
        'add'
    );
    handle_crud_actions('autor', [], [], 'delete');
    handle_crud_actions(
        'autor',
        ['imie', 'nazwisko'],
        [
            'imie' => 'validate_name',
            'nazwisko' => 'validate_name'
        ],
        'edit'
    );    
}

function handle_ksiazka_actions($action)
{
    handle_crud_actions(
        'ksiazka',
        ['tytul', 'zdjecie'],
        [
            'tytul' => 'validate_book_title',
            'zdjecie' => 'validate_image_path'
        ],
        'add'
    );
    handle_crud_actions('ksiazka', [], [], 'delete');
    handle_crud_actions(
        'ksiazka',
        ['tytul', 'zdjecie'],
        [
            'tytul' => 'validate_book_title',
            'zdjecie' => 'validate_image_path'
        ],
        'edit'
    );
}

function handle_gatunek_actions($action)
{
    handle_crud_actions(
        'gatunek',
        ['nazwa'],
        [
            'nazwa' => 'validate_name'
        ],
        'add'
    );
    handle_crud_actions('gatunek', [], [], 'delete');
    handle_crud_actions(
        'gatunek',
        ['nazwa'],
        [
            'nazwa' => 'validate_name'
        ],
        'edit'
    );    
}

function handle_wydawnictwo_actions($action)
{
    handle_crud_actions(
        'wydawnictwo',
        ['nazwa', 'kraj'],
        [
            'nazwa' => 'validate_name',
            'kraj' => 'validate_name'
        ],
        'add'
    );
    handle_crud_actions('wydawnictwo', [], [], 'delete');
    handle_crud_actions(
        'wydawnictwo',
        ['nazwa', 'kraj'],
        [
            'nazwa' => 'validate_name',
            'kraj' => 'validate_name'
        ],
        'edit'
    );   
}

function handle_czytelnik_actions($action)
{
    handle_crud_actions(
        'czytelnik', // nazwa tabeli
        ['imie', 'nazwisko', 'telefon', 'email', 'login', 'haslo'], // nazwa kolumn
        [
            'imie' => 'validate_name',
            'nazwisko' => 'validate_name',
            'telefon' => function($params) {
                $params['table'] = 'czytelnik';
                $params['column'] = 'telefon';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_phone($params);
            },
            'email' => function($params) {
                $params['table'] = 'czytelnik';
                $params['column'] = 'email';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_email($params);
            },
            'login' => function($params) {
                $params['table'] = 'czytelnik';
                $params['column'] = 'login';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_login($params);
            },
            'haslo' => 'validate_password'
        ],
        'add',
        'ID',
        ['nr_karty' => 'generate_card_number']
    );
    handle_crud_actions('czytelnik', [], [], 'delete');
    handle_crud_actions(
        'czytelnik',
        ['imie', 'nazwisko', 'telefon', 'email', 'login'],
        [
            'imie' => 'validate_name',
            'nazwisko' => 'validate_name',
            'telefon' => function($params) {
                $params['table'] = 'czytelnik';
                $params['column'] = 'telefon';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_phone($params);
            },
            'email' => function($params) {
                $params['table'] = 'czytelnik';
                $params['column'] = 'email';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_email($params);
            },
            'login' => function($params) {
                $params['table'] = 'czytelnik';
                $params['column'] = 'login';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_login($params);
            }
        ],
        'edit'
    );    
}

function handle_wypozyczenie_actions($action) 
{
   handle_crud_actions(
        'wypozyczenie', //data_oddania czyli data w którym oddano ksiazke
        ['ID_czytelnika', 'ID_egzemplarza', 'ID_pracownika', 'data_wypozyczenia', 'termin_oddania', 'data_oddania'],
        [
            'ID_czytelnika' => function($params) {
                $params['table'] = 'czytelnik';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_egzemplarza' => function($params) {
                $params['table'] = 'egzemplarz';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_pracownika' => function($params) {
                $params['table'] = 'pracownik';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'data_wypozyczenia' => 'validate_date',
            'termin_oddania' => 'validate_date',
            'data_oddania' => 'validate_date'
        ],
        'add'
    );
    handle_crud_actions('wypozyczenie', [], [], 'delete');
    handle_crud_actions(
        'wypozyczenie',
        ['ID_czytelnika', 'ID_egzemplarza', 'ID_pracownika', 'data_wypozyczenia', 'termin_oddania', 'data_oddania'],
        [
            'ID_czytelnika' => function($params) {
                $params['table'] = 'czytelnik';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_egzemplarza' => function($params) {
                $params['table'] = 'egzemplarz';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_pracownika' => function($params) {
                $params['table'] = 'pracownik';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'data_wypozyczenia' => 'validate_date',
            'termin_oddania' => 'validate_date',
            'data_oddania' => 'validate_date'
        ],
        'edit'
    );        
}

function handle_egzemplarz_actions($action)
{
    handle_crud_actions(
        'egzemplarz',
        ['ID_wydania', 'czy_dostepny', 'stan'],
        [
            'ID_wydania' => function($params) {
                $params['table'] = 'wydanie';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'czy_dostepny' => null,
            'stan' => null                 
        ],
        'add'
    );
    handle_crud_actions('egzemplarz', [], [], 'delete');
    handle_crud_actions(
        'egzemplarz',
        ['ID_wydania', 'czy_dostepny', 'stan'],
        [
            'ID_wydania' => function($params) {
                $params['table'] = 'wydanie';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'czy_dostepny' => null,
            'stan' => null         
        ],
        'edit'
    );    
}

function handle_autor_ksiazki_actions($action)
{
    handle_crud_actions(
        'autor_ksiazki',
        ['ID_autora', 'ID_ksiazki'],
        [
            'ID_autora' => function($params) {
                $params['table'] = 'autor';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_ksiazki' => function($params) {
                $params['table'] = 'ksiazka';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            }
        ],
        'add'
    );
    handle_crud_actions('autor_ksiazki', [], [], 'delete');
    handle_crud_actions(
        'autor_ksiazki',
        ['ID_autora', 'ID_ksiazki'],
        [
            'ID_autora' => function($params) {
                $params['table'] = 'autor';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_ksiazki' => function($params) {
                $params['table'] = 'ksiazka';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            }
        ],
        'edit'
    );   
}

function handle_gatunek_ksiazki_actions($action)
{
    handle_crud_actions(
        'gatunek_ksiazki',
        ['ID_gatunku', 'ID_ksiazki'],
        [
            'ID_gatunku' => function($params) {
                $params['table'] = 'gatunek';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_ksiazki' => function($params) {
                $params['table'] = 'ksiazka';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            }
        ],
        'add'
    );
    handle_crud_actions('gatunek_ksiazki', [], [], 'delete');
    handle_crud_actions(
        'gatunek_ksiazki',
        ['ID_gatunku', 'ID_ksiazki'],
        [
            'ID_gatunku' => function($params) {
                $params['table'] = 'gatunek';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_ksiazki' => function($params) {
                $params['table'] = 'ksiazka';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            }
        ],
        'edit'
    );
}

function handle_wydanie_actions($action)
{
    handle_crud_actions(
        'wydanie',
        ['ID_ksiazki', 'ID_wydawnictwa', 'ISBN', 'data_wydania', 'numer_wydania', 'jezyk', 'ilosc_stron', 'czy_elektronicznie'],
        [
            'ID_ksiazki' => function($params) {
                $params['table'] = 'ksiazka';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_wydawnictwa' => function($params) {
                $params['table'] = 'wydawnictwo';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ISBN' => function($params) {
                $params['table'] = 'wydanie';
                $params['column'] = 'ISBN';
                $error = check_if_exists($params);
                if ($error)
                    return $error;
                
                return validate_ISBN($params); 
            },
            'data_wydania' => 'validate_date',
            'numer_wydania' => function($params) {
                $params['table'] = 'wydanie';
                $params['column'] = 'numer_wydania';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_edition_number($params);
            },
            'jezyk' => 'validate_language',
            'ilosc_stron' => 'validate_page_count',
        ],
        'add'       
    );
    handle_crud_actions('wydanie', [], [], 'delete');
    handle_crud_actions(
        'wydanie',
        ['ID_ksiazki', 'ID_wydawnictwa', 'ISBN', 'data_wydania', 'numer_wydania', 'jezyk', 'ilosc_stron', 'czy_elektronicznie'],
        [
            'ID_ksiazki' => function($params) {
                $params['table'] = 'ksiazka';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_wydawnictwa' => function($params) {
                $params['table'] = 'wydawnictwo';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ISBN' => function($params) {
                $params['table'] = 'wydanie';
                $params['column'] = 'ISBN';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_ISBN($params);   
            },
            'data_wydania' => 'validate_date',
            'numer_wydania' => function($params) {
                $params['table'] = 'wydanie';
                $params['column'] = 'numer_wydania';
                $error = check_if_exists($params);
                if ($error)
                    return $error;
                
                return validate_edition_number($params);
            },
            'jezyk' => 'validate_language',
            'ilosc_stron' => 'validate_page_count'
        ],
        'edit'
    );  
}               

function handle_rezerwacja_actions($action)
{
    handle_crud_actions(
        'rezerwacja',
        ['ID_wydania', 'ID_czytelnika', 'data_rezerwacji', 'czy_wydana'],
        [
            'ID_wydania' => function($params) {
                $params['table'] = 'wydanie';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_czytelnika' => function($params) {
                $params['table'] = 'czytelnik';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'data_rezerwacji' => 'validate_date'
        ],
        'add'
    );
    handle_crud_actions('rezerwacja', [], [], 'delete');
    handle_crud_actions(
        'rezerwacja',
        ['ID_wydania', 'ID_czytelnika', 'data_rezerwacji', 'czy_wydana'],
        [
            'ID_wydania' => function($params) {
                $params['table'] = 'wydanie';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'ID_czytelnika' => function($params) {
                $params['table'] = 'czytelnik';
                $params['ID'] = $params['value'];
                return check_ID_exists($params);
            },
            'data_rezerwacji' => 'validate_date'
        ],
        'edit'
    );    
}

function handle_pracownik_actions($action) 
{
    handle_crud_actions(
        'pracownik',
        ['imie', 'nazwisko', 'poziom_uprawnien', 'email', 'login', 'haslo'],
        [
            'imie' => 'validate_name',
            'nazwisko' => 'validate_name',
            'email' => function($params) {
                $params['table'] = 'pracownik';               
                $params['column'] = 'email';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_email($params);
            },
            'login' => function($params) {
                $params['table'] = 'pracownik';               
                $params['column'] = 'login';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_login($params);
            },
            'haslo' => 'validate_password'
        ],
        'add'
    );
    handle_crud_actions('pracownik', [], [], 'delete');
    handle_crud_actions(
        'pracownik',
        ['imie', 'nazwisko', 'poziom_uprawnien', 'email', 'login'],
        [
            'imie' => 'validate_name',
            'nazwisko' => 'validate_name',
            'email' => function($params) {
                $params['table'] = 'pracownik';
                $params['column'] = 'email';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_email($params);
            },
            'login' => function($params) {
                $params['table'] = 'pracownik';
                $params['column'] = 'login';
                $error = check_if_exists($params);
                if ($error)
                    return $error;

                return validate_login($params);
            }
        ],
        'edit'
    );    
}
?>