<?php
/**
 * Authentication, OTP, and account management backend.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Centralized OTP settings.
 */
function aurora_get_otp_settings() {
    $defaults = [
        'length'                   => 6,
        'expires_in'               => 10 * MINUTE_IN_SECONDS,
        'max_attempts_per_day'     => 10,
        'resend_cooldown'          => 60,
        'pending_registration_ttl' => 15 * MINUTE_IN_SECONDS,
        'pending_change_ttl'       => 15 * MINUTE_IN_SECONDS,
    ];

    return apply_filters( 'aurora_otp_settings', $defaults );
}

/**
 * Direct SMTP email sending (like Python script)
 */
function aurora_send_email_direct( $to, $subject, $body_html ) {
    require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
    require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
    require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer( true );

    try {
        // SMTP Configuration (like Python script)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shamsullahd9999@gmail.com';
        $mail->Password   = 'zipp fwkq oyeo atnh';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Sender and recipient
        $mail->setFrom( 'shamsullahd9999@gmail.com', get_bloginfo( 'name' ) );
        $mail->addAddress( $to );

        // Email content
        $mail->isHTML( true );
        $mail->Subject = $subject;
        $mail->Body    = $body_html;
        $mail->AltBody = strip_tags( $body_html );

        // Send email
        $result = $mail->send();
        
        if ( $result ) {
            error_log( 'Aurora Email Sent Successfully to: ' . $to );
        }
        
        return $result;

    } catch ( Exception $e ) {
        error_log( 'Aurora Email FAILED to: ' . $to . ' | Error: ' . $mail->ErrorInfo );
        return false;
    }
}

/**
 * Configure SMTP settings for wp_mail (backup method)
 */
add_action( 'phpmailer_init', 'aurora_configure_smtp' );
function aurora_configure_smtp( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->Username   = 'shamsullahd9999@gmail.com';
    $phpmailer->Password   = 'zipp fwkq oyeo atnh';
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->From       = 'shamsullahd9999@gmail.com';
    $phpmailer->FromName   = get_bloginfo( 'name' );
    $phpmailer->CharSet    = 'UTF-8';
}

/**
 * Reliable email sending with direct SMTP
 */
function aurora_send_email( $to, $subject, $body, $headers = [] ) {
    // Use direct SMTP sending (like Python script)
    return aurora_send_email_direct( $to, $subject, $body );
}

/**
 * Helper to check if string exists in array
 */
function in_array_any( $haystack, $needle ) {
    if ( ! is_array( $haystack ) ) {
        return false;
    }
    foreach ( $haystack as $item ) {
        if ( strpos( $item, $needle ) !== false ) {
            return true;
        }
    }
    return false;
}

/**
 * Create OTP storage tables.
 */
add_action( 'after_setup_theme', 'aurora_create_auth_tables' );
function aurora_create_auth_tables() {
    global $wpdb;

    $otp_table     = $wpdb->prefix . 'aurora_otps';
    $attempt_table = $wpdb->prefix . 'aurora_otp_attempts';
    $charset       = $wpdb->get_charset_collate();

    $otp_sql = "CREATE TABLE $otp_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL DEFAULT 0,
        otp_code varchar(12) NOT NULL,
        email varchar(150) NOT NULL,
        action_type varchar(50) NOT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        expires_at datetime NOT NULL,
        is_used tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id),
        KEY email (email),
        KEY user_action (user_id, action_type)
    ) $charset;";

    $attempt_sql = "CREATE TABLE $attempt_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL DEFAULT 0,
        email varchar(150) NOT NULL,
        attempt_date date NOT NULL,
        attempt_count int(11) NOT NULL DEFAULT 1,
        PRIMARY KEY  (id),
        KEY email (email),
        KEY attempt_date (attempt_date)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $otp_sql );
    dbDelta( $attempt_sql );
}

/**
 * Check both authenticated and public nonces for AJAX calls.
 */
