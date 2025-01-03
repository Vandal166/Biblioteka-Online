<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');
require_once(BASE_PATH . 'php/helpers.php');
 
if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'administrator') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}

require_once(BASE_PATH . 'php/admin/formularze/form_actions.php');
clear_form_data();

/**
 * Wywoływany przez formularze w panelu administracyjnym. NA PRZYKLAD:
 * <form action="php/admin/formularze/main_formularz.php" method="POST">
 *              <input type="hidden" name="formularz" value="autor"> // nazwa tabeli do której odnosi się formularz
 *               <input type="hidden" name="action" value="add"> // akcja do wykonania
 * 
 * - $formularz: nazwa formularza, z którego przyszły dane (np. 'autor', 'ksiazka').
 * - $action: akcja do wykonania (np. 'add', 'delete', 'edit').
 * 
 * Logika:
 * - Sprawdza, czy żądanie jest metodą POST.
 * - Jeśli zmienne $formularz i $action są ustawione, wywołuje odpowiednią funkcję obsługującą akcje dla danego formularza.
 * - Funkcja jest dynamicznie tworzona na podstawie nazwy formularza np:
 *   'autor' -> 'handle_' + 'autor' + '_actions' -> handle_autor_actions
 * 
 */
$formularz = isset($_POST['formularz']) ? $_POST['formularz'] : (isset($_GET['formularz']) ? $_GET['formularz'] : null); // nazwa formularza z którego przyszły dane np 'autor', 'ksiazka'
$action = isset($_POST['action']) ? $_POST['action'] : null; // akcja do wykonania np 'add', 'delete', 'edit'
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if ($formularz && $action) 
    {
        // wywołanie odpowiedniej funkcji obsługującej akcje dla danego formularza
        // np. jesli $formularz = 'autor', to zostanie wywołana funkcja 'handle_autor_actions($action)' z pliku 'form_actions.php'
        // ponieważ takie nazwy mają funkcje obsługujące akcje dla formularzy. NP: 'handle_' + $formularz + '_actions' -> handle_autor_actions
        $action_function = 'handle_' . $formularz . '_actions';
        if (function_exists($action_function)) {
            $action_function($action);
        } else {
            echo "<p>Błedny formularz</p>";
        }
    }
    $conn->close();
}
if (!$formularz)
    $formularz = 'autor';

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administracyjny</title>
    <base href="/Biblioteka/"> <!-- bazowa sciezka dla odnośników -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">  
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="php/reservation.php">Rezerwacja Książek</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- if użytkownik jest zalogowany, wyświetl "Wyloguj" -->
                    <li><a href="php/logout.php" id="logoutBtn">Wyloguj się</a></li>
                <?php else: ?>
                    <!-- if użytkownik nie jest zalogowany, wyświetl "Zaloguj się" -->
                    <li><a href="php/login.php">Zaloguj się</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>   

    <section id="pasek">
        <ul>
            <li><button disabled>Formularze</button></li>        
            <li><a href="php/admin/tabele/main_tabela.php?tabela=<?php echo $formularz; ?>"><button>Tabele</button></a></li>
        </ul>
    </section>
    
    <section id="panel"> 
        <ul>          
            <!-- /**
             * Lista formularzy w postaci przycisków 
             *
             * Tablica $formularze zawiera klucz jako nazwę formularza i wartość jako etykietę przycisku. np. 'autor' => 'Autor'
             * Dla każdego elementu w tablicy generowany jest element listy <li> zawierający formularz.
             * Formularz zawiera ukryte pole z nazwą formularza oraz przycisk do przesyłania formularza.
             *
             * @var array $formularze Array z nazwami formularzy i ich etykietami.             
             */ -->
            <?php
            $formularze = [
                'autor' => 'Autor',
                'autor_ksiazki' => 'Autor-Książka',
                'czytelnik' => 'Czytelnik',
                'egzemplarz' => 'Egzemplarz',
                'gatunek' => 'Gatunek',
                'gatunek_ksiazki' => 'Gatunek-Książka',
                'ksiazka' => 'Książka',
                'pracownik' => 'Pracownik',
                'rezerwacja' => 'Rezerwacja',
                'wydanie' => 'Wydanie',
                'wydawnictwo' => 'Wydawnictwo',
                'wypozyczenie' => 'Wypożyczenie'
            ];
            
            foreach ($formularze as $key => $value) {                
                 echo '<li>
                <form action="php/admin/formularze/main_formularz.php" method="POST">
                <input type="hidden" name="formularz" value="' . $key . '">';
                if ($formularz === $key) {
                    echo '<button type="submit" disabled>' . $value . '</button>';
                } else {
                    echo '<button type="submit">' . $value . '</button>';
                }
                echo '</form>
                </li>';                
            }
            ?>            
        </ul>
    </section>

        <?php // wyświetlenie nazwy formularza
            if ($formularz)                
                echo '<h1 class="form_name">' . $formularze[$formularz] . '</h1>';
            ?>

        <?php
        // zaladowanie odpowiedniego formularza na podstawie zmiennej $formularz
        // np. jesli $formularz = 'autor', to zostanie zaladowany plik 'autor_formularz.php' bo takie nazwy maja pliki formularzy + '_formularz.php'
        if ($formularz && array_key_exists($formularz, $formularze)) {
            include($formularz . '_formularz.php');
        } else {
            echo "<p>Błedny formularz</p>";
        }
        ?>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>