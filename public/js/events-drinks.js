/* ============================================
   JavaScript pour events/drinks.blade.php
   Gestion des boissons d'événement
   ============================================ */

function downloadPDF() {
    // Rediriger vers la route de téléchargement PDF
    const eventId = window.location.pathname.split('/')[2];
    window.open(`/events/${eventId}/drinks/download-pdf`, '_blank');
}

function exportToExcel() {
    // Rediriger vers la route d'export Excel
    const eventId = window.location.pathname.split('/')[2];
    window.open(`/events/${eventId}/drinks/export-excel`, '_blank');
}

// Mise à jour automatique du prix par défaut
document.addEventListener('DOMContentLoaded', function() {
    const drinkSelect = document.getElementById('drink_id');
    if (drinkSelect) {
        drinkSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const defaultPrice = selectedOption.getAttribute('data-price');
            const priceInput = document.getElementById('price_override');
            if (priceInput) {
                priceInput.placeholder = `Prix par défaut: ${defaultPrice} FC`;
            }
        });
    }
});
