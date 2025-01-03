
<section id="tabela">
    <?php
    
    // Zapytanie SQL
    $sql = "SELECT * FROM wypozyczenie";
    $result = $conn->query($sql);

    echo "<table>";
    // Nagłówki tabeli
    echo "<thead>
            <tr>
                <th>ID</th>
                <th>ID czytelnika</th>
                <th>ID egzemplarza</th>
                <th>ID pracownika</th>
                <th>Data wypożyczenia</th>
                <th>Termin oddania</th>
                <th>Data oddania</th>
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
                    <td>" . $row["ID_czytelnika"] . "</td>
                    <td>" . $row["ID_egzemplarza"] . "</td>
                    <td>" . $row["ID_pracownika"] . "</td>
                    <td>" . $row["data_wypozyczenia"] . "</td>
                    <td>" . $row["termin_oddania"] . "</td>
                    <td>" . $row["data_oddania"] . "</td>
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
