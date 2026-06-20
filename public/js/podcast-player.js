document.addEventListener("alpine:init", () => {
    Alpine.store("podcastPlayer", {
        current: null,
        isPlaying: false,
        isMinimized: false,
        duration: 0,
        currentTime: 0,
        volume: 0.2,
        audioEl: null,
        progressInterval: null,
        
        playlist: [],
        currentIndex: -1,

        setPlaylist(list) {
            this.playlist = list || [];
            // Si ya hay algo reproduciéndose (navegación SPA), actualizamos su índice
            if (this.current) {
                this.currentIndex = this.playlist.findIndex(p => String(p.id) === String(this.current.id));
            }
        },

        get hasNext() {
            return this.currentIndex >= 0 && this.currentIndex < this.playlist.length - 1;
        },

        get hasPrevious() {
            return this.currentIndex > 0;
        },

        playNext() {
            if (this.hasNext) {
                this.play(this.playlist[this.currentIndex + 1]);
            }
        },

        playPrevious() {
            if (this.hasPrevious) {
                this.play(this.playlist[this.currentIndex - 1]);
            }
        },

        getAudioEl() {
            if (!this.audioEl || !document.body.contains(this.audioEl)) {
                this.audioEl = document.querySelector(".cvplayer-bar audio");
                if (this.audioEl) {
                    this.audioEl.volume = this.volume;
                }
            }
            return this.audioEl;
        },

        play(podcast) {
            const audio = this.getAudioEl();
            if (!audio) return;

            const isSameTrack = this.current?.id === podcast.id;
            this.current = podcast;
            
            // SOLUCIÓN: Convertimos ambos IDs a String para asegurar que hagan "match"
            this.currentIndex = this.playlist.findIndex(p => String(p.id) === String(podcast.id));

            if (!isSameTrack) {
                audio.src = podcast.src;
                audio.load();
            }

            audio.play();
            this.isPlaying = true;
            this.isMinimized = false;
            this.startProgressTracking();
        },

        toggle() {
            const audio = this.getAudioEl();
            if (!audio || !this.current) return;
            if (this.isPlaying) {
                audio.pause();
                this.isPlaying = false;
            } else {
                audio.play();
                this.isPlaying = true;
            }
        },

        toggleMinimize() {
            this.isMinimized = !this.isMinimized;
        },

        seek(value) {
            const audio = this.getAudioEl();
            if (audio) audio.currentTime = value;
        },

        setVolume(value) {
            const audio = this.getAudioEl();
            if (audio) audio.volume = value;
        },

        close() {
            const audio = this.getAudioEl();
            if (audio) {
                audio.pause();
                audio.removeAttribute("src");
                audio.load();
            }
            this.current = null;
            this.isPlaying = false;
            this.isMinimized = false;
            this.currentTime = 0;
            this.duration = 0;
            this.currentIndex = -1;
            clearInterval(this.progressInterval);
        },

        onEnded() {
            if (this.hasNext) {
                this.playNext();
            } else {
                this.isPlaying = false;
                this.currentTime = 0;
            }
        },

        startProgressTracking() {
            clearInterval(this.progressInterval);
            this.progressInterval = setInterval(() => {
                const audio = this.getAudioEl();
                if (audio && !audio.paused) {
                    this.currentTime = Math.floor(audio.currentTime);
                    this.duration = Math.floor(audio.duration) || 0;
                }
            }, 1000);
        },

        get formattedCurrent() {
            return this.formatTime(this.currentTime);
        },
        get formattedDuration() {
            return this.formatTime(this.duration);
        },
        formatTime(seconds) {
            if (!seconds) return "0:00";
            const m = Math.floor(seconds / 60);
            const s = Math.floor(seconds % 60).toString().padStart(2, "0");
            return `${m}:${s}`;
        },
    });
});