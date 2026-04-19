// Summernote Initialization for Admin Settings

$(document).ready(function() {
    if (typeof $.fn.summernote === 'undefined') {
        console.error('Summernote plugin not loaded.');
        return;
    }

    if ($('#impressum_content').length) {
        $('#impressum_content').summernote({
            lang: 'de-DE',
            height: 300,
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    }

    if ($('#privacy_policy').length) {
        $('#privacy_policy').summernote({
            lang: 'de-DE',
            height: 300,
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    }
});
