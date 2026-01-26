<?php
/**
 * Checkout page template override
 */
get_header();
?>
<main id="primary" class="site-main checkout-layout">
  <div class="checkout-wrapper">
    <h1 class="page-title"><?php esc_html_e( 'Checkout', 'woocommerce' ); ?></h1>
    
    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
      <div class="col2-set" id="customer_details">
        <div class="col-1">
          <?php do_action( 'woocommerce_checkout_billing' ); ?>
        </div>
        <div class="col-2">
          <?php do_action( 'woocommerce_checkout_shipping' ); ?>
        </div>
      </div>

      <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

      <h3 id="order_review_heading"><?php esc_html_e( 'Order summary', 'woocommerce' ); ?></h3>

      <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

      <div id="order_review" class="woocommerce-checkout-review-order">
        <?php do_action( 'woocommerce_checkout_order_review' ); ?>
      </div>

      <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
    </form>
  </div>
</main>
<?php get_footer(); ?>
