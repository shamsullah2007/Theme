<?php
/**
 * Edit Address
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/edit-address.php.
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing Address', 'woocommerce' ) : esc_html__( 'Shipping Address', 'woocommerce' );

?>

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
