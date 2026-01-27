# Aurora Authentication - Quick Setup Checklist

## Pre-Flight Check ✈️

- [ ] WordPress 5.0+ installed and active
- [ ] WooCommerce plugin installed (for My Account page)
- [ ] Aurora theme is active
- [ ] Have access to WordPress Admin dashboard
- [ ] Email delivery configured (SMTP/mail plugin)

---

## Backend Verification

- [ ] File `/theme/inc/auth.php` exists (617 lines)
- [ ] `functions.php` includes auth.php line 19:
  ```php
  require_once get_template_directory() . '/inc/auth.php';
  ```
- [ ] Database tables auto-created on first use:
  - [ ] `wp_aurora_otps`
  - [ ] `wp_aurora_otp_attempts`

---

## Asset Files

- [ ] CSS: `/theme/assets/css/auth.css` ✓ Created
- [ ] JavaScript: `/theme/assets/js/auth.js` ✓ Created
- [ ] Enqueued in `functions.php`:
  - [ ] `wp_enqueue_style( 'aurora-auth', ... )`
  - [ ] `wp_enqueue_script( 'aurora-auth', ... )`

---

## Frontend Template Pages

Create pages with these templates:

1. **Registration Page**
   - [ ] WordPress Page created (title: "Sign Up" or "Register")
   - [ ] Template: `template-registration.php`
   - [ ] Page ID: ________ (note for later)
   - [ ] Published
   - [ ] Publicly accessible

2. **Login Page**
   - [ ] WordPress Page created (title: "Sign In" or "Login")
   - [ ] Template: `template-login.php`
   - [ ] Page ID: ________ (note for later)
   - [ ] Published
   - [ ] Publicly accessible

3. **Forgot Password Page**
   - [ ] WordPress Page created (title: "Forgot Password")
   - [ ] Template: `template-forgot-password.php`
   - [ ] Page ID: ________ (note for later)
   - [ ] Published
   - [ ] Publicly accessible

---

## Navigation Setup

- [ ] Header navigation links updated:
  - [ ] "Sign In" link → Registration page (for guests)
  - [ ] "My Account" link → WooCommerce My Account (for logged-in)
  - [ ] "Sign Out" link → Uses `wp_logout_url()`
  
- [ ] Links within templates:
  - [ ] Registration → Login (footer link)
  - [ ] Login → Registration & Forgot Password (footer links)
  - [ ] Forgot Password → Login (footer link)

---

## Email Configuration

- [ ] Email service tested:
  - [ ] WP Mail SMTP OR
  - [ ] Easy WP SMTP OR
  - [ ] Server mail configured
  
- [ ] Test email sent from WordPress:
  - Admin → Tools → Send Test Email (if available)
  
- [ ] From email address set:
  - Admin → Settings → General → Email Address

---

## Nonce & Security

- [ ] Nonces generated in functions.php:
  - [ ] `aurora_auth_nonce` 
  - [ ] `aurora_profile_nonce`
  
- [ ] Passed to JavaScript via `wp_localize_script()`
  - [ ] Check browser console: `console.log(auroraTheme.authNonce)`

---

## Testing Workflows

### Test 1: User Registration
- [ ] Visit Sign Up page
- [ ] Form loads without errors
- [ ] Fill in: First Name, Last Name, Email, Password, agree to terms
- [ ] Click "Send Verification Code"
- [ ] Message: "OTP sent to your email"
- [ ] Check email inbox for OTP code
- [ ] Enter OTP (6 digits)
- [ ] Click "Create Account"
- [ ] Success message appears
- [ ] Redirected to My Account page
- [ ] New user appears in WordPress Users

### Test 2: Password Login
- [ ] Visit Login page
- [ ] Click "Email & Password" tab
- [ ] Enter registered email and password
- [ ] Click "Sign In"
- [ ] Redirected to My Account page
- [ ] Logged in successfully

### Test 3: OTP Login
- [ ] Visit Login page
- [ ] Click "Verification Code" tab
- [ ] Enter registered email
- [ ] Click "Send Verification Code"
- [ ] Message: "OTP sent to your email"
- [ ] Check email for OTP code
- [ ] Enter OTP
- [ ] Click "Sign In"
- [ ] Redirected to My Account page
- [ ] Logged in successfully

### Test 4: Forgot Password
- [ ] Visit Forgot Password page
- [ ] Enter registered email
- [ ] Click "Send Verification Code"
- [ ] Message: "OTP sent to your email"
- [ ] Check email for OTP code
- [ ] Enter OTP
- [ ] Click "Verify Code"
- [ ] New password form appears
- [ ] Enter new password and confirm
- [ ] Click "Reset Password"
- [ ] Success message appears
- [ ] Log out
- [ ] Log in with new password
- [ ] Successfully logged in

### Test 5: Rate Limiting
- [ ] Try requesting OTP more than 5 times in one day
- [ ] Should show: "Too many attempts. Try again tomorrow."
- [ ] Try requesting OTP within 60 seconds of previous request
- [ ] Should show: "Please wait before requesting another code"

---

## Browser/Mobile Testing

