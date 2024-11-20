document.addEventListener('DOMContentLoaded', function() {

    console.log('Focus management script loaded'); // Confirm script loading

    // Define selectors for focusable elements
    var focusableSelectors = 'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])';
    
    function updateFocusableElements() {
        // Get all focusable elements on the page
        var focusableElements = Array.from(document.querySelectorAll(focusableSelectors));
        console.log('Focusable elements:', focusableElements);

        // Update tabindex for all elements
        focusableElements.forEach(function(element) {
            if (element.hasAttribute('tabindex') && element.getAttribute('tabindex') === '-1') {
                element.setAttribute('tabindex', '0');
            }
        });
    }

    // Initial update
    updateFocusableElements();

    // Optionally, you could set up a MutationObserver to handle dynamic changes
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                updateFocusableElements();
            }
        });
    });

    // Observe the entire document for changes
    observer.observe(document.body, { childList: true, subtree: true, attributes: true });

    // Handle focus trapping within specific elements, like popups
    function trapFocus(element) {
        var focusableElements = Array.from(element.querySelectorAll(focusableSelectors));
        var firstFocusable = focusableElements[0];
        var lastFocusable = focusableElements[focusableElements.length - 1];
        
        function handleTab(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) { // Shift + Tab
                    if (document.activeElement === firstFocusable) {
                        e.preventDefault();
                        lastFocusable.focus();
                    }
                } else { // Tab
                    if (document.activeElement === lastFocusable) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                }
            }
        }

        element.addEventListener('keydown', handleTab);
    }

    // Example usage: trap focus within a specific element when it is shown
    var popup = document.getElementById('popup');
    if (popup) {
        trapFocus(popup);
    }

    // Identify all focusable elements on the page
    var focusableElements = document.querySelectorAll('a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select, [tabindex]:not([tabindex="-1"])');

    console.log('Focusable elements:', focusableElements);

    // Test focusing on the first focusable element
    if (focusableElements.length > 0) {
        focusableElements[0].focus();
        console.log('Focused on the first focusable element:', focusableElements[0]);
    }

    // Add your previous focus management functionality
    focusableElements.forEach(function(element) {
        element.addEventListener('focus', function() {
            console.log('Focused on:', element);
        });
    });

});
