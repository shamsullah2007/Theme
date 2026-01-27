<?php
/**
 * WooCommerce single product main content (theme override)
 */
defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product ) {
    return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'aurora-product-single', $product ); ?>>

  <div class="product-media">
    <?php
    /**
     * Hook: woocommerce_product_images.
     *
     * @hooked woocommerce_show_product_images - 20
     */
    do_action( 'woocommerce_product_images' );
    ?>
  </div>

  <div class="product-info">
    <div class="product-header">
      <?php
      /**
       * Hook: woocommerce_single_product_title.
       *
       * @hooked woocommerce_template_single_title - 5
       */
      do_action( 'woocommerce_single_product_title' );

      /**
       * Hook: woocommerce_single_product_meta.
       *
       * @hooked woocommerce_template_single_meta - 10
       */
      do_action( 'woocommerce_single_product_meta' );
      ?>
    </div>

    <div class="product-price-section">
      <?php
      /**
       * Hook: woocommerce_single_product_summary.
       *
       * @hooked woocommerce_template_single_price - 10
       * @hooked woocommerce_template_single_excerpt - 20
       * @hooked woocommerce_template_single_add_to_cart - 30
       * @hooked woocommerce_template_single_meta - 40
       * @hooked woocommerce_template_single_sharing - 50
       * @hooked WC_Structured_Data::generate_product_schema() - 60
       */
      do_action( 'woocommerce_single_product_summary' );
      ?>
    </div>

    <div class="product-trust">
      <div class="trust-badges">
        <span class="badge">✓ Verified Seller</span>
        <span class="badge">Free Returns</span>
      </div>
    </div>
  </div>

</div>

<div class="product-details">
  <?php
  /**
   * Hook: woocommerce_after_single_product_summary.
   *
   * @hooked woocommerce_output_product_data_tabs - 10
   * @hooked woocommerce_upsell_display - 15
   * @hooked woocommerce_output_related_products - 20
   */
  do_action( 'woocommerce_after_single_product_summary' );
  ?>
</div>
