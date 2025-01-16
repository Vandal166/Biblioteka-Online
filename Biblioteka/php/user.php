<?php
session_start();

if (!isset($_SESSION['user_id']) || isset($_SESSION['poziom_uprawnien'])) {
    header("Location: ../index.php"); // Brak dostępu
    exit();
}

$conn = new mysqli("localhost", "root", "", "biblioteka");

$userID = $_SESSION['user_id'];

// Pobranie danych użytkownika
$sqlUser = "SELECT imie, nazwisko, nr_karty, telefon, email FROM czytelnik WHERE ID = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("i", $userID);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Pobranie rezerwacji użytkownika
$sqlReservations = "
    SELECT 
        rezerwacja.id AS reservation_id,
        ksiazka.tytul,
        wydanie.numer_wydania,
        wydanie.data_wydania,
        rezerwacja.data_rezerwacji
    FROM rezerwacja
    JOIN wydanie ON rezerwacja.ID_wydania = wydanie.id
    JOIN ksiazka ON wydanie.ID_ksiazki = ksiazka.id
    WHERE rezerwacja.ID_czytelnika = ?
";
$stmt = $conn->prepare($sqlReservations);
$stmt->bind_param("i", $userID);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil użytkownika</title>
    <link rel="stylesheet" href="/Biblioteka/css/style.css">
    <link rel="stylesheet" href="/Biblioteka/css/user.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona Główna</a></li>
            <li><a href="../php/books.php">Przeglądaj Książki</a></li>
            <li><a href="../php/logout.php" id="logoutBtn">Wyloguj się</a></li>
        </ul>
    </nav>
</header>

<section id="title">
<h1>= Witaj <?php echo htmlspecialchars($_SESSION['login']); ?> na swoim profilu! =</h1>
</section>



<h2>Twoje dane:</h2>
<table border=1>
    <tr>
        <th>Imię</th>
        <td><?php echo htmlspecialchars($userData['imie']); ?></td>
    </tr>
    <tr>
        <th>Nazwisko</th>
        <td><?php echo htmlspecialchars($userData['nazwisko']); ?></td>
    </tr>
    <tr>
        <th>Numer karty</th>
        <td><?php echo htmlspecialchars($userData['nr_karty']); ?></td>
    </tr>
    <tr>
        <th>Telefon</th>
        <td><?php echo htmlspecialchars($userData['telefon']); ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?php echo htmlspecialchars($userData['email']); ?></td>
    </tr>
</table>

<h2>Twoje rezerwacje:</h2>
<?php if (count($reservations) > 0): ?>
    <table id="epicwin" border="1">
        <thead>
            <tr>
                <th>Tytuł książki</th>
                <th>Numer wydania</th>
                <th>Data wydania</th>
                <th>Data rezerwacji</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservation['tytul']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['numer_wydania']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['data_wydania']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['data_rezerwacji']); ?></td>
                    <td>
                        <button onclick="cancelReservation(<?php echo $reservation['reservation_id']; ?>)">Anuluj</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Brak rezerwacji.</p>
<?php endif; ?>

<footer>
    <p>&copy; 2024 Biblioteka Online | Wszystkie prawa zastrzeżone</p>
</footer>

<script>
    function cancelReservation(reservationID) {
        fetch('/Biblioteka/php/cancel_reservation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reservationID })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload(); // Odśwież stronę po anulowaniu rezerwacji
        })
        .catch(error => console.error('Błąd:', error));
    }
</script>

</body>
</html>