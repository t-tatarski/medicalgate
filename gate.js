document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('mpg-accept');
    if (!btn) return;

    btn.addEventListener('click', function () {

        const days = 30;
        const maxAge = days * 24 * 60 * 60;

        document.cookie = "mpg_professional=1; path=/; max-age=" + maxAge;

        document.getElementById('mpg-overlay').remove();
    });
});
