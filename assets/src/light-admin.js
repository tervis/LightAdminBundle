/** LightAdminBundle */
import { initThemeSwitch } from './js/theme-switch.js';
import ToggleSwitch from './js/toggle-switch.js';
import { handleBatchDelete } from './js/batch-delete.js';


document.addEventListener('DOMContentLoaded', function() {
    
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
     */
    const updateSidebarState = () => {
        if (sidebar && body) {
            if (isSmallScreen()) {
                // If on a small screen, ensure sidebar is collapsed
                sidebar.classList.add('active');
                body.classList.add('active');
            } else {
                // If on a large screen, ensure sidebar is expanded (or remove collapse classes)
                sidebar.classList.remove('active');
                body.classList.remove('active');
            }
        }
    };

    // Initial state setup on page load
    updateSidebarState();

    // Add resize listener to update sidebar state dynamically
    window.addEventListener('resize', updateSidebarState);

    if (sidebarCollapse && sidebar && body) {
        sidebarCollapse.addEventListener('click', () => {
            //console.log('Toggling sidebar and body classes.');
            sidebar.classList.toggle('active');
            body.classList.toggle('active');
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
    initThemeSwitch(); // Call the theme switch initialization

});