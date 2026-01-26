<?php
/**
 * Aurora Storefront functions and definitions
 */

define( 'AURORA_VERSION', '1.0.0' );

aurora_includes();

function aurora_includes() {
    require_once get_template_directory() . '/inc/customizer.php';
    require_once get_template_directory() . '/inc/admin-pages.php';
}

add_action( 'after_setup_theme', 'aurora_setup' );
function aurora_setup() {
    load_theme_textdomain( 'aurora', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', [
        'height'      => 48,
        'width'       => 160,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );

    add_theme_support( 'woocommerce', [
        'thumbnail_image_width' => 420,
        'single_image_width'    => 720,
        'product_grid'          => [
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 6,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 6,
        ],
    ] );

    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'aurora' ),
        'utility' => __( 'Utility Menu', 'aurora' ),
        'footer'  => __( 'Footer Menu', 'aurora' ),
    ] );
}

add_action( 'widgets_init', 'aurora_widgets_init' );
function aurora_widgets_init() {
    register_sidebar( [
        'name'          => __( 'Shop Filters', 'aurora' ),
        'id'            => 'shop-filters',
        'description'   => __( 'Widgets shown in the shop filter column.', 'aurora' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );

    register_sidebar( [
        'name'          => __( 'Footer', 'aurora' ),
        'id'            => 'footer',
        'description'   => __( 'Footer widget area.', 'aurora' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
}

add_action( 'wp_enqueue_scripts', 'aurora_scripts' );
function aurora_scripts() {
    $theme_version = wp_get_theme()->get( 'Version' );

    wp_enqueue_style( 'aurora-theme', get_template_directory_uri() . '/assets/css/theme.css', [], $theme_version );
    wp_enqueue_style( 'aurora-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', [ 'woocommerce-general' ], $theme_version );
    wp_enqueue_style( 'aurora-admin-pages', get_template_directory_uri() . '/assets/css/admin-pages.css', [], $theme_version );
    wp_enqueue_style( 'aurora-overrides', get_template_directory_uri() . '/assets/css/overrides.css', [ 'aurora-theme', 'aurora-woocommerce', 'aurora-admin-pages' ], $theme_version );
    
    wp_enqueue_script( 'aurora-animations', get_template_directory_uri() . '/assets/js/animations.js', [ 'jquery' ], $theme_version, true );
    wp_enqueue_script( 'aurora-theme', get_template_directory_uri() . '/assets/js/theme.js', [ 'jquery' ], $theme_version, true );

    wp_localize_script( 'aurora-theme', 'auroraTheme', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'homeUrl' => home_url( '/' ),
    ] );
}

add_filter( 'woocommerce_show_page_title', '__return_false' );

add_filter( 'woocommerce_output_related_products_args', 'aurora_related_products_args' );
function aurora_related_products_args( $args ) {
    $args['posts_per_page'] = 4;
    $args['columns']        = 4;
    return $args;
}

add_filter( 'loop_shop_per_page', 'aurora_products_per_page', 20 );
function aurora_products_per_page( $cols ) {
    return 12;
}

add_action( 'init', 'aurora_register_image_sizes' );
function aurora_register_image_sizes() {
    add_image_size( 'aurora-card', 400, 400, true );
    add_image_size( 'aurora-hero', 1600, 600, true );
}

add_action( 'pre_get_posts', 'aurora_enhance_product_search' );
function aurora_enhance_product_search( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_search && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) {
        $meta_query = [];

        if ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) {
            $min = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : 0;
            $max = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : 999999;
            $meta_query[] = [
                'key'     => '_price',
                'value'   => [ $min, $max ],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ];
        }

        if ( isset( $_GET['rating'] ) ) {
            $meta_query[] = [
                'key'     => '_wc_average_rating',
                'value'   => floatval( $_GET['rating'] ),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ];
        }

        if ( ! empty( $meta_query ) ) {
            $query->set( 'meta_query', $meta_query );
        }
    }
}

// WooCommerce wrapper tweaks.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'aurora_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'aurora_wrapper_end', 10 );

function aurora_wrapper_start() {
    echo '<main id="primary" class="site-main">';
}

function aurora_wrapper_end() {
    echo '</main>';
}

add_filter( 'woocommerce_add_to_cart_fragments', 'aurora_header_cart_count' );
function aurora_header_cart_count( $fragments ) {
    ob_start();
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    ?>
    <span class="aurora-cart-count"><?php echo esc_html( $count ); ?></span>
    <?php
    $fragments['span.aurora-cart-count'] = ob_get_clean();
    return $fragments;
}

// Elementor compatibility: allow all WP elements.
add_action( 'elementor/theme/register_locations', function( $elementor_theme_manager ) {
    $elementor_theme_manager->register_all_core_location();
} );
