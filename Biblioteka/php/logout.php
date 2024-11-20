<?php
// start sesji, aby móc modyfikować dane sesji
session_start();

// usuwanie danych użytkownika z sesji
session_unset(); // usuwa wszystkie zmienne sesyjne
session_destroy(); // konczy sesję

// przekierowanie użytkownika na stronę główną po wylogowaniu
header("Location: ../index.php");
exit();
?>