- [ ] Desktop (1920x1080 or larger)
  - [ ] Layout looks good
  - [ ] No horizontal scrolling
  - [ ] Buttons/forms are usable
  
- [ ] Tablet (768px width)
  - [ ] Responsive layout
  - [ ] Touch-friendly buttons
  - [ ] Form elements accessible
  
- [ ] Mobile (375px width)
  - [ ] Single-column layout
  - [ ] Large touch targets
  - [ ] No overflow issues
  - [ ] OTP input readable

---

## CSS/Style Verification

- [ ] Auth pages have blue header (--primary-color)
- [ ] Form inputs have focus states (blue border)
- [ ] Buttons have hover effects
- [ ] Error messages show in red
- [ ] Success messages show in green
- [ ] Loading spinner appears during requests
- [ ] Trust badges display below forms
- [ ] Consistent spacing and alignment

---

## JavaScript Console Check

- [ ] No errors in browser console (F12)
- [ ] Variables available:
  - [ ] `AuroraAuth` object exists
  - [ ] `auroraTheme.ajaxUrl` is set
  - [ ] `auroraTheme.authNonce` is set
- [ ] AJAX requests successful (Network tab → XHR)
- [ ] Responses valid JSON

---

## Admin Area Checks

- [ ] Users page shows new registered user
- [ ] User profile has:
  - [ ] Email verified
  - [ ] Registration date
  - [ ] User role (customer)
  
- [ ] WooCommerce customer list shows new customers
- [ ] No error logs related to auth

---

## Performance Checks

- [ ] Page load time < 3 seconds
- [ ] CSS/JS files loaded (check Network tab)
- [ ] No 404 errors for assets
- [ ] AJAX requests complete within 5 seconds
- [ ] No memory limit issues in error logs

---

## Security Audit

- [ ] HTTPS enabled (not critical for local, important for production)
- [ ] No hardcoded credentials in code
- [ ] Email addresses not exposed in HTML
- [ ] Password inputs have `type="password"`
- [ ] Forms have CSRF protection (nonces)
- [ ] AJAX endpoints check user capabilities
- [ ] Rate limiting prevents brute force

---

## Final Verification

- [ ] All templates load without PHP errors
- [ ] Database tables have data:
  - [ ] `wp_aurora_otps` has records after OTP requests
  - [ ] `wp_aurora_otp_attempts` tracks requests
  
- [ ] Email headers are configured:
  - [ ] From address correct
  - [ ] Subject lines clear
  - [ ] HTML formatting works
  
- [ ] Redirects work correctly:
  - [ ] After registration → My Account
  - [ ] After login → My Account
  - [ ] After logout → Home page
  - [ ] After password reset → Login page

---

## Customization Options

- [ ] Brand colors updated in CSS:
  - [ ] Primary color
  - [ ] Button colors
  - [ ] Link colors
  
- [ ] Email templates customized:
  - [ ] Subject lines match brand
  - [ ] OTP code formatting clear
  - [ ] Footer includes company info
  
- [ ] Settings configured:
  - [ ] OTP length (default 6)
  - [ ] OTP expiry time (default 10 min)
  - [ ] Max daily attempts (default 5)
  - [ ] Resend cooldown (default 60 sec)

---

## Go-Live Checklist

- [ ] All tests passed above ✓
- [ ] Staging environment working
- [ ] Production server configured
- [ ] Database backups created
- [ ] Email delivery tested in production
- [ ] HTTPS certificate installed
- [ ] SSL/TLS enabled
- [ ] WordPress security hardened:
  - [ ] Admin user renamed
  - [ ] Strong passwords set
  - [ ] Security plugins activated
  - [ ] Firewall configured
  
- [ ] Monitoring setup:
  - [ ] Error logging enabled
  - [ ] AJAX error tracking
  - [ ] User registration monitoring
  
- [ ] Documentation:
  - [ ] Provided AUTHENTICATION_INTEGRATION_GUIDE.md
  - [ ] Trained support team
  - [ ] FAQ document created

---

## Troubleshooting Shortcuts

**Issue**: "Invalid nonce" error
→ Clear cache, reload page, check console

**Issue**: OTP not received
→ Check SMTP settings, test email plugin, check spam folder

**Issue**: User not created
→ Check error logs, verify email is unique, check OTP timeout

**Issue**: Redirect not working
→ Verify My Account page exists, check dashboardUrl in wp_localize_script()

**Issue**: CSS not loading
→ Check file path in enqueue, verify .css file exists, clear cache

**Issue**: JavaScript errors
→ Open console (F12), check jQuery loaded, verify auroraTheme object

---

## Support Resources

- **Main Guide**: `AUTHENTICATION_INTEGRATION_GUIDE.md`
- **API Reference**: `AUTHENTICATION_SYSTEM_GUIDE.md`
- **Code Files**:
  - Backend: `theme/inc/auth.php`
  - Styling: `theme/assets/css/auth.css`
  - Frontend: `theme/assets/js/auth.js`
  - Templates: `theme/template-*.php`

---

**Status**: Ready for testing
**Date Started**: _________________
**Completion Date**: _________________
**Tested By**: _________________
**Go-Live Date**: _________________

---

✅ **Once all items are checked, your Aurora Authentication System is ready to use!**
