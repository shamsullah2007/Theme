<?php
/**
 * Products loop item price
 */
defined( 'ABSPATH' ) || exit;
global $product;
echo '<span class="price">' . $product->get_price_html() . '</span>';
