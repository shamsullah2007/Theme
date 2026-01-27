# Aurora Authentication System - Integration & Setup Guide

## Overview

The Aurora Authentication system provides a complete frontend-backend authentication solution for your WordPress theme. It includes registration, login, forgot password, and account management with modern, responsive design.

## System Architecture

```
Frontend (User-Facing)
├── template-registration.php (Sign up page)
├── template-login.php (Login page)
├── template-forgot-password.php (Password reset)
└── assets/
    ├── css/auth.css (Styling)
    └── js/auth.js (AJAX handlers)

Backend (Server-Side)
├── inc/auth.php (Core authentication system)
└── Database Tables
    ├── wp_aurora_otps (OTP codes)
    └── wp_aurora_otp_attempts (Rate limiting)
```

## Frontend Files

### 1. Registration Page (`template-registration.php`)
**Purpose**: User sign-up with email OTP verification

**Two-Step Flow**:
- **Step 1**: Collect first name, last name, email, username (optional), password
- **Step 2**: OTP verification (6-digit code)

**Key Features**:
- Form validation before sending OTP
- Resend timer (60-second cooldown)
- Trust badges (security indicators)
- Clear success/error messaging
- Auto-redirect to dashboard on success

**Usage**:
```php
// Create a page and assign template "template-registration.php"
// Or use it as body_class:
if ( is_page() && get_page_template_slug() === 'template-registration.php' ) {
    // Page will use registration template
}
```

**AJAX Endpoints Used**:
- `aurora_request_registration_otp` - Request OTP (Step 1)
- `aurora_complete_registration` - Verify OTP & create user (Step 2)

---

### 2. Login Page (`template-login.php`)
**Purpose**: User login with dual authentication methods

**Dual Authentication Methods**:
- **Method 1**: Email + Password (traditional)
- **Method 2**: Email + OTP (passwordless)

**Features**:
- Tab-based interface (switch between methods)
- Remember me option (Method 1)
- Forgot password link
- OTP timer and resend
- Trust badges

**Usage**:
```php
// Create a page and assign template "template-login.php"
// Link from header: wp_logout_url( get_permalink( $login_page_id ) )
```

**AJAX Endpoints Used**:
- Standard WordPress form: `wp-login.php` (Method 1)
- `aurora_request_login_otp` - Request OTP (Method 2, Step 1)
- `aurora_login_with_otp` - Verify OTP & login (Method 2, Step 2)

---

### 3. Forgot Password Page (`template-forgot-password.php`)
**Purpose**: Password reset via email and OTP

**Three-Step Flow**:
- **Step 1**: Enter email address
- **Step 2**: Verify OTP sent to email
- **Step 3**: Enter new password

**Features**:
- Email validation
- OTP verification with timer
- Password confirmation
- Success confirmation screen
- Back buttons for easy navigation
- Trust badges

**Usage**:
```php
// Create a page and assign template "template-forgot-password.php"
// Link from login page: <a href="forgot-password-page-url">Forgot Password?</a>
```

**AJAX Endpoints Used**:
- `aurora_reset_password` - Request reset OTP (Step 1)
- `aurora_confirm_password_reset` - Reset password (Step 3)

---

## Backend Files

### Core Authentication File (`inc/auth.php`)

**Key Functions**:

#### 1. OTP Management
```php
// Generate and send OTP
aurora_issue_otp( $email, $action = 'login', $user_id = 0 )

// Verify OTP code
aurora_verify_otp_code( $email, $otp_code, $action = 'login' )

// Get OTP settings
aurora_get_otp_settings()
```

#### 2. Registration AJAX Endpoints
```javascript
// Request registration OTP
POST /wp-admin/admin-ajax.php
Action: aurora_request_registration_otp
Data: {
    first_name,
    last_name,
    email,
    username (optional),
    password,
    agree_terms,
    nonce
}
Response: { success: true/false, data: { message } }

// Complete registration
POST /wp-admin/admin-ajax.php
Action: aurora_complete_registration
Data: {
    email,
    otp_code,
    nonce
}
Response: { success: true/false, data: { message, user_id? } }
```

#### 3. Login AJAX Endpoints
```javascript
// Request OTP for login
POST /wp-admin/admin-ajax.php
Action: aurora_request_login_otp
Data: {
    email,
    nonce
}
Response: { success: true/false, data: { message } }

// Login with OTP
POST /wp-admin/admin-ajax.php
Action: aurora_login_with_otp
Data: {
    email,
    otp_code,
    nonce
}
Response: { success: true/false, data: { message } }
```

