<?php
/**
 * Single product template with full gallery, variations, and reviews support
 */
get_header();
?>
<main id="primary" class="site-main product-single-layout">
  <?php while ( have_posts() ) : the_post(); ?>
    <?php wc_get_template_part( 'content', 'single-product' ); ?>
  <?php endwhile; ?>
</main>
<?php get_footer(); ?>
