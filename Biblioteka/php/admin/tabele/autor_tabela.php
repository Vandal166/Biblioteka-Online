<section id="tabela">
    <?php
    
    // Zapytanie SQL
    $sql = "SELECT * FROM autor";
    $result = $conn->query($sql);

    echo "<table>";
    // Nagłówki tabeli
    echo "<thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
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
