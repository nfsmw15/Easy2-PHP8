document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.carousel').forEach(function (el) {
        new bootstrap.Carousel(el, { interval: 5000 });
    });

    // Dark Mode Toggle
    var toggle = document.getElementById('theme-toggle');
    var icon   = document.getElementById('theme-icon');

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('easy2_theme', theme);
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
