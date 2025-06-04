// theme-switch.js (or directly in your HTML <script> tags)


// Helper functions
function getStoredTheme() {
    return localStorage.getItem('theme');
}

function setStoredTheme(theme) {
    localStorage.setItem('theme', theme);
}

function getPreferredTheme() {
    const storedTheme = getStoredTheme();
    if (storedTheme) {
        return storedTheme;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function setTheme(theme) {
    if (theme === 'auto') {
        document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    } else {
        document.documentElement.setAttribute('data-bs-theme', theme);
    }
}

function showActiveTheme(theme, focus = false) {
    const themeSwitcher = document.querySelector('#bd-theme');

    if (!themeSwitcher) {
        return;
    }

    const themeSwitcherText = document.querySelector('#bd-theme-text');
    const activeThemeIcon = document.querySelector('.theme-icon-active use');
    const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);

    // Check if the button for the active theme exists before proceeding
    if (!btnToActive) {
        console.warn(`Theme switch button for value "${theme}" not found.`);
        return;
    }
    const svgOfActiveBtn = btnToActive.querySelector('svg use')?.getAttribute('href');

    document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
        element.classList.remove('active');
        element.setAttribute('aria-pressed', 'false');
    });

    btnToActive.classList.add('active');
    btnToActive.setAttribute('aria-pressed', 'true');

    if (activeThemeIcon && svgOfActiveBtn) {
        activeThemeIcon.setAttribute('href', svgOfActiveBtn);
    }

    if (themeSwitcherText) {
        const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`;
        themeSwitcher.setAttribute('aria-label', themeSwitcherLabel);
    }


    if (focus) {
        themeSwitcher.focus();
    }
}


export function initThemeSwitch() {
    const preferredTheme = getPreferredTheme();
    setTheme(preferredTheme);
    showActiveTheme(preferredTheme, false);

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const storedTheme = getStoredTheme();
        if (storedTheme !== 'light' && storedTheme !== 'dark') {
            setTheme(getPreferredTheme());
            showActiveTheme(getPreferredTheme(), false);
        }
    });

    document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const theme = toggle.getAttribute('data-bs-theme-value');
            setStoredTheme(theme);
            setTheme(theme);
            showActiveTheme(theme, true);
        });
    });
}