function aurora_verify_ajax_nonce() {
    $pairs = [
        'nonce'      => 'aurora_profile_nonce',
        'auth_nonce' => 'aurora_auth_nonce',
    ];

    foreach ( $pairs as $field => $action ) {
        if ( isset( $_POST[ $field ] ) && wp_verify_nonce( $_POST[ $field ], $action ) ) {
            return;
        }
    }

    wp_send_json_error( [ 'message' => __( 'Security check failed.', 'aurora' ) ], 403 );
}

function aurora_generate_secure_otp( $length ) {
    $length = max( 4, min( 10, absint( $length ) ) );
    $min    = (int) pow( 10, $length - 1 );
    $max    = (int) pow( 10, $length ) - 1;

    return (string) random_int( $min, $max );
}

function aurora_get_daily_attempts( $email ) {
    global $wpdb;

    $attempt_table = $wpdb->prefix . 'aurora_otp_attempts';
    $today         = gmdate( 'Y-m-d' );

    return (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT attempt_count FROM $attempt_table WHERE email = %s AND attempt_date = %s",
        $email,
        $today
    ) );
}

function aurora_increment_attempts( $email ) {
    global $wpdb;

    $attempt_table = $wpdb->prefix . 'aurora_otp_attempts';
    $today         = gmdate( 'Y-m-d' );
    $existing      = aurora_get_daily_attempts( $email );

    if ( $existing ) {
        $wpdb->update(
            $attempt_table,
            [ 'attempt_count' => $existing + 1 ],
            [ 'email' => $email, 'attempt_date' => $today ],
            [ '%d' ],
            [ '%s', '%s' ]
        );
    } else {
        $wpdb->insert(
            $attempt_table,
            [
                'email'         => $email,
                'attempt_date'  => $today,
                'attempt_count' => 1,
            ],
            [ '%s', '%s', '%d' ]
        );
    }
}

