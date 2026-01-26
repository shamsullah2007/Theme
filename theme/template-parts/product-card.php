<?php
/**
 * Product card partial
 * @var WC_Product $product
 */
if ( ! isset( $product ) || ! $product ) {
    return;
}
?>
<li class="aurora-product">
  <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
    <div class="thumb">
      <?php echo $product->get_image( 'aurora-card' ); ?>
    </div>
    <h3 class="product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
    <div class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
    <div class="rating">
      <?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
    </div>
  </a>
  <?php woocommerce_template_loop_add_to_cart( [ 'class' => 'button add_to_cart_button' ] ); ?>
</li>
