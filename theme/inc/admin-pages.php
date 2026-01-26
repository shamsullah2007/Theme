<?php
/**
 * Aurora - Admin & User Pages functionality
 */

// ===== Registration & Login Pages =====

add_shortcode( 'aurora_login_form', 'aurora_login_form_shortcode' );
function aurora_login_form_shortcode() {
    if ( is_user_logged_in() ) {
        return '<p>' . sprintf( __( 'You are already logged in. <a href="%s">Go to your account</a>.', 'aurora' ), esc_url( wc_get_page_permalink( 'myaccount' ) ) ) . '</p>';
    }

    ob_start();
    ?>
    <div class="aurora-login-form">
        <form method="post" action="<?php echo esc_url( wp_login_url( home_url() ) ); ?>" class="login-form">
            <div class="form-group">
                <label for="log"><?php esc_html_e( 'Username or Email', 'aurora' ); ?></label>
                <input type="text" name="log" id="log" class="form-control" required />
            </div>
            <div class="form-group">
                <label for="pwd"><?php esc_html_e( 'Password', 'aurora' ); ?></label>
                <input type="password" name="pwd" id="pwd" class="form-control" required />
            </div>
            <div class="form-group checkbox">
                <label><input type="checkbox" name="rememberme" value="forever" /> <?php esc_html_e( 'Remember Me', 'aurora' ); ?></label>
            </div>
            <button type="submit" class="button button-primary"><?php esc_html_e( 'Sign In', 'aurora' ); ?></button>
            <div class="form-links">
                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost Password?', 'aurora' ); ?></a>
                <a href="<?php echo esc_url( site_url( '/register/' ) ); ?>"><?php esc_html_e( 'Create Account', 'aurora' ); ?></a>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode( 'aurora_registration_form', 'aurora_registration_form_shortcode' );
function aurora_registration_form_shortcode() {
    if ( is_user_logged_in() ) {
        return '<p>' . sprintf( __( 'You are already registered. <a href="%s">Go to your account</a>.', 'aurora' ), esc_url( wc_get_page_permalink( 'myaccount' ) ) ) . '</p>';
    }

    $error = '';
    $success = '';

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['aurora_register_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_POST['aurora_register_nonce'], 'aurora_register' ) ) {
            $error = __( 'Nonce verification failed.', 'aurora' );
        } else {
            $username = sanitize_text_field( isset( $_POST['username'] ) ? $_POST['username'] : '' );
            $email = sanitize_email( isset( $_POST['email'] ) ? $_POST['email'] : '' );
            $password = isset( $_POST['password'] ) ? $_POST['password'] : '';
            $password_confirm = isset( $_POST['password_confirm'] ) ? $_POST['password_confirm'] : '';

            if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
                $error = __( 'All fields are required.', 'aurora' );
            } elseif ( strlen( $password ) < 6 ) {
                $error = __( 'Password must be at least 6 characters.', 'aurora' );
            } elseif ( $password !== $password_confirm ) {
                $error = __( 'Passwords do not match.', 'aurora' );
            } elseif ( username_exists( $username ) ) {
                $error = __( 'Username already exists.', 'aurora' );
            } elseif ( email_exists( $email ) ) {
                $error = __( 'Email already registered.', 'aurora' );
            } else {
                $user_id = wp_create_user( $username, $password, $email );
                if ( ! is_wp_error( $user_id ) ) {
                    $success = __( 'Registration successful! You can now log in.', 'aurora' );
                    wp_safe_remote_post( wp_login_url( home_url() ), [
                        'body' => [
                            'log' => $username,
                            'pwd' => $password,
                            'wp-submit' => 'Log In',
                            'redirect_to' => wc_get_page_permalink( 'myaccount' ),
                        ],
                    ] );
                } else {
                    $error = $user_id->get_error_message();
                }
            }
        }
    }

    ob_start();
    ?>
    <div class="aurora-registration-form">
        <?php if ( $error ) : ?>
            <div class="alert alert-danger"><?php echo esc_html( $error ); ?></div>
        <?php endif; ?>
        <?php if ( $success ) : ?>
            <div class="alert alert-success"><?php echo esc_html( $success ); ?></div>
        <?php endif; ?>

        <form method="post" class="register-form">
            <?php wp_nonce_field( 'aurora_register', 'aurora_register_nonce' ); ?>
            <div class="form-group">
                <label for="username"><?php esc_html_e( 'Username', 'aurora' ); ?></label>
                <input type="text" name="username" id="username" class="form-control" required />
            </div>
            <div class="form-group">
                <label for="email"><?php esc_html_e( 'Email', 'aurora' ); ?></label>
                <input type="email" name="email" id="email" class="form-control" required />
            </div>
            <div class="form-group">
                <label for="password"><?php esc_html_e( 'Password', 'aurora' ); ?></label>
                <input type="password" name="password" id="password" class="form-control" required />
            </div>
            <div class="form-group">
                <label for="password_confirm"><?php esc_html_e( 'Confirm Password', 'aurora' ); ?></label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" required />
            </div>
            <button type="submit" class="button button-primary"><?php esc_html_e( 'Register', 'aurora' ); ?></button>
            <div class="form-links">
                <a href="<?php echo esc_url( site_url( '/login/' ) ); ?>"><?php esc_html_e( 'Already have an account? Log In', 'aurora' ); ?></a>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

