/* ============================================
   JavaScript pour la gestion des invités
   Gestion des invités
   ============================================ */

function downloadGuestsPDF() {
    window.open('/guests/download-pdf', '_blank');
}

function downloadGuestPDF(guestId) {
    window.open(`/guests/${guestId}/download-pdf`, '_blank');
}
