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
    <div class="aurora-login-container">
        <div class="aurora-login-wrapper">
            <div class="login-card">
                <h1><?php esc_html_e( 'Welcome Back', 'aurora' ); ?></h1>
                <p class="login-subtitle"><?php esc_html_e( 'Sign in to your account to continue', 'aurora' ); ?></p>

                <!-- Login Form -->
                <form method="post" action="<?php echo esc_url( wp_login_url( home_url() ) ); ?>" class="login-form" id="aurora-login-form">
                    <div class="form-group">
                        <label for="log"><?php esc_html_e( 'Email or Username', 'aurora' ); ?></label>
                        <input type="text" name="log" id="log" class="form-input" placeholder="<?php esc_attr_e( 'Enter your email or username', 'aurora' ); ?>" required />
                    </div>

                    <div class="form-group">
                        <label for="pwd"><?php esc_html_e( 'Password', 'aurora' ); ?></label>
                        <input type="password" name="pwd" id="pwd" class="form-input" placeholder="<?php esc_attr_e( 'Enter your password', 'aurora' ); ?>" required />
                    </div>

                    <div class="form-group checkbox">
                        <label><input type="checkbox" name="rememberme" value="forever" /> <?php esc_html_e( 'Remember me', 'aurora' ); ?></label>
                    </div>

                    <button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Sign In', 'aurora' ); ?></button>
                </form>

                <!-- Forgot Password Link -->
                <div class="login-divider">
                    <span><?php esc_html_e( 'or', 'aurora' ); ?></span>
                </div>

                <button type="button" class="button button-secondary button-large" id="forgot-password-toggle"><?php esc_html_e( 'Forgot Password?', 'aurora' ); ?></button>

                <!-- Forgot Password Form (Hidden) -->
                <form class="forgot-password-form" id="aurora-forgot-form" style="display: none;">
                    <div class="form-group">
                        <label for="forgot-email"><?php esc_html_e( 'Email Address', 'aurora' ); ?></label>
                        <input type="email" id="forgot-email" class="form-input" placeholder="<?php esc_attr_e( 'Enter your email', 'aurora' ); ?>" required />
                    </div>
                    <button type="button" class="button button-primary button-large" id="send-reset-otp"><?php esc_html_e( 'Send OTP', 'aurora' ); ?></button>
                    <button type="button" class="button button-secondary" id="cancel-forgot"><?php esc_html_e( 'Back to Login', 'aurora' ); ?></button>

                    <!-- OTP Section (Hidden) -->
                    <div id="reset-otp-section" style="display: none; margin-top: 20px;">
                        <div class="form-group">
                            <label for="reset-otp"><?php esc_html_e( 'Enter OTP (sent to your email)', 'aurora' ); ?></label>
                            <input type="text" id="reset-otp" class="form-input" placeholder="<?php esc_attr_e( '000000', 'aurora' ); ?>" maxlength="6" required />
                        </div>

                        <div class="form-group">
                            <label for="reset-new-password"><?php esc_html_e( 'New Password', 'aurora' ); ?></label>
                            <input type="password" id="reset-new-password" class="form-input" placeholder="<?php esc_attr_e( 'Enter new password', 'aurora' ); ?>" required />
                        </div>

                        <button type="button" class="button button-success button-large" id="confirm-reset-password"><?php esc_html_e( 'Reset Password', 'aurora' ); ?></button>
                        <button type="button" class="button button-secondary" id="skip-reset"><?php esc_html_e( 'Skip', 'aurora' ); ?></button>
                    </div>
                </form>

                <!-- Register Link -->
                <div class="login-footer">
                    <p><?php esc_html_e( "Don't have an account?", 'aurora' ); ?> <a href="<?php echo esc_url( get_page_by_path( 'registration' ) ? get_permalink( get_page_by_path( 'registration' ) ) : add_query_arg( 'action', 'register', wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="register-link"><?php esc_html_e( 'Create one now', 'aurora' ); ?></a></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .aurora-login-container {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7ff 0%, #f0f4ff 100%);
            padding: 40px 20px;
        }

        .aurora-login-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .login-card h1 {
            font-size: 28px;
            margin: 0 0 8px 0;
            color: #1a1a1a;
            font-weight: 700;
            text-align: center;
        }

        .login-subtitle {
            font-size: 14px;
            color: #6b7280;
            text-align: center;
            margin: 0 0 30px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: #0b57d0;
            box-shadow: 0 0 0 3px rgba(11, 87, 208, 0.1);
        }

        .form-group.checkbox {
            display: flex;
            align-items: center;
        }

        .form-group.checkbox label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-weight: 500;
            cursor: pointer;
        }

        .form-group.checkbox input {
            cursor: pointer;
        }

        .button {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            display: inline-block;
            text-decoration: none;
        }

        .button-primary {
            background: #0b57d0;
            color: white;
        }

        .button-primary:hover {
            background: #0942a6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(11, 87, 208, 0.3);
        }

        .button-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .button-secondary:hover {
            background: #e5e7eb;
        }

        .button-success {
            background: #10b981;
            color: white;
        }

        .button-success:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .button-large {
            width: 100%;
        }

        .login-divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
            color: #9ca3af;
            font-size: 13px;
        }

        .login-divider span {
            background: white;
            padding: 0 12px;
            position: relative;
            z-index: 1;
        }

        .login-divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: 0;
        }

        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .login-footer p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }

        .register-link {
            color: #0b57d0;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .register-link:hover {
            color: #0942a6;
            text-decoration: underline;
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            .login-card {
                padding: 30px 20px;
            }

            .login-card h1 {
                font-size: 24px;
            }
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            const nonce = '<?php echo wp_create_nonce( 'aurora_profile_nonce' ); ?>';

            // Toggle forgot password form
            $('#forgot-password-toggle').on('click', function() {
                $('#aurora-login-form').hide();
                $('#forgot-password-toggle').hide();
                $('#aurora-forgot-form').show();
            });

            $('#cancel-forgot').on('click', function() {
                $('#aurora-forgot-form').hide();
                $('#reset-otp-section').hide();
                $('#aurora-login-form').show();
                $('#forgot-password-toggle').show();
            });

            // Send reset OTP
            $('#send-reset-otp').on('click', function() {
                const email = $('#forgot-email').val();
                if (!email) {
                    showMessage('Please enter your email', 'error');
                    return;
                }

                $.ajax({
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    type: 'POST',
                    data: {
                        action: 'aurora_reset_password',
                        nonce: nonce,
                        email: email
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.data.message, 'success');
                            $('#reset-otp-section').show();
                            $('#send-reset-otp').prop('disabled', true);
                        } else {
                            showMessage(response.data.message, 'error');
                        }
                    }
                });
            });

            // Confirm password reset
            $('#confirm-reset-password').on('click', function() {
                const email = $('#forgot-email').val();
                const otp = $('#reset-otp').val();
                const password = $('#reset-new-password').val();

                if (!otp || otp.length !== 6) {
                    showMessage('Please enter a valid 6-digit OTP', 'error');
                    return;
                }

                if (!password || password.length < 8) {
                    showMessage('Password must be at least 8 characters', 'error');
                    return;
                }

                $.ajax({
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    type: 'POST',
                    data: {
                        action: 'aurora_confirm_password_reset',
                        nonce: nonce,
                        email: email,
                        otp: otp,
                        new_password: password
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Password reset successfully! Redirecting...', 'success');
                            setTimeout(() => window.location.href = '<?php echo wc_get_page_permalink( 'myaccount' ); ?>', 2000);
                        } else {
                            showMessage(response.data.message, 'error');
                        }
                    }
                });
            });

            $('#skip-reset').on('click', function() {
                $('#aurora-forgot-form').hide();
                $('#reset-otp-section').hide();
                $('#aurora-login-form').show();
                $('#forgot-password-toggle').show();
            });

            function showMessage(message, type) {
                const $message = $('<div class="message ' + type + '">' + message + '</div>');
                $('#aurora-login-form').before($message);
                if (type !== 'error') {
                    setTimeout(() => $message.fadeOut(() => $message.remove()), 3000);
                }
            }
        });
    </script>
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
            <a href="<?php echo esc_url( add_query_arg( 'action', 'bulk-add' ) ); ?>" class="button button-primary"><?php esc_html_e( '+ Bulk Add (Multiple)', 'aurora' ); ?></a>
        </div>

        <?php
        if ( 'add' === $action || 'edit' === $action ) {
            aurora_product_form( $product_id );
        } elseif ( 'bulk-add' === $action ) {
            aurora_bulk_product_form();
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

function aurora_bulk_product_form() {
    $submitted = false;
    $success_count = 0;
    $error_message = '';

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['aurora_bulk_product_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_POST['aurora_bulk_product_nonce'], 'aurora_bulk_product_form' ) ) {
            $error_message = __( 'Nonce verification failed.', 'aurora' );
        } else {
            $submitted = true;
            
            // Get all product entries from hidden form data
            $product_names = isset( $_POST['product_names'] ) ? (array) $_POST['product_names'] : array();
            $product_prices = isset( $_POST['product_prices'] ) ? (array) $_POST['product_prices'] : array();
            $product_stocks = isset( $_POST['product_stocks'] ) ? (array) $_POST['product_stocks'] : array();
            $product_skus = isset( $_POST['product_skus'] ) ? (array) $_POST['product_skus'] : array();
            $product_descs = isset( $_POST['product_descs'] ) ? (array) $_POST['product_descs'] : array();
            $products_categories = isset( $_POST['products_categories'] ) ? $_POST['products_categories'] : array();

            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );

            foreach ( $product_names as $index => $product_name ) {
                $product_name = sanitize_text_field( $product_name );
                
                if ( empty( $product_name ) ) {
                    continue;
                }

                $product_price = floatval( isset( $product_prices[ $index ] ) ? $product_prices[ $index ] : 0 );
                $product_stock = absint( isset( $product_stocks[ $index ] ) ? $product_stocks[ $index ] : 0 );
                $product_sku = sanitize_text_field( isset( $product_skus[ $index ] ) ? $product_skus[ $index ] : '' );
                $product_desc = wp_kses_post( isset( $product_descs[ $index ] ) ? $product_descs[ $index ] : '' );
                $category_ids = isset( $products_categories[ $index ] ) ? array_map( 'absint', (array) $products_categories[ $index ] ) : array();

                if ( $product_price <= 0 ) {
                    continue;
                }

                try {
                    $new_product = new WC_Product_Simple();
                    $new_product->set_name( $product_name );
                    $new_product->set_description( $product_desc );
                    $new_product->set_regular_price( $product_price );
                    $new_product->set_stock_quantity( $product_stock );
                    if ( ! empty( $product_sku ) ) {
                        $new_product->set_sku( $product_sku );
                    }
                    $new_product->set_category_ids( $category_ids );
                    $new_product->set_status( 'publish' );
                    $product_id = $new_product->save();

                    // Handle image upload for bulk products
                    if ( ! empty( $_FILES['product_images']['name'][ $index ] ) ) {
                        $_FILES['product_image'] = [
                            'name'     => $_FILES['product_images']['name'][ $index ],
                            'type'     => $_FILES['product_images']['type'][ $index ],
                            'tmp_name' => $_FILES['product_images']['tmp_name'][ $index ],
                            'error'    => $_FILES['product_images']['error'][ $index ],
                            'size'     => $_FILES['product_images']['size'][ $index ],
                        ];

                        $attachment_id = media_handle_upload( 'product_image', $product_id );
                        if ( ! is_wp_error( $attachment_id ) ) {
                            $new_product->set_image_id( $attachment_id );
                            $new_product->save();
                        }
                    }

                    $success_count++;
                } catch ( Exception $e ) {
                    error_log( 'Bulk product error: ' . $e->getMessage() );
                    continue;
                }
            }
        }
    }

    ?>
    <div class="bulk-product-form-container">
        <h2><?php esc_html_e( 'Bulk Add Products (Image Gallery)', 'aurora' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Upload images first, then click each image to add product details. Fill in Name and Price for each product.', 'aurora' ); ?></p>

        <?php if ( $error_message ) : ?>
            <div class="alert alert-danger"><?php echo esc_html( $error_message ); ?></div>
        <?php endif; ?>

        <?php if ( $submitted && $success_count > 0 ) : ?>
            <div class="alert alert-success">
                <?php printf( 
                    esc_html__( '✓ %d product(s) added successfully! <a href="%s">View All Products</a>', 'aurora' ), 
                    $success_count, 
                    esc_url( add_query_arg( 'action', 'list' ) ) 
                ); ?>
            </div>
        <?php endif; ?>

        <form method="post" id="bulk-product-form" class="bulk-product-form" enctype="multipart/form-data">
            <?php wp_nonce_field( 'aurora_bulk_product_form', 'aurora_bulk_product_nonce' ); ?>

            <!-- IMAGE UPLOAD SECTION -->
            <div class="image-upload-section">
                <h3><?php esc_html_e( 'Step 1: Upload Images', 'aurora' ); ?></h3>
                <p><?php esc_html_e( 'You can upload up to 10 images at once. Supported formats: JPG, PNG, GIF', 'aurora' ); ?></p>
                <div class="image-upload-input">
                    <input type="file" name="product_images[]" id="bulk-image-input" class="form-control" accept="image/*" multiple />
                    <small><?php esc_html_e( 'Select multiple images using Ctrl+Click or Shift+Click', 'aurora' ); ?></small>
                </div>
                <div id="image-preview-container" class="image-preview-container">
                    <!-- Thumbnails will appear here -->
                </div>
            </div>

            <!-- PRODUCT DETAILS SECTION -->
            <div class="product-details-section">
                <h3><?php esc_html_e( 'Step 2: Click Image & Add Details', 'aurora' ); ?></h3>
                <p><?php esc_html_e( 'Select an image above, then fill in the details below', 'aurora' ); ?></p>

                <div id="product-details-form" class="product-details-form">
                    <div class="no-image-selected">
                        <p><?php esc_html_e( '📷 Upload images above and click them to add details', 'aurora' ); ?></p>
                    </div>
                </div>
            </div>

            <!-- HIDDEN FORM DATA CONTAINER -->
            <div id="form-data-container" style="display:none;"></div>

            <div class="bulk-form-actions">
                <button type="submit" class="button button-primary" id="submit-bulk-products-btn"><?php esc_html_e( '✓ Add All Products', 'aurora' ); ?></button>
                <a href="<?php echo esc_url( add_query_arg( 'action', 'list' ) ); ?>" class="button"><?php esc_html_e( 'Cancel', 'aurora' ); ?></a>
            </div>
        </form>
    </div>

    <script>
    jQuery(function($) {
        let productData = {};
        let selectedImageIndex = -1;
        let categoriesData = <?php echo json_encode( aurora_get_categories_data() ); ?>;

        // Handle image selection
        $('#bulk-image-input').on('change', function() {
            productData = {};
            selectedImageIndex = -1;
            const files = this.files;

            // Limit to 10 images
            if (files.length > 10) {
                alert('<?php esc_attr_e( 'Maximum 10 images allowed', 'aurora' ); ?>');
                return;
            }

            // Initialize product data for each file
            for (let i = 0; i < files.length; i++) {
                productData[i] = {
                    file: files[i],
                    name: '',
                    price: '',
                    stock: '0',
                    sku: '',
                    description: '',
                    categories: []
                };
            }

            // Build thumbnails once and render (order-safe)
            let previewHTML = '<div class="thumbnails-row">';
            let loadedCount = 0;
            const totalFiles = files.length;
            const thumbBuffers = new Array(totalFiles).fill('');

            for (let index = 0; index < files.length; index++) {
                const file = files[index];
                if (!file.type.startsWith('image/')) {
                    loadedCount++;
                    continue;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    thumbBuffers[index] = '<div class="thumbnail-item" data-index="' + index + '">' +
                        '<img src="' + e.target.result + '" alt="Product ' + (index + 1) + '" />' +
                        '<span class="thumb-label">Pic ' + (index + 1) + '</span>' +
                        '</div>';

                    loadedCount++;
                    if (loadedCount === totalFiles) {
                        previewHTML += thumbBuffers.join('') + '</div>';
                        $('#image-preview-container').html(previewHTML);

                        // Bind click after thumbnails exist
                        $('.thumbnail-item').off('click').on('click', function() {
                            selectedImageIndex = parseInt($(this).data('index'));
                            $('.thumbnail-item').removeClass('active');
                            $(this).addClass('active');
                            renderProductForm(selectedImageIndex);
                        });
                    }
                };
                reader.readAsDataURL(file);
            }

            $('#submit-bulk-products-btn').prop('disabled', false);
        });

        // Render product details form
        function renderProductForm(index) {
            if (!productData[index]) return;

            let categoryOptions = '';
            if (productData[index].categories && productData[index].categories.length) {
                // Categories already selected - keep them
                categoryOptions = '<select name="products_categories[' + index + '][]" class="form-control category-select" multiple size="4">' +
                    categoriesData +
                    '</select>';
            } else {
                categoryOptions = '<select name="products_categories[' + index + '][]" class="form-control category-select" multiple size="4">' +
                    categoriesData +
                    '</select>';
            }

            let currentData = productData[index];
            let formHTML = '<div class="product-form-wrapper">' +
                '<div class="form-image-display">' +
                '<div style="width: 180px; height: 180px; background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">' +
                '<img id="current-thumb-' + index + '" src="" alt="Product" style="max-width: 100%; max-height: 100%; object-fit: contain;" />' +
                '</div>' +
                '</div>' +
                '<div class="form-inputs">' +
                '<div class="form-group">' +
                '<label>Product Name *</label>' +
                '<input type="text" name="product_names[]" class="form-control product-name-input" value="' + esc_attr(currentData.name) + '" required />' +
                '</div>' +

                '<div class="form-row">' +
                '<div class="form-group">' +
                '<label>Price *</label>' +
                '<input type="number" name="product_prices[]" class="form-control product-price-input" step="0.01" value="' + esc_attr(currentData.price) + '" required />' +
                '</div>' +
                '<div class="form-group">' +
                '<label>Stock</label>' +
                '<input type="number" name="product_stocks[]" class="form-control product-stock-input" value="' + esc_attr(currentData.stock) + '" />' +
                '</div>' +
                '<div class="form-group">' +
                '<label>SKU</label>' +
                '<input type="text" name="product_skus[]" class="form-control product-sku-input" value="' + esc_attr(currentData.sku) + '" />' +
                '</div>' +
                '</div>' +

                '<div class="form-group">' +
                '<label>Categories</label>' +
                categoryOptions +
                '<small>Hold Ctrl (Cmd on Mac) for multiple</small>' +
                '</div>' +

                '<div class="form-group">' +
                '<label>Description</label>' +
                '<textarea name="product_descs[]" class="form-control product-desc-input" rows="4">' + esc_attr(currentData.description) + '</textarea>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('#product-details-form').html(formHTML);

            // Display image
            if (productData[index].file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#current-thumb-' + index).attr('src', e.target.result);
                };
                reader.readAsDataURL(productData[index].file);
            }

            // Bind input changes to save data
            $(document).on('input change', '.product-name-input, .product-price-input, .product-stock-input, .product-sku-input, .product-desc-input, .category-select', function() {
                if (selectedImageIndex >= 0) {
                    productData[selectedImageIndex].name = $('.product-name-input').val();
                    productData[selectedImageIndex].price = $('.product-price-input').val();
                    productData[selectedImageIndex].stock = $('.product-stock-input').val();
                    productData[selectedImageIndex].sku = $('.product-sku-input').val();
                    productData[selectedImageIndex].description = $('.product-desc-input').val();
                    productData[selectedImageIndex].categories = $('.category-select').val();

                    // Auto-generate SKU if empty
                    if (!productData[selectedImageIndex].sku && productData[selectedImageIndex].name) {
                        let autoSku = productData[selectedImageIndex].name.toUpperCase().replace(/\s+/g, '-').substring(0, 20);
                        $('.product-sku-input').val(autoSku);
                        productData[selectedImageIndex].sku = autoSku;
                    }
                }
            });
        }

        // Handle form submission
        $('#bulk-product-form').on('submit', function(e) {
            // Clear previous hidden inputs
            $('#form-data-container').html('');

            // Create hidden inputs from productData
            let hasValidData = false;
            for (let index in productData) {
                let data = productData[index];
                if (data.name && data.price) {
                    hasValidData = true;
                    $('#form-data-container').append(
                        '<input type="hidden" name="product_names[]" value="' + esc_attr(data.name) + '" />' +
                        '<input type="hidden" name="product_prices[]" value="' + esc_attr(data.price) + '" />' +
                        '<input type="hidden" name="product_stocks[]" value="' + esc_attr(data.stock) + '" />' +
                        '<input type="hidden" name="product_skus[]" value="' + esc_attr(data.sku) + '" />' +
                        '<input type="hidden" name="product_descs[]" value="' + esc_attr(data.description) + '" />'
                    );

                    // Add categories
                    if (data.categories && data.categories.length) {
                        $.each(data.categories, function(i, catId) {
                            $('#form-data-container').append(
                                '<input type="hidden" name="products_categories[' + index + '][]" value="' + esc_attr(catId) + '" />'
                            );
                        });
                    }
                }
            }

            if (!hasValidData) {
                e.preventDefault();
                alert('<?php esc_attr_e( 'Please add at least one product with Name and Price', 'aurora' ); ?>');
            }
        });

        // Disable submit button initially
        $('#submit-bulk-products-btn').prop('disabled', true);
    });

    function esc_attr(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    </script>

    <?php
}

