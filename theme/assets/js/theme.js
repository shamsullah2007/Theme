(function ($) {
    $(document).ready(function () {

        // Safe no-op fallbacks for tracking scripts if not configured
        window.rdt = window.rdt || function () { return null; };
        window.snaptr = window.snaptr || function () { return null; };
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
