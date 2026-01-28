<?php

add_action( 'admin_menu', function() {
    add_theme_page( __( 'Customize' ), __( 'Customize' ), 'edit_theme_options', 'customize.php' );
});
add_action( 'after_setup_theme', function() {
    add_theme_support( 'customize-selective-refresh-widgets' );
});
/**
 * Aurora Storefront functions and definitions
 */

define( 'AURORA_VERSION', '1.0.0' );

aurora_includes();

function aurora_includes() {
    require_once get_template_directory() . '/inc/customizer.php';
    require_once get_template_directory() . '/inc/admin-pages.php';
    require_once get_template_directory() . '/inc/auth.php';
}

// Global SMTP configuration (Gmail with app password)
add_action( 'phpmailer_init', function( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // tls
    $phpmailer->Username   = 'shamsullahd9999@gmail.com';
    $phpmailer->Password   = 'zipp fwkq oyeo atnh'; // paste app password here
    $phpmailer->From       = 'shamsullahd9999@gmail.com';
    $phpmailer->FromName   = 'Amzone';
} );

// Enforce OTP configuration for registration/login flows
add_filter( 'aurora_otp_settings', function( $settings ) {
    $settings['length']               = 6;                  // 6-digit codes
    $settings['expires_in']           = 10 * MINUTE_IN_SECONDS; // expire in 10 minutes
    $settings['resend_cooldown']      = 60;                 // 60s between sends
    $settings['max_attempts_per_day'] = 10;                 // max 10 sends per day per email
    return $settings;
} );

// Disable default WooCommerce registration form - use custom OTP template instead
add_filter( 'woocommerce_enable_myaccount_registration', '__return_false' );

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

// Hide admin-only menu items for non-admin customers
add_filter( 'wp_nav_menu_objects', 'aurora_filter_menu_objects', 10, 2 );
function aurora_filter_menu_objects( $items, $args ) {
    // Only filter the primary menu
    if ( $args->theme_location !== 'primary' ) {
        return $items;
    }
    
    // Check if user is admin
    $is_admin = is_user_logged_in() && ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' ) );
    
    // If admin, show all items
    if ( $is_admin ) {
        return $items;
    }
    
    // For non-admin users (customers and guests), filter out admin pages
    $filtered = array();
    
    foreach ( $items as $item ) {
        $should_hide = false;
        
        // Check page title
        $item_title = strtolower( trim( $item->title ) );
        if ( in_array( $item_title, array( 'login', 'register', 'product manager', 'order management', 'order managment', 'sample page', 'productmanger' ) ) ) {
            $should_hide = true;
        }
        
        // Check URL slug
        if ( ! $should_hide && isset( $item->url ) ) {
            $url = strtolower( $item->url );
            $hide_patterns = array(
                'login',
                'register',
                'product-manager',
                'product_manager',
                'order-management',
                'order_management',
                'order-manager',
                'order_manager',
                'sample-page',
                'productmanger'
            );
            
            foreach ( $hide_patterns as $pattern ) {
                if ( strpos( $url, $pattern ) !== false ) {
                    $should_hide = true;
                    break;
                }
            }
        }
        
        if ( ! $should_hide ) {
            $filtered[] = $item;
        }
    }
    
    return $filtered;
}

// Custom Menu Walker to hide admin pages from customers
class Aurora_Menu_Walker extends Walker_Nav_Menu {
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        // List of admin pages to hide (include common typos/variants)
        $admin_pages = array(
            'login',
            'register',
            'product-manager', 'product_manager', 'productmanger', 'product manger', 'productmangment',
            'order-management', 'order_management', 'order-manager', 'order_manager', 'order managment', 'ordermangment', 'order mangment',
            'sample-page'
        );

        $should_hide = false;

        // Check URL
        $item_url   = isset( $item->url ) ? strtolower( $item->url ) : '';
        foreach ( $admin_pages as $page ) {
            if ( strpos( $item_url, $page ) !== false ) {
                $should_hide = true;
                break;
            }
        }

        // Check linked page slug if available
        if ( ! $should_hide && ! empty( $item->object_id ) ) {
            $linked_post = get_post( $item->object_id );
            if ( $linked_post && in_array( strtolower( $linked_post->post_name ), $admin_pages, true ) ) {
                $should_hide = true;
            }
        }

        // Check title text
        if ( ! $should_hide ) {
            $item_title = strtolower( trim( $item->title ) );
            foreach ( $admin_pages as $page ) {
                if ( strpos( $item_title, $page ) !== false ) {
                    $should_hide = true;
                    break;
                }
            }
        }

