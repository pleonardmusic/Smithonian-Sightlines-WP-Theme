document.addEventListener("DOMContentLoaded", function() {
    const audioPlayers = document.querySelectorAll("audio");

    audioPlayers.forEach(audio => {
        const container = document.createElement("div");
        container.classList.add("custom-audio-player");

        // Play/Pause Button
        const playPauseButton = document.createElement("button");
        playPauseButton.classList.add("play-pause");
        playPauseButton.setAttribute("aria-label", "Play/Pause");
        const playPauseIcon = document.createElement("img");
        playPauseIcon.src = "/wp-content/uploads/2024/08/play-button-new.png";
        playPauseIcon.alt = "Play button"; // Added alt text
        playPauseButton.appendChild(playPauseIcon);
        container.appendChild(playPauseButton);

        // Current Time
        const currentTime = document.createElement("span");
        currentTime.classList.add("time");
        currentTime.innerText = "0:00";
        container.appendChild(currentTime);

        // Separator
        const separator = document.createElement("span");
        separator.classList.add("separator");
        separator.innerText = "/";
        container.appendChild(separator);

        // Duration
        const duration = document.createElement("span");
        duration.classList.add("duration");
        duration.innerText = "0:00";
        container.appendChild(duration);

        // Progress Bar
        const progressContainer = document.createElement("div");
        progressContainer.classList.add("progress-container");
        const progressBar = document.createElement("div");
        progressBar.classList.add("progress-bar");
        progressContainer.appendChild(progressBar);
        container.appendChild(progressContainer);

        // Sound Toggle Button
        const soundToggleButton = document.createElement("button");
        soundToggleButton.classList.add("sound-toggle");
        soundToggleButton.setAttribute("aria-label", "Mute/Unmute");
        const soundToggleIcon = document.createElement("img");
        soundToggleIcon.src = "/wp-content/uploads/2024/08/sound-on-new.png";
        soundToggleIcon.alt = "Sound on"; // Added alt text
        soundToggleButton.appendChild(soundToggleIcon);
        container.appendChild(soundToggleButton);

        // Insert the custom player before the audio element
        audio.parentNode.insertBefore(container, audio);
        audio.style.display = "none";

        // Event listeners
        audio.addEventListener("loadedmetadata", function() {
            duration.innerText = formatTime(audio.duration);
        });

        audio.addEventListener("timeupdate", function() {
            currentTime.innerText = formatTime(audio.currentTime);
            progressBar.style.width = (audio.currentTime / audio.duration) * 100 + "%";
        });

        playPauseButton.addEventListener("click", function() {
            if (audio.paused) {
                audio.play();
                playPauseIcon.src = "/wp-content/uploads/2024/08/Pause-Button-New.png";
                playPauseIcon.alt = "Pause button"; // Update alt text on play
            } else {
                audio.pause();
                playPauseIcon.src = "/wp-content/uploads/2024/08/play-button-new.png";
                playPauseIcon.alt = "Play button"; // Update alt text on pause
            }
        });

        soundToggleButton.addEventListener("click", function() {
            audio.muted = !audio.muted;
            soundToggleIcon.src = audio.muted ? "/wp-content/uploads/2024/08/sound-mute-new.png" : "/wp-content/uploads/2024/08/sound-on-new.png";
            soundToggleIcon.alt = audio.muted ? "Sound muted" : "Sound on"; // Update alt text
        });

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            return `${minutes}:${remainingSeconds < 10 ? "0" : ""}${remainingSeconds}`;
        }
    });
});