#### 4. Password Reset AJAX Endpoints
```javascript
// Request password reset OTP
POST /wp-admin/admin-ajax.php
Action: aurora_reset_password
Data: {
    email,
    nonce
}
Response: { success: true/false, data: { message } }

// Confirm password reset
POST /wp-admin/admin-ajax.php
Action: aurora_confirm_password_reset
Data: {
    email,
    new_password,
    otp_code,
    nonce
}
Response: { success: true/false, data: { message } }
```

---

## CSS Styling (`assets/css/auth.css`)

**Coverage**: All authentication pages - registration, login, forgot password

**Key Features**:
- **Responsive Design**:
  - Desktop (1024px+)
  - Tablet (600px - 1023px)
  - Mobile (< 600px)
  - Extra small (<480px)

- **Components**:
  - Auth cards with shadow effects
  - Form inputs with focus states
  - Buttons (primary, secondary, link)
  - Message alerts (success, error, info)
  - Tabs and tab content
  - OTP input with special formatting
  - Trust badges
  - Loading spinners

- **Dark Mode**: Automatic support via `@media (prefers-color-scheme: dark)`
- **Accessibility**: Reduced motion support, high contrast alerts
- **Animation**: Smooth transitions and effects

**Color Scheme**:
```css
--primary-color: #0b57d0 (Amazon Blue)
--success-color: #10b981 (Green)
--error-color: #ef4444 (Red)
--text-primary: #1a1a1a (Dark)
--text-secondary: #6b7280 (Gray)
```

---

## JavaScript Handler (`assets/js/auth.js`)

**Main Object**: `AuroraAuth`

**Key Methods**:

### Registration
```javascript
AuroraAuth.initRegistration()
AuroraAuth.requestRegistrationOTP()
AuroraAuth.completeRegistration()
AuroraAuth.validateRegistrationForm()
```

### Login
```javascript
AuroraAuth.initLogin()
AuroraAuth.switchTab(tabId)
AuroraAuth.requestLoginOTP()
AuroraAuth.loginWithOTP()
```

### Forgot Password
```javascript
AuroraAuth.initForgotPassword()
AuroraAuth.requestPasswordResetOTP()
AuroraAuth.verifyPasswordResetOTP()
AuroraAuth.confirmPasswordReset()
```

### Utilities
```javascript
AuroraAuth.showMessage(container, message, type)
AuroraAuth.setLoadingState(button, isLoading)
AuroraAuth.startResendTimer(type, seconds)
AuroraAuth.isValidEmail(email)
```

---

## Database Tables

### 1. `wp_aurora_otps`
**Purpose**: Store OTP codes and expiration information

**Columns**:
```sql
id (INT, PRIMARY KEY)
user_id (BIGINT, 0 for guests)
otp_code (VARCHAR 10)
email (VARCHAR 100)
action_type (VARCHAR 20) - 'registration', 'login', 'reset', 'email_change'
created_at (DATETIME)
expires_at (DATETIME)
is_used (TINYINT, 0 or 1)

Indexes:
- KEY (email)
- KEY (user_id, action_type)
```

**Auto-Created**: Yes (on first use via `aurora_issue_otp()`)

### 2. `wp_aurora_otp_attempts`
**Purpose**: Track OTP request attempts for rate limiting

**Columns**:
```sql
id (INT, PRIMARY KEY)
user_id (BIGINT, 0 for guests)
email (VARCHAR 100)
attempt_date (DATE)
attempt_count (INT)

Indexes:
- UNIQUE KEY (email, attempt_date)
```

**Auto-Created**: Yes (on first use)

**Rate Limiting Rules**:
- **Max 5 OTP requests per email per day**
- **60-second cooldown between requests**

---

## Setup Instructions

### Step 1: Verify Backend is Active
```bash
# Check that inc/auth.php is included in functions.php
# Should see: require_once get_template_directory() . '/inc/auth.php';

# Verify AJAX nonces are exposed:
# wp_localize_script() should include 'authNonce' and 'profileNonce'
```

### Step 2: Create Template Pages in WordPress

