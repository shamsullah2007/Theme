# Aurora Auth Backend – Setup and Usage Guide

## Overview

The Aurora theme now includes a **comprehensive authentication and account management system** with OTP (One-Time Password) email verification.

This backend provides:
- **OTP-based user registration**
- **Email/password login** + **OTP login** (passwordless)
- **Forgot password** flow with OTP
- **Email change** with OTP verification
- **Password change** with OTP or current password
- **Profile image** upload/management

---

## 1. Database Tables

### OTP Storage: `{prefix}_aurora_otps`

| Column        | Type           | Purpose                              |
|---------------|----------------|--------------------------------------|
| `id`          | bigint(20)     | Primary key                          |
| `user_id`     | bigint(20)     | 0 for registration, else WP user ID  |
| `otp_code`    | varchar(12)    | 6-digit OTP code (default)           |
| `email`       | varchar(150)   | Recipient email                      |
| `action_type` | varchar(50)    | E.g. `register`, `login`, `password_reset`, `email_change`, `password_change` |
| `created_at`  | datetime       | Timestamp OTP was created            |
| `expires_at`  | datetime       | Timestamp OTP expires (default 10 min) |
| `is_used`     | tinyint(1)     | 1 if verified/consumed, 0 otherwise  |

**Indexes**: email, user_action (user_id + action_type)

### Attempt Tracking: `{prefix}_aurora_otp_attempts`

| Column          | Type        | Purpose                              |
|-----------------|-------------|--------------------------------------|
| `id`            | bigint(20)  | Primary key                          |
| `user_id`       | bigint(20)  | 0 for guest attempts, else WP user ID|
| `email`         | varchar(150)| Email that requested OTP             |
| `attempt_date`  | date        | Date of attempts (daily limit)       |
| `attempt_count` | int(11)     | Number of OTPs requested today       |

**Indexes**: email, attempt_date

**Daily Limit**: By default, **5 OTP requests per email per day**.

---

## 2. User Meta

| Meta Key              | Purpose                                 |
|-----------------------|-----------------------------------------|
| `aurora_profile_image`| Attachment ID of custom profile picture |

Retrieved via: `aurora_get_user_profile_image( $user_id )`

---

## 3. Transients (Pending State)

### Registration
- **Key**: `aurora_reg_{token}`
- **Value**: `[ 'email', 'password', 'username', 'first_name', 'last_name' ]`
- **TTL**: 15 minutes (default)

### Email Change
- **Key**: `aurora_email_change_{user_id}`
- **Value**: `[ 'new_email' => '...' ]`
- **TTL**: 15 minutes (default)

---

## 4. Configuration

Adjust OTP settings via filter:

```php
add_filter( 'aurora_otp_settings', function( $settings ) {
    $settings['length']                  = 6;             // OTP digit count
    $settings['expires_in']              = 10 * MINUTE_IN_SECONDS;  // 10 minutes
    $settings['max_attempts_per_day']    = 5;
    $settings['resend_cooldown']         = 60;            // seconds before resend
    $settings['pending_registration_ttl']= 15 * MINUTE_IN_SECONDS;
    $settings['pending_change_ttl']      = 15 * MINUTE_IN_SECONDS;
    return $settings;
} );
```

---

## 5. AJAX Endpoints

### 5.1 User Registration (OTP-First)

**Step 1: Request OTP**
- **Action**: `aurora_request_registration_otp`
- **Logged In**: No
- **Nonce**: `auth_nonce` (or `nonce`)
- **Params**: `email`, `password`, `username` (optional), `first_name` (optional), `last_name` (optional)
- **Response**: `{ success: true, token: "...", message: "OTP sent to email..." }`

**Step 2: Verify OTP & Complete Registration**
- **Action**: `aurora_complete_registration`
- **Logged In**: No
- **Nonce**: `auth_nonce`
- **Params**: `token`, `otp`
- **Response**: `{ success: true, message: "Registration complete...", redirect: "/my-account/" }`

**Example JS**:
```javascript
// Step 1
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_request_registration_otp',
  auth_nonce: auroraTheme.authNonce,
  email: 'user@example.com',
  password: 'SecurePass123',
  first_name: 'Jane',
  last_name: 'Doe'
}, function( res ) {
  if ( res.success ) {
    // Save res.data.token, show OTP input
  }
} );

// Step 2
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_complete_registration',
  auth_nonce: auroraTheme.authNonce,
  token: savedToken,
  otp: '123456'
}, function( res ) {
  if ( res.success ) {
    window.location.href = res.data.redirect;
  }
} );
```

---

### 5.2 Login (Email + OTP)

**Step 1: Request OTP**
- **Action**: `aurora_request_login_otp`
- **Nonce**: `auth_nonce`
- **Params**: `email`
- **Response**: `{ success: true, message: "OTP sent to email..." }`

**Step 2: Verify OTP & Log In**
- **Action**: `aurora_login_with_otp`
- **Nonce**: `auth_nonce`
- **Params**: `email`, `otp`
- **Response**: `{ success: true, message: "Logged in successfully.", redirect: "/my-account/" }`

