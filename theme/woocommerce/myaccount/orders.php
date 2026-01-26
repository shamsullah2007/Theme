<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php esc_html_e( 'Order', 'woocommerce' ); ?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?php esc_html_e( 'Date', 'woocommerce' ); ?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce' ); ?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span></th>
				<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><span class="nobr"><?php esc_html_e( 'Actions', 'woocommerce' ); ?></span></th>
			</tr>
		</thead>

		<tbody>
			<?php
			foreach ( $customer_orders->orders as $customer_order ) {
				$order      = wc_get_order( $customer_order );
				$item_count = $order->get_item_count() - $order->get_item_count_refunded();
				?>
				<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order">
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?php esc_attr_e( 'Order', 'woocommerce' ); ?>">
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
							<?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
						</a>
					</td>
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="<?php esc_attr_e( 'Date', 'woocommerce' ); ?>">
						<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
					</td>
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php esc_attr_e( 'Status', 'woocommerce' ); ?>">
						<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
					</td>
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
						<?php
						/* translators: 1: formatted order total 2: total order items */
						echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) );
						?>
					</td>
					<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="<?php esc_attr_e( 'Actions', 'woocommerce' ); ?>">
						<?php
						$actions = wc_get_account_orders_actions( $order );

						if ( ! empty( $actions ) ) {
							foreach ( $actions as $key => $action ) {
								$class = 'woocommerce-button button ' . sanitize_html_class( $key );
								if ( $key === 'cancel' ) {
									$class .= ' aurora-cancel-order';
									echo '<a href="' . esc_url( $action['url'] ) . '" class="' . esc_attr( $class ) . '" data-order-id="' . esc_attr( $order->get_id() ) . '">' . esc_html( $action['name'] ) . '</a>';
								} else {
									echo '<a href="' . esc_url( $action['url'] ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $action['name'] ) . '</a>';
								}
							}
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $customer_orders->current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $customer_orders->current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->current_page ) !== intval( $customer_orders->max_num_pages ) ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $customer_orders->current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<?php esc_html_e( 'No order has been made yet.', 'woocommerce' ); ?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>

<style>
.aurora-cancel-order {
	background: #dc2626 !important;
	border-color: #dc2626 !important;
	color: white !important;
}
.aurora-cancel-order:hover {
	background: #b91c1c !important;
	border-color: #b91c1c !important;
}
</style>

<script>
jQuery(document).ready(function($) {
	$('.aurora-cancel-order').on('click', function(e) {
		e.preventDefault();
		
		if (!confirm('<?php esc_html_e( 'Are you sure you want to cancel this order? This action cannot be undone.', 'aurora' ); ?>')) {
			return;
		}
		
		var $button = $(this);
		var orderId = $button.data('order-id');
		var originalText = $button.text();
		
		$.ajax({
			url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			type: 'POST',
			data: {
				action: 'aurora_cancel_customer_order',
				order_id: orderId,
				nonce: '<?php echo wp_create_nonce( 'aurora_cancel_order' ); ?>'
			},
			beforeSend: function() {
				$button.text('<?php esc_html_e( 'Cancelling...', 'aurora' ); ?>').prop('disabled', true);
			},
			success: function(response) {
				if (response.success) {
					alert('<?php esc_html_e( 'Order cancelled successfully!', 'aurora' ); ?>');
					location.reload();
				} else {
					alert(response.data.message || '<?php esc_html_e( 'Error cancelling order.', 'aurora' ); ?>');
					$button.text(originalText).prop('disabled', false);
				}
			},
			error: function() {
				alert('<?php esc_html_e( 'Error cancelling order.', 'aurora' ); ?>');
				$button.text(originalText).prop('disabled', false);
			}
		});
	});
});
</script>
