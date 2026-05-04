document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.mySwiper', {
        loop: true,
        grabCursor: true,
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
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

    /* Carouseles */
    const cfg = {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: true,
        grabCursor: true,
        pagination: {
            clickable: true
        },
        breakpoints: {
            640: {
                slidesPerView: 2
            },
            1024: {
                slidesPerView: 3
            },
            1280: {
                slidesPerView: 4
            }
        }
    };
    new Swiper('.servicesSwiper', {
        ...cfg,
        pagination: {
            el: '.servicesSwiper .swiper-pagination',
            clickable: true
        }
    });

    new Swiper('.coursesSwiper', {
        ...cfg,
        pagination: {
            el: '.coursesSwiper .swiper-pagination',
            clickable: true
        }
    });
    new Swiper('.teachersSwiper', {
        ...cfg,
        pagination: {
            el: '.teachersSwiper .swiper-pagination',
            clickable: true
        }
    });
    new Swiper('.conventionsSwiper', {
        ...cfg,
        pagination: {
            el: '.conventionsSwiper .swiper-pagination',
            clickable: true
        }
    });

    /* Scroll Reveal */
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