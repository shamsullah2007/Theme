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

?>

<style>
    .woocommerce-MyAccount-wrapper {
        display: grid !important;
        grid-template-columns: 280px 1fr !important;
        gap: 40px !important;
        align-items: start !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        width: 100% !important;
    }

    .woocommerce-MyAccount-content {
        grid-column: 2 !important;
        width: 100% !important;
    }

    /* Force sidebar to be in first column */
    .aurora-myaccount-sidebar {
        grid-column: 1 !important;
        grid-row: 1 / -1 !important;
    }

    @media (max-width: 768px) {
        .woocommerce-MyAccount-wrapper {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        
        .woocommerce-MyAccount-content {
            grid-column: 1 !important;
        }
    }
</style>

<div class="woocommerce-MyAccount-wrapper">

    <?php
    // Include custom sidebar
    $sidebar_file = dirname( __FILE__ ) . '/myaccount/sidebar-navigation.php';
    if ( file_exists( $sidebar_file ) ) {
        include $sidebar_file;
    }
    ?>

    <div class="woocommerce-MyAccount-content">
        <?php do_action( 'woocommerce_before_account_navigation' ); ?>

        <nav class="woocommerce-MyAccount-navigation" style="display: none;">
            <ul>
                <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                    <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                        <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <?php do_action( 'woocommerce_before_account_content' ); ?>

        <div class="woocommerce-MyAccount-content-inner">
            <?php
                /**
                 * The woocommerce_account_content hook.
                 *
                 * @hooked woocommerce_account_content - 10
                 */
                do_action( 'woocommerce_account_content' );
            ?>
        </div>

        <?php do_action( 'woocommerce_after_account_content' ); ?>
    </div>

</div>