function aurora_issue_otp( $user_id, $email, $action_type ) {
    global $wpdb;

    $settings   = aurora_get_otp_settings();
    $otp_table  = $wpdb->prefix . 'aurora_otps';
    $email      = sanitize_email( $email );
    $action_key = sanitize_key( $action_type );

    if ( ! $email || ! is_email( $email ) ) {
        return [ 'success' => false, 'message' => __( 'Enter a valid email address.', 'aurora' ) ];
    }

    $attempts = aurora_get_daily_attempts( $email );
    if ( $attempts >= $settings['max_attempts_per_day'] ) {
        return [
            'success' => false,
            'message' => __( 'You reached the maximum OTP requests for today. Please create your account tomorrow.', 'aurora' ),
        ];
    }

    $last_sent = $wpdb->get_var( $wpdb->prepare(
        "SELECT UNIX_TIMESTAMP(created_at) FROM $otp_table WHERE email = %s AND action_type = %s ORDER BY created_at DESC LIMIT 1",
        $email,
        $action_key
    ) );

    if ( $last_sent && ( time() - (int) $last_sent ) < $settings['resend_cooldown'] ) {
        return [
            'success' => false,
            'message' => sprintf(
                __( 'OTP already sent. Please wait %d seconds before requesting again.', 'aurora' ),
                $settings['resend_cooldown']
            ),
        ];
    }

    $otp_code   = aurora_generate_secure_otp( $settings['length'] );
    $expires_at = gmdate( 'Y-m-d H:i:s', time() + $settings['expires_in'] );

    $wpdb->insert(
        $otp_table,
        [
            'user_id'     => absint( $user_id ),
            'otp_code'    => $otp_code,
            'email'       => $email,
            'action_type' => $action_key,
            'expires_at'  => $expires_at,
        ],
        [ '%d', '%s', '%s', '%s', '%s' ]
    );

    aurora_increment_attempts( $email );

    $user_data     = $user_id ? get_userdata( $user_id ) : null;
    $display_name  = ( $user_data && ! empty( $user_data->display_name ) ) ? $user_data->display_name : $email;
    $action_label  = ucwords( str_replace( '_', ' ', $action_key ) );
    $expires_mins  = (int) ( $settings['expires_in'] / MINUTE_IN_SECONDS );

    $subject = sprintf( __( 'Your %s OTP Code - %s', 'aurora' ), $action_label, get_bloginfo( 'name' ) );
    
    // HTML email body with better formatting
    $body = "
    <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0b57d0; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9fafb; padding: 30px; border-left: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; }
                .otp-code { font-size: 32px; font-weight: bold; color: #0b57d0; text-align: center; padding: 20px; background: white; border: 2px dashed #0b57d0; border-radius: 8px; margin: 20px 0; letter-spacing: 4px; }
                .footer { background: #f3f4f6; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 8px 8px; }
                .warning { background: #fee2e2; color: #991b1b; padding: 10px; border-left: 4px solid #dc2626; margin: 15px 0; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class=\"container\">
                <div class=\"header\">
                    <h2>Your One-Time Password</h2>
                </div>
                <div class=\"content\">
                    <p>Hi " . esc_html( $display_name ) . ",</p>
                    <p>You requested a verification code for " . esc_html( $action_label ) . " on " . esc_html( get_bloginfo( 'name' ) ) . ".</p>
                    <p>Your one-time password (OTP) is:</p>
                    <div class=\"otp-code\">" . esc_html( $otp_code ) . "</div>
                    <p><strong>Expires in: " . esc_html( $expires_mins ) . " minutes</strong></p>
                    <p>Please enter this code in your browser window to complete your request.</p>
                    <div class=\"warning\">
                        <strong>Security Notice:</strong> If you did not request this code, please ignore this email. Do not share this code with anyone.
                    </div>
                </div>
                <div class=\"footer\">
                    <p>&copy; " . esc_html( get_bloginfo( 'name' ) ) . ". All rights reserved.</p>
                </div>
            </div>
        </body>
    </html>
    ";

    // Set email headers for HTML
    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    
    // Send email using our SMTP-configured function
    $mail_sent = aurora_send_email( $email, $subject, $body, $headers );

    return [
        'success'       => true,
        'message'       => sprintf( __( 'OTP sent to %s', 'aurora' ), $email ),
        'attempts_left' => max( 0, $settings['max_attempts_per_day'] - $attempts - 1 ),
    ];
}

function aurora_verify_otp_code( $user_id, $email, $otp_code, $action_type ) {
    global $wpdb;

    $otp_table   = $wpdb->prefix . 'aurora_otps';
    $email       = sanitize_email( $email );
    $otp_code    = sanitize_text_field( $otp_code );
    $action_key  = sanitize_key( $action_type );

    $sql  = "SELECT * FROM $otp_table WHERE email = %s AND otp_code = %s AND action_type = %s AND is_used = 0 AND expires_at > UTC_TIMESTAMP()";
    $args = [ $email, $otp_code, $action_key ];

    if ( $user_id ) {
        $sql  .= ' AND user_id = %d';
        $args[] = $user_id;
    }

    $sql  .= ' ORDER BY created_at DESC LIMIT 1';
    $otp   = $wpdb->get_row( $wpdb->prepare( $sql, $args ) );

    if ( ! $otp ) {
        return [ 'success' => false, 'message' => __( 'Invalid or expired OTP.', 'aurora' ) ];
    }

    $wpdb->update(
        $otp_table,
        [ 'is_used' => 1 ],
        [ 'id' => $otp->id ],
        [ '%d' ],
        [ '%d' ]
    );

    return [ 'success' => true, 'message' => __( 'OTP verified.', 'aurora' ) ];
}

function aurora_store_pending_registration( $data ) {
    $settings = aurora_get_otp_settings();
    $token    = bin2hex( random_bytes( 16 ) );

    set_transient( 'aurora_reg_' . $token, $data, $settings['pending_registration_ttl'] );

    return $token;
}

function aurora_get_pending_registration( $token ) {
    return get_transient( 'aurora_reg_' . sanitize_key( $token ) );
}

function aurora_clear_pending_registration( $token ) {
    delete_transient( 'aurora_reg_' . sanitize_key( $token ) );
}

function aurora_store_pending_email_change( $user_id, $new_email ) {
    $settings = aurora_get_otp_settings();
    set_transient(
        'aurora_email_change_' . absint( $user_id ),
        [ 'new_email' => sanitize_email( $new_email ) ],
        $settings['pending_change_ttl']
    );
}

function aurora_get_pending_email_change( $user_id ) {
    return get_transient( 'aurora_email_change_' . absint( $user_id ) );
}

function aurora_clear_pending_email_change( $user_id ) {
    delete_transient( 'aurora_email_change_' . absint( $user_id ) );
}

function aurora_unique_username_from_email( $email, $preferred = '' ) {
    $base = $preferred ? sanitize_user( $preferred, true ) : sanitize_user( current( explode( '@', $email ) ), true );
    $base = $base ? $base : 'user';

    $username = $base;
    $i        = 1;
    while ( username_exists( $username ) ) {
        $username = $base . $i;
        $i++;
    }

    return $username;
}

// === Registration (OTP-first) ===
add_action( 'wp_ajax_nopriv_aurora_request_registration_otp', 'aurora_request_registration_otp_ajax' );
function aurora_request_registration_otp_ajax() {
    aurora_verify_ajax_nonce();

    $email      = sanitize_email( $_POST['email'] ?? '' );
    $password   = $_POST['password'] ?? '';
    $username   = sanitize_user( $_POST['username'] ?? '', true );
    $first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
    $last_name  = sanitize_text_field( $_POST['last_name'] ?? '' );

    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => __( 'Enter a valid email.', 'aurora' ) ] );
    }

    if ( strlen( $password ) < 8 ) {
        wp_send_json_error( [ 'message' => __( 'Password must be at least 8 characters.', 'aurora' ) ] );
    }

    if ( email_exists( $email ) ) {
        wp_send_json_error( [ 'message' => __( 'An account already exists for that email.', 'aurora' ) ] );
    }

    if ( $username && username_exists( $username ) ) {
        wp_send_json_error( [ 'message' => __( 'Username is taken. Please choose another.', 'aurora' ) ] );
    }

    $pending_token = aurora_store_pending_registration(
        compact( 'email', 'password', 'username', 'first_name', 'last_name' )
    );

    $result = aurora_issue_otp( 0, $email, 'register' );

    if ( ! $result['success'] ) {
        wp_send_json_error( $result );
    }

    wp_send_json_success( array_merge( $result, [ 'token' => $pending_token ] ) );
}

