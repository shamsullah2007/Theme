(function ($) {
    $(document).ready(function () {
        // Hide admin-only menu items
        const adminPatterns = [
            'login',
            'register',
            'product-manager',
            'product_manager',
            'productmanger',
            'product mangment',
            'order-management',
            'order_management',
            'order-manager',
            'order managment',
            'ordermangment',
            'order mangment',
            'sample-page',
            'sample page'
        ];

        $('.primary-nav .menu-item a').each(function () {
            const href = $(this).attr('href') || '';
            const text = $(this).text().toLowerCase();
            let shouldHide = false;

            // Check URL
            for (let pattern of adminPatterns) {
                if (href.toLowerCase().includes(pattern)) {
                    shouldHide = true;
                    break;
                }
            }

            // Check text
            if (!shouldHide) {
                for (let pattern of adminPatterns) {
                    if (text.includes(pattern)) {
                        shouldHide = true;
                        break;
                    }
                }
            }

            if (shouldHide) {
                $(this).closest('.menu-item').hide();
            }
        });

        // Simple slider drag for trending section
        $('.product-grid.slider').each(function () {
            const el = $(this)[0];
            let isDown = false; let startX; let scrollLeft;
            $(this).on('mousedown', function (e) { isDown = true; startX = e.pageX - el.offsetLeft; scrollLeft = el.scrollLeft; });
            $(this).on('mouseleave mouseup', function () { isDown = false; });
            $(this).on('mousemove', function (e) { if (!isDown) return; e.preventDefault(); const x = e.pageX - el.offsetLeft; const walk = (x - startX) * 1.2; el.scrollLeft = scrollLeft - walk; });
        });
    });
})(jQuery);
