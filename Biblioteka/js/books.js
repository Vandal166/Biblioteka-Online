document.addEventListener("DOMContentLoaded", () => 
{
    const booksContainer = document.getElementById("books");
    const paginationContainer = document.getElementById("pagination");
    const itemsPerPage = 12; // Liczba książek na stronę

    let currentPage = 1;

    // Funkcja do pobierania książek z API
    async function fetchBooks(page = 1) 
    {
        const response = await fetch(`booksAPI.php?page=${page}`);
        const data = await response.json();

        renderBooks(data.books);
        renderPagination(data.totalPages, page);
    }

    // Funkcja do renderowania książek w gridzie
    function renderBooks(books) {
    booksContainer.innerHTML = books
        .map(book => `
            <div class="book-item">
                <img src="${book.zdjecie}" alt="${book.tytul}" 
                     style="width: 100%; height: auto;" 
                     onclick="handleBookClick(${book.id}, '${book.tytul}')"
                     onerror="this.onerror=null; this.src='/Biblioteka/images/error.jpg';">
                <h3>${book.tytul}</h3>
                <p>Autorzy: ${book.autorzy}</p>
            </div>
        `)
        .join("");
    }
    
    
    // Funkcja do renderowania przycisków paginacji
    function renderPagination(totalPages, currentPage) 
    {
        paginationContainer.innerHTML = "";
        for (let i = 1; i <= totalPages; i++) 
        {
            const button = document.createElement("button");
            button.textContent = i;
            button.classList.toggle("active", i === currentPage);
            button.addEventListener("click", () => 
            {
                fetchBooks(i);
            });
            paginationContainer.appendChild(button);
        }
    }

    // Wczytanie początkowej strony
    fetchBooks(currentPage);
});

// Sekcja Pop-up --------------------------------------------------------------------------------------------

let currentBookID = null; // Zmienna globalna do przechowywania ID książki
let currentBookTitle = null; // Zmienna globalna do przechowywania tytułu książki

function openEditionModal(bookID, bookTitle) {
    // Przechowaj ID i tytuł książki w zmiennych globalnych
    currentBookID = bookID;
    currentBookTitle = bookTitle;

    // Ustaw tytuł popupu
    document.getElementById("modalBookTitle").textContent = bookTitle;

    // Pobierz dostępne wydania dla książki
    fetch(`/Biblioteka/php/get-editions.php?bookID=${bookID}`)
        .then(response => response.json())
        .then(data => {
            const { editions, isLoggedIn, poziomUprawnien } = data;
            const editionList = document.getElementById("editionList");
            editionList.innerHTML = ""; // Wyczyść poprzednią listę

            editions.forEach(edition => {
                const li = document.createElement("li");

                // Popraw ścieżkę do PDF
                const pdfUrl = edition.pdf ? edition.pdf.replace('/php/', '/') : null;

                // Czy wydanie ma PDF
                const pdfLink = pdfUrl
                    ? `<a href="${pdfUrl}" target="_blank">Otwórz PDF</a>`
                    : '<span>PDF niedostępny</span>';

                // Status rezerwacji - ukryj przyciski dla bibliotekarzy i administratorów
                const reserveButton = isLoggedIn && poziomUprawnien !== 'bibliotekarz' && poziomUprawnien !== 'administrator'
                    ? edition.reserved
                        ? `<button onclick="cancelReservation(${edition.reservation_id})">Anuluj rezerwację</button>`
                        : `<button onclick="reserveEdition(${edition.id})">Zarezerwuj</button>`
                    : ''; // Brak przycisku dla bibliotekarzy/administratorów

                li.innerHTML = `
                    <p>
                        Wydanie: ${edition.nazwa} (${edition.jezyk}, ${edition.ilosc_stron} stron, ${edition.data_wydania}) 
                    </p>
                    <div>${pdfLink}</div>
                    <div>${reserveButton}</div>
                `;
                editionList.appendChild(li);
            });

            // Pokaż popup
            document.getElementById("editionModal").style.display = "block";
            document.body.style.overflow = "hidden"; // Zablokuj scroll na stronie
        })
        .catch(error => console.error("Błąd:", error));
}

function handleBookClick(bookID, bookTitle) {
    window.location.href = `/Biblioteka/php/books.php?bookID=${bookID}&bookTitle=${encodeURIComponent(bookTitle)}`;
}

function reserveEdition(editionID) {
    fetch(`/Biblioteka/php/reserve.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ editionID })
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            // Odśwież popup po rezerwacji
            openEditionModal(currentBookID, currentBookTitle);
        })
        .catch(error => console.error("Błąd:", error));
}

function cancelReservation(reservationID) {
    fetch(`/Biblioteka/php/cancel_reservation.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ reservationID })
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            // Odśwież popup po anulowaniu rezerwacji
            openEditionModal(currentBookID, currentBookTitle);
        })
        .catch(error => console.error("Błąd:", error));
}

function closeEditionModal() {
    document.getElementById("editionModal").style.display = "none";
    document.body.style.overflow = ""; // Odblokuj scroll na stronie
}
