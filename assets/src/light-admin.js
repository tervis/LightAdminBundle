/** LightAdminBundle */
import { initThemeSwitch } from './js/theme-switch.js';
import ToggleSwitch from './js/toggle-switch.js';
import { handleBatchDelete } from './js/batch-delete.js';

// --- Local Storage Keys ---
const LOCAL_STORAGE_SIDEBAR_KEY = 'lightAdminSidebarActive';
/**
 * Saves the current sidebar state to local storage.
 * @param {boolean} isActive - True if the sidebar is active (collapsed), false otherwise.
 */
const saveSidebarState = (isActive) => {
    try {
        localStorage.setItem(LOCAL_STORAGE_SIDEBAR_KEY, isActive);
    } catch (e) {
        console.error('Error saving sidebar state to local storage:', e);
    }
};
/**
 * Loads the sidebar state from local storage and applies it.
 * This should ideally be called as early as possible.
 *
 * Note: For immediate visual application before full DOMContentLoaded,
 * you might need to run a small inline script in your HTML <head>
 * or ensure this script is deferred but its initial application
 * is handled by CSS based on a class (e.g., if a class is added to <body>).
 * For this JS, we'll apply it at DOMContentLoaded.
 */
const loadSidebarState = () => {
    const sidebar = document.getElementById('sidebar');
    const body = document.querySelector('body');

    if (sidebar && body) {
        try {
            const savedState = localStorage.getItem(LOCAL_STORAGE_SIDEBAR_KEY);
            if (savedState !== null) {
                const isActive = savedState === 'true'; // localStorage stores booleans as strings
                if (isActive) {
                    sidebar.classList.add('active');
                    body.classList.add('active');
                } else {
                    sidebar.classList.remove('active');
                    body.classList.remove('active');
                }
            }
        } catch (e) {
            console.error('Error loading sidebar state from local storage:', e);
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // --- Apply saved sidebar state immediately on DOMContentLoaded ---
    loadSidebarState();

    // --- Sidebar Collapse Logic ---
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const body = document.querySelector('body');

    // Define the breakpoint for small screens (should match CSS breakpoint)
    const SMALL_SCREEN_BREAKPOINT = 768;

    /**
     * Checks if the current screen width is considered a small screen.
     * @returns {boolean} True if it's a small screen, false otherwise.
     */
    const isSmallScreen = () => window.innerWidth < SMALL_SCREEN_BREAKPOINT;

    /**
     * Updates the sidebar and body classes based on screen size.
     * Also saves the state to local storage.
     */
    const updateSidebarState = () => {
        if (sidebar && body) {
            if (isSmallScreen()) {
                // If on a small screen, ensure sidebar is collapsed
                sidebar.classList.add('active');
                body.classList.add('active');
                saveSidebarState(true); // Save state on small screen collapse
            } else {
                // If on a large screen, ensure sidebar is expanded (or remove collapse classes)
                sidebar.classList.remove('active');
                body.classList.remove('active');
                saveSidebarState(false); // Save state on large screen expand
            }
        }
    };

    // Initial state setup on page load
    updateSidebarState();

    // Add listener to update sidebar state dynamically
    window.addEventListener('resize', updateSidebarState);

    if (sidebarCollapse && sidebar && body) {
        sidebarCollapse.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            body.classList.toggle('active');
            // Save the new state after toggling
            saveSidebarState(!sidebar.classList.contains('active'));
        });
    }


    // --- Toggle Switches Initialization ---
    document.querySelectorAll('td.field-boolean .form-switch input[type="checkbox"]').forEach(toggleField => {
        try {
            new ToggleSwitch(toggleField);
        } catch (error) {
            console.error('Error initializing ToggleSwitch:', error);
        }
    });

    // --- Batch Delete Button Listener ---
    const batchDeleteButton = document.getElementById('batchDeleteButton');
    if (batchDeleteButton) {
        batchDeleteButton.addEventListener('click', handleBatchDelete);
    }

    // --- Theme Switcher Initialization ---
    initThemeSwitch();

});