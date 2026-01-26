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

    wp_enqueue_style( 'aurora-theme', get_template_directory_uri() . '/assets/css/theme.css', [], $theme_version );
    wp_enqueue_style( 'aurora-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', [ 'woocommerce-general' ], $theme_version );
    wp_enqueue_style( 'aurora-admin-pages', get_template_directory_uri() . '/assets/css/admin-pages.css', [], $theme_version );
    wp_enqueue_style( 'aurora-order-manager', get_template_directory_uri() . '/assets/css/order-manager.css', [], $theme_version );
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

// ===== OTP & Profile Management System =====

// Create tables on theme activation
add_action( 'after_setup_theme', 'aurora_create_otp_tables' );
function aurora_create_otp_tables() {
    global $wpdb;
    
    $otp_table = $wpdb->prefix . 'aurora_otps';
    $attempt_table = $wpdb->prefix . 'aurora_otp_attempts';
    
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$otp_table'" ) != $otp_table ) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $otp_table (
            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id bigint(20) NOT NULL,
            otp_code varchar(6) NOT NULL,
            email varchar(100) NOT NULL,
            action_type varchar(50) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime NOT NULL,
            is_used tinyint(1) DEFAULT 0,
            KEY user_id (user_id),
            KEY email (email)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$attempt_table'" ) != $attempt_table ) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $attempt_table (
            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id bigint(20) NOT NULL,
            email varchar(100) NOT NULL,
            attempt_date date NOT NULL,
            attempt_count int(11) DEFAULT 1,
            KEY user_id (user_id),
            KEY email (email),
            KEY attempt_date (attempt_date)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}

// Generate and send OTP
function aurora_generate_and_send_otp( $user_id, $email, $action_type ) {
    global $wpdb;
    
    $otp_table = $wpdb->prefix . 'aurora_otps';
    $attempt_table = $wpdb->prefix . 'aurora_otp_attempts';
    
    // Check daily attempt limit
    $today = date( 'Y-m-d' );
    $attempts = $wpdb->get_var( $wpdb->prepare(
        "SELECT attempt_count FROM $attempt_table WHERE user_id = %d AND email = %s AND attempt_date = %s",
        $user_id, $email, $today
    ) );
    
    if ( ! $attempts ) {
        $attempts = 0;
    }
    
    if ( $attempts >= 5 ) {
        return array(
            'success' => false,
            'message' => __( 'Maximum OTP attempts (5) reached today. Please try again tomorrow.', 'aurora' )
        );
    }
    
    // Generate 6-digit OTP
    $otp_code = str_pad( mt_rand( 0, 999999 ), 6, '0', STR_PAD_LEFT );
    $expires_at = date( 'Y-m-d H:i:s', time() + 10 * 60 ); // 10 minutes
    
    // Store OTP in database
    $wpdb->insert(
        $otp_table,
        array(
            'user_id' => $user_id,
            'otp_code' => $otp_code,
            'email' => $email,
            'action_type' => $action_type,
            'expires_at' => $expires_at
        ),
        array( '%d', '%s', '%s', '%s', '%s' )
    );
    
    // Update attempt count
    if ( $attempts == 0 ) {
        $wpdb->insert(
            $attempt_table,
            array(
                'user_id' => $user_id,
                'email' => $email,
                'attempt_date' => $today,
                'attempt_count' => 1
            ),
            array( '%d', '%s', '%s', '%d' )
        );
    } else {
        $wpdb->update(
            $attempt_table,
            array( 'attempt_count' => $attempts + 1 ),
            array( 'user_id' => $user_id, 'email' => $email, 'attempt_date' => $today ),
            array( '%d' ),
            array( '%d', '%s', '%s' )
        );
    }
    
    // Send OTP via email
    $user = get_user_by( 'ID', $user_id );
    $action_text = ( 'email_change' === $action_type ) ? 'change your email' : 'reset your password';
    
    $email_body = sprintf(
        __( 'Hello %s,\n\nYour OTP code is: %s\n\nThis code will expire in 10 minutes.\n\nIf you did not request this, please ignore this email.\n\nBest regards,\nThe Store Team', 'aurora' ),
        $user->display_name,
        $otp_code
    );
    
    $subject = sprintf( __( 'Your OTP Code to %s', 'aurora' ), $action_text );
    
    wp_mail( $email, $subject, $email_body );
    
    return array(
        'success' => true,
        'message' => sprintf( __( 'OTP sent to %s', 'aurora' ), $email ),
        'attempts_left' => 5 - $attempts - 1
    );
}