**Example JS**:
```javascript
// Step 1
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_request_login_otp',
  auth_nonce: auroraTheme.authNonce,
  email: 'user@example.com'
}, function( res ) {
  if ( res.success ) {
    // Show OTP input
  }
} );

// Step 2
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_login_with_otp',
  auth_nonce: auroraTheme.authNonce,
  email: 'user@example.com',
  otp: '654321'
}, function( res ) {
  if ( res.success ) {
    window.location.href = res.data.redirect;
  }
} );
```

---

### 5.3 Forgot Password (OTP Reset)

**Step 1: Request OTP**
- **Action**: `aurora_reset_password`
- **Logged In**: No (public)
- **Nonce**: `auth_nonce`
- **Params**: `email`
- **Response**: `{ success: true, message: "If that email is registered, an OTP has been sent." }`

**Step 2: Verify OTP & Set New Password**
- **Action**: `aurora_confirm_password_reset`
- **Nonce**: `auth_nonce`
- **Params**: `email`, `otp`, `new_password`
- **Response**: `{ success: true, message: "Password reset successfully." }`

**Example JS**:
```javascript
// Step 1
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_reset_password',
  auth_nonce: auroraTheme.authNonce,
  email: 'user@example.com'
}, function( res ) {
  if ( res.success ) {
    // Show OTP + new password fields
  }
} );

// Step 2
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_confirm_password_reset',
  auth_nonce: auroraTheme.authNonce,
  email: 'user@example.com',
  otp: '111222',
  new_password: 'NewPass456'
}, function( res ) {
  if ( res.success ) {
    // Password reset complete
  }
} );
```

---

### 5.4 Account: Change Email

**Step 1: Request OTP (sent to current email)**
- **Action**: `aurora_request_otp`
- **Logged In**: Yes
- **Nonce**: `nonce` (aurora_profile_nonce)
- **Params**: `action_type: 'email_change'`, `email: newEmail@example.com`, `password: currentPassword`
- **Response**: `{ success: true, message: "OTP sent to your current email..." }`

**Step 2: Verify OTP & Update**
- **Action**: `aurora_update_email`
- **Params**: `otp`
- **Response**: `{ success: true, message: "Email updated successfully." }`

**Example JS**:
```javascript
// Step 1
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_request_otp',
  nonce: auroraTheme.profileNonce,
  action_type: 'email_change',
  email: 'newemail@example.com',
  password: 'CurrentPass'
}, function( res ) {
  if ( res.success ) {
    // Show OTP input
  }
} );

// Step 2
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_update_email',
  nonce: auroraTheme.profileNonce,
  otp: '333444'
}, function( res ) {
  if ( res.success ) {
    // Email changed
  }
} );
```

---

### 5.5 Account: Change Password

**Option A: Using Current Password + OTP**
**Step 1: Request OTP**
- **Action**: `aurora_request_otp`
- **Logged In**: Yes
- **Params**: `action_type: 'password_change'`
- **Response**: `{ success: true, message: "OTP sent to your email..." }`

**Step 2: Update Password**
- **Action**: `aurora_update_password`
- **Params**: `current_password`, `new_password`, `otp`
- **Response**: `{ success: true, message: "Password updated successfully." }`

**Option B: Using OTP Only (No Current Password)**
- The backend accepts either `current_password` OR `otp`. If `otp` is provided and valid, `current_password` is not required.

**Example JS**:
```javascript
// Step 1
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_request_otp',
  nonce: auroraTheme.profileNonce,
  action_type: 'password_change'
}, function( res ) {
  if ( res.success ) {
    // Show OTP input
  }
} );

// Step 2
$.post( auroraTheme.ajaxUrl, {
  action: 'aurora_update_password',
  nonce: auroraTheme.profileNonce,
  current_password: 'OldPass',
  new_password: 'NewPass789',
  otp: '555666'
}, function( res ) {
  if ( res.success ) {
    // Password changed
  }
} );
```

---

### 5.6 Profile Image Upload

- **Action**: `aurora_upload_profile_image`
- **Logged In**: Yes
- **Nonce**: `nonce` (aurora_profile_nonce)
- **File Field**: `profile_image`
- **Response**: `{ success: true, message: "Profile image updated.", image_url: "http://..." }`

**Example JS**:
```javascript
var formData = new FormData();
formData.append('action', 'aurora_upload_profile_image');
formData.append('nonce', auroraTheme.profileNonce);
formData.append('profile_image', fileInput.files[0]);

$.ajax({
  url: auroraTheme.ajaxUrl,
  type: 'POST',
  data: formData,
  contentType: false,
  processData: false,
  success: function(res) {
    if (res.success) {
      $('#profile-img').attr('src', res.data.image_url);
    }
  }
});
```

---

## 6. PHP Helper Functions

