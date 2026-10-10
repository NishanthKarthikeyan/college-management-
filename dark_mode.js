// ── Dark Mode Manager (shared across all pages) ──
(function() {
    // Apply saved theme IMMEDIATELY before render to prevent flash
    const saved = localStorage.getItem('rms_theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
})();

function applyDarkToggleUI(theme) {
    const icon  = document.getElementById('darkIcon');
    const label = document.getElementById('darkLabel');
    if (!icon || !label) return;
    if (theme === 'dark') {
        icon.className = 'bi bi-sun-fill';
        label.textContent = 'Light Mode';
    } else {
        icon.className = 'bi bi-moon-fill';
        label.textContent = 'Dark Mode';
    }
}

function toggleDarkMode() {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    localStorage.setItem('rms_theme', next);
    document.documentElement.setAttribute('data-theme', next);
    applyDarkToggleUI(next);
}

document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('rms_theme') || 'light';
    applyDarkToggleUI(saved);
});