// Verify OTP
function aurora_verify_otp( $user_id, $email, $otp_code, $action_type ) {
    global $wpdb;
    
    $otp_table = $wpdb->prefix . 'aurora_otps';
    
    $otp = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $otp_table WHERE user_id = %d AND email = %s AND otp_code = %s AND action_type = %s AND is_used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1",
        $user_id, $email, $otp_code, $action_type
    ) );
    
    if ( ! $otp ) {
        return array(
            'success' => false,
            'message' => __( 'Invalid or expired OTP. Please request a new one.', 'aurora' )
        );
    }
    
    // Mark OTP as used
    $wpdb->update(
        $otp_table,
        array( 'is_used' => 1 ),
        array( 'id' => $otp->id ),
        array( '%d' ),
        array( '%d' )
    );
    
    return array(
        'success' => true,
        'message' => __( 'OTP verified successfully!', 'aurora' )
    );
}

// Update user email with verification
add_action( 'wp_ajax_aurora_update_email', 'aurora_update_email_ajax' );
function aurora_update_email_ajax() {
    check_ajax_referer( 'aurora_profile_nonce', 'nonce' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Not logged in' ) );
    }
    
    $user_id = get_current_user_id();
    $new_email = sanitize_email( $_POST['email'] );
    $otp_code = sanitize_text_field( $_POST['otp'] );
    
    if ( ! $new_email || ! $otp_code ) {
        wp_send_json_error( array( 'message' => 'Missing required fields' ) );
    }
    
    // Verify OTP
    $verify = aurora_verify_otp( $user_id, $new_email, $otp_code, 'email_change' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( $verify );
    }
    
    // Check if email already exists
    if ( email_exists( $new_email ) ) {
        wp_send_json_error( array( 'message' => __( 'This email is already in use.', 'aurora' ) ) );
    }
    
    // Update email
    wp_update_user( array(
        'ID' => $user_id,
        'user_email' => $new_email
    ) );
    
    wp_send_json_success( array( 'message' => __( 'Email updated successfully!', 'aurora' ) ) );
}

// Update user password with verification
add_action( 'wp_ajax_aurora_update_password', 'aurora_update_password_ajax' );
function aurora_update_password_ajax() {
    check_ajax_referer( 'aurora_profile_nonce', 'nonce' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Not logged in' ) );
    }
    
    $user_id = get_current_user_id();
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $otp_code = sanitize_text_field( $_POST['otp'] );
    
    $user = get_user_by( 'ID', $user_id );
    
    // Verify current password
    if ( ! wp_check_password( $current_password, $user->user_pass ) ) {
        wp_send_json_error( array( 'message' => __( 'Current password is incorrect.', 'aurora' ) ) );
    }
    
    // Verify OTP
    $verify = aurora_verify_otp( $user_id, $user->user_email, $otp_code, 'password_change' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( $verify );
    }
    
    // Update password
    wp_update_user( array(
        'ID' => $user_id,
        'user_pass' => $new_password
    ) );
    
    wp_send_json_success( array( 'message' => __( 'Password updated successfully!', 'aurora' ) ) );
}

// Reset password with OTP
add_action( 'wp_ajax_aurora_reset_password', 'aurora_reset_password_ajax' );
function aurora_reset_password_ajax() {
    check_ajax_referer( 'aurora_profile_nonce', 'nonce' );
    
    $email = sanitize_email( $_POST['email'] );
    
    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        wp_send_json_error( array( 'message' => __( 'No account found with this email.', 'aurora' ) ) );
    }
    
    // Generate and send OTP
    $result = aurora_generate_and_send_otp( $user->ID, $email, 'password_reset' );
    
    wp_send_json( $result );
}

