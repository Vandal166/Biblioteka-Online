
<section id="tabela">
    <?php
    
    // Zapytanie SQL
    $sql = "SELECT * FROM ksiazka";
    $result = $conn->query($sql);

    echo "<table>";
    // Nagłówki tabeli
    echo "<thead>
            <tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Zdjęcie</th>
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
                    <td>" . $row["tytul"] . "</td>
                    <td>" . $row["zdjecie"] . "</td>
                </tr>";
        }
    } 
    else 
    {
        // Pusty wiersz z wiadomością
        echo "<tr>
                <td colspan='3' class='no-data'>Brak danych</td>
            </tr>";
    }
    echo "</tbody>";
    echo "</table>";

    ?>
</section>
