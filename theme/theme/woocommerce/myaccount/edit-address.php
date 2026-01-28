<?php
/**
 * Edit Address
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/edit-address.php.
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing Address', 'woocommerce' ) : esc_html__( 'Shipping Address', 'woocommerce' );

?>

<style>
    .myaccount-page-wrapper {
        display: grid !important;
        grid-template-columns: 280px 1fr !important;
        gap: 40px !important;
        align-items: start !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        width: 100% !important;
    }

    .myaccount-content {
        grid-column: 2 !important;
        width: 100% !important;
    }

    /* Override any WooCommerce wrapper styles */
    .woocommerce-MyAccount-content {
        display: contents !important;
    }

    @media (max-width: 768px) {
        .myaccount-page-wrapper {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        
        .myaccount-content {
            grid-column: 1 !important;
        }
    }
</style>

<div class="myaccount-page-wrapper">
    <?php
    // Include custom sidebar
    $sidebar_file = dirname( __FILE__ ) . '/sidebar-navigation.php';
    if ( file_exists( $sidebar_file ) ) {
        include $sidebar_file;
    }
    ?>

    <div class="myaccount-content" style="grid-column: 2;">

<?php

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<form method="post">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<h3><?php echo esc_html( $page_title ); ?></h3>

	<?php do_action( "woocommerce_edit_account_form_before_{$load_address}" ); ?>

	<div class="woocommerce-address-fields">
		<?php foreach ( $address as $key => $field ) : ?>
			<?php woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $customer->{"get_$key"}() ) ); ?>
		<?php endforeach; ?>
	</div>

	<?php do_action( "woocommerce_edit_account_form_after_{$load_address}" ); ?>

	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

	<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
	<button type="submit" class="woocommerce-Button button" name="save_address" value="<?php esc_attr_e( 'Save Address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save Address', 'woocommerce' ); ?></button>
	<input type="hidden" name="action" value="edit_address" />

</form>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>

    </div><!-- .myaccount-content -->
</div><!-- .myaccount-page-wrapper -->