add_action( 'wp_ajax_nopriv_aurora_complete_registration', 'aurora_complete_registration_ajax' );
function aurora_complete_registration_ajax() {
    aurora_verify_ajax_nonce();

    $token = sanitize_text_field( $_POST['token'] ?? '' );
    $otp   = sanitize_text_field( $_POST['otp'] ?? '' );
    $email = sanitize_email( $_POST['email'] ?? '' );

    if ( ! $token || ! $otp ) {
        wp_send_json_error( [ 'message' => __( 'Missing registration data. Please start registration again.', 'aurora' ) ] );
    }

    // Retrieve pending registration data
    $pending = aurora_get_pending_registration( $token );
    if ( ! $pending ) {
        wp_send_json_error( [ 'message' => __( 'Registration session expired. Please restart the registration process.', 'aurora' ) ], 400 );
    }

    // Validate email matches to prevent tampering
    if ( sanitize_email( $email ) !== sanitize_email( $pending['email'] ) ) {
        wp_send_json_error( [ 'message' => __( 'Email mismatch. Please restart the registration process.', 'aurora' ) ], 400 );
    }

    // Verify OTP code against the email - MUST verify before creating account
    $verify = aurora_verify_otp_code( 0, $pending['email'], $otp, 'register' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( [ 'message' => $verify['message'], 'message' => __( 'Invalid or expired OTP. Please request a new code.', 'aurora' ) ], 400 );
    }

    // Double-check: ensure email doesn't already exist (registration between OTP request and completion)
    if ( email_exists( $pending['email'] ) ) {
        wp_send_json_error( [ 'message' => __( 'An account already exists for that email. Please sign in instead.', 'aurora' ) ], 400 );
    }

    // Generate unique username
    $username = aurora_unique_username_from_email( $pending['email'], $pending['username'] );

    // ONLY NOW create the user account after OTP verification
    $user_id = wp_create_user( $username, $pending['password'], $pending['email'] );
    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( [ 'message' => $user_id->get_error_message() ], 500 );
    }

    // Set user metadata
    wp_update_user( [
        'ID'         => $user_id,
        'first_name' => $pending['first_name'],
        'last_name'  => $pending['last_name'],
        'role'       => 'customer'
    ] );

    // Clear the pending registration token
    aurora_clear_pending_registration( $token );

    // Log the user in
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );

    wp_send_json_success( [
        'message'  => __( 'Registration complete. You are now logged in.', 'aurora' ),
        'redirect' => wc_get_page_permalink( 'myaccount' ),
    ] );
}