        // Skip rendering this item if it should be hidden
        if ( $should_hide ) {
            return;
        }

        // Otherwise, render it normally
        parent::start_el( $output, $item, $depth, $args, $id );
    }
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
    $otp_settings  = function_exists( 'aurora_get_otp_settings' ) ? aurora_get_otp_settings() : [];

    wp_enqueue_style( 'aurora-theme', get_template_directory_uri() . '/assets/css/theme.css', [], $theme_version );
    wp_enqueue_style( 'aurora-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', [ 'woocommerce-general' ], $theme_version );
    wp_enqueue_style( 'aurora-admin-pages', get_template_directory_uri() . '/assets/css/admin-pages.css', [], $theme_version );
    wp_enqueue_style( 'aurora-order-manager', get_template_directory_uri() . '/assets/css/order-manager.css', [], $theme_version );
    wp_enqueue_style( 'aurora-overrides', get_template_directory_uri() . '/assets/css/overrides.css', [ 'aurora-theme', 'aurora-woocommerce', 'aurora-admin-pages' ], $theme_version );
    
    wp_enqueue_script( 'aurora-animations', get_template_directory_uri() . '/assets/js/animations.js', [ 'jquery' ], $theme_version, true );
    wp_enqueue_script( 'aurora-theme', get_template_directory_uri() . '/assets/js/theme.js', [ 'jquery' ], $theme_version, true );
    wp_enqueue_script( 'aurora-auth', get_template_directory_uri() . '/assets/js/auth.js', [ 'jquery', 'aurora-theme' ], $theme_version, true );

    $aurora_vars = [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'homeUrl' => home_url( '/' ),
        'dashboardUrl' => wc_get_page_permalink( 'myaccount' ),
        'authNonce' => wp_create_nonce( 'aurora_auth_nonce' ),
        'profileNonce' => wp_create_nonce( 'aurora_profile_nonce' ),
        'otpLength' => $otp_settings['length'] ?? 6,
        'otpMaxAttempts' => $otp_settings['max_attempts_per_day'] ?? 10,
        'otpResendCooldown' => $otp_settings['resend_cooldown'] ?? 60,
    ];

    wp_localize_script( 'aurora-theme', 'auroraTheme', $aurora_vars );
    wp_localize_script( 'aurora-auth', 'auroraTheme', $aurora_vars );
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
    
    // Allow guests to view published pages
    if ( ! is_user_logged_in() && ! is_admin() && $query->is_main_query() ) {
        if ( $query->is_singular( 'page' ) ) {
            // For single page requests, only show published pages
            $query->set( 'post_status', 'publish' );
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
    return $where;
}

// Ensure WooCommerce critical pages are published and accessible to guests
add_action( 'wp_loaded', 'aurora_fix_page_visibility_for_guests' );
function aurora_fix_page_visibility_for_guests() {
    // Only run this once per admin session
    if ( get_transient( 'aurora_page_visibility_fixed' ) ) {
        return;
    }
    
    // Fix visibility for critical WooCommerce pages
    $critical_pages = array(
        'shop'      => 'Shop',
        'cart'      => 'Cart',
        'checkout'  => 'Checkout',
        'myaccount' => 'My Account',
    );
    
    foreach ( $critical_pages as $page_type => $page_name ) {
        $page_id = wc_get_page_id( $page_type );
        
        if ( $page_id && $page_id > 0 ) {
            $page = get_post( $page_id );
            
            // If page exists and is not published, make it published
            if ( $page && 'publish' !== $page->post_status ) {
                wp_update_post( array(
                    'ID'          => $page_id,
                    'post_status' => 'publish',
                ) );
            }
        }
    }
    
    // Also ensure other important pages are published
    $pages_to_check = get_pages( array(
        'post_status' => array( 'private', 'draft' ),
    ) );
    
    foreach ( $pages_to_check as $page ) {
        $title_lower = strtolower( $page->post_title );
        // Publish pages with these keywords
        $publish_keywords = array( 'shop', 'cart', 'checkout', 'account', 'login', 'register', 'sample' );
        
        foreach ( $publish_keywords as $keyword ) {
            if ( strpos( $title_lower, $keyword ) !== false ) {
                wp_update_post( array(
                    'ID'          => $page->ID,
                    'post_status' => 'publish',
                ) );
                break;
            }
        }
    }
    
    set_transient( 'aurora_page_visibility_fixed', true, HOUR_IN_SECONDS );
}

// Remove private pages from menu for guests
add_filter( 'wp_get_nav_menu_items', 'aurora_filter_private_pages_from_menu' );
function aurora_filter_private_pages_from_menu( $items ) {
    if ( ! is_user_logged_in() ) {
        $items = array_filter( $items, function( $item ) {
            if ( 'page_item' === $item->type || 'page' === $item->type ) {
                $page = get_post( $item->object_id );
                return ( $page && 'publish' === $page->post_status );
            }
            return true;
        } );
    }
    return $items;
}

// Fallback primary menu that lists all published pages
function aurora_primary_menu_fallback() {
    $pages = get_pages( [
        'post_status' => 'publish',
        'orderby'     => 'post_title',
        'sort_column' => 'post_title',
        'exclude'     => [ get_option( 'page_on_front' ), get_option( 'page_for_posts' ) ],
    ] );

    if ( empty( $pages ) ) {
        return;
    }

    $is_admin = is_user_logged_in() && ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' ) );
    $hide_slugs = [
        'login', 'register', 'registration',
        'product-manager', 'product_manager', 'productmanger', 'product-manger', 'product-mangment',
        'order-management', 'order_manager', 'order-manager', 'order-managment', 'order-mangment', 'ordermangment',
        'sample-page'
    ];

    $current_post_id = get_queried_object_id();
    echo '<ul class="menu menu-primary">';
    foreach ( $pages as $page ) {
        if ( ! $is_admin ) {
            $slug = strtolower( $page->post_name );
            if ( in_array( $slug, $hide_slugs, true ) ) {
                continue;
            }
        }
        $active_class = ( $current_post_id === $page->ID ) ? 'current-menu-item' : '';
        printf(
            '<li class="menu-item %1$s"><a href="%2$s">%3$s</a></li>',
            esc_attr( $active_class ),
            esc_url( get_permalink( $page->ID ) ),
            esc_html( $page->post_title )
        );
    }
    echo '</ul>';
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
// Enable guest checkout for WooCommerce
add_filter( 'woocommerce_checkout_fields', 'aurora_guest_checkout_support' );
function aurora_guest_checkout_support( $fields ) {
    if ( ! is_admin() && ! is_user_logged_in() ) {
        // Guest checkout is enabled by default in WooCommerce
        // This ensures billing fields are shown without account creation
        if ( isset( $fields['account'] ) ) {
            unset( $fields['account'] );
        }
    }
    return $fields;
}

// Ensure WooCommerce allows guests to shop
add_filter( 'woocommerce_is_store_notice_dismissed', '__return_true' );
add_action( 'init', 'aurora_ensure_guest_shopping' );
function aurora_ensure_guest_shopping() {
    // WooCommerce guest shopping is enabled by default
    // Ensure no restrictions are in place
}
// Custom My Account Sidebar
add_action( 'woocommerce_account_content', 'aurora_add_myaccount_sidebar', 1 );
function aurora_add_myaccount_sidebar() {
    if ( is_page() && is_user_logged_in() ) {
        // Load custom sidebar
        $sidebar_file = get_template_directory() . '/woocommerce/myaccount/sidebar-navigation.php';
        if ( file_exists( $sidebar_file ) ) {
            echo '<div class="aurora-myaccount-wrapper" style="display: grid; grid-template-columns: 280px 1fr; gap: 30px; align-items: start;">';
            include $sidebar_file;
            echo '<div class="aurora-myaccount-content">';
        }
    }
}

add_action( 'woocommerce_account_content', 'aurora_close_myaccount_wrapper', 999 );
function aurora_close_myaccount_wrapper() {
    if ( is_page() && is_user_logged_in() ) {
        $sidebar_file = get_template_directory() . '/woocommerce/myaccount/sidebar-navigation.php';
        if ( file_exists( $sidebar_file ) ) {
            echo '</div></div>';
        }
    }
}

// Use custom my-account template
add_filter( 'wc_get_template', 'aurora_myaccount_template', 10, 5 );
function aurora_myaccount_template( $template, $template_name, $args, $template_path, $default_path ) {
    if ( 'myaccount/dashboard.php' === $template_name && is_user_logged_in() ) {
        // Check if we're on the my-account page
        if ( is_page() && get_queried_object()->post_name === 'my-account' ) {
            $custom_template = get_template_directory() . '/template-myaccount.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
    }
    return $template;
}

// Add body class for my-account pages
add_filter( 'body_class', 'aurora_myaccount_body_class' );
function aurora_myaccount_body_class( $classes ) {
    if ( is_page() && function_exists( 'is_account_page' ) && is_account_page() ) {
        $classes[] = 'aurora-myaccount-page';
    }
    return $classes;
}