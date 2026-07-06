// Filament reinyecta y reejecuta este script en cada navegación SPA
// (wire:navigate), así que basta con inicializar de inmediato en cada
// ejecución (el DOM ya existe: el script se imprime al final del stack de
// @push('scripts')). Se envuelve en un IIFE porque, si `initDynamicSwiper`
// se declarara en el scope global, la segunda ejecución lanzaría
// "Identifier ya declarado".
(function () {
    const selector = '.authorsSwiper';
    const sliderEl = document.querySelector(selector);
    if (!sliderEl) return;

    // Si ya existe una instancia (mismo elemento reutilizado por el morph
    // de Livewire), destruirla antes de crear una nueva.
    if (sliderEl.swiper) {
        sliderEl.swiper.destroy(true, true);
    }

    const slideCount = sliderEl.querySelectorAll('.swiper-slide').length;
    const canLoop = slideCount > 4;

    new Swiper(selector, {
        slidesPerView: 'auto',
        spaceBetween: 24,
        freeMode: true,
        loop: canLoop,
        grabCursor: true,
        // true (default de Swiper): si el usuario arrastra más allá del
        // umbral, se cancela el click posterior sobre el <a> del slide,
        // para que soltar el mouse tras desplazar no navegue al autor.
        preventClicks: true,
        preventClicksPropagation: true,
    });
})();
