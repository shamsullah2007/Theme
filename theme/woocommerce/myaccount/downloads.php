<?php
/**
 * Downloads
 *
 * Shows download links on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/downloads.php.
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

<?php

do_action( 'woocommerce_before_account_downloads', $has_downloads ); ?>

<?php if ( $has_downloads ) : ?>

	<table class="woocommerce-downloads-table shop_table shop_table_responsive account-downloads-table">
		<thead>
			<tr>
				<th class="woocommerce-downloads-table__header woocommerce-downloads-table__header-download-product"><span class="nobr"><?php esc_html_e( 'Product', 'woocommerce' ); ?></span></th>
				<th class="woocommerce-downloads-table__header woocommerce-downloads-table__header-download-file"><span class="nobr"><?php esc_html_e( 'File', 'woocommerce' ); ?></span></th>
				<th class="woocommerce-downloads-table__header woocommerce-downloads-table__header-download-expires"><span class="nobr"><?php esc_html_e( 'Expires', 'woocommerce' ); ?></span></th>
				<th class="woocommerce-downloads-table__header woocommerce-downloads-table__header-download-actions"><span class="nobr"><?php esc_html_e( 'Download', 'woocommerce' ); ?></span></th>
			</tr>
		</thead>

		<tbody>
			<?php
			foreach ( $downloads as $download ) {
				?>
				<tr class="woocommerce-downloads-table__row woocommerce-downloads-table__row--file-<?php echo esc_attr( $download['id'] ); ?>">
					<td class="woocommerce-downloads-table__cell woocommerce-downloads-table__cell-product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
						<a href="<?php echo esc_url( $download['product_url'] ); ?>">
							<?php echo esc_html( $download['product_name'] ); ?>
						</a>
					</td>
					<td class="woocommerce-downloads-table__cell woocommerce-downloads-table__cell-file-name" data-title="<?php esc_attr_e( 'File', 'woocommerce' ); ?>">
						<?php echo esc_html( $download['file_name'] ); ?>
					</td>
					<td class="woocommerce-downloads-table__cell woocommerce-downloads-table__cell-file-expires" data-title="<?php esc_attr_e( 'Expires', 'woocommerce' ); ?>">
						<?php
						if ( ! empty( $download['access_expires'] ) ) {
							echo esc_html( wc_format_datetime( $download['access_expires'] ) );
						} else {
							esc_html_e( 'Never', 'woocommerce' );
						}
						?>
					</td>
					<td class="woocommerce-downloads-table__cell woocommerce-downloads-table__cell-download-actions" data-title="<?php esc_attr_e( 'Download', 'woocommerce' ); ?>">
						<a href="<?php echo esc_url( $download['download_url'] ); ?>" class="woocommerce-MyAccount-downloads-file button">
							<?php esc_html_e( 'Download', 'woocommerce' ); ?>
						</a>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>

	<?php do_action( 'woocommerce_before_account_downloads_pagination' ); ?>

	<?php if ( 1 < $downloads_limit ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( $page > 1 ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'downloads', $page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( $downloads_limit > $offset + count( $downloads ) ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'downloads', $page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<?php esc_html_e( 'No downloads available yet.', 'woocommerce' ); ?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>

    </div><!-- .myaccount-content -->
</div><!-- .myaccount-page-wrapper -->
