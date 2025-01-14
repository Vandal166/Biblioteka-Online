
<section id="tabela">
    <?php
   
    // Zapytanie SQL
    $sql = "SELECT * FROM rezerwacja";
    $result = $conn->query($sql);

    echo "<table>";
    // Nagłówki tabeli
    echo "<thead>
            <tr>
                <th>ID</th>
                <th>ID wydania</th>
                <th>ID czytelnika</th>
                <th>Data rezerwacji</th>
                <th>Czy wydana</th>
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
                    <td>" . $row["ID_wydania"] . "</td>
                    <td>" . $row["ID_czytelnika"] . "</td>
                    <td>" . $row["data_rezerwacji"] . "</td>
                    <td>" . ($row["czy_wydana"] ? 'Tak' : 'Nie') . "</td>
                </tr>";
        }
    } 
    else 
    {
        // Pusty wiersz z wiadomością
        echo "<tr>
                <td colspan='5' class='no-data'>Brak danych</td>
            </tr>";
    }
    echo "</tbody>";
    echo "</table>";

    ?>
</section>
