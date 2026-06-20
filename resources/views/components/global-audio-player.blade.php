@persist('reproductor-podcasts')
<div
    x-data
    x-show="$store.podcastPlayer.current"
    x-cloak
    x-transition:enter="cvplayer-enter"
    x-transition:enter-start="cvplayer-enter-start"
    x-transition:enter-end="cvplayer-enter-end"
    class="cvplayer-bar"
    :class="{ 'cvplayer-bar--minimized': $store.podcastPlayer.isMinimized }"
    style="display: none;">
    <button class="cvplayer-toggle-btn" @click="$store.podcastPlayer.toggleMinimize()" title="Minimizar/Maximizar reproductor">
        <svg x-show="!$store.podcastPlayer.isMinimized" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
        <svg x-show="$store.podcastPlayer.isMinimized" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    </button>

    <audio x-ref="globalAudio" @ended="$store.podcastPlayer.onEnded()"></audio>

    <div class="cvplayer-inner">
        <div class="cvplayer-meta">
            <div class="cvplayer-cover">
                <template x-if="$store.podcastPlayer.current?.cover">
                    <img :src="$store.podcastPlayer.current.cover" :alt="$store.podcastPlayer.current.title">
                </template>
                <template x-if="!$store.podcastPlayer.current?.cover">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                </template>
            </div>
            <div class="cvplayer-text">
                <p class="cvplayer-title" x-text="$store.podcastPlayer.current?.title"></p>
                <p class="cvplayer-subtitle">Cepre Vallejo · Podcast</p>
            </div>
        </div>

        <div class="cvplayer-controls">
            <div class="cvplayer-play-group">
                <button class="cvplayer-skip-btn"
                    @click="$store.podcastPlayer.playPrevious()"
                    :disabled="!$store.podcastPlayer.hasPrevious"
                    :style="!$store.podcastPlayer.hasPrevious ? 'opacity: 0.3; cursor: default;' : ''">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z" />
                    </svg>
                </button>

                <button class="cvplayer-play-btn" @click="$store.podcastPlayer.toggle()">
                    <svg x-show="!$store.podcastPlayer.isPlaying" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                    <svg x-show="$store.podcastPlayer.isPlaying" x-cloak viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 5h4v14H6zm8 0h4v14h-4z" />
                    </svg>
                </button>

                <button class="cvplayer-skip-btn"
                    @click="$store.podcastPlayer.playNext()"
                    :disabled="!$store.podcastPlayer.hasNext"
                    :style="!$store.podcastPlayer.hasNext ? 'opacity: 0.3; cursor: default;' : ''">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z" />
                    </svg>
                </button>
            </div>

            <div class="cvplayer-progress-row">
                <span class="cvplayer-time" x-text="$store.podcastPlayer.formattedCurrent"></span>
                <input
                    type="range"
                    class="cvplayer-seek"
                    min="0"
                    :max="$store.podcastPlayer.duration || 0"
                    x-model.number="$store.podcastPlayer.currentTime"
                    @input="$store.podcastPlayer.seek($event.target.value)">
                <span class="cvplayer-time" x-text="$store.podcastPlayer.formattedDuration"></span>
            </div>
        </div>

        <div class="cvplayer-extra">
            <div class="cvplayer-volume">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 10v4h4l5 5V5L7 10H3zm13.5 2A4.5 4.5 0 0014 7.97v8.05c1.48-.73 2.5-2.25 2.5-4.02z" />
                </svg>
                <input
                    type="range"
                    class="cvplayer-volume-slider"
                    min="0" max="1" step="0.05"
                    x-model.number="$store.podcastPlayer.volume"
                    @input="$store.podcastPlayer.setVolume($event.target.value)">
            </div>
            <button class="cvplayer-close-btn" @click="$store.podcastPlayer.close()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
@endpersist