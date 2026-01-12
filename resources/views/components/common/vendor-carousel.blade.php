<div x-data="{
    currentSlide: 0,
    totalSlides: 8,
    slidesPerView: 4,

    init() {
        this.updateSlidesPerView();
        window.addEventListener('resize', () => this.updateSlidesPerView());
    },

    updateSlidesPerView() {
        const width = window.innerWidth;
        if (width < 768) { // Mobile
            this.slidesPerView = 1;
        } else if (width < 1024) { // Tablet
            this.slidesPerView = 2;
        } else { // Desktop
            this.slidesPerView = 4;
        }
    },

    getMaxSlide() {
        return this.totalSlides - this.slidesPerView;
    },

    next() {
        if (this.currentSlide < this.getMaxSlide()) {
            this.currentSlide += 1;
        } else {
            this.currentSlide = 0; // Loop to start
        }
    },

    prev() {
        if (this.currentSlide > 0) {
            this.currentSlide -= 1;
        } else {
            this.currentSlide = this.getMaxSlide(); // Loop to end
        }
    },

    goToSlide(index) {
        if (index >= 0 && index <= this.getMaxSlide()) {
            this.currentSlide = index;
        }
    }
}"
     x-init="updateSlidesPerView()"
     class="relative overflow-hidden w-full mx-auto max-w-7xl">

    <!-- Slides Container -->
    <div
        class="flex transition-transform duration-500 ease-in-out"
        :style="`transform: translateX(-${currentSlide * (100 / slidesPerView)}%);`"
    >
        <!-- Slides (8 total as in your example) -->
        <template x-for="i in totalSlides" :key="i">
            <div
                :class="{
                    'w-full': slidesPerView === 1,
                    'w-1/2': slidesPerView === 2,
                    'w-1/3': slidesPerView === 3,
                    'w-1/4': slidesPerView === 4
                }"
                class="flex-shrink-0 px-2"
            >
                <div class="h-64 rounded-lg bg-gradient-to-br from-purple-500/10 to-pink-500/10
                          flex items-center justify-center border border-zinc-200 dark:border-zinc-800
                          hover:shadow-lg transition-all duration-300">
                    <span class="text-lg font-semibold text-zinc-700 dark:text-zinc-300">
                        Slide <span x-text="i"></span>
                    </span>
                </div>
            </div>
        </template>
    </div>

    <!-- Navigation Buttons -->
    <!-- Left Button -->
    <button
        @click="prev()"
        class="absolute left-2 top-1/2 transform -translate-y-1/2
               bg-white/90 dark:bg-zinc-800/90 backdrop-blur-sm
               p-2 rounded-full shadow-lg transition-all duration-300
               hover:scale-110 hover:shadow-xl border border-zinc-300 dark:border-zinc-700
               disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:scale-100"
        :disabled="currentSlide === 0"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-700 dark:text-zinc-300"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <!-- Right Button -->
    <button
        @click="next()"
        class="absolute right-2 top-1/2 transform -translate-y-1/2
               bg-white/90 dark:bg-zinc-800/90 backdrop-blur-sm
               p-2 rounded-full shadow-lg transition-all duration-300
               hover:scale-110 hover:shadow-xl border border-zinc-300 dark:border-zinc-700
               disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:scale-100"
        :disabled="currentSlide >= getMaxSlide()"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-700 dark:text-zinc-300"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Dots Indicator (No counter) -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
        <template x-for="i in getMaxSlide() + 1" :key="i">
            <button
                @click="goToSlide(i-1)"
                class="w-2 h-2 rounded-full transition-all duration-300"
                :class="currentSlide === i-1 ?
                       'bg-purple-600 w-4 dark:bg-purple-400' :
                       'bg-zinc-400/50 dark:bg-zinc-600/50 hover:bg-zinc-500 dark:hover:bg-zinc-500'"
                :aria-label="'Go to slide ' + i"
            ></button>
        </template>
    </div>
</div>
