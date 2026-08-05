const STORAGE_KEY = 'mushroom-farm-theme';
const systemThemeQuery = window.matchMedia('(prefers-color-scheme: dark)');

function getStoredTheme() {
    try {
        const theme = localStorage.getItem(STORAGE_KEY);

        return ['light', 'dark', 'system'].includes(theme) ? theme : 'system';
    } catch {
        return 'system';
    }
}

function getResolvedTheme(theme) {
    return theme === 'system' ? (systemThemeQuery.matches ? 'dark' : 'light') : theme;
}

function updateThemeControls(theme) {
    document.querySelectorAll('[data-theme-value]').forEach((button) => {
        button.setAttribute('aria-pressed', String(button.dataset.themeValue === theme));
    });
}

function applyTheme(theme) {
    document.documentElement.dataset.theme = getResolvedTheme(theme);
    document.documentElement.dataset.themeMode = theme;
    updateThemeControls(theme);
}

function saveTheme(theme) {
    try {
        localStorage.setItem(STORAGE_KEY, theme);
    } catch {
        // The current browser context does not allow persistent storage.
    }
}

function initTheme() {
    const theme = getStoredTheme();

    applyTheme(theme);

    document.querySelectorAll('[data-theme-value]').forEach((button) => {
        button.addEventListener('click', () => {
            const selectedTheme = button.dataset.themeValue;

            if (! ['light', 'dark', 'system'].includes(selectedTheme)) {
                return;
            }

            saveTheme(selectedTheme);
            applyTheme(selectedTheme);
        });
    });

    systemThemeQuery.addEventListener('change', () => {
        if (getStoredTheme() === 'system') {
            applyTheme('system');
        }
    });
}

document.addEventListener('DOMContentLoaded', initTheme);
