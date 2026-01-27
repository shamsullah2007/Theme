<?php
/**
 * Template Name: Forgot Password
 * Description: Password reset with OTP verification
 */
get_header();
?>

<body data-page="forgot-password">
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
        <h1><?php esc_html_e( 'Reset Password', 'aurora' ); ?></h1>
        
        <!-- Messages -->
        <div id="forgot-messages" style="margin-bottom: 20px;"></div>

        <!-- Step 1: Email Input -->
        <form id="aurora-forgot-email-form" class="step" data-step="1" method="POST" style="display: block;">
            <div style="margin-bottom: 15px;">
                <label for="forgot-email"><?php esc_html_e( 'Email', 'aurora' ); ?></label>
                <input type="email" id="forgot-email" name="email" class="form-control" required>
            </div>
            <button type="button" id="btn-forgot-send" class="button button-primary" style="width: 100%; padding: 12px;">
                <?php esc_html_e( 'Send Code', 'aurora' ); ?>
            </button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="<?php echo esc_url( wp_login_url() ); ?>" class="button" style="background: none; color: #0073aa; text-decoration: none;">
                    <?php esc_html_e( 'Back to Login', 'aurora' ); ?>
                </a>
            </div>
            <?php wp_nonce_field( 'aurora_forgot_nonce' ); ?>
        </form>

        <!-- Step 2: OTP Verification -->
        <form id="aurora-forgot-otp-form" class="step" data-step="2" method="POST" style="display: none;">
            <div style="margin-bottom: 15px;">
                <label for="forgot-otp"><?php esc_html_e( 'Verification Code', 'aurora' ); ?></label>
                <input type="text" id="forgot-otp" name="otp" class="form-control" placeholder="000000" maxlength="6" required>
            </div>
            <button type="button" id="btn-forgot-verify" class="button button-primary" style="width: 100%; padding: 12px;">
                <?php esc_html_e( 'Verify Code', 'aurora' ); ?>
            </button>
            <div style="text-align: center; margin-top: 15px;">
                <button type="button" id="btn-forgot-resend" class="button" style="background: none; color: #0073aa; text-decoration: none;" disabled>
                    <?php esc_html_e( 'Resend in 60s', 'aurora' ); ?>
                </button>
            </div>
            <button type="button" id="btn-back-forgot-email" class="button" style="width: 100%; padding: 12px; margin-top: 10px;">
                <?php esc_html_e( 'Back', 'aurora' ); ?>
            </button>
            <?php wp_nonce_field( 'aurora_forgot_otp_nonce' ); ?>
        </form>

        <!-- Step 3: New Password -->
        <form id="aurora-new-password-form" class="step" data-step="3" method="POST" style="display: none;">
            <div style="margin-bottom: 15px;">
                <label for="new-password"><?php esc_html_e( 'New Password', 'aurora' ); ?></label>
                <input type="password" id="new-password" name="password" class="form-control" minlength="8" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label for="confirm-password"><?php esc_html_e( 'Confirm Password', 'aurora' ); ?></label>
                <input type="password" id="confirm-password" name="confirm_password" class="form-control" minlength="8" required>
            </div>
            <button type="button" id="btn-reset-password" class="button button-primary" style="width: 100%; padding: 12px;">
                <?php esc_html_e( 'Reset Password', 'aurora' ); ?>
            </button>
            <?php wp_nonce_field( 'aurora_reset_password_nonce' ); ?>
        </form>

        <div id="forgot-success-message" style="display: none; margin-top: 20px;">
            <div style="padding: 15px; border: 1px solid #c5e6c5; background: #f3fff3; border-radius: 4px; color: #1f6b1f;">
                <?php esc_html_e( 'Password reset successfully. You can sign in with your new password.', 'aurora' ); ?>
            </div>
            <div style="text-align: center; margin-top: 12px;">
                <a href="<?php echo esc_url( wp_login_url() ); ?>" class="button button-primary" style="padding: 10px 16px;">
                    <?php esc_html_e( 'Go to Sign In', 'aurora' ); ?>
                </a>
            </div>
        </div>

        <p style="text-align: center; margin-top: 20px;">
            <a href="<?php echo esc_url( wp_login_url() ); ?>">
                <?php esc_html_e( 'Back to Sign In', 'aurora' ); ?>
            </a>
        </p>
    </div>
</main>

<?php get_footer(); ?>
