<?php
/**
 * WooCommerce catch-all template
 */
get_header();
?>
<main id="primary" class="site-main shop-layout">
  <aside class="shop-filters">
    <form class="filter-form" method="get" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
      <label><?php esc_html_e( 'Price min', 'aurora' ); ?></label>
      <input type="number" name="min_price" step="0.01" value="<?php echo isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : ''; ?>" />
      <label><?php esc_html_e( 'Price max', 'aurora' ); ?></label>
      <input type="number" name="max_price" step="0.01" value="<?php echo isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : ''; ?>" />
      <label><?php esc_html_e( 'Minimum rating', 'aurora' ); ?></label>
      <select name="rating">
        <option value=""><?php esc_html_e( 'Any', 'aurora' ); ?></option>
        <option value="4" <?php selected( isset( $_GET['rating'] ) && '4' === $_GET['rating'] ); ?>>4+</option>
        <option value="3" <?php selected( isset( $_GET['rating'] ) && '3' === $_GET['rating'] ); ?>>3+</option>
        <option value="2" <?php selected( isset( $_GET['rating'] ) && '2' === $_GET['rating'] ); ?>>2+</option>
      </select>
      <button type="submit" class="button"><?php esc_html_e( 'Filter', 'aurora' ); ?></button>
    </form>

    <?php if ( is_active_sidebar( 'shop-filters' ) ) : ?>
      <?php dynamic_sidebar( 'shop-filters' ); ?>
    <?php endif; ?>
  </aside>

  <section class="shop-content">
    <?php woocommerce_content(); ?>
  </section>
</main>
<?php
get_footer();