// === OTP Login ===
add_action( 'wp_ajax_nopriv_aurora_request_login_otp', 'aurora_request_login_otp_ajax' );
function aurora_request_login_otp_ajax() {
    aurora_verify_ajax_nonce();

    $email = sanitize_email( $_POST['email'] ?? '' );
    $user  = get_user_by( 'email', $email );

    if ( ! $user ) {
        wp_send_json_error( [ 'message' => __( 'No account found for that email.', 'aurora' ) ] );
    }

    $result = aurora_issue_otp( $user->ID, $email, 'login' );

    if ( ! $result['success'] ) {
        wp_send_json_error( $result );
    }

    wp_send_json_success( $result );
}

add_action( 'wp_ajax_nopriv_aurora_login_with_otp', 'aurora_login_with_otp_ajax' );
function aurora_login_with_otp_ajax() {
    aurora_verify_ajax_nonce();

    $email    = sanitize_email( $_POST['email'] ?? '' );
    $otp_code = sanitize_text_field( $_POST['otp'] ?? '' );

    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        wp_send_json_error( [ 'message' => __( 'Invalid email or OTP.', 'aurora' ) ] );
    }

    $verify = aurora_verify_otp_code( $user->ID, $email, $otp_code, 'login' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( $verify );
    }

    wp_set_current_user( $user->ID );
    wp_set_auth_cookie( $user->ID, true );

    wp_send_json_success( [
        'message'  => __( 'Logged in successfully.', 'aurora' ),
        'redirect' => wc_get_page_permalink( 'myaccount' ),
    ] );
}

// === Account: Email change ===
add_action( 'wp_ajax_aurora_request_otp', 'aurora_request_otp_ajax' );
function aurora_request_otp_ajax() {
    aurora_verify_ajax_nonce();

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'message' => __( 'Please sign in first.', 'aurora' ) ] );
    }

    $user        = wp_get_current_user();
    $action_type = sanitize_text_field( $_POST['action_type'] ?? '' );

    if ( 'email_change' === $action_type ) {
        $new_email         = sanitize_email( $_POST['email'] ?? '' );
        $current_password  = $_POST['password'] ?? '';

        if ( ! $new_email || ! is_email( $new_email ) ) {
            wp_send_json_error( [ 'message' => __( 'Enter a valid new email.', 'aurora' ) ] );
        }

        if ( email_exists( $new_email ) ) {
            wp_send_json_error( [ 'message' => __( 'That email is already in use.', 'aurora' ) ] );
        }

        if ( ! wp_check_password( $current_password, $user->user_pass ) ) {
            wp_send_json_error( [ 'message' => __( 'Current password is incorrect.', 'aurora' ) ] );
        }

        aurora_store_pending_email_change( $user->ID, $new_email );
        $result = aurora_issue_otp( $user->ID, $user->user_email, 'email_change' );
    } elseif ( 'password_change' === $action_type ) {
        $result = aurora_issue_otp( $user->ID, $user->user_email, 'password_change' );
    } else {
        wp_send_json_error( [ 'message' => __( 'Unsupported OTP action.', 'aurora' ) ] );
    }

    if ( $result['success'] ) {
        wp_send_json_success( $result );
    }

    wp_send_json_error( $result );
}

