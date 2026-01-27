/* Aurora Theme - Enhanced Animations & Interactions */

(function ($) {
    $(document).ready(function () {
        try {

            // Smooth scroll animations on page load
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe product cards for scroll animations
            $('.aurora-product, .category-card, .featured-rows, .trending').each(function () {
                const el = this;
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });

            // Product card hover magnification
            $('.aurora-product').on('mouseenter', function () {
                $(this).find('.thumb').stop().animate({ transform: 'scale(1.15)' }, 300);
            }).on('mouseleave', function () {
                $(this).find('.thumb').stop().animate({ transform: 'scale(1)' }, 300);
            });

            // Cart count pulse animation
            $(document).on('wc_fragments_refreshed', function () {
                const cartCount = $('.aurora-cart-count');
                cartCount.css('animation', 'pulse 0.6s ease').on('animationend', function () {
                    $(this).css('animation', '');
                });
            });

            // Search field focus animation
            $('.search-field, .search-category').on('focus', function () {
                $(this).css({
                    'box-shadow': '0 0 0 3px rgba(11, 87, 208, 0.2)',
                    'transform': 'translateY(-2px)',
                    'border-color': '#0b57d0'
                });
            }).on('blur', function () {
                $(this).css({
                    'box-shadow': 'none',
                    'transform': 'translateY(0)',
                    'border-color': '#e5e7eb'
                });
            });

            // Button ripple effect
            $('.button, button, input[type="submit"]').on('click', function (e) {
                const btn = $(this);
                const ripple = $('<span class="ripple"></span>');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.css({
                    width: size,
                    height: size,
                    left: x,
                    top: y,
                    position: 'absolute',
                    borderRadius: '50%',
                    background: 'rgba(255, 255, 255, 0.5)',
                    pointerEvents: 'none',
                    animation: 'buttonRipple 0.6s ease-out'
                });

                btn.css('position', 'relative').append(ripple);

                setTimeout(() => ripple.remove(), 600);
            });

            // Trending slider drag functionality
            const sliders = $('.product-grid.slider');
            sliders.each(function () {
                let isDown = false;
                let startX;
                let scrollLeft;
                const el = this;

                $(this).on('mousedown', function (e) {
                    isDown = true;
                    startX = e.pageX - el.offsetLeft;
                    scrollLeft = el.scrollLeft;
                    $(this).css('cursor', 'grabbing');
                });

                $(document).on('mouseleave mouseup', function () {
                    isDown = false;
                    $(el).css('cursor', 'grab');
                });

                $(this).on('mousemove', function (e) {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - el.offsetLeft;
                    const walk = (x - startX) * 1.2;
                    el.scrollLeft = scrollLeft - walk;
                });
            });

            // Form input animations
            $('.form-group input, .form-group textarea, .form-group select').on('focus', function () {
                $(this).closest('.form-group').find('label').css({
                    color: '#0b57d0',
                    transform: 'scale(0.95)'
                });
            }).on('blur', function () {
                $(this).closest('.form-group').find('label').css({
                    color: '#1f2937',
                    transform: 'scale(1)'
                });
            });

            // Navbar underline animation
            $('.primary-nav a').on('mouseenter', function () {
                $(this).css({
                    'color': '#fff',
                    'background': 'rgba(11, 87, 208, 0.2)'
                });
            }).on('mouseleave', function () {
                $(this).css({
                    'color': '#e5e7eb',
                    'background': 'transparent'
                });
            });

            // Category card hover effect
            $('.category-card').on('mouseenter', function () {
                $(this).css({
                    'transform': 'translateY(-6px)',
                    'box-shadow': '0 12px 24px rgba(11, 87, 208, 0.2)',
                    'border-color': '#0b57d0'
                });
            }).on('mouseleave', function () {
                $(this).css({
                    'transform': 'translateY(0)',
                    'box-shadow': 'none',
                    'border-color': '#e5e7eb'
                });
            });

            // Add staggered animation to product lists
            $('.product-grid > .aurora-product').each(function (index) {
                $(this).css({
                    'animation-delay': (index * 0.05) + 's'
                });
            });

            // Scroll reveal for sections
            $(window).on('scroll', function () {
                const scrollTop = $(this).scrollTop();

                // Parallax effect for hero
                $('.hero').css('transform', 'translateY(' + (scrollTop * 0.5) + 'px)');

                // Fade in cart on scroll
                $('.cart-link').css({
                    'opacity': Math.max(0.7, 1 - (scrollTop / 500))
                });
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.innerHTML = `
      @keyframes buttonRipple {
        0% {
          transform: scale(0);
          opacity: 1;
        }
        100% {
          transform: scale(4);
          opacity: 0;
        }
      }
    `;
            document.head.appendChild(style);

        } catch (err) {
            console.warn('Animations script disabled due to error:', err);
        }
    });
});
