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

// Automatically add published pages to primary menu
add_filter( 'wp_nav_menu_items', 'aurora_add_pages_to_menu', 10, 2 );
function aurora_add_pages_to_menu( $items, $args ) {
    // Only add to primary menu and if there's no custom menu assigned
    if ( 'primary' === $args->theme_location ) {
        $pages = get_pages( array(
            'post_status' => 'publish',
            'orderby'     => 'post_title',
            'sort_column' => 'post_title',
            'exclude'     => array( get_option( 'page_on_front' ), get_option( 'page_for_posts' ) ),
        ) );

        if ( ! empty( $pages ) ) {
            $current_post_id = get_queried_object_id();
            foreach ( $pages as $page ) {
                $active_class = ( $current_post_id === $page->ID ) ? 'current-menu-item' : '';
                $items .= sprintf(
                    '<li class="menu-item %s"><a href="%s">%s</a></li>',
                    $active_class,
                    esc_url( get_permalink( $page->ID ) ),
                    esc_html( $page->post_title )
                );
            }
        }
    }
    return $items;
}

// Enhance checkout form styling with CSS classes
add_filter( 'woocommerce_checkout_fields', 'aurora_checkout_fields_wrapper' );
function aurora_checkout_fields_wrapper( $fields ) {
    foreach ( $fields as $section => $field_group ) {
        if ( is_array( $field_group ) ) {
            foreach ( $field_group as $key => $field ) {
                $fields[ $section ][ $key ]['class'][] = 'aurora-checkout-field';
            }
        }
    }
    return $fields;
}

// Add custom body class for checkout page
add_filter( 'body_class', 'aurora_checkout_body_class' );
function aurora_checkout_body_class( $classes ) {
    if ( is_checkout() ) {
        $classes[] = 'aurora-checkout-page';
    }
    return $classes;
}

// Category Color Management
add_action( 'product_cat_edit_form_fields', 'aurora_category_color_field', 10, 2 );
function aurora_category_color_field( $term, $taxonomy ) {
    $color = get_term_meta( $term->term_id, 'aurora_category_color', true );
    $color = $color ? $color : '#0b57d0';
    ?>
    <tr class="form-field">
        <th scope="row"><label for="aurora_category_color"><?php esc_html_e( 'Category Color', 'aurora' ); ?></label></th>
        <td>
            <input type="color" id="aurora_category_color" name="aurora_category_color" value="<?php echo esc_attr( $color ); ?>" />
            <p class="description"><?php esc_html_e( 'Choose a color for this category heading', 'aurora' ); ?></p>
        </td>
    </tr>
    <?php
}

add_action( 'edited_product_cat', 'aurora_save_category_color', 10, 2 );
function aurora_save_category_color( $term_id, $tt_id ) {
    if ( isset( $_POST['aurora_category_color'] ) ) {
        update_term_meta( $term_id, 'aurora_category_color', sanitize_hex_color( $_POST['aurora_category_color'] ) );
    }
}

add_action( 'create_product_cat', 'aurora_create_category_color', 10, 2 );
function aurora_create_category_color( $term_id, $tt_id ) {
    if ( isset( $_POST['aurora_category_color'] ) ) {
        add_term_meta( $term_id, 'aurora_category_color', sanitize_hex_color( $_POST['aurora_category_color'] ) );
    }
}

// Add category color field to add category form
add_action( 'product_cat_add_form_fields', 'aurora_category_color_add_field' );
function aurora_category_color_add_field() {
    ?>
    <div class="form-field">
        <label for="aurora_category_color"><?php esc_html_e( 'Category Color', 'aurora' ); ?></label>
        <input type="color" id="aurora_category_color" name="aurora_category_color" value="#0b57d0" />
        <p class="description"><?php esc_html_e( 'Choose a color for this category heading', 'aurora' ); ?></p>
    </div>
    <?php
}

// Apply category color to product titles
add_filter( 'woocommerce_product_title', 'aurora_apply_category_color_to_title', 10, 2 );
function aurora_apply_category_color_to_title( $title, $product_id ) {
    if ( is_admin() ) {
        return $title;
    }

    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return $title;
    }

    $categories = $product->get_category_ids();
    if ( ! empty( $categories ) ) {
        $primary_cat_id = $categories[0];
        $color = get_term_meta( $primary_cat_id, 'aurora_category_color', true );
        if ( $color ) {
            return '<span style="color: ' . esc_attr( $color ) . '; font-weight: 700;">' . $title . '</span>';
        }
    }

    return $title;
}

// Apply category colors to single product page
add_filter( 'the_title', 'aurora_apply_category_color_single_product', 10, 2 );
function aurora_apply_category_color_single_product( $title, $post_id ) {
    if ( is_admin() || ! is_singular( 'product' ) ) {
        return $title;
    }

    $product = wc_get_product( $post_id );
    if ( ! $product ) {
        return $title;
    }

    $categories = $product->get_category_ids();
    if ( ! empty( $categories ) ) {
        $primary_cat_id = $categories[0];
        $color = get_term_meta( $primary_cat_id, 'aurora_category_color', true );
        if ( $color ) {
            return '<span style="color: ' . esc_attr( $color ) . '; font-weight: 700;">' . $title . '</span>';
        }
    }

    return $title;
}

// Helper function to get product manager page URL
function aurora_get_product_manager_url( $action = 'list' ) {
    // Try to find the page with the product manager shortcode
    $args = array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        's'              => 'aurora_admin_product_manager',
        'posts_per_page' => 1,
    );
    
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        $post_id = $query->posts[0]->ID;
        return add_query_arg( 'action', $action, get_permalink( $post_id ) );
    }
    
    // Fallback: look for admin page
    $admin_pages = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'admin%',
        'post_type'  => 'page',
    ) );
    
    if ( ! empty( $admin_pages ) ) {
        return add_query_arg( 'action', $action, get_permalink( $admin_pages[0]->ID ) );
    }
    
    // Last fallback: admin dashboard
    return add_query_arg( array(
        'page'   => 'aurora_admin_product_manager',
        'action' => $action,
    ), admin_url( 'admin.php' ) );
}

// Elementor compatibility: allow all WP elements.
add_action( 'elementor/theme/register_locations', function( $elementor_theme_manager ) {
    $elementor_theme_manager->register_all_core_location();
} );
