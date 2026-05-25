$(function () {
    $('.carousel').carousel({ interval: 5000 });

    // Captcha refresh on click
    $('.captcha-img').css('cursor', 'pointer').on('click', function () {
        $(this).attr('src', './?captcha=img&generate=' + Math.random());
    });

    // History back buttons
    $('[data-history-back]').on('click', function (e) {
        e.preventDefault();
        window.history.back();
    });
});