add_action( 'wp_ajax_aurora_update_email', 'aurora_update_email_ajax' );
function aurora_update_email_ajax() {
    aurora_verify_ajax_nonce();

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'message' => __( 'Please sign in first.', 'aurora' ) ] );
    }

    $user    = wp_get_current_user();
    $pending = aurora_get_pending_email_change( $user->ID );
    $otp     = sanitize_text_field( $_POST['otp'] ?? '' );

    if ( ! $pending || empty( $pending['new_email'] ) ) {
        wp_send_json_error( [ 'message' => __( 'No email change request found or it expired.', 'aurora' ) ] );
    }

    $verify = aurora_verify_otp_code( $user->ID, $user->user_email, $otp, 'email_change' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( $verify );
    }

    if ( email_exists( $pending['new_email'] ) ) {
        wp_send_json_error( [ 'message' => __( 'Email already in use.', 'aurora' ) ] );
    }

    wp_update_user( [ 'ID' => $user->ID, 'user_email' => $pending['new_email'] ] );
    aurora_clear_pending_email_change( $user->ID );

    wp_send_json_success( [ 'message' => __( 'Email updated successfully.', 'aurora' ) ] );
}

// === New Email Change Flow: Request OTP on current email ===
add_action( 'wp_ajax_aurora_request_email_change_otp', 'aurora_request_email_change_otp_ajax' );
function aurora_request_email_change_otp_ajax() {
    aurora_verify_ajax_nonce();

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'message' => __( 'Please sign in first.', 'aurora' ) ] );
    }

    $user      = wp_get_current_user();
    $new_email = sanitize_email( $_POST['new_email'] ?? '' );

    if ( ! $new_email || ! is_email( $new_email ) ) {
        wp_send_json_error( [ 'message' => __( 'Please enter a valid email address.', 'aurora' ) ] );
    }

    if ( email_exists( $new_email ) ) {
        wp_send_json_error( [ 'message' => __( 'That email is already in use.', 'aurora' ) ] );
    }

    if ( $new_email === $user->user_email ) {
        wp_send_json_error( [ 'message' => __( 'The new email must be different from your current email.', 'aurora' ) ] );
    }

    // Store the pending email change
    aurora_store_pending_email_change( $user->ID, $new_email );

    // Send OTP to the CURRENT email (existing email)
    $result = aurora_issue_otp( $user->ID, $user->user_email, 'email_change' );

    if ( $result['success'] ) {
        wp_send_json_success( $result );
    }

    wp_send_json_error( $result );
}

// === New Email Change Flow: Verify OTP and update email ===
add_action( 'wp_ajax_aurora_verify_email_change', 'aurora_verify_email_change_ajax' );
function aurora_verify_email_change_ajax() {
    aurora_verify_ajax_nonce();

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'message' => __( 'Please sign in first.', 'aurora' ) ] );
    }

    $user      = wp_get_current_user();
    $new_email = sanitize_email( $_POST['new_email'] ?? '' );
    $otp       = sanitize_text_field( $_POST['otp'] ?? '' );
    $pending   = aurora_get_pending_email_change( $user->ID );

    if ( ! $pending || empty( $pending['new_email'] ) ) {
        wp_send_json_error( [ 'message' => __( 'No email change request found or it expired.', 'aurora' ) ] );
    }

    if ( $new_email !== $pending['new_email'] ) {
        wp_send_json_error( [ 'message' => __( 'Email mismatch. Please try again.', 'aurora' ) ] );
    }

    // Verify OTP against the current email
    $verify = aurora_verify_otp_code( $user->ID, $user->user_email, $otp, 'email_change' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( $verify );
    }

    if ( email_exists( $pending['new_email'] ) ) {
        wp_send_json_error( [ 'message' => __( 'Email already in use.', 'aurora' ) ] );
    }

    // Update user email
    wp_update_user( [ 'ID' => $user->ID, 'user_email' => $pending['new_email'] ] );
    aurora_clear_pending_email_change( $user->ID );

    wp_send_json_success( [ 'message' => __( 'Email updated successfully.', 'aurora' ) ] );
}

