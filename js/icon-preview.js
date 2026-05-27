(function () {
    'use strict';
    var VALID = /^[a-zA-Z0-9_\- ]*$/;

    function initIconPreview(inputId, previewId) {
        var input   = document.getElementById(inputId);
        var preview = document.getElementById(previewId);
        if (!input || !preview) return;

        function update() {
            var val = input.value.trim();
            while (preview.firstChild) preview.removeChild(preview.firstChild);

            if (!val) return;

            if (!VALID.test(val)) {
                var warn = document.createElement('span');
                warn.className = 'text-danger';
                var warnIcon = document.createElement('i');
                warnIcon.className = 'fa fa-times-circle';
                warn.appendChild(warnIcon);
                warn.appendChild(document.createTextNode(' Ungültige Zeichen — nur a-z, 0-9, Bindestrich, Unterstrich, Leerzeichen erlaubt'));
                preview.appendChild(warn);
                return;
            }

            var wrap = document.createElement('span');
            wrap.className = 'text-secondary';
            wrap.appendChild(document.createTextNode('Vorschau: '));
            var icon = document.createElement('i');
            icon.className = 'fa fa-fw ' + val;
            wrap.appendChild(icon);
            wrap.appendChild(document.createTextNode(' ' + val));
            preview.appendChild(wrap);
        }

        input.addEventListener('input', update);
        update();
    }

    initIconPreview('icon-add',  'icon-preview-add');
    initIconPreview('icon-edit', 'icon-preview-edit');
}());
