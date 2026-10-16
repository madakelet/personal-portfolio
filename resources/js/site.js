// Theme toggle: auto -> light -> dark, stored per browser.
// The <head> applies the stored value before paint; this keeps the button in sync.
const root = document.documentElement;
const button = document.getElementById('theme');
const order = ['auto', 'light', 'dark'];

function applyTheme(theme) {
    if (theme === 'auto') root.removeAttribute('data-theme');
    else root.dataset.theme = theme;
    if (button) button.textContent = theme;
    try {
        localStorage.setItem('theme', theme);
    } catch (e) {}
}

let saved = 'auto';
try {
    saved = localStorage.getItem('theme') || 'auto';
} catch (e) {}
applyTheme(order.includes(saved) ? saved : 'auto');

button?.addEventListener('click', () => {
    applyTheme(order[(order.indexOf(button.textContent) + 1) % order.length]);
});
