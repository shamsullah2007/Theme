<?php
/**
 * Template Name: Registration with OTP
 * Description: Custom registration page with email verification
 */
get_header();
?>

<body data-page="registration">
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
        <h1><?php esc_html_e( 'Create Account', 'aurora' ); ?></h1>
        
        <!-- Messages -->
        <div id="reg-messages" style="margin-bottom: 20px;"></div>

        <!-- Registration Form (Step 1) -->
        <form id="aurora-registration-form" method="POST">
            <input type="hidden" id="reg-otp-token" name="reg_token" value="">
            <div style="display: block;" data-step="registration-form">
                <div style="margin-bottom: 15px;">
                    <label for="reg-first-name"><?php esc_html_e( 'First Name', 'aurora' ); ?></label>
                    <input type="text" id="reg-first-name" name="first_name" class="form-control" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="reg-last-name"><?php esc_html_e( 'Last Name', 'aurora' ); ?></label>
                    <input type="text" id="reg-last-name" name="last_name" class="form-control" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="reg-email"><?php esc_html_e( 'Email', 'aurora' ); ?></label>
                    <input type="email" id="reg-email" name="email" class="form-control" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="reg-username"><?php esc_html_e( 'Username (Optional)', 'aurora' ); ?></label>
                    <input type="text" id="reg-username" name="username" class="form-control">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="reg-password"><?php esc_html_e( 'Password', 'aurora' ); ?></label>
                    <input type="password" id="reg-password" name="password" class="form-control" minlength="8" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="reg-password-confirm"><?php esc_html_e( 'Confirm Password', 'aurora' ); ?></label>
                    <input type="password" id="reg-password-confirm" name="password_confirm" class="form-control" minlength="8" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label><input type="checkbox" id="reg-agree-terms" name="agree_terms" required> <?php esc_html_e( 'I agree to Terms', 'aurora' ); ?></label>
                </div>
                <button type="button" id="btn-request-reg-otp" class="button button-primary" style="width: 100%; padding: 14px; font-size: 18px; font-weight: 600;">
                    <?php esc_html_e( 'Register', 'aurora' ); ?>
                </button>
                <p style="text-align: center; color: #666; font-size: 14px; margin-top: 10px;">
                    <?php esc_html_e( 'We will send a verification code to your email', 'aurora' ); ?>
                </p>
            </div>

            <!-- Step 2: OTP -->
            <div style="display: none;" data-step="registration-otp">
                <h2><?php esc_html_e( 'Verify Email', 'aurora' ); ?></h2>
                <p><?php esc_html_e( 'Enter the 6-digit code sent to your email. If you do not see it, check spam.', 'aurora' ); ?></p>
                <div style="margin-bottom: 15px;">
                    <label for="reg-otp-input"><?php esc_html_e( 'Verification Code', 'aurora' ); ?></label>
                    <input type="text" id="reg-otp-input" name="otp" class="form-control" placeholder="000000" maxlength="6" required>
                </div>
                <button type="button" id="btn-complete-registration" class="button button-primary" style="width: 100%; padding: 12px;">
                    <?php esc_html_e( 'Create Account', 'aurora' ); ?>
                </button>
                <div style="margin-top: 15px; text-align: center;">
                    <button type="button" id="btn-resend-reg-otp" class="button button-secondary" disabled>
                        <?php esc_html_e( 'Resend in 60s', 'aurora' ); ?>
                    </button>
                    <div style="font-size: 13px; color: #666; margin-top: 6px;">
                        <?php esc_html_e( 'You can request up to 10 codes per day.', 'aurora' ); ?>
                    </div>
                </div>
                <button type="button" id="btn-back-to-registration" class="button" style="width: 100%; padding: 12px; margin-top: 10px;">
                    <?php esc_html_e( 'Back', 'aurora' ); ?>
                </button>
            </div>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <?php esc_html_e( 'Already have account?', 'aurora' ); ?>
            <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Sign In', 'aurora' ); ?></a>
        </p>
    </div>
</main>

<?php get_footer(); ?>
