document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.carousel').forEach(function (el) {
        new bootstrap.Carousel(el, { interval: 5000 });
    });
});