// Confirm password reset with OTP
add_action( 'wp_ajax_aurora_confirm_password_reset', 'aurora_confirm_password_reset_ajax' );
function aurora_confirm_password_reset_ajax() {
    check_ajax_referer( 'aurora_profile_nonce', 'nonce' );
    
    $email = sanitize_email( $_POST['email'] );
    $otp_code = sanitize_text_field( $_POST['otp'] );
    $new_password = $_POST['new_password'];
    
    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        wp_send_json_error( array( 'message' => __( 'No account found with this email.', 'aurora' ) ) );
    }
    
    // Verify OTP
    $verify = aurora_verify_otp( $user->ID, $email, $otp_code, 'password_reset' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( $verify );
    }
    
    // Update password
    wp_update_user( array(
        'ID' => $user->ID,
        'user_pass' => $new_password
    ) );
    
    wp_send_json_success( array( 'message' => __( 'Password reset successfully!', 'aurora' ) ) );
}

// Handle profile image upload
add_action( 'wp_ajax_aurora_upload_profile_image', 'aurora_upload_profile_image_ajax' );
function aurora_upload_profile_image_ajax() {
    check_ajax_referer( 'aurora_profile_nonce', 'nonce' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Not logged in' ) );
    }
    
    if ( ! isset( $_FILES['profile_image'] ) ) {
        wp_send_json_error( array( 'message' => 'No file uploaded' ) );
    }
    
    $user_id = get_current_user_id();
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    
    $attachment_id = media_handle_upload( 'profile_image', 0 );
    
    if ( is_wp_error( $attachment_id ) ) {
        wp_send_json_error( array( 'message' => $attachment_id->get_error_message() ) );
    }
    
    // Delete old profile image if exists
    $old_image_id = get_user_meta( $user_id, 'aurora_profile_image', true );
    if ( $old_image_id && is_numeric( $old_image_id ) ) {
        wp_delete_attachment( $old_image_id, true );
    }
    
    // Save new profile image ID
    update_user_meta( $user_id, 'aurora_profile_image', $attachment_id );
    
    $image_url = wp_get_attachment_url( $attachment_id );
    
    wp_send_json_success( array(
        'message' => __( 'Profile image updated!', 'aurora' ),
        'image_url' => $image_url
    ) );
}

// Get user profile image
function aurora_get_user_profile_image( $user_id ) {
    $image_id = get_user_meta( $user_id, 'aurora_profile_image', true );
    
    if ( $image_id && is_numeric( $image_id ) ) {
        return wp_get_attachment_url( $image_id );
    }
    
    // Return default avatar
    return get_avatar_url( $user_id, array( 'size' => 150 ) );
}

// Send OTP AJAX
add_action( 'wp_ajax_aurora_request_otp', 'aurora_request_otp_ajax' );
function aurora_request_otp_ajax() {
    check_ajax_referer( 'aurora_profile_nonce', 'nonce' );
    
    $action_type = sanitize_text_field( $_POST['action_type'] );
    
    if ( 'email_change' === $action_type || 'password_change' === $action_type ) {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => 'Not logged in' ) );
        }
        
        $user_id = get_current_user_id();
        $user = get_user_by( 'ID', $user_id );
        
        if ( 'email_change' === $action_type ) {
            $new_email = sanitize_email( $_POST['email'] );
            $current_password = $_POST['password'];
            
            // Verify current password
            if ( ! wp_check_password( $current_password, $user->user_pass ) ) {
                wp_send_json_error( array( 'message' => __( 'Password is incorrect.', 'aurora' ) ) );
            }
            
            $email = $new_email;
        } else {
            $email = $user->user_email;
        }
        
        $result = aurora_generate_and_send_otp( $user_id, $email, $action_type );
    } elseif ( 'password_reset' === $action_type ) {
        $email = sanitize_email( $_POST['email'] );
        $user = get_user_by( 'email', $email );
        
        if ( ! $user ) {
            // Don't reveal if email exists for security
            wp_send_json_success( array(
                'message' => __( 'If an account exists with this email, you will receive an OTP shortly.', 'aurora' )
            ) );
            return;
        }
        
        $result = aurora_generate_and_send_otp( $user->ID, $email, 'password_reset' );
    }
    
    wp_send_json( $result );
}