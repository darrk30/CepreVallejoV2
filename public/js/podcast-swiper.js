document.addEventListener('DOMContentLoaded', function () {
    // Inicializar carrusel de autores
    const initDynamicSwiper = (selector) => {
        const sliderEl = document.querySelector(selector);
        if (!sliderEl) return;

        const slideCount = sliderEl.querySelectorAll('.swiper-slide').length;
        const canLoop = slideCount > 4;

        new Swiper(selector, {
            slidesPerView: 'auto',
            spaceBetween: 24,
            freeMode: true,
            loop: canLoop,
            grabCursor: true,
        });
    };

    initDynamicSwiper('.authorsSwiper');
});