function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('theme') || 'dark';

    applyTheme(savedTheme);

    if (!themeToggle) return;

    window.toggleTheme = () => {
        const nextTheme = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', nextTheme);
        applyTheme(nextTheme);
    };

    themeToggle.addEventListener('click', window.toggleTheme);

    function applyTheme(theme) {
        document.documentElement.dataset.theme = theme;
        if (!themeToggle) return;

        const isLight = theme === 'light';
        themeToggle.setAttribute('aria-label', isLight ? 'Ativar tema escuro' : 'Ativar tema claro');
        themeToggle.setAttribute('title', isLight ? 'Ativar tema escuro' : 'Ativar tema claro');
        themeToggle.dataset.themeLabel = isLight ? 'Escuro' : 'Claro';
        themeToggle.innerHTML = `${isLight
            ? '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.8A8.5 8.5 0 1 1 11.2 3 6.6 6.6 0 0 0 21 12.8Z"></path></svg>'
            : '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>'}<span>${isLight ? 'Escuro' : 'Claro'}</span>`;
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeToggle);
} else {
    initThemeToggle();
}
