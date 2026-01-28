<?php
/**
 * My Account
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

// Remove default navigation completely
remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );

?>

<style>
    /* Aggressively hide all default WooCommerce My Account navigation */
    .woocommerce-MyAccount-navigation,
    .woocommerce-MyAccount-navigation-menu,
    .woocommerce-account .woocommerce-MyAccount-navigation,
    .woocommerce .woocommerce-MyAccount-navigation,
    ul.woocommerce-MyAccount-navigation,
    nav.woocommerce-MyAccount-navigation {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
    }
</style>

<?php 
/**
 * My Account content area.
 *
 * Individual page templates (dashboard, orders, addresses, etc.) 
 * handle their own sidebar and layout.
 */
?>

<?php do_action( 'woocommerce_account_content' ); ?>
