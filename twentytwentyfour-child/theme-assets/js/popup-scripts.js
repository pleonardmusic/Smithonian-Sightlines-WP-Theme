document.addEventListener('DOMContentLoaded', function() {
    var popup = document.getElementById('popup');
    var closePopupButton = document.getElementById('close-popup-button');
    var popupLogoLink = document.querySelector('.popup-logo-container img').closest('a');  // Correctly targeting the parent <a> of .popup-logo-container
    var focusableElements = [];
    var trapOn = false;

    function updateFocusableElements() {
        // Include all focusable elements: close button, logo link, and grid links
        focusableElements = [closePopupButton, popupLogoLink, ...popup.querySelectorAll('.thumbnail-link')];
        console.log('Focusable elements updated:', focusableElements);
        testFocus();
    }

    function testFocus() {
        if (focusableElements.length > 0) {
            console.log('Testing focus on the close button.');
            setTimeout(() => {
                focusableElements[0].focus();  // Focus on the close button initially
                console.log('Close button should now be focused.');
            }, 0);
        } else {
            console.log('No focusable elements found.');
        }
    }

    function showPopup() {
        updateFocusableElements();  // Update focusable elements when showing the popup
        popup.style.display = 'block'; // Ensure the popup is visible
        popup.setAttribute("aria-hidden", "false");
        popup.classList.add('show');
        popup.classList.remove('closed'); // Ensure to remove 'closed' if it's present
        // Set initial focus on the close button
        setTimeout(() => {
            focusableElements[0].focus();  // Set focus on the close button
        }, 0);
        trapOn = true;
    }

    function hidePopup() {
        popup.classList.remove('show'); // Remove 'show' class
        popup.classList.add('closed'); // Add 'closed' class
        setTimeout(function() {
            popup.style.display = 'none'; // Hide after animation
            popup.setAttribute("aria-hidden", "true");
            trapOn = false;
        }, 500); // Match this duration with the CSS animation duration
    }

    document.querySelectorAll('.custom-nav-button').forEach(function(button) {
        button.addEventListener('click', function() {
            if (popup.classList.contains('show')) {
                hidePopup();
            } else {
                showPopup();
            }
        });
    });

    closePopupButton.addEventListener('click', function() {
        hidePopup();
    });

    closePopupButton.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hidePopup();
        }
    });

    popup.addEventListener('keydown', function(event) {
        if (trapOn) {
            if (event.key === 'Tab') {
                event.preventDefault();

                const currentIndex = focusableElements.indexOf(document.activeElement);
                let nextIndex = event.shiftKey ? currentIndex - 1 : currentIndex + 1;

                // Ensure focus cycling correctly
                if (nextIndex < 0) {
                    nextIndex = focusableElements.length - 1;  // Loop back to the last focusable element
                } else if (nextIndex >= focusableElements.length) {
                    nextIndex = 0;  // Loop back to the first focusable element
                }

                console.log('Current index:', currentIndex);
                console.log('Next index:', nextIndex);

                focusableElements[nextIndex].focus();
            }
        }
    });

    popup.addEventListener('focusin', function() {
        if (trapOn) {
            console.log('Focusin event detected. Focus trapped.');
        }
    });
});
