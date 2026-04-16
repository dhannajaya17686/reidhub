/**
 * User Profile Dropdown Menu Handler
 * Manages opening, closing, and keyboard navigation of the user dropdown menu
 */

document.addEventListener('DOMContentLoaded', function() {
    const menuTrigger = document.getElementById('user-menu-trigger');
    const dropdownMenu = document.getElementById('user-dropdown-menu');

    if (!menuTrigger || !dropdownMenu) {
        console.warn('User menu trigger or dropdown menu not found');
        return;
    }

    /**
     * Toggle dropdown menu visibility
     */
    function toggleDropdown() {
        const isOpen = dropdownMenu.classList.contains('dropdown-active');
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }

    /**
     * Open the dropdown menu
     */
    function openDropdown() {
        dropdownMenu.classList.add('dropdown-active');
        menuTrigger.setAttribute('aria-expanded', 'true');

        // Focus first menu item for keyboard navigation
        const firstMenuItem = dropdownMenu.querySelector('[role="menuitem"]');
        if (firstMenuItem) {
            requestAnimationFrame(() => firstMenuItem.focus());
        }
    }

    /**
     * Close the dropdown menu
     */
    function closeDropdown() {
        dropdownMenu.classList.remove('dropdown-active');
        menuTrigger.setAttribute('aria-expanded', 'false');
        menuTrigger.focus();
    }

    /**
     * Handle menu item clicks - close dropdown after navigation
     */
    function handleMenuItemClick(event) {
        const target = event.target.closest('[role="menuitem"]');
        if (target) {
            // If it's a link, let it navigate naturally and close the dropdown
            if (target.tagName === 'A' || target.tagName === 'BUTTON') {
                closeDropdown();
            }
        }
    }

    /**
     * Handle keyboard navigation
     */
    function handleKeyboard(event) {
        const isOpen = dropdownMenu.classList.contains('dropdown-active');

        // Escape key closes the menu
        if (event.key === 'Escape' && isOpen) {
            closeDropdown();
            event.preventDefault();
            return;
        }

        // If menu is not open, only Enter or Space opens it
        if (!isOpen) {
            if (event.key === 'Enter' || event.key === ' ') {
                openDropdown();
                event.preventDefault();
            }
            return;
        }

        // Menu is open - handle arrow key navigation
        const menuItems = Array.from(dropdownMenu.querySelectorAll('[role="menuitem"]'));
        const focusedItem = document.activeElement;
        let focusIndex = menuItems.indexOf(focusedItem);

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            focusIndex = (focusIndex + 1) % menuItems.length;
            menuItems[focusIndex].focus();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            focusIndex = (focusIndex - 1 + menuItems.length) % menuItems.length;
            menuItems[focusIndex].focus();
        } else if (event.key === 'Home') {
            event.preventDefault();
            menuItems[0].focus();
        } else if (event.key === 'End') {
            event.preventDefault();
            menuItems[menuItems.length - 1].focus();
        }
    }

    /**
     * Close dropdown when clicking outside
     */
    function handleClickOutside(event) {
        if (!menuTrigger.contains(event.target) && !dropdownMenu.contains(event.target)) {
            closeDropdown();
        }
    }

    /**
     * Close dropdown when navigating away
     */
    function handleMenuItemNavigation(event) {
        const target = event.target.closest('[role="menuitem"]');
        if (target && target.tagName === 'A') {
            // Let the navigation happen naturally, menu will close
            closeDropdown();
        }
    }

    // Event listeners
    menuTrigger.addEventListener('click', toggleDropdown);
    menuTrigger.addEventListener('keydown', handleKeyboard);
    dropdownMenu.addEventListener('click', handleMenuItemClick);
    dropdownMenu.addEventListener('keydown', handleKeyboard);
    dropdownMenu.addEventListener('click', handleMenuItemNavigation);
    document.addEventListener('click', handleClickOutside);

    // Close dropdown on focus outside
    document.addEventListener('focusin', function(event) {
        const isOpen = dropdownMenu.classList.contains('dropdown-active');
        if (
            isOpen &&
            !menuTrigger.contains(event.target) &&
            !dropdownMenu.contains(event.target)
        ) {
            closeDropdown();
        }
    });

    // Initialize aria-expanded attribute
    menuTrigger.setAttribute('aria-expanded', 'false');
});
