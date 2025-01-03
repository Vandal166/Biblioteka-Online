
<section id="tabela">
    <?php
    // Zapytanie SQL
    $sql = "SELECT * FROM czytelnik";
    $result = $conn->query($sql);

    echo "<table>";
    // Nagłówki tabeli
    echo "<thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Numer karty</th>
                <th>Telefon</th>
                <th>E-mail</th>
                <th>Login</th>
            </tr>
        </thead>";
    echo "<tbody>";

    // Sprawdzenie, czy są dane
    if ($result->num_rows > 0) 
    {
        // Wyświetlanie danych
        while ($row = $result->fetch_assoc()) 
        {
            echo "<tr>
                    <td>" . $row["ID"] . "</td>
                    <td>" . $row["imie"] . "</td>
                    <td>" . $row["nazwisko"] . "</td>
                    <td>" . $row["nr_karty"] . "</td>
                    <td>" . $row["telefon"] . "</td>
                    <td>" . $row["email"] . "</td>
                    <td>" . $row["login"] . "</td>
                </tr>";
        }
    } 
    else 
    {
        // Pusty wiersz z wiadomością
        echo "<tr>
                <td colspan='7' class='no-data'>Brak danych</td>
            </tr>";
    }
    echo "</tbody>";
    echo "</table>";

    ?>
</section>