// === Account: Password change ===
add_action( 'wp_ajax_aurora_update_password', 'aurora_update_password_ajax' );
function aurora_update_password_ajax() {
    aurora_verify_ajax_nonce();

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'message' => __( 'Please sign in first.', 'aurora' ) ] );
    }

    $user             = wp_get_current_user();
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $otp              = sanitize_text_field( $_POST['otp'] ?? '' );

    if ( strlen( $new_password ) < 8 ) {
        wp_send_json_error( [ 'message' => __( 'Password must be at least 8 characters.', 'aurora' ) ] );
    }

    $has_valid_current = wp_check_password( $current_password, $user->user_pass );

    if ( $otp ) {
        $verify = aurora_verify_otp_code( $user->ID, $user->user_email, $otp, 'password_change' );
        if ( ! $verify['success'] ) {
            wp_send_json_error( $verify );
        }
    } elseif ( ! $has_valid_current ) {
        wp_send_json_error( [ 'message' => __( 'Provide a valid current password or OTP.', 'aurora' ) ] );
    }

    wp_set_password( $new_password, $user->ID );

    wp_send_json_success( [ 'message' => __( 'Password updated successfully.', 'aurora' ) ] );
}

// === Forgot password (OTP) ===
add_action( 'wp_ajax_nopriv_aurora_reset_password', 'aurora_reset_password_ajax' );
add_action( 'wp_ajax_aurora_reset_password', 'aurora_reset_password_ajax' );
function aurora_reset_password_ajax() {
    aurora_verify_ajax_nonce();

    $email = sanitize_email( $_POST['email'] ?? '' );
    $user  = get_user_by( 'email', $email );

    if ( $user ) {
        aurora_issue_otp( $user->ID, $email, 'password_reset' );
    }

    wp_send_json_success( [ 'message' => __( 'If that email is registered, an OTP has been sent.', 'aurora' ) ] );
}

add_action( 'wp_ajax_nopriv_aurora_confirm_password_reset', 'aurora_confirm_password_reset_ajax' );
add_action( 'wp_ajax_aurora_confirm_password_reset', 'aurora_confirm_password_reset_ajax' );
function aurora_confirm_password_reset_ajax() {
    aurora_verify_ajax_nonce();

    $email        = sanitize_email( $_POST['email'] ?? '' );
    $otp_code     = sanitize_text_field( $_POST['otp'] ?? '' );
    $new_password = $_POST['new_password'] ?? '';

    if ( strlen( $new_password ) < 8 ) {
        wp_send_json_error( [ 'message' => __( 'Password must be at least 8 characters.', 'aurora' ) ] );
    }

    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        wp_send_json_error( [ 'message' => __( 'Invalid email or OTP.', 'aurora' ) ] );
    }

    $verify = aurora_verify_otp_code( $user->ID, $email, $otp_code, 'password_reset' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( $verify );
    }

    wp_set_password( $new_password, $user->ID );

    wp_send_json_success( [ 'message' => __( 'Password reset successfully.', 'aurora' ) ] );
}

// === OTP Login with Password Reset Flow ===
add_action( 'wp_ajax_nopriv_aurora_verify_login_otp_only', 'aurora_verify_login_otp_only_ajax' );
function aurora_verify_login_otp_only_ajax() {
    aurora_verify_ajax_nonce();

    $email    = sanitize_email( $_POST['email'] ?? '' );
    $otp_code = sanitize_text_field( $_POST['otp'] ?? '' );

    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        wp_send_json_error( [ 'message' => __( 'Invalid email or OTP.', 'aurora' ) ] );
    }

    $verify = aurora_verify_otp_code( $user->ID, $email, $otp_code, 'login' );
    if ( ! $verify['success'] ) {
        wp_send_json_error( $verify );
    }

    // Generate a temporary token valid for 10 minutes
    $temp_token = wp_hash( $user->ID . time() . wp_generate_password() );
    set_transient( 'aurora_otp_verified_' . $user->ID, $temp_token, 10 * MINUTE_IN_SECONDS );

    wp_send_json_success( [
        'message'    => __( 'OTP verified successfully.', 'aurora' ),
        'temp_token' => $temp_token,
    ] );
}