// ===== Admin Product Management =====

add_shortcode( 'aurora_admin_product_manager', 'aurora_admin_product_manager_shortcode' );
function aurora_admin_product_manager_shortcode() {
    if ( ! current_user_can( 'manage_woocommerce_products' ) && ! current_user_can( 'manage_options' ) ) {
        return '<p class="alert alert-danger">' . esc_html__( 'You do not have permission to access this page.', 'aurora' ) . '</p>';
    }

    $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
    $product_id = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0;

    ob_start();
    ?>
    <div class="aurora-admin-panel">
        <h1><?php esc_html_e( 'Manage Products', 'aurora' ); ?></h1>

        <div class="admin-nav">
            <a href="<?php echo esc_url( add_query_arg( 'action', 'list' ) ); ?>" class="button <?php echo 'list' === $action ? 'active' : ''; ?>"><?php esc_html_e( 'All Products', 'aurora' ); ?></a>
            <a href="<?php echo esc_url( add_query_arg( 'action', 'add' ) ); ?>" class="button button-primary"><?php esc_html_e( '+ Add Product', 'aurora' ); ?></a>
        </div>

        <?php
        if ( 'add' === $action || 'edit' === $action ) {
            aurora_product_form( $product_id );
        } else {
            aurora_products_list();
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}

function aurora_product_form( $product_id = 0 ) {
    $product = $product_id ? wc_get_product( $product_id ) : null;
    $is_edit = ! ! $product;

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['aurora_product_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_POST['aurora_product_nonce'], 'aurora_product_form' ) ) {
            echo '<div class="alert alert-danger">' . esc_html__( 'Nonce verification failed.', 'aurora' ) . '</div>';
            return;
        }

        $product_data = array(
            'name'        => sanitize_text_field( isset( $_POST['product_name'] ) ? $_POST['product_name'] : '' ),
            'description' => wp_kses_post( isset( $_POST['product_desc'] ) ? $_POST['product_desc'] : '' ),
            'regular_price' => floatval( isset( $_POST['product_price'] ) ? $_POST['product_price'] : 0 ),
            'stock_qty'   => absint( isset( $_POST['product_stock'] ) ? $_POST['product_stock'] : 0 ),
            'sku'         => sanitize_text_field( isset( $_POST['product_sku'] ) ? $_POST['product_sku'] : '' ),
            'category_ids' => isset( $_POST['product_categories'] ) ? array_map( 'absint', $_POST['product_categories'] ) : array(),
        );

        if ( $is_edit ) {
            $product->set_name( $product_data['name'] );
            $product->set_description( $product_data['description'] );
            $product->set_regular_price( $product_data['regular_price'] );
            $product->set_stock_quantity( $product_data['stock_qty'] );
            $product->set_sku( $product_data['sku'] );
            $product->set_category_ids( $product_data['category_ids'] );
            
            // Handle image upload
            if ( ! empty( $_FILES['product_image']['name'] ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                
                $attachment_id = media_handle_upload( 'product_image', $product->get_id() );
                if ( ! is_wp_error( $attachment_id ) ) {
                    $product->set_image_id( $attachment_id );
                }
            }
            
            $product->save();
            echo '<div class="alert alert-success">' . esc_html__( 'Product updated successfully.', 'aurora' ) . '</div>';
        } else {
            $new_product = new WC_Product_Simple();
            $new_product->set_name( $product_data['name'] );
            $new_product->set_description( $product_data['description'] );
            $new_product->set_regular_price( $product_data['regular_price'] );
            $new_product->set_stock_quantity( $product_data['stock_qty'] );
            $new_product->set_sku( $product_data['sku'] );
            $new_product->set_category_ids( $product_data['category_ids'] );
            $new_product->set_status( 'publish' );
            $product_id = $new_product->save();
            
            // Handle image upload for new product
            if ( ! empty( $_FILES['product_image']['name'] ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                
                $attachment_id = media_handle_upload( 'product_image', $product_id );
                if ( ! is_wp_error( $attachment_id ) ) {
                    $new_product->set_image_id( $attachment_id );
                    $new_product->save();
                }
            }
            
            echo '<div class="alert alert-success">' . sprintf( esc_html__( 'Product created successfully. <a href="%s">View All</a>', 'aurora' ), esc_url( add_query_arg( 'action', 'list' ) ) ) . '</div>';
        }
    }

    ?>
    <form method="post" class="product-form" enctype="multipart/form-data">
        <?php wp_nonce_field( 'aurora_product_form', 'aurora_product_nonce' ); ?>
        <div class="form-group">
            <label for="product_name"><?php esc_html_e( 'Product Name *', 'aurora' ); ?></label>
            <input type="text" name="product_name" id="product_name" class="form-control" value="<?php echo $product ? esc_attr( $product->get_name() ) : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="product_sku"><?php esc_html_e( 'SKU', 'aurora' ); ?></label>
            <input type="text" name="product_sku" id="product_sku" class="form-control" value="<?php echo $product ? esc_attr( $product->get_sku() ) : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="product_price"><?php esc_html_e( 'Price *', 'aurora' ); ?></label>
            <input type="number" name="product_price" id="product_price" class="form-control" step="0.01" value="<?php echo $product ? esc_attr( $product->get_regular_price() ) : ''; ?>" required />
        </div>
        <div class="form-group">
            <label for="product_stock"><?php esc_html_e( 'Stock Quantity *', 'aurora' ); ?></label>
            <input type="number" name="product_stock" id="product_stock" class="form-control" value="<?php echo $product ? esc_attr( $product->get_stock_quantity() ) : '0'; ?>" required />
        </div>
        <div class="form-group">
            <label for="product_categories"><?php esc_html_e( 'Categories', 'aurora' ); ?></label>
            <select name="product_categories[]" id="product_categories" class="form-control aurora-category-select" multiple size="5">
                <?php
                $categories = get_terms( array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                ) );
                $selected_categories = $product ? $product->get_category_ids() : array();
                if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                    foreach ( $categories as $category ) {
                        $category_color = get_term_meta( $category->term_id, 'aurora_category_color', true );
                        $category_color = $category_color ? $category_color : '#0b57d0';
                        $style = 'style="border-left: 4px solid ' . esc_attr( $category_color ) . ';"';
                        printf(
                            '<option value="%d" %s data-color="%s">%s</option>',
                            $category->term_id,
                            in_array( $category->term_id, $selected_categories ) ? 'selected' : '',
                            esc_attr( $category_color ),
                            esc_html( $category->name )
                        );
                    }
                }
                ?>
            </select>
            <small><?php esc_html_e( 'Hold Ctrl (Cmd on Mac) to select multiple categories. Colors are automatically assigned to each category.', 'aurora' ); ?></small>
        </div>
        <div class="form-group">
            <label for="product_image"><?php esc_html_e( 'Product Image', 'aurora' ); ?></label>
            <?php if ( $product && $product->get_image_id() ) : ?>
                <div class="current-product-image" style="margin-bottom: 10px;">
                    <?php echo wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ); ?>
                    <p><small><?php esc_html_e( 'Current image - upload new to replace', 'aurora' ); ?></small></p>
                </div>
            <?php endif; ?>
            <input type="file" name="product_image" id="product_image" class="form-control" accept="image/*" />
            <small><?php esc_html_e( 'Upload product image (JPG, PNG, GIF)', 'aurora' ); ?></small>
        </div>
        <div class="form-group">
            <label for="product_desc"><?php esc_html_e( 'Description', 'aurora' ); ?></label>
            <textarea name="product_desc" id="product_desc" class="form-control" rows="6"><?php echo $product ? wp_kses_post( $product->get_description() ) : ''; ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="button button-primary"><?php echo $is_edit ? esc_html__( 'Update Product', 'aurora' ) : esc_html__( 'Add Product', 'aurora' ); ?></button>
            <a href="<?php echo esc_url( add_query_arg( 'action', 'list' ) ); ?>" class="button"><?php esc_html_e( 'Cancel', 'aurora' ); ?></a>
        </div>
    </form>
    <?php
}

