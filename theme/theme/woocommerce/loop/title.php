<?php
/**
 * Product loop item title
 */
defined( 'ABSPATH' ) || exit;
global $product;
echo '<h2 class="woocommerce-loop-product__title">' . $product->get_name() . '</h2>';
