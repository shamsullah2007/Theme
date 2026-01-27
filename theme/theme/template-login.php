<?php
/**
 * Template Name: Login Page
 * Description: Custom login page with password and OTP options
 */
get_header();
?>

<body data-page="login">
<main id="primary" class="site-main">
    <style>
        .entry-content label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }
        .entry-content .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            font-family: inherit;
        }
        .entry-content .form-control:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.1);
        }
        .entry-content .button {
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f5f5f5;
            color: #333;
            font-size: 16px;
        }
        .entry-content .button:hover {
            background: #ebebeb;
        }
        .entry-content .button-primary {
            background: #0073aa;
            color: white;
            border-color: #0073aa;
        }
        .entry-content .button-primary:hover {
            background: #005a87;
        }
    </style>
    <div class="entry-content" style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <h1><?php esc_html_e( 'Sign In', 'aurora' ); ?></h1>
        
        <!-- Messages -->
        <div id="login-messages" style="margin-bottom: 20px;"></div>

        <!-- Login Tabs -->
        <div style="border-bottom: 2px solid #ddd; margin-bottom: 30px; display: flex; gap: 20px;">
            <button type="button" class="tab-button active" data-tab="password-login" style="padding: 10px 0; border: none; background: none; cursor: pointer; font-weight: 500;">
                <?php esc_html_e( 'Email & Password', 'aurora' ); ?>
            </button>
            <button type="button" class="tab-button" data-tab="otp-login" style="padding: 10px 0; border: none; background: none; cursor: pointer; font-weight: 500;">
                <?php esc_html_e( 'Verification Code', 'aurora' ); ?>
            </button>
        </div>

        <!-- Password Login Form -->
        <form id="aurora-password-login-form" class="tab-content active" data-tab="password-login" method="POST">
            <div style="margin-bottom: 15px;">
                <label for="login-email"><?php esc_html_e( 'Email', 'aurora' ); ?></label>
                <input type="email" id="login-email" name="email" class="form-control" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label for="login-password"><?php esc_html_e( 'Password', 'aurora' ); ?></label>
                <input type="password" id="login-password" name="password" class="form-control" required>
            </div>
            <button type="button" id="btn-login-password" class="button button-primary" style="width: 100%; padding: 12px;">
                <?php esc_html_e( 'Sign In', 'aurora' ); ?>
            </button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="<?php echo esc_url( add_query_arg( 'action', 'lostpassword' ) ); ?>" class="button" style="background: none; color: #0073aa; text-decoration: none;">
                    <?php esc_html_e( 'Forgot Password?', 'aurora' ); ?>
                </a>
            </div>
            <?php wp_nonce_field( 'aurora_login_nonce' ); ?>
        </form>

        <!-- OTP Login Form -->
        <form id="aurora-otp-login-form" class="tab-content" data-tab="otp-login" method="POST" style="display: none;">
            <div style="margin-bottom: 15px;">
                <label for="otp-login-email"><?php esc_html_e( 'Email', 'aurora' ); ?></label>
                <input type="email" id="otp-login-email" name="email" class="form-control" required>
            </div>
            <button type="button" id="btn-request-login-otp" class="button button-primary" style="width: 100%; padding: 12px;">
                <?php esc_html_e( 'Send Code', 'aurora' ); ?>
            </button>

            <!-- OTP Input Section -->
            <div id="otp-login-section" style="display: none; margin-top: 20px;">
                <div style="margin-bottom: 15px;">
                    <label for="otp-login-code"><?php esc_html_e( 'Verification Code', 'aurora' ); ?></label>
                    <input type="text" id="otp-login-code" name="otp" class="form-control" placeholder="000000" maxlength="6" required>
                </div>
                <button type="button" id="btn-verify-login-otp" class="button button-primary" style="width: 100%; padding: 12px;">
                    <?php esc_html_e( 'Verify & Continue', 'aurora' ); ?>
                </button>
                <div style="text-align: center; margin-top: 15px;">
                    <button type="button" id="btn-resend-login-otp" class="button" style="background: none; color: #0073aa; text-decoration: none;" disabled>
                        <?php esc_html_e( 'Resend in 60s', 'aurora' ); ?>
                    </button>
                </div>
            </div>

            <!-- Password Reset or Skip Section -->
            <div id="otp-password-reset-section" style="display: none; margin-top: 20px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                <p style="margin-top: 0; color: #555;"><?php esc_html_e( 'Your identity is verified. Would you like to set a new password?', 'aurora' ); ?></p>
                
                <!-- Password Reset Form -->
                <div id="password-reset-form" style="display: none;">
                    <div style="margin-bottom: 15px;">
                        <label for="reset-new-password"><?php esc_html_e( 'New Password', 'aurora' ); ?></label>
                        <input type="password" id="reset-new-password" name="new_password" class="form-control" minlength="8" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label for="reset-confirm-password"><?php esc_html_e( 'Confirm Password', 'aurora' ); ?></label>
                        <input type="password" id="reset-confirm-password" name="confirm_password" class="form-control" minlength="8" required>
                    </div>
                    <button type="button" id="btn-reset-password-and-login" class="button button-primary" style="width: 100%; padding: 12px;">
                        <?php esc_html_e( 'Set Password & Sign In', 'aurora' ); ?>
                    </button>
                    <button type="button" id="btn-toggle-password-form" class="button" style="width: 100%; padding: 12px; margin-top: 10px;">
                        <?php esc_html_e( 'Skip for now', 'aurora' ); ?>
                    </button>
                </div>

                <!-- Buttons to Toggle Password Reset -->
                <div id="password-reset-buttons" style="display: block;">
                    <button type="button" id="btn-reset-password-prompt" class="button button-primary" style="width: 100%; padding: 12px; margin-bottom: 10px;">
                        <?php esc_html_e( 'Set New Password', 'aurora' ); ?>
                    </button>
                    <button type="button" id="btn-skip-password-reset" class="button" style="width: 100%; padding: 12px;">
                        <?php esc_html_e( 'Skip & Sign In Now', 'aurora' ); ?>
                    </button>
                </div>
            </div>
            <?php wp_nonce_field( 'aurora_login_otp_nonce' ); ?>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <?php esc_html_e( 'No account?', 'aurora' ); ?>
            <a href="<?php echo esc_url( get_page_link( get_page_by_path( 'registration' ) ) ?: add_query_arg( 'action', 'register' ) ); ?>">
                <?php esc_html_e( 'Create one', 'aurora' ); ?>
            </a>
        </p>
    </div>
</main>

<?php get_footer(); ?>
