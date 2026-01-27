<?php
/**
 * Cart page template override
 */
get_header();
?>
<main id="primary" class="site-main cart-layout">
  <div class="cart-wrapper">
    <h1 class="page-title"><?php esc_html_e( 'Shopping Cart', 'woocommerce' ); ?></h1>
    
    <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
      <?php do_action( 'woocommerce_before_cart_table' ); ?>

      <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
          <tr>
            <th class="product-remove"></th>
            <th class="product-thumbnail"></th>
            <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
            <th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
            <th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
            <th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php do_action( 'woocommerce_before_cart_contents' ); ?>
          <?php
          foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
              ?>
              <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                <td class="product-remove">
                  <?php
                    echo apply_filters(
                      'woocommerce_cart_item_remove_link',
                      sprintf(
                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">×</a>',
                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                        esc_attr__( 'Remove this item', 'woocommerce' ),
                        esc_attr( $product_id ),
                        esc_attr( $_product->get_sku() )
                      ),
                      $cart_item_key
                    );
                  ?>
                </td>

                <td class="product-thumbnail">
                  <?php
                  $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                  if ( ! $product_id ) {
                    echo $thumbnail;
                  } else {
                    printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail );
                  }
                  ?>
                </td>

                <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                  <?php
                  if ( ! $product_id ) {
                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) );
                  } else {
                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                  }
                  do_action( 'woocommerce_cart_item_subtotal', $cart_item, $cart_item_key );
                  ?>
                </td>

                <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                  <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                </td>

                <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
                  <?php
                  if ( $_product->is_sold_individually() ) {
                    $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', esc_attr( $cart_item_key ) );
                  } else {
                    $product_quantity = woocommerce_quantity_input(
                      [
                        'input_value' => $cart_item['quantity'],
                        'max_value'   => $_product->get_max_purchase_quantity(),
                        'min_value'   => '0',
                        'product_name'=> $_product->get_name(),
                      ],
                      $_product,
                      false
                    );
                  }
                  echo wp_kses_post( apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ) );
                  ?>
                </td>

                <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
                  <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_price( $_product ) * $cart_item['quantity'], $cart_item, $cart_item_key ); ?>
                </td>
              </tr>
              <?php
            }
          }
          ?>
          <?php do_action( 'woocommerce_cart_contents' ); ?>
        </tbody>
      </table>
      <?php do_action( 'woocommerce_after_cart_table' ); ?>

      <div class="actions">
        <a href="<?php echo esc_url( wc_get_shop_page_url() ); ?>" class="button"><?php esc_html_e( 'Continue Shopping', 'woocommerce' ); ?></a>
        <button type="submit" class="button" name="update_cart"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
      </div>
    </form>

    <?php do_action( 'woocommerce_after_cart' ); ?>
  </div>

  <aside class="cart-totals">
    <?php do_action( 'woocommerce_cart_collaterals' ); ?>
  </aside>
</main>
<?php get_footer(); ?>
