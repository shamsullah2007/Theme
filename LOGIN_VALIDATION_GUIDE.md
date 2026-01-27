# Login Page Validation & Testing Guide

## Overview
The login page now has an enhanced authentication flow with OTP-based password recovery. Users can:
1. Login with email & password (standard)
2. Login with OTP verification code (no password needed)
3. **NEW**: After OTP verification, reset password OR skip and login directly

---

## Updated Features

### 1. Standard Password Login
- **Tab**: "Email & Password"
- **Flow**: Email → Password → Sign In
- **Forgot Password Link**: Routes to password reset page
- **Status**: ✅ Unchanged

### 2. OTP Verification Login
- **Tab**: "Verification Code"
- **New Enhanced Flow**:
  ```
  Email 
    ↓
  Send Code (OTP requested)
    ↓
  Enter OTP Code
    ↓
  Verify & Continue (OTP verified)
    ↓
  [PASSWORD RESET SECTION SHOWN]
    ↓
  Option A: Set New Password & Sign In
    OR
  Option B: Skip & Sign In Now
  ```

---

## Test Scenarios

### Scenario 1: OTP Login with Password Reset
**Objective**: Verify user can login via OTP and set a new password

**Steps**:
1. Go to login page → "Verification Code" tab
2. Enter email address (must have account)
3. Click "Send Code"
4. Check inbox for OTP email
5. Enter 6-digit OTP code
6. Click "Verify & Continue"
7. **Expected**: Password reset section appears with two options
8. Click "Set New Password"
9. Enter new password (minimum 8 characters)
10. Confirm password
11. Click "Set Password & Sign In"
12. **Expected**: Password is updated and user is logged in → redirect to dashboard

**Validation Points**:
- ✅ OTP email received
- ✅ OTP code validates correctly
- ✅ Password reset section displays after OTP verification
- ✅ New password is accepted and user logs in
- ✅ Session is established (user stays logged in)

---

### Scenario 2: OTP Login with Skip Password Reset
**Objective**: Verify user can login via OTP without resetting password

**Steps**:
1. Go to login page → "Verification Code" tab
2. Enter email address
3. Click "Send Code"
4. Check inbox for OTP email
5. Enter 6-digit OTP code
6. Click "Verify & Continue"
7. **Expected**: Password reset section appears
8. Click "Skip & Sign In Now"
9. **Expected**: User is logged in without password change → redirect to dashboard

**Validation Points**:
- ✅ OTP verified successfully
- ✅ "Skip & Sign In Now" button works
- ✅ User logs in without password change
- ✅ Session is established
- ✅ Previous password remains unchanged

---

### Scenario 3: OTP Login Password Reset - Toggle Form
**Objective**: Verify user can toggle password reset form

**Steps**:
1. Complete OTP verification (Scenario 1 steps 1-6)
2. Click "Set New Password"
3. **Expected**: Password form appears
4. Click "Skip for now"
5. **Expected**: Form closes, original buttons show again
6. Click "Skip & Sign In Now"
7. **Expected**: User logs in without password change

**Validation Points**:
- ✅ Password form toggle works smoothly
- ✅ User can switch between options multiple times
- ✅ Skip button correctly collapses the form

---

### Scenario 4: Invalid/Expired OTP
**Objective**: Verify error handling for invalid OTP

**Steps**:
1. Go to login page → "Verification Code" tab
2. Enter email
3. Click "Send Code"
4. Enter wrong OTP (e.g., 000000 if received different code)
5. Click "Verify & Continue"
6. **Expected**: Error message appears: "Invalid or expired OTP"

**Validation Points**:
- ✅ Invalid OTP rejected with clear error
- ✅ User stays on OTP entry form
- ✅ Can retry with correct code

---

### Scenario 5: Password Requirements Validation
**Objective**: Verify password validation during reset

**Steps**:
1. Complete OTP verification (Scenario 1 steps 1-6)
2. Click "Set New Password"
3. Enter password less than 8 characters (e.g., "pass123")
4. Click "Set Password & Sign In"
5. **Expected**: Error message: "Password must be at least 8 characters"
6. Enter valid password (8+ characters)
7. Confirm with different password
8. **Expected**: Error message: "Passwords do not match"
9. Confirm with matching password
10. Click "Set Password & Sign In"
11. **Expected**: Password set successfully, user logs in

**Validation Points**:
- ✅ Minimum 8 character requirement enforced
- ✅ Password confirmation validation works
- ✅ Clear error messages displayed
- ✅ Valid password accepted

---

### Scenario 6: Session Timeout
**Objective**: Verify temporary token expires

**Steps**:
1. Complete OTP verification (Scenario 1 steps 1-6)
2. Wait 10 minutes (token expiration time)
3. Try to click "Set Password & Sign In"
4. **Expected**: Error message: "Session expired. Please verify OTP again."

**Validation Points**:
- ✅ Temporary tokens have 10-minute expiration
- ✅ User must re-verify OTP if session expires
- ✅ Clear error message about session expiration

---

### Scenario 7: Non-existent Email
**Objective**: Verify handling of non-existent email accounts