add_action( 'wp_ajax_nopriv_aurora_login_with_verified_otp', 'aurora_login_with_verified_otp_ajax' );
function aurora_login_with_verified_otp_ajax() {
    aurora_verify_ajax_nonce();

    $email      = sanitize_email( $_POST['email'] ?? '' );
    $temp_token = sanitize_text_field( $_POST['temp_token'] ?? '' );

    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        wp_send_json_error( [ 'message' => __( 'User not found.', 'aurora' ) ] );
    }

    // Verify temp token exists and is valid
    $stored_token = get_transient( 'aurora_otp_verified_' . $user->ID );
    if ( ! $stored_token || $stored_token !== $temp_token ) {
        wp_send_json_error( [ 'message' => __( 'Session expired. Please verify OTP again.', 'aurora' ) ] );
    }

    // Clear the temporary token
    delete_transient( 'aurora_otp_verified_' . $user->ID );

    // Log the user in
    wp_set_current_user( $user->ID );
    wp_set_auth_cookie( $user->ID, true );

    wp_send_json_success( [
        'message'  => __( 'Logged in successfully.', 'aurora' ),
        'redirect' => wc_get_page_permalink( 'myaccount' ),
    ] );
}

add_action( 'wp_ajax_nopriv_aurora_reset_password_and_login', 'aurora_reset_password_and_login_ajax' );
function aurora_reset_password_and_login_ajax() {
    aurora_verify_ajax_nonce();

    $email      = sanitize_email( $_POST['email'] ?? '' );
    $temp_token = sanitize_text_field( $_POST['temp_token'] ?? '' );
    $new_password = $_POST['new_password'] ?? '';

    if ( strlen( $new_password ) < 8 ) {
        wp_send_json_error( [ 'message' => __( 'Password must be at least 8 characters.', 'aurora' ) ] );
    }

    $user = get_user_by( 'email', $email );
    if ( ! $user ) {
        wp_send_json_error( [ 'message' => __( 'User not found.', 'aurora' ) ] );
    }

    // Verify temp token exists and is valid
    $stored_token = get_transient( 'aurora_otp_verified_' . $user->ID );
    if ( ! $stored_token || $stored_token !== $temp_token ) {
        wp_send_json_error( [ 'message' => __( 'Session expired. Please verify OTP again.', 'aurora' ) ] );
    }

    // Clear the temporary token
    delete_transient( 'aurora_otp_verified_' . $user->ID );

    // Reset password
    wp_set_password( $new_password, $user->ID );

    // Log the user in
    wp_set_current_user( $user->ID );
    wp_set_auth_cookie( $user->ID, true );

    wp_send_json_success( [
        'message'  => __( 'Password reset and logged in successfully.', 'aurora' ),
        'redirect' => wc_get_page_permalink( 'myaccount' ),
    ] );
}

// === Profile image helpers ===
add_action( 'wp_ajax_aurora_upload_profile_image', 'aurora_upload_profile_image_ajax' );
function aurora_upload_profile_image_ajax() {
    aurora_verify_ajax_nonce();

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'message' => __( 'Please sign in first.', 'aurora' ) ] );
    }

    if ( empty( $_FILES['profile_image'] ) ) {
        wp_send_json_error( [ 'message' => __( 'No file uploaded.', 'aurora' ) ] );
    }

    $user_id = get_current_user_id();

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attachment_id = media_handle_upload( 'profile_image', 0 );

    if ( is_wp_error( $attachment_id ) ) {
        wp_send_json_error( [ 'message' => $attachment_id->get_error_message() ] );
    }

    $old_image_id = get_user_meta( $user_id, 'aurora_profile_image', true );
    if ( $old_image_id && is_numeric( $old_image_id ) ) {
        wp_delete_attachment( $old_image_id, true );
    }

    update_user_meta( $user_id, 'aurora_profile_image', $attachment_id );

    wp_send_json_success( [
        'message'   => __( 'Profile image updated.', 'aurora' ),
        'image_url' => wp_get_attachment_url( $attachment_id ),
    ] );
}

function aurora_get_user_profile_image( $user_id ) {
    $image_id = get_user_meta( $user_id, 'aurora_profile_image', true );

    if ( $image_id && is_numeric( $image_id ) ) {
        return wp_get_attachment_url( $image_id );
    }

    return get_avatar_url( $user_id, [ 'size' => 150 ] );
}
