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
    .woocommerce-MyAccount-wrapper {
        display: grid !important;
        grid-template-columns: 280px 1fr !important;
        gap: 40px !important;
        align-items: start !important;
        max-width: none !important;
        margin: 0 !important;
        width: 100% !important;
        padding: 0 !important;
        background: transparent !important;
    }

    .woocommerce-MyAccount-wrapper > div:not(.aurora-myaccount-sidebar) {
        grid-column: 2 !important;
    }

    .aurora-myaccount-sidebar {
        grid-column: 1 !important;
        grid-row: 1 / -1 !important;
    }

    @media (max-width: 768px) {
        .woocommerce-MyAccount-wrapper {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }

        .woocommerce-MyAccount-wrapper > div:not(.aurora-myaccount-sidebar) {
            grid-column: 1 !important;
        }
    }
</style>

<div class="woocommerce-MyAccount-wrapper">
    <?php
    // Include sidebar
    $sidebar_file = dirname( __FILE__ ) . '/sidebar-navigation.php';
    if ( file_exists( $sidebar_file ) ) {
        include $sidebar_file;
    }
    ?>
    
    <div style="grid-column: 2;">

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