**Steps**:
1. Go to login page → "Verification Code" tab
2. Enter email that doesn't have account
3. Click "Send Code"
4. **Expected**: Error message: "No account found for that email."

**Validation Points**:
- ✅ Non-existent emails rejected
- ✅ Clear error message
- ✅ No OTP sent for non-existent accounts

---

### Scenario 8: Resend OTP Code
**Objective**: Verify OTP can be resent

**Steps**:
1. Go to login page → "Verification Code" tab
2. Enter email
3. Click "Send Code"
4. Wait for resend button to activate (60 seconds)
5. Click "Resend in 60s" (when countdown finishes)
6. **Expected**: New OTP sent, countdown resets to 60s
7. Check inbox for new OTP
8. Enter new OTP
9. Click "Verify & Continue"
10. **Expected**: OTP verified successfully

**Validation Points**:
- ✅ 60-second cooldown before resend
- ✅ New OTP sent and valid
- ✅ New OTP works for verification
- ✅ Countdown timer resets after resend

---

### Scenario 9: Tab Switching
**Objective**: Verify form state when switching between tabs

**Steps**:
1. Go to "Verification Code" tab
2. Enter email
3. Click "Send Code"
4. **Expected**: OTP section shows
5. Switch to "Email & Password" tab
6. **Expected**: OTP section hidden, standard form shown
7. Switch back to "Verification Code" tab
8. **Expected**: OTP section visible again

**Validation Points**:
- ✅ Tab switching works smoothly
- ✅ Form state preserved when switching
- ✅ No data loss during tab switch

---

## Success Criteria Checklist

- [ ] User can login with email + password (standard flow)
- [ ] User can request OTP for login
- [ ] OTP email is received within seconds
- [ ] User can verify OTP and see password reset section
- [ ] User can set new password and login
- [ ] User can skip password reset and login directly
- [ ] Password must be at least 8 characters
- [ ] Passwords must match when resetting
- [ ] Invalid OTP shows error message
- [ ] Non-existent email shows error message
- [ ] Session timeout works after 10 minutes
- [ ] OTP can be resent after 60-second cooldown
- [ ] Tab switching doesn't lose data
- [ ] All buttons show loading states during AJAX
- [ ] Error and success messages display correctly

---

## Browser Compatibility
Test the following:
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

## Security Considerations

1. **OTP Verification**
   - OTP tokens expire after 10 minutes
   - Temporary session tokens expire after 10 minutes
   - OTP is single-use (consumed upon verification)

2. **Password Reset**
   - Requires verified OTP first
   - Minimum 8 character requirement
   - No plain-text password transmission

3. **Session Management**
   - Auth cookies set with WordPress standards
   - Cookies use secure flags (HTTPS in production)

---

## Known Limitations & Future Enhancements

1. **Current Behavior**
   - OTP valid for 10 minutes
   - Resend cooldown: 60 seconds
   - Password minimum: 8 characters

2. **Suggested Improvements**
   - Add SMS OTP option alongside email
   - Implement passwordless authentication option
   - Add login attempt rate limiting
   - Add 2FA for enhanced security

---

## Troubleshooting

### Issue: OTP Email Not Received
**Solution**:
- Check email spam folder
- Verify SMTP settings in [inc/auth.php](../../theme/inc/auth.php)
- Check server error logs

### Issue: "Session expired" Error After OTP
**Solution**:
- Temporary token valid for 10 minutes
- User must complete password reset or skip within this window
- If exceeded, user must verify OTP again

### Issue: Password Reset Not Working
**Solution**:
- Ensure password is at least 8 characters
- Check that passwords match
- Verify session is still active (not expired)

---

## Files Modified

1. **Template**: [template-login.php](../../theme/template-login.php)
   - Added password reset section after OTP verification
   - Added toggle buttons for password form

2. **JavaScript**: [assets/js/auth.js](../../theme/assets/js/auth.js)
   - Updated `loginWithOTP()` to verify OTP only (without auto-login)
   - Added `skipPasswordResetAndLogin()` function
   - Added `resetPasswordAndLogin()` function
   - Updated `initLogin()` with new event handlers

3. **PHP Backend**: [inc/auth.php](../../theme/inc/auth.php)
   - Added `aurora_verify_login_otp_only_ajax()` - verify OTP without login
   - Added `aurora_login_with_verified_otp_ajax()` - login after OTP verification
   - Added `aurora_reset_password_and_login_ajax()` - reset password and login

---

## Testing Commands (for developers)

### Test OTP Verification
```javascript
// In browser console
AuroraAuth.loginWithOTP();
```

### Check Session Storage
```javascript
console.log(sessionStorage.getItem('aurora_otp_verified_email'));
console.log(sessionStorage.getItem('aurora_otp_verified_token'));
```

### Check Temporary Token
```php
// In PHP (for testing)
$temp_token = get_transient( 'aurora_otp_verified_' . $user_id );
```

---

## Deployment Notes

1. Clear browser cache after deployment
2. Test on staging environment first
3. Verify SMTP email configuration
4. Check WordPress error logs for any issues
5. Monitor user feedback for edge cases