| Function                                          | Description                                                   |
|---------------------------------------------------|---------------------------------------------------------------|
| `aurora_get_otp_settings()`                       | Returns OTP configuration array                               |
| `aurora_issue_otp( $user_id, $email, $action_type )` | Generates and sends OTP via email. Returns success/error array |
| `aurora_verify_otp_code( $user_id, $email, $otp, $action_type )` | Verifies OTP and marks it as used. Returns success/error array |
| `aurora_get_daily_attempts( $email )`             | Checks how many OTPs have been requested today for email       |
| `aurora_store_pending_registration( $data )`      | Stores pending registration data, returns token               |
| `aurora_get_pending_registration( $token )`       | Retrieves pending registration data                           |
| `aurora_unique_username_from_email( $email, $preferred = '' )` | Generates unique username from email                 |
| `aurora_store_pending_email_change( $user_id, $new_email )` | Stores new email in transient for verification      |
| `aurora_get_pending_email_change( $user_id )`     | Retrieves pending email change data                           |
| `aurora_get_user_profile_image( $user_id )`       | Returns profile image URL or default avatar                   |

---

## 7. Security Features

- **Nonce verification**: All AJAX calls verify nonces (`aurora_auth_nonce` for public, `aurora_profile_nonce` for logged-in users)
- **Daily attempt limits**: 5 OTP requests per email per day
- **Resend cooldown**: 60 seconds between OTP requests (default)
- **OTP expiration**: 10 minutes (default)
- **One-time use**: OTPs are marked as used after verification
- **Sanitization**: All inputs sanitized using WordPress functions
- **Password validation**: Minimum 8 characters
- **Email validation**: Uses `is_email()` and checks for existing accounts

---

## 8. WordPress Integration

- **User creation**: Uses `wp_create_user()` with automatic username generation
- **Authentication**: Sets auth cookie with `wp_set_auth_cookie()`
- **WooCommerce compatibility**: Redirects to My Account page after login/registration
- **Email delivery**: Uses `wp_mail()` (compatible with SMTP plugins)
- **Database**: Uses `$wpdb` with prepared statements
- **Transients**: Uses WordPress transient API for temporary data
- **User meta**: Profile image stored as user meta

---

## 9. Setup Instructions

1. **Theme Activation**: Tables are created automatically on theme load (`after_setup_theme` hook)
2. **SMTP Configuration** (Recommended):
   - Install an SMTP plugin like **WP Mail SMTP** or **Easy WP SMTP**
   - Configure your email service (Gmail, SendGrid, Mailgun, etc.)
   - Test email delivery
3. **Frontend Nonces**: Nonces are automatically localized to `auroraTheme.authNonce` and `auroraTheme.profileNonce`
4. **Existing Forms**: Update login/registration forms to use new AJAX endpoints
5. **Styling**: OTP input fields, success/error messages already styled in existing theme files

---

## 10. Customization Examples

### Change OTP Length
```php
add_filter( 'aurora_otp_settings', function( $settings ) {
    $settings['length'] = 8;  // 8-digit OTP
    return $settings;
} );
```

### Custom OTP Email Template
```php
add_filter( 'wp_mail', function( $args ) {
    if ( strpos( $args['subject'], 'OTP' ) !== false ) {
        // Customize email body
        $args['message'] = 'Custom HTML template: ' . $args['message'];
    }
    return $args;
} );
```

### Add reCAPTCHA to Registration
```php
add_action( 'aurora_before_registration_otp', function() {
    // Verify reCAPTCHA token
    if ( ! verify_recaptcha( $_POST['recaptcha'] ) ) {
        wp_send_json_error( [ 'message' => 'reCAPTCHA verification failed.' ] );
    }
} );
```

---

## 11. Troubleshooting

**OTP emails not arriving?**
- Check spam/junk folder
- Verify SMTP plugin is configured and active
- Test with WP Mail SMTP's built-in test tool
- Check server error logs

**"Daily OTP limit reached" error?**
- Default is 5 per day per email
- Increase via `aurora_otp_settings` filter
- Reset by deleting rows from `{prefix}_aurora_otp_attempts`

**"Invalid or expired OTP" error?**
- OTPs expire after 10 minutes (default)
- Check system time/timezone settings
- Verify email address matches exactly
- User may have entered OTP incorrectly

**Registration not completing?**
- Check transient timeout (default 15 minutes)
- Verify user isn't already registered
- Check for WordPress errors in debug.log

---

## 12. File Structure

```
theme/
├── functions.php              # Includes inc/auth.php, adds nonces to JS
├── inc/
│   ├── auth.php              # Complete OTP/auth backend (NEW)
│   ├── admin-pages.php       # Admin panels, order management
│   └── customizer.php        # Theme customizer
└── woocommerce/
    └── myaccount/
        └── dashboard.php     # Account management UI
```

---

## Summary

The Aurora auth backend provides a **production-ready, secure OTP verification system** that:
- ✅ Creates users only after email verification
- ✅ Supports passwordless login
- ✅ Protects against abuse with rate limiting
- ✅ Integrates seamlessly with WordPress and WooCommerce
- ✅ Uses industry best practices (nonces, prepared statements, secure tokens)
- ✅ Is fully extensible via filters and hooks

**All AJAX handlers are registered and ready to use in your frontend forms.**
