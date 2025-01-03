
<section id="tabela">
    <?php

    // Zapytanie SQL
    $sql = "SELECT * FROM wydanie";
    $result = $conn->query($sql);

    echo "<table>";
    // Nagłówki tabeli
    echo "<thead>
            <tr>
                <th>ID</th>
                <th>ID książki</th>
                <th>ID wydawnictwa</th>
                <th>ISBN</th>
                <th>Data wydania</th>
                <th>Numer wydania</th>
                <th>Język</th>
                <th>Ilość stron</th>
                <th>Czy elektornicznie</th>
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
                    <td>" . $row["ID_ksiazki"] . "</td>
                    <td>" . $row["ID_wydawnictwa"] . "</td>
                    <td>" . $row["ISBN"] . "</td>
                    <td>" . $row["data_wydania"] . "</td>
                    <td>" . $row["numer_wydania"] . "</td>                        
                    <td>" . $row["jezyk"] . "</td>
                    <td>" . $row["ilosc_stron"] . "</td>
                    <td>" . $row["czy_elektronicznie"] . "</td>
                </tr>";
        }
    } 
    else 
    {
        // Pusty wiersz z wiadomością
        echo "<tr>
                <td colspan='9' class='no-data'>Brak danych</td>
            </tr>";
    }
    echo "</tbody>";
    echo "</table>";

    ?>
</section>
