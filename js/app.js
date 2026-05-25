document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.carousel').forEach(function (el) {
        new bootstrap.Carousel(el, { interval: 5000 });
    });

    // Captcha refresh on click
    document.querySelectorAll('.captcha-img').forEach(function (img) {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function () {
            this.src = '?captcha=img&generate=' + Math.random();
        });
    });

    // History back buttons
    document.querySelectorAll('[data-history-back]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            window.history.back();
        });
    });

    // Dark Mode Toggle
    var toggle = document.getElementById('theme-toggle');
    var icon   = document.getElementById('theme-icon');

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.cookie = 'easy2_theme=' + theme + '; path=/; SameSite=Strict; max-age=31536000';
        if (icon) {
            icon.className = theme === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
        }
    }

    // Sync icon with current theme on load
    var current = document.documentElement.getAttribute('data-bs-theme') || 'light';
    if (icon) {
        icon.className = current === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            var t = document.documentElement.getAttribute('data-bs-theme') || 'light';
            applyTheme(t === 'dark' ? 'light' : 'dark');
        });
    }
});
