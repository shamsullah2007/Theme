<?php
/**
 * Addresses
 *
 * Shows customer addresses on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/addresses.php.
 */

defined( 'ABSPATH' ) || exit;

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

<div class="woocommerce-address-fields">

	<?php if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_taxes' ) !== 'no' ) : ?>
		<p><?php esc_html_e( 'Below are the addresses on file for your account.', 'woocommerce' ); ?></p>
	<?php endif; ?>

	<?php foreach ( $customer_addresses as $name => $address ) : ?>

		<div class="woocommerce-address">
			<h3 class="woocommerce-Address-title title">
				<?php
				if ( 'billing' === $name ) {
					esc_html_e( 'Billing address', 'woocommerce' );
				} else {
					esc_html_e( 'Shipping address', 'woocommerce' );
				}
				?>
			</h3>
			<address>
				<?php
					/**
					 * The woocommerce_address_formats filter is deprecated and will be removed in a future version.
					 *
					 * @deprecated 2.7.0
					 */
					$format = apply_filters( 'woocommerce_address_formats', array( 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country' ), $name );

					foreach ( $format as $field ) {
						$field_value = isset( $address[ $field ] ) ? $address[ $field ] : '';
						if ( empty( $field_value ) ) {
							continue;
						}

						if ( 'state' === $field ) {
							$field_value = isset( WC()->countries->states[ $address['country'] ][ $field_value ] ) ? WC()->countries->states[ $address['country'] ][ $field_value ] : $field_value;
						} elseif ( 'country' === $field ) {
							$field_value = isset( WC()->countries->countries[ $field_value ] ) ? WC()->countries->countries[ $field_value ] : $field_value;
						}

						echo '<div>' . wp_kses_post( $field_value ) . '</div>';
					}
				?>
			</address>

			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="woocommerce-Button button"><?php esc_html_e( 'Edit', 'woocommerce' ); ?></a>
		</div>

	<?php endforeach; ?>

</div>

<?php do_action( 'woocommerce_after_customer_addresses' ); ?>

    </div>
</div>
