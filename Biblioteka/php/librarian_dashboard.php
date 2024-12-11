<?php
session_start();
if (!isset($_SESSION['poziom_uprawnien']) || $_SESSION['poziom_uprawnien'] !== 'bibliotekarz') {
    header("Location: index.php"); // Brak dostępu
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel bibliotekarski</title>
    <link rel="stylesheet" href="/Biblioteka/css/style.css">
</head>
<body>
<header>
        <nav>
            <ul>
                <?php if(isset($_SESSION['poziom_uprawnien']) && $_SESSION['poziom_uprawnien'] === 'administrator'): ?>
                    <!-- if administrator, wyświetl guzik do panelu admina -->
                    <li><a href="php/admin_dashboard.php" >Panel administracyjny</a></li>
                <?php elseif(isset($_SESSION['poziom_uprawnien']) && $_SESSION['poziom_uprawnien'] === 'bibliotekarz'): ?>
                    <!-- if bibliotekarz, wyświetl guzik do panelu bibliotekarza -->
                    <li><a href="php/librarian_dashboard.php" >Panel bibliotekarski</a></li>
                <?php endif; ?>


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

    <h1>Witaj, <?php echo $_SESSION['login']; ?>!</h1>
    <p>To jest strona z perspektywy bibliotekarza.</p>

    <footer>
        <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
    </footer>
    
    <script src="/Biblioteka/js/librarian_dashboard_script.js"></script>
</body>
</html>