function aurora_get_categories_data() {
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ) );

    $html = '';
    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
        foreach ( $categories as $category ) {
            $category_color = get_term_meta( $category->term_id, 'aurora_category_color', true );
            $category_color = $category_color ? $category_color : '#0b57d0';
            $html .= sprintf(
                '<option value="%d" data-color="%s">%s</option>',
                $category->term_id,
                esc_attr( $category_color ),
                esc_html( $category->name )
            );
        }
    }
    return $html;
}
// ===== Order Management =====

add_shortcode( 'aurora_order_manager', 'aurora_order_manager_shortcode' );
function aurora_order_manager_shortcode() {
    if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
        return '<p class="alert alert-danger">' . esc_html__( 'You do not have permission to access this page.', 'aurora' ) . '</p>';
    }

    ob_start();
    ?>
    <div class="aurora-order-manager">
        <div class="order-manager-header">
            <h1><?php esc_html_e( 'Order Management', 'aurora' ); ?></h1>
            <div class="order-stats">
                <?php
                $processing = wc_orders_count( 'processing' );
                $pending = wc_orders_count( 'pending' );
                $packed = wc_orders_count( 'packed' );
                $shipped = wc_orders_count( 'shipped' );
                ?>
                <div class="stat-card">
                    <span class="stat-number"><?php echo esc_html( $pending ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Pending', 'aurora' ); ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo esc_html( $processing ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Processing', 'aurora' ); ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo esc_html( $packed ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Packed', 'aurora' ); ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo esc_html( $shipped ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Shipped', 'aurora' ); ?></span>
                </div>
            </div>
        </div>

        <div class="order-filters">
            <select id="order-status-filter" class="filter-select">
                <option value="all"><?php esc_html_e( 'Active Orders', 'aurora' ); ?></option>
                <option value="pending"><?php esc_html_e( 'Pending Payment', 'aurora' ); ?></option>
                <option value="processing"><?php esc_html_e( 'Processing', 'aurora' ); ?></option>
                <option value="packed"><?php esc_html_e( 'Packed', 'aurora' ); ?></option>
                <option value="shipped"><?php esc_html_e( 'Shipped', 'aurora' ); ?></option>
            </select>
            <input type="text" id="order-search" class="search-input" placeholder="<?php esc_attr_e( 'Search by Order ID or Customer Name...', 'aurora' ); ?>" />
        </div>

        <div class="orders-grid">
            <?php
            $args = array(
                'limit' => -1,
                'orderby' => 'date',
                'order' => 'DESC',
                'status' => array( 'pending', 'processing', 'on-hold', 'packed', 'shipped' ),
            );
            $orders = wc_get_orders( $args );

            if ( ! empty( $orders ) ) {
                foreach ( $orders as $order ) {
                    aurora_render_order_card( $order );
                }
            } else {
                echo '<p class="no-orders">' . esc_html__( 'No orders found.', 'aurora' ) . '</p>';
            }
            ?>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Status filter
        $('#order-status-filter').on('change', function() {
            var status = $(this).val();
            if (status === 'all') {
                $('.order-card').show();
            } else {
                $('.order-card').hide();
                $('.order-card[data-status="' + status + '"]').show();
            }
        });

        // Search filter
        $('#order-search').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.order-card').each(function() {
                var orderText = $(this).text().toLowerCase();
                if (orderText.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Status update
        $(document).on('change', '.order-status-select', function() {
            var $select = $(this);
            var orderId = $select.data('order-id');
            var newStatus = $select.val();
            var $card = $select.closest('.order-card');

            $.ajax({
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                type: 'POST',
                data: {
                    action: 'aurora_update_order_status',
                    order_id: orderId,
                    status: newStatus,
                    nonce: '<?php echo wp_create_nonce( 'aurora_order_status' ); ?>'
                },
                beforeSend: function() {
                    $select.prop('disabled', true);
                    $card.addClass('updating');
                },
                success: function(response) {
                    if (response.success) {
                        $card.attr('data-status', newStatus);
                        $card.find('.status-badge').remove();
                        $card.find('.order-header').append(
                            '<span class="status-badge status-' + newStatus + '">' + 
                            response.data.status_label + 
                            '</span>'
                        );
                        
                        // Show success message
                        var $message = $('<div class="status-update-message">Status updated!</div>');
                        $card.append($message);
                        setTimeout(function() {
                            $message.fadeOut(function() { $(this).remove(); });
                        }, 2000);
                    } else {
                        alert('Error updating order status');
                    }
                },
                complete: function() {
                    $select.prop('disabled', false);
                    $card.removeClass('updating');
                }
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

function aurora_render_order_card( $order ) {
    $order_id = $order->get_id();
    $order_status = $order->get_status();
    $order_date = $order->get_date_created()->format( 'M d, Y H:i' );
    $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    $customer_email = $order->get_billing_email();
    $order_total = $order->get_formatted_order_total();
    $items = $order->get_items();
    ?>
    <div class="order-card" data-status="<?php echo esc_attr( $order_status ); ?>">
        <div class="order-header">
            <div class="order-id">
                <strong>#<?php echo esc_html( $order_id ); ?></strong>
            </div>
            <span class="status-badge status-<?php echo esc_attr( $order_status ); ?>">
                <?php echo esc_html( wc_get_order_status_name( $order_status ) ); ?>
            </span>
        </div>

        <div class="order-customer">
            <div class="customer-icon">👤</div>
            <div class="customer-info">
                <strong><?php echo esc_html( $customer_name ); ?></strong>
                <span class="customer-email"><?php echo esc_html( $customer_email ); ?></span>
            </div>
        </div>

        <div class="order-date">
            <span class="date-icon">📅</span>
            <?php echo esc_html( $order_date ); ?>
        </div>

        <div class="order-items">
            <strong><?php esc_html_e( 'Products:', 'aurora' ); ?></strong>
            <ul class="product-list">
                <?php foreach ( $items as $item ) : 
                    $product = $item->get_product();
                    $product_name = $item->get_name();
                    $quantity = $item->get_quantity();
                ?>
                <li>
                    <?php if ( $product && $product->get_image_id() ) : ?>
                        <img src="<?php echo esc_url( wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) ); ?>" alt="" class="product-thumb" />
                    <?php endif; ?>
                    <span class="product-name"><?php echo esc_html( $product_name ); ?></span>
                    <span class="product-qty">× <?php echo esc_html( $quantity ); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="order-total">
            <strong><?php esc_html_e( 'Total:', 'aurora' ); ?></strong>
            <span class="total-amount"><?php echo wp_kses_post( $order_total ); ?></span>
        </div>

        <div class="order-actions">
            <select class="order-status-select" data-order-id="<?php echo esc_attr( $order_id ); ?>">
                <option value="pending" <?php selected( $order_status, 'pending' ); ?>><?php esc_html_e( 'Pending Payment', 'aurora' ); ?></option>
                <option value="processing" <?php selected( $order_status, 'processing' ); ?>><?php esc_html_e( 'Processing', 'aurora' ); ?></option>
                <option value="packed" <?php selected( $order_status, 'packed' ); ?>><?php esc_html_e( 'Packed', 'aurora' ); ?></option>
                <option value="shipped" <?php selected( $order_status, 'shipped' ); ?>><?php esc_html_e( 'Shipped', 'aurora' ); ?></option>
                <option value="completed" <?php selected( $order_status, 'completed' ); ?>><?php esc_html_e( 'Completed', 'aurora' ); ?></option>
                <option value="cancelled" <?php selected( $order_status, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'aurora' ); ?></option>
            </select>
            <a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>" class="button button-secondary" target="_blank">
                <?php esc_html_e( 'View Details', 'aurora' ); ?>
            </a>
        </div>
    </div>
    <?php
}

// AJAX handler for order status update
add_action( 'wp_ajax_aurora_update_order_status', 'aurora_update_order_status_ajax' );
function aurora_update_order_status_ajax() {
    check_ajax_referer( 'aurora_order_status', 'nonce' );

    if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Permission denied' ) );
    }

    $order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
    $new_status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';

    if ( ! $order_id || ! $new_status ) {
        wp_send_json_error( array( 'message' => 'Invalid data' ) );
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( array( 'message' => 'Order not found' ) );
    }

    $order->update_status( $new_status );
    
    // Add order note
    $order->add_order_note( sprintf( 
        __( 'Order status changed to %s by admin.', 'aurora' ), 
        wc_get_order_status_name( $new_status ) 
    ) );

    wp_send_json_success( array(
        'status_label' => wc_get_order_status_name( $new_status ),
        'message' => 'Status updated successfully'
    ) );
}

// Register custom order statuses
add_action( 'init', 'aurora_register_custom_order_statuses' );
function aurora_register_custom_order_statuses() {
    register_post_status( 'wc-packed', array(
        'label'                     => _x( 'Packed', 'Order status', 'aurora' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Packed <span class="count">(%s)</span>', 'Packed <span class="count">(%s)</span>', 'aurora' )
    ) );

    register_post_status( 'wc-shipped', array(
        'label'                     => _x( 'Shipped', 'Order status', 'aurora' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>', 'aurora' )
    ) );
}

// Add custom statuses to WooCommerce
add_filter( 'wc_order_statuses', 'aurora_add_custom_order_statuses' );
function aurora_add_custom_order_statuses( $order_statuses ) {
    $new_order_statuses = array();

    foreach ( $order_statuses as $key => $status ) {
        $new_order_statuses[ $key ] = $status;
        
        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-packed'] = _x( 'Packed', 'Order status', 'aurora' );
            $new_order_statuses['wc-shipped'] = _x( 'Shipped', 'Order status', 'aurora' );
        }
    }

    return $new_order_statuses;
}

// Allow customers to cancel orders before shipped
add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'aurora_valid_order_statuses_for_cancel', 10, 2 );
function aurora_valid_order_statuses_for_cancel( $statuses, $order ) {
    // Allow cancellation for pending, processing, on-hold, and packed orders
    // But NOT for shipped, completed, or cancelled orders
    return array( 'pending', 'processing', 'on-hold', 'packed' );
}

// Add cancel button to My Account orders page
add_filter( 'woocommerce_my_account_my_orders_actions', 'aurora_add_cancel_order_action', 10, 2 );
function aurora_add_cancel_order_action( $actions, $order ) {
    $order_status = $order->get_status();
    
    // Allow cancellation if order is not shipped, completed, or already cancelled
    if ( in_array( $order_status, array( 'pending', 'processing', 'on-hold', 'packed' ) ) ) {
        $actions['cancel'] = array(
            'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
            'name' => __( 'Cancel Order', 'aurora' ),
        );
    }
    
    return $actions;
}

// AJAX handler for customer order cancellation
add_action( 'wp_ajax_aurora_cancel_customer_order', 'aurora_cancel_customer_order_ajax' );
function aurora_cancel_customer_order_ajax() {
    check_ajax_referer( 'aurora_cancel_order', 'nonce' );
    
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'You must be logged in to cancel orders.' ) );
    }
    
    $order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
    
    if ( ! $order_id ) {
        wp_send_json_error( array( 'message' => 'Invalid order ID.' ) );
    }
    
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( array( 'message' => 'Order not found.' ) );
    }
    
    // Check if user owns the order
    if ( $order->get_customer_id() != get_current_user_id() ) {
        wp_send_json_error( array( 'message' => 'You do not have permission to cancel this order.' ) );
    }
    
    // Check if order can be cancelled
    $order_status = $order->get_status();
    if ( ! in_array( $order_status, array( 'pending', 'processing', 'on-hold', 'packed' ) ) ) {
        wp_send_json_error( array( 'message' => 'This order cannot be cancelled. It may have already been shipped.' ) );
    }
    
    // Cancel the order
    $order->update_status( 'cancelled', __( 'Order cancelled by customer.', 'aurora' ) );
    
    wp_send_json_success( array(
        'message' => 'Order cancelled successfully.'
    ) );
}