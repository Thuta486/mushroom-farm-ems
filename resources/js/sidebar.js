const STORAGE_KEY = 'mushroom-farm-sidebar-width';
const MIN_WIDTH = 192;
const MAX_WIDTH = 352;
const DEFAULT_WIDTH = 256;

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function applySidebarWidth(width) {
    document.documentElement.style.setProperty('--sidebar-width', `${width}px`);
}

function loadSidebarWidth() {
    const stored = localStorage.getItem(STORAGE_KEY);

    if (! stored) {
        return DEFAULT_WIDTH;
    }

    const parsed = Number.parseInt(stored, 10);

    return Number.isFinite(parsed) ? clamp(parsed, MIN_WIDTH, MAX_WIDTH) : DEFAULT_WIDTH;
}

function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const resizer = document.getElementById('sidebar-resizer');
    const toggle = document.getElementById('sidebar-toggle');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (! sidebar) {
        return;
    }

    applySidebarWidth(loadSidebarWidth());

    const closeMobileSidebar = () => {
        sidebar.classList.remove('is-open');
        backdrop?.classList.add('hidden');
    };

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('is-open');
        backdrop?.classList.toggle('hidden', ! sidebar.classList.contains('is-open'));
    });

    backdrop?.addEventListener('click', closeMobileSidebar);

    sidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                closeMobileSidebar();
            }
        });
    });

    if (! resizer) {
        return;
    }

    let isResizing = false;

    const onMouseMove = (event) => {
        if (! isResizing) {
            return;
        }

        const width = clamp(event.clientX, MIN_WIDTH, MAX_WIDTH);
        applySidebarWidth(width);
    };

    const onMouseUp = () => {
        if (! isResizing) {
            return;
        }

        isResizing = false;
        document.body.classList.remove('sidebar-resizing');

        const currentWidth = sidebar.getBoundingClientRect().width;
        localStorage.setItem(STORAGE_KEY, String(Math.round(currentWidth)));

        window.removeEventListener('mousemove', onMouseMove);
        window.removeEventListener('mouseup', onMouseUp);
    };

    resizer.addEventListener('mousedown', (event) => {
        if (window.innerWidth < 1024) {
            return;
        }

        event.preventDefault();
        isResizing = true;
        document.body.classList.add('sidebar-resizing');

        window.addEventListener('mousemove', onMouseMove);
        window.addEventListener('mouseup', onMouseUp);
    });
}

document.addEventListener('DOMContentLoaded', initSidebar);
