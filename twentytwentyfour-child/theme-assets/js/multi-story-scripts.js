jQuery(document).ready(function($) {
    // Initialize the popup
    var postLinks = $('.post-popup-link');
    var postPopup = $('#post-popup');
    var postPopupContent = $('#post-popup-content');
    var closePostPopup = $('#close-post-popup');

    function ensureLinksFocusable() {
        // Make sure all post-popup-link elements are focusable
        postLinks.each(function() {
            $(this).attr('tabindex', '0');
        });
    }

    function updateLinkFocusability() {
        // Update tabindex after popup is opened or closed
        var expandedLink = $('.post-popup-link[aria-expanded="true"]');
        expandedLink.attr('tabindex', '0'); // Ensure the link is focusable
        setTimeout(function() {
            postPopup.css('display', 'none');
            postPopupContent.empty();
            postPopup.removeClass('show');
            expandedLink.attr('tabindex', '-1'); // Reset tabindex when popup is closed
            
            // Conditionally return focus based on a flag or state
            if (shouldReturnFocus) {
                expandedLink.focus(); // Return focus to the link
            }
        }, 500);
    }
    

    postLinks.on('click', function(e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        var $currentLink = $(this);

        fetch("/wp-json/wp/v2/posts/" + postId).then(function(response) {
            return response.json();
        }).then(function(post) {
            postPopupContent.html("<h1 id='post-popup-title in-view' style='text-align: center; margin-top: 40px; opacity: unset;'>" + post.title.rendered + "</h1>" + post.content.rendered);
            
            postPopup.addClass('slide-up').removeClass('slide-down').css('display', 'flex').attr('aria-hidden', 'false');
            $currentLink.attr('aria-expanded', 'true');
            postPopup.scrollTop(0);
            postPopup.addClass('show');

            // Focus the close button after the popup is displayed
            closePostPopup.focus();

            // Ensure all links are focusable
            ensureLinksFocusable();

            // Initialize custom audio players inside the popup
            initCustomAudioPlayers(postPopupContent);
        });
    });

    closePostPopup.on('click', function() {
        postPopup.removeClass('slide-up').addClass('slide-down').attr('aria-hidden', 'true');
        updateLinkFocusability();
    });

    // Handling focus trapping inside the popup
    $(document).on('keydown', function(e) {
        if (postPopup.hasClass('show')) {
            var focusableElements = postPopupContent.find('a, button, input, [tabindex]:not([tabindex="-1"])');
            var allFocusableElements = focusableElements.add(closePostPopup); // Include close button in focusable elements
            var firstFocusableElement = allFocusableElements.first();
            var lastFocusableElement = allFocusableElements.last();

            if (e.key === 'Tab') {
                if (e.shiftKey) { // Shift + Tab
                    if ($(document.activeElement).is(firstFocusableElement)) {
                        e.preventDefault();
                        lastFocusableElement.focus();
                    }
                } else { // Tab
                    if ($(document.activeElement).is(lastFocusableElement)) {
                        e.preventDefault();
                        firstFocusableElement.focus();
                    }
                }
            }
        }
    });

    // Function to initialize custom audio players inside specific content
    function initCustomAudioPlayers(content) {
        content.find('audio').each(function() {
            var audioElement = $(this);
            audioElement.hide();

            // Create a custom audio player and append it after the hidden audio element
            var container = $('<div class="custom-audio-player"></div>');

            // Play/Pause button with image
            var playPauseButton = $('<button class="play-pause"></button>');
            var playPauseIcon = $('<img src="/wp-content/uploads/2024/08/play-button-new.png" alt="Play/Pause">');
            playPauseButton.append(playPauseIcon);
            container.append(playPauseButton);

            // Time display
            var currentTime = $('<span class="time">0:00</span>');
            container.append(currentTime);

            // Separator
            var separator = $('<span class="separator">/</span>');
            container.append(separator);

            // Duration display
            var duration = $('<span class="duration">0:00</span>');
            container.append(duration);

            // Progress bar
            var progressContainer = $('<div class="progress-container"></div>');
            var progressBar = $('<div class="progress-bar"></div>');
            progressContainer.append(progressBar);
            container.append(progressContainer);

            // Sound toggle button with image
            var soundToggleButton = $('<button class="sound-toggle"></button>');
            var soundToggleIcon = $('<img src="/wp-content/uploads/2024/08/sound-on-new.png" alt="Sound Toggle">');
            soundToggleButton.append(soundToggleIcon);
            container.append(soundToggleButton);

            container.insertAfter(audioElement);

            // Event listeners for custom player controls
            playPauseButton.on('click', function() {
                if (audioElement[0].paused) {
                    audioElement[0].play();
                    playPauseIcon.attr('src', '/wp-content/uploads/2024/08/Pause-Button-New.png');
                } else {
                    audioElement[0].pause();
                    playPauseIcon.attr('src', '/wp-content/uploads/2024/08/play-button-new.png');
                }
            });

            soundToggleButton.on('click', function() {
                audioElement[0].muted = !audioElement[0].muted;
                soundToggleIcon.attr('src', audioElement[0].muted ? '/wp-content/uploads/2024/08/sound-mute-new.png' : '/wp-content/uploads/2024/08/sound-on-new.png');
            });

            audioElement.on('loadedmetadata', function() {
                duration.text(formatTime(audioElement[0].duration));
            });

            audioElement.on('timeupdate', function() {
                currentTime.text(formatTime(audioElement[0].currentTime));
                progressBar.css('width', (audioElement[0].currentTime / audioElement[0].duration) * 100 + '%');
            });

            progressContainer.on('click', function(e) {
                var offset = $(this).offset();
                var totalWidth = $(this).width();
                var clickPosition = e.pageX - offset.left;
                var clickPercent = clickPosition / totalWidth;
                audioElement[0].currentTime = audioElement[0].duration * clickPercent;
            });

            function formatTime(seconds) {
                var minutes = Math.floor(seconds / 60);
                var remainingSeconds = Math.floor(seconds % 60);
                return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
            }
        });
    }

    // Ensure links are focusable after page load
    setTimeout(ensureLinksFocusable, 500); // Adjust delay as needed
});
