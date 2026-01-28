<?php
/**
 * Template Name: My Account
 * Custom My Account page with sidebar
 */

get_header();

// Remove default WooCommerce navigation
remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );
?>

<style>
    /* Hide all default WooCommerce My Account navigation */
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

<main id="primary" class="site-main">
    <div class="myaccount-page-wrapper">
        <?php
        // Include the blue sidebar
        $sidebar_file = get_template_directory() . '/woocommerce/myaccount/sidebar-navigation.php';
        if ( file_exists( $sidebar_file ) ) {
            include $sidebar_file;
        }
        ?>

        <div class="myaccount-content">
            <?php 
            // Display WooCommerce account content
            if ( function_exists( 'woocommerce_account_content' ) ) {
                woocommerce_account_content();
            } else {
                // Fallback: display page content
                while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'aurora-page' ); ?>>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                <?php endwhile;
            }
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