**Registration Page**:
1. Go to WordPress Admin → Pages → Add New
2. Title: "Sign Up" (or "Register")
3. Set Template: "template-registration.php"
4. Publish

**Login Page**:
1. Go to WordPress Admin → Pages → Add New
2. Title: "Sign In" (or "Login")
3. Set Template: "template-login.php"
4. Publish

**Forgot Password Page**:
1. Go to WordPress Admin → Pages → Add New
2. Title: "Forgot Password" (or "Reset Password")
3. Set Template: "template-forgot-password.php"
4. Publish

### Step 3: Update Navigation Links

**In Header/Navigation**:
```php
// Login/Register link for guests
<?php if ( ! is_user_logged_in() ) : ?>
    <a href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>">
        <?php _e( 'Sign In', 'aurora' ); ?>
    </a>
<?php endif; ?>

// My Account link for logged-in users
<?php if ( is_user_logged_in() ) : ?>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
        <?php _e( 'My Account', 'aurora' ); ?>
    </a>
    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
        <?php _e( 'Sign Out', 'aurora' ); ?>
    </a>
<?php endif; ?>
```

### Step 4: Test the System

**Test Registration**:
1. Go to Sign Up page
2. Fill in form: name, email, password, terms
3. Click "Send Verification Code"
4. Check email for OTP code
5. Enter OTP and create account
6. Should see success message and redirect to dashboard

**Test Login**:
1. Go to Sign In page
2. Test Method 1: Email + Password
   - Enter credentials
   - Click "Sign In"
   - Should redirect to dashboard
3. Log out
4. Go to Sign In page
5. Test Method 2: Email + OTP
   - Enter email
   - Click "Send Verification Code"
   - Check email for OTP
   - Enter code and login
   - Should redirect to dashboard

**Test Forgot Password**:
1. Go to Forgot Password page
2. Enter email address
3. Click "Send Verification Code"
4. Check email for OTP
5. Enter OTP code
6. Set new password and confirm
7. Should see success message
8. Log in with new password

---

## Security Features

### 1. Nonce Verification
All AJAX requests require valid WordPress nonces:
```php
// Generated in functions.php:
'authNonce' => wp_create_nonce( 'aurora_auth_nonce' ),
'profileNonce' => wp_create_nonce( 'aurora_profile_nonce' ),
```

### 2. Rate Limiting
- **5 OTP requests per email per day**
- **60-second cooldown between requests**
- Enforced on server-side

### 3. OTP Security
- **6-digit alphanumeric codes**
- **10-minute expiration** (configurable)
- **One-time use** (marked as used after verification)
- **Separate database table** for tracking

### 4. Input Sanitization
All user inputs are sanitized:
```php
// Email
$email = sanitize_email( $_POST['email'] );

// Password
$password = $_POST['password']; // Validated but not sanitized (security best practice)

// Text fields
$first_name = sanitize_text_field( $_POST['first_name'] );
```

### 5. SQL Injection Prevention
All database queries use prepared statements:
```php
$wpdb->prepare( "SELECT * FROM table WHERE email = %s", $email )
```

### 6. Password Security
- **Minimum 6 characters**
- **Stored using WordPress wp_hash_password()**
- **Never stored in plaintext**

---

## Customization

### Change OTP Settings
```php
// In functions.php or theme settings:
add_filter( 'aurora_otp_settings', function( $settings ) {
    return array_merge( $settings, [
        'otp_length' => 8,           // Default: 6
        'otp_expiry' => 15 * MINUTE_IN_SECONDS, // Default: 10 min
        'max_attempts_per_day' => 10, // Default: 5
        'resend_cooldown' => 120,    // Default: 60 seconds
    ] );
} );
```

### Change Color Scheme
```css
/* In custom CSS or override */
:root {
    --primary-color: #your-color !important;
    --success-color: #your-color !important;
    /* etc */
}
```

### Change Email Template
Edit the email content in `inc/auth.php`, function `aurora_issue_otp()`:
```php
$email_subject = 'Your verification code is: ' . $otp_code;
$email_body = 'Use this code to verify your account...';
```

---

## Troubleshooting

### Issue: "Invalid nonce" error
**Solution**: 
- Clear browser cache
- Verify `wp_create_nonce()` is called in functions.php
- Check that JavaScript includes `auroraTheme.authNonce`

