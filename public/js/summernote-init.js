/* ============================================
   JavaScript pour l'initialisation de Summernote
   ============================================ */

$(document).ready(function() {
    // Initialiser Summernote sur le champ message
    $('.summernote').summernote({
        height: 200,
        lang: 'fr-FR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        placeholder: 'Écrivez votre message ici...',
        callbacks: {
            onInit: function() {
                console.log('Summernote initialisé sur le champ message');
            }
        }
    });
});