function aurora_products_list() {
    $per_page = 20;
    $paged = max( 1, absint( isset( $_GET['paged'] ) ? $_GET['paged'] : 1 ) );
    $offset = ( $paged - 1 ) * $per_page;

    $args = [
        'limit'  => $per_page,
        'offset' => $offset,
        'return' => 'objects',
    ];

    $products = wc_get_products( $args );
    $total = wc_get_products( array_merge( $args, [ 'limit' => -1 ] ) );
    $total_pages = ceil( count( $total ) / $per_page );

    // Handle delete
    if ( isset( $_GET['delete'] ) && wp_verify_nonce( isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '', 'delete_product' ) ) {
        $delete_id = absint( $_GET['delete'] );
        wp_delete_post( $delete_id, true );
        echo '<div class="alert alert-success">' . esc_html__( 'Product deleted.', 'aurora' ) . '</div>';
    }

    ?>
    <div class="products-table-wrapper">
        <table class="products-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'ID', 'aurora' ); ?></th>
                    <th><?php esc_html_e( 'Name', 'aurora' ); ?></th>
                    <th><?php esc_html_e( 'SKU', 'aurora' ); ?></th>
                    <th><?php esc_html_e( 'Price', 'aurora' ); ?></th>
                    <th><?php esc_html_e( 'Stock', 'aurora' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'aurora' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $products as $product ) {
                    ?>
                    <tr>
                        <td><?php echo esc_html( $product->get_id() ); ?></td>
                        <td><strong><?php echo esc_html( $product->get_name() ); ?></strong></td>
                        <td><?php echo esc_html( $product->get_sku() ); ?></td>
                        <td><?php echo wp_kses_post( $product->get_price_html() ); ?></td>
                        <td><?php echo esc_html( $product->get_stock_quantity() ?? 0 ); ?></td>
                        <td>
                            <a href="<?php echo esc_url( add_query_arg( [ 'action' => 'edit', 'product_id' => $product->get_id() ] ) ); ?>" class="button button-small"><?php esc_html_e( 'Edit', 'aurora' ); ?></a>
                            <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'delete', $product->get_id() ), 'delete_product' ) ); ?>" class="button button-small" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'aurora' ); ?>');"><?php esc_html_e( 'Delete', 'aurora' ); ?></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php if ( $total_pages > 1 ) : ?>
        <div class="pagination">
            <?php
            for ( $i = 1; $i <= $total_pages; $i++ ) {
                $page_url = add_query_arg( 'paged', $i );
                $class = $i === $paged ? 'active' : '';
                echo '<a href="' . esc_url( $page_url ) . '" class="page-link ' . esc_attr( $class ) . '">' . esc_html( $i ) . '</a>';
            }
            ?>
        </div>
    <?php endif; ?>
    <?php
}