### Issue: OTP not received
**Solution**:
- Check WordPress email settings (Settings → General)
- Test with a plugin: WP Mail SMTP
- Check spam folder
- Verify email is not blocked by host

### Issue: User not created after registration
**Solution**:
- Check database errors in WordPress debug log
- Verify email is unique (not already registered)
- Check that OTP is correct
- Verify nonce is valid

### Issue: Login redirect not working
**Solution**:
- Verify `dashboardUrl` is set in `wp_localize_script()`
- Check WooCommerce My Account page exists and is published
- Clear browser cache

### Issue: CSS not loading
**Solution**:
- Verify file path in functions.php enqueue
- Check file exists at: `/theme/assets/css/auth.css`
- Clear WordPress cache (if using caching plugin)

### Issue: JavaScript errors in console
**Solution**:
- Check jQuery is loaded
- Verify auth.js is enqueued in functions.php
- Check for browser console errors (F12)
- Verify all required variables: `auroraTheme`, `jQuery`

---

## Email Integration

### Configure Email Sending

**Option 1: Using WP Mail SMTP**
1. Install plugin: "WP Mail SMTP"
2. Configure with your email service (Gmail, SendGrid, etc.)
3. Test email sending

**Option 2: Using Easy WP SMTP**
1. Install plugin: "Easy WP SMTP"
2. Configure SMTP server details
3. Test connection

**Option 3: Server Mail
**
1. Ensure PHP mail() is configured on server
2. Contact hosting provider for configuration

### Customize Email Content

Edit `/theme/inc/auth.php`, search for `wp_mail()` calls:

```php
// Registration OTP email
$email_subject = 'Verify your Aurora account';
$email_body = sprintf(
    'Your verification code is: <strong>%s</strong><br>This code expires in 10 minutes.',
    $otp_code
);

// Login OTP email
$email_subject = 'Your Aurora verification code';
$email_body = sprintf(
    'Use this code to sign in: <strong>%s</strong>',
    $otp_code
);

// Password reset email
$email_subject = 'Reset your Aurora password';
$email_body = sprintf(
    'Your password reset code is: <strong>%s</strong>',
    $otp_code
);
```

---

## Advanced Features

### Account Management (My Account Dashboard)

Located in: `/woocommerce/myaccount/dashboard.php`

**Features Available**:
- Change Email (with OTP verification)
- Change Password (with current password or OTP)
- Upload Profile Picture
- View Account Information

**AJAX Endpoints**:
```javascript
// Request OTP for account changes
aurora_request_otp

// Update email
aurora_update_email

// Update password
aurora_update_password

// Upload profile image
aurora_upload_profile_image
```

---

## Performance Optimization

### Enqueue Optimization
CSS and JS are only loaded when needed:
```php
// Check if on auth page before enqueueing
if ( is_page_template( 'template-registration.php' ) || 
     is_page_template( 'template-login.php' ) || 
     is_page_template( 'template-forgot-password.php' ) ) {
    // Enqueue auth-specific CSS/JS
}
```

### Database Optimization
- Regular cleanup of expired OTPs (recommended weekly)
- Indexes on frequently queried columns

**Add to wp-cron**:
```php
add_action( 'aurora_cleanup_expired_otps', function() {
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->prefix}aurora_otps WHERE expires_at < NOW()" );
} );

// Schedule to run weekly
if ( ! wp_next_scheduled( 'aurora_cleanup_expired_otps' ) ) {
    wp_schedule_event( time(), 'weekly', 'aurora_cleanup_expired_otps' );
}
```

---

## Support & Resources

**Files Reference**:
- Backend: `/theme/inc/auth.php` (617 lines)
- Styling: `/theme/assets/css/auth.css` (~600 lines)
- JavaScript: `/theme/assets/js/auth.js` (~500 lines)
- Templates:
  - `/theme/template-registration.php`
  - `/theme/template-login.php`
  - `/theme/template-forgot-password.php`

**Documentation**:
- See `AUTHENTICATION_SYSTEM_GUIDE.md` for detailed API reference
- See individual template files for HTML structure

**Next Steps**:
1. Complete setup following "Setup Instructions"
2. Test all workflows (registration, login, password reset)
3. Customize email templates for your brand
4. Configure email sending service
5. Monitor error logs for issues
6. Gather user feedback for improvements

---

**Version**: 1.0.0
**Last Updated**: 2024
**License**: [Your Theme License]
