/**
 * NextGen Admin - Theme Toggle System
 * Handles dark/light mode switching with localStorage persistence
 */

(function () {
    'use strict';

    const THEME_KEY = 'nextgen-admin-theme';
    const THEME_DARK = 'dark-mode';
    const THEME_LIGHT = 'light-mode';

    /**
     * Initialize theme on page load
     */
    function initTheme() {
        // Get saved theme or default to dark
        const savedTheme = localStorage.getItem(THEME_KEY) || THEME_DARK;

        // Apply theme immediately to prevent flash
        document.body.classList.remove(THEME_DARK, THEME_LIGHT);
        document.body.classList.add(savedTheme);

        // Update icon
        updateIcon(savedTheme);
    }

    /**
     * Toggle between dark and light themes
     */
    function toggleTheme() {
        const currentTheme = document.body.classList.contains(THEME_LIGHT)
            ? THEME_LIGHT
            : THEME_DARK;

        const newTheme = currentTheme === THEME_LIGHT ? THEME_DARK : THEME_LIGHT;

        // Apply new theme
        document.body.classList.remove(THEME_DARK, THEME_LIGHT);
        document.body.classList.add(newTheme);

        // Save to localStorage
        localStorage.setItem(THEME_KEY, newTheme);

        // Update icon with animation
        updateIcon(newTheme);

        // Trigger custom event for other scripts
        window.dispatchEvent(new CustomEvent('themeChanged', {
            detail: { theme: newTheme }
        }));
    }

    /**
     * Update the theme toggle icon
     */
    function updateIcon(theme) {
        const button = document.getElementById('light-dark-mode');
        if (!button) return;

        const icon = button.querySelector('i');
        if (!icon) return;

        // Remove existing icon classes
        icon.classList.remove('ri-moon-line', 'ri-sun-line');

        // Add appropriate icon based on theme
        if (theme === THEME_LIGHT) {
            icon.classList.add('ri-moon-line'); // Show moon in light mode (to switch to dark)
        } else {
            icon.classList.add('ri-sun-line'); // Show sun in dark mode (to switch to light)
        }
    }

    /**
     * Add visual feedback on button click
     */
    function addButtonFeedback(button) {
        button.style.transform = 'scale(0.9) rotate(180deg)';
        setTimeout(() => {
            button.style.transform = '';
        }, 300);
    }

    // Initialize theme before DOM loads to prevent flash
    initTheme();

    // Setup event listener when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('light-dark-mode');

        if (toggleButton) {
            toggleButton.addEventListener('click', function (e) {
                e.preventDefault();
                addButtonFeedback(this);
                toggleTheme();
            });
        }

        // Re-apply theme in case of any conflicts
        initTheme();

        console.log('✨ NextGen Theme System initialized');
    });

    // Also expose toggle function globally for manual use
    window.toggleTheme = toggleTheme;
})();
