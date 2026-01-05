document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('mpg-accept');
    if (!btn) return;

    btn.addEventListener('click', () => {
        const days = mpgData.cookieDays || 365;
        const expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = `mpg_professional=1; expires=${expires}; path=/`;
        location.reload();
    });
});
