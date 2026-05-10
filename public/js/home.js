document.addEventListener('DOMContentLoaded', function () {
    // 1. Slider Principal (Suele ser 1 solo slide visible, pero valida si tienes más de 1)
    const mainSlides = document.querySelectorAll('.mySwiper .swiper-slide').length;
    new Swiper('.mySwiper', {
        loop: mainSlides > 1, // Solo hace loop si hay más de 1
        grabCursor: true,
        effect: 'fade',
        fadeEffect: { crossFade: true },
        autoplay: {
            delay: 5500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
    });

    // 2. Función para inicializar carruseles dinámicos
    const initDynamicSwiper = (selector) => {
        const sliderEl = document.querySelector(selector);
        if (!sliderEl) return;

        const slideCount = sliderEl.querySelectorAll('.swiper-slide').length;
        
        // Configuramos el loop dinámico: 
        // Si en escritorio mostramos 4, necesitamos al menos 5 para que el loop no de error.
        const canLoop = slideCount > 4; 

        new Swiper(selector, {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: canLoop, 
            grabCursor: true,
            pagination: {
                el: `${selector} .swiper-pagination`,
                clickable: true
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
                1280: { slidesPerView: 4 }
            }
        });
    };

    // 3. Inicializar cada uno
    initDynamicSwiper('.servicesSwiper');
    initDynamicSwiper('.coursesSwiper');
    initDynamicSwiper('.teachersSwiper');
    initDynamicSwiper('.conventionsSwiper');

    /* --- Intersection Observer (Mantenlo igual) --- */
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                obs.unobserve(e.target);
            }
        });
    }, {
        threshold: 0.10,
        rootMargin: '0px 0px -30px 0px'
    });

    document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .stagger')
        .forEach(el => obs.observe(el));
});