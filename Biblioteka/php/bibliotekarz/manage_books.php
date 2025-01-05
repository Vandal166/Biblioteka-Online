<?php
session_start();
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Biblioteka/');

require_once(BASE_PATH . 'php/db_connection.php');

if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: /Biblioteka/index.php"); // Brak dostępu
    exit();
}
// Pobranie listy książek z bazy danych
$query = "SELECT ksiazka.ID, ksiazka.tytul, autor.imie, autor.nazwisko 
        FROM ksiazka
        LEFT JOIN autor_ksiazki ON ksiazka.ID = autor_ksiazki.ID_ksiazki
        LEFT JOIN autor ON autor_ksiazki.ID_autora = autor.ID";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html> 
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzaj Książkami</title> 
    <base href="/Biblioteka/"> <!-- bazowa sciezka dla odnośników -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bibliotekarz.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="/Biblioteka/index.php">Strona Główna</a></li>
                <li><a href="/Biblioteka/php/reservation.php">Rezerwacja Książek</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- if użytkownik jest zalogowany, wyświetl "Wyloguj" -->
                    <li><a href="/Biblioteka/php/logout.php" id="logoutBtn">Wyloguj się</a></li>
                <?php else: ?>
                    <!-- if użytkownik nie jest zalogowany, wyświetl "Zaloguj się" -->
                    <li><a href="/Biblioteka/php/login.php">Zaloguj się</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>  

    <section id="panel">              
            <ul>          
                <li><a href="/Biblioteka/php/bibliotekarz/manage_books.php"><button disabled>Zarządzaj Książkami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/manage_exemplars.php"><button>Zarządzaj Egzemplarzami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/manage_borrowings.php"><button>Zarządzaj Wypożyczeniami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/manage_users.php"><button>Zarządzaj Czytelnikami</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reservation.php"><button>Rezerwacja Książek</button></a></li>
                <li><a href="/Biblioteka/php/bibliotekarz/reports.php"><button>Raporty</button></a></li>
            </ul>
    </section>   
        
      
    <section id="tabela">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tytuł</th>
                    <th>Autor</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $book['ID']; ?></td>
                        <td><?php echo $book['tytul']; ?></td>
                        <td><?php echo $book['imie'] . ' ' . $book['nazwisko']; ?></td>
                        <td class="actions"> 
                            <!-- akcje -->
                            <a href="/Biblioteka/php/bibliotekarz/edit_book.php?id=<?php echo $book['ID']; ?>"><button>Edytuj</button></a>
                            <a href="/Biblioteka/php/bibliotekarz/delete_book.php?id=<?php echo $book['ID']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę książkę?');"><button>Usuń</button></a>
                        </td>                    
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
</body>
</html>