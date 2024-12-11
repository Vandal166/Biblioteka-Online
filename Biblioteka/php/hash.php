<?php
// Sprawdź, czy przekazano hasło jako argument
if (isset($argv[1])) 
{
    $plain_password = $argv[1]; // Pobierz hasło z argumentu w konsoli

    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    
    echo "Hasło: $plain_password\n";
    echo "Zaszyfrowane hasło: $hashed_password\n";
} 
else 
{
    echo "Podaj hasło jako argument w konsoli!\n";
    echo "Przykład: php hash_password.php TwojeHaslo123\n";
}
?>
