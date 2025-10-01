/* ============================================
   JavaScript pour guests/drink-choices.blade.php
   Gestion des choix de boissons des invités
   ============================================ */

// Données des invités pour JavaScript
const guests = @json($guests);
const event = @json($event);
const invitations = @json($invitations ?? []);

function downloadPDF() {
    // Rediriger vers la route de téléchargement PDF
    const eventId = event.id || window.location.pathname.split('/')[2];
    window.open(`/events/${eventId}/drink-choices/download-pdf`, '_blank');
}

function exportToExcel() {
    // Créer un tableau temporaire pour l'export
    const table = document.getElementById('guestsTable');
    const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
    
    let csvContent = "Nom,Email,Téléphone,Statut RSVP,Choix de boissons\n";
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const email = row.cells[1]?.textContent || '';
        const phone = row.cells[2]?.textContent || '';
        const status = row.getAttribute('data-status');
        const drinks = row.getAttribute('data-drinks') || 'Aucun';
        
        csvContent += `"${name}","${email}","${phone}","${status}","${drinks}"\n`;
    });
    
    // Télécharger le fichier CSV
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `choix_boissons_${event.name || 'evenement'}_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printTable() {
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('guestsTable');
    const visibleRows = Array.from(table.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
    
    let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Choix de Boissons - ${event.name || 'Événement'}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .header { text-align: center; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Choix de Boissons</h1>
                <h2>${event.name || 'Événement'}</h2>
                <p>Date: ${new Date().toLocaleDateString('fr-FR')}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Statut RSVP</th>
                        <th>Choix de boissons</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    visibleRows.forEach(row => {
        const name = row.getAttribute('data-name');
        const email = row.cells[1]?.textContent || '';
        const phone = row.cells[2]?.textContent || '';
        const status = row.getAttribute('data-status');
        const drinks = row.getAttribute('data-drinks') || 'Aucun';
        
        html += `
            <tr>
                <td>${name}</td>
                <td>${email}</td>
                <td>${phone}</td>
                <td>${status}</td>
                <td>${drinks}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </body>
        </html>
    `;
    
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.print();
}

// Initialisation des événements
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des fonctionnalités si nécessaire
    console.log('✅ Drink choices page loaded');
});
