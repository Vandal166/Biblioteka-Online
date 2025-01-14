// nasłuchiwanie na załadowanie strony
document.addEventListener('DOMContentLoaded', () => {
    
    // smooth scroll danych<td> po sortowaniu
    const table = document.querySelector('.sortable'); 
    table.addEventListener('click', function(event) 
    {
        if (!event.target.closest('.actions')) {
            table.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
    
}); 

// Event listener dla ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Event listener dla klikania poza modal
document.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('.modal, .popup');
    modals.forEach(modal => {
        if (event.target === modal) {
            closeModal();
        }
    });
});

//modal dodawania
function openAddBookModal() {
    document.getElementById('addBookModal').style.display = 'block';
    // wylaczenie scrolla na stronie ale dozwolone w modalu
    document.body.style.overflow = 'hidden';
}     

// Funkcja wyświetlająca globalny pop-up sukcesu
function showGlobalSuccessMessage(message) {
    const popup = document.getElementById('successPopup');
    const messageContainer = document.getElementById('successPopupMessage');
    messageContainer.textContent = message;
    popup.style.display = 'flex';
}
function closeModal()
{
    //zamkniecie
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
    document.querySelectorAll('.popup').forEach(popup => {
        popup.style.display = 'none';
    });
    document.body.style.overflow = 'auto';
}

