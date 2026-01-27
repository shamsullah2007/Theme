# Aurora Authentication System - Testing Guide

## Overview

This guide provides detailed test cases for the complete Aurora Authentication system. Follow each test case step-by-step to ensure all features work correctly.

---

## Test Environment Setup

### Prerequisites
- [ ] Aurora theme is active
- [ ] WordPress 5.0+ running
- [ ] WooCommerce installed and My Account page exists
- [ ] Email configured (test with any valid email)
- [ ] All files deployed (auth.php, auth.css, auth.js, templates)
- [ ] Browser DevTools available (F12)

### Test Accounts
Create these test accounts for comprehensive testing:

| Email | Password | Purpose |
|-------|----------|---------|
| test1@example.com | Test@123 | Registration flow |
| test2@example.com | Test@456 | Login flow |
| test3@example.com | Test@789 | OTP login flow |
| existing@example.com | Existing@123 | Duplicate email test |

---

## Test Suite 1: Registration Flow

### TC-1.1: Valid Registration with All Fields
**Objective**: Complete successful registration with all form fields

**Steps**:
1. Navigate to Sign Up page
2. Verify page loads without errors
3. Fill in form:
   - First Name: "John"
   - Last Name: "Doe"
   - Email: "test1@example.com"
   - Username: "johndoe" (optional)
   - Password: "Test@123456"
4. Check "I agree to terms and conditions"
5. Click "Send Verification Code" button

**Expected Results**:
- [ ] Button shows loading spinner
- [ ] Success message: "Verification code sent to your email"
- [ ] Form transitions to OTP verification
- [ ] Email received with 6-digit OTP code
- [ ] Resend button shows "Resend in 60s" timer

**Verification**:
```
✓ Success message displayed
✓ OTP input field visible
✓ Email contains OTP code
✓ Timer starts at 60 seconds
```

---

### TC-1.2: OTP Verification (Valid Code)
**Objective**: Complete registration by entering correct OTP

**Steps**:
1. Copy OTP code from email
2. Enter OTP in 6-digit input field
3. Click "Create Account" button

**Expected Results**:
- [ ] Button shows loading spinner
- [ ] Success message: "Account created successfully!"
- [ ] Automatic redirect to My Account page
- [ ] User logged in automatically
- [ ] New user appears in WordPress Users admin

**Verification**:
```
POST /wp-admin/admin-ajax.php
Action: aurora_complete_registration
Status: 200 OK
Response: {"success":true,"data":{"message":"..."}}
```

---

### TC-1.3: Invalid OTP Entry
**Objective**: Verify error handling for incorrect OTP

**Steps**:
1. In OTP field, enter: "999999" (wrong code)
2. Click "Create Account" button

**Expected Results**:
- [ ] Error message: "Invalid or expired code"
- [ ] Form remains on OTP step
- [ ] User not created
- [ ] OTP input cleared

**Verification**:
```
Response: {"success":false,"data":{"message":"Invalid code"}}
User not created in database
```

---

### TC-1.4: Expired OTP
**Objective**: Test OTP expiration (10 minutes default)

**Prerequisites**: 
- OTP from TC-1.1 is at least 10 minutes old

**Steps**:
1. Navigate back to Sign Up page
2. Fill in form again (same email)
3. Do NOT click "Send Verification Code" yet
4. Wait for previous OTP to expire (10 min)
5. Enter old OTP code
6. Click "Create Account"

**Expected Results**:
- [ ] Error message: "Code has expired"
- [ ] User not created

---

### TC-1.5: Missing Form Fields
**Objective**: Test form validation for required fields

**Test Case A: Missing First Name**
1. Leave First Name blank
2. Fill other fields correctly
3. Click "Send Verification Code"

**Expected Result**:
- [ ] Error: "First name is required"
- [ ] AJAX request not sent

**Test Case B: Missing Email**
1. Fill First Name, Last Name, Password
2. Leave Email blank
3. Click "Send Verification Code"

**Expected Result**:
- [ ] Error: "Valid email is required"
- [ ] AJAX request not sent

**Test Case C: Missing Password**
1. Fill Name, Email
2. Leave Password blank
3. Click "Send Verification Code"

**Expected Result**:
- [ ] Error: "Password must be at least 6 characters"

**Test Case D: Password Too Short**
1. Enter password: "123"
2. Click "Send Verification Code"

**Expected Result**:
- [ ] Error: "Password must be at least 6 characters"

**Test Case E: Terms Not Agreed**
1. Fill all fields
2. Do NOT check "I agree to terms"
3. Click "Send Verification Code"

**Expected Result**:
- [ ] Error: "You must agree to the terms and conditions"

---

### TC-1.6: Invalid Email Format
**Objective**: Verify email format validation

**Steps**:
1. First Name: "John"
2. Last Name: "Doe"
3. Email: "notanemail"
4. Click "Send Verification Code"

**Expected Result**:
- [ ] Error: "Valid email is required"
- [ ] AJAX not sent

**Test Variants**:
- Email: "test@"
- Email: "@example.com"
- Email: "test.example.com"
- Email: "test@.com"

---

### TC-1.7: Duplicate Email Registration
**Objective**: Prevent duplicate email registration

**Prerequisites**: Email "existing@example.com" already registered

**Steps**:
1. First Name: "Jane"
2. Last Name: "Smith"
3. Email: "existing@example.com"
4. Password: "Test@123"
5. Click "Send Verification Code"

**Expected Result**:
- [ ] Error: "Email already registered"
- [ ] OTP not sent
- [ ] New account not created

---

### TC-1.8: Back to Registration Button
**Objective**: Test returning from OTP step

**Steps**:
1. Fill registration form
2. Click "Send Verification Code"
3. Click "Back to Registration" button

**Expected Result**:
- [ ] Form returns to initial state
- [ ] Form fields cleared
- [ ] OTP input hidden
- [ ] Ready for new registration attempt

---

### TC-1.9: Resend OTP Code
**Objective**: Test OTP resend functionality

**Steps**:
1. Complete registration form
2. Send OTP
3. Wait for timer to complete (or click after 60s)
4. Click "Resend Code" button

**Expected Result**:
- [ ] New OTP sent to email
- [ ] New OTP code received
- [ ] Previous OTP code invalidated
- [ ] Timer resets to 60s
- [ ] Success message shown

**Edge Case - Multiple Resends**:
1. Send OTP (1st attempt)
2. Resend OTP (2nd attempt) - after 60s
3. Resend OTP (3rd attempt) - after 60s
4. Try to resend 4th time immediately

**Expected Result**:
- [ ] After 5 total attempts: "Too many attempts. Try again tomorrow."

---

### TC-1.10: Responsive Layout - Mobile
**Objective**: Verify registration form on mobile devices

**Setup**: Browser width 375px or less

**Steps**:
1. Navigate to Sign Up page
2. Verify layout and functionality

**Expected Results**:
- [ ] Form is single-column layout
- [ ] Input fields are full width
- [ ] Buttons are large (44px+ height) for touch
- [ ] Text is readable (no small fonts)
- [ ] No horizontal scrolling
- [ ] OTP input is clearly visible
- [ ] Messages display properly
- [ ] All interactive elements are touch-friendly

---

## Test Suite 2: Login Flow - Password Method

### TC-2.1: Valid Login with Email and Password
**Objective**: Successful login using email and password

**Prerequisites**: Test account test2@example.com / Test@456 exists

**Steps**:
1. Navigate to Login page
2. Verify "Email & Password" tab is active by default
3. Enter Email: "test2@example.com"
4. Enter Password: "Test@456"
5. Verify "Keep me signed in" option visible
6. Click "Sign In" button

**Expected Results**:
- [ ] Form submits to WordPress wp-login.php
- [ ] Session created
- [ ] Redirect to My Account page
- [ ] User logged in (verify in profile menu)
- [ ] Browser console shows no errors

**Verification**:
```
✓ User email verified in WP admin Users list
✓ Last login time updated
✓ User session active
```

---

### TC-2.2: Invalid Password Login
**Objective**: Verify error handling for incorrect password

**Steps**:
1. Email: "test2@example.com"
2. Password: "WrongPassword"
3. Click "Sign In"

**Expected Result**:
- [ ] Error message: "Invalid email or password"
- [ ] User not logged in
- [ ] Remain on login page

---

### TC-2.3: Non-existent Email Login
**Objective**: Verify error for non-existent account

**Steps**:
1. Email: "nonexistent@example.com"
2. Password: "Test@123"
3. Click "Sign In"

**Expected Result**:
- [ ] Error message: "Invalid email or password"
- [ ] User not logged in

---

### TC-2.4: Empty Field Validation
**Objective**: Validate required fields

**Test Case A: No Email**
1. Email field: empty
2. Password: "Test@456"
3. Submit form

**Expected Result**: Form HTML5 validation triggers

**Test Case B: No Password**
1. Email: "test2@example.com"
2. Password field: empty
3. Submit form

**Expected Result**: Form HTML5 validation triggers

---

### TC-2.5: Remember Me Option
**Objective**: Test persistent login option

**Steps**:
1. Email: "test2@example.com"
2. Password: "Test@456"
3. Check "Keep me signed in"
4. Click "Sign In"
5. Close browser
6. Reopen website

**Expected Result**:
- [ ] User still logged in after browser close
- [ ] Session cookie set with extended expiry
- [ ] User authenticated without re-login

---

### TC-2.6: Forgot Password Link
**Objective**: Verify Forgot Password link from Login page

**Steps**:
1. On Login page
2. Click "Forgot Password?" link

**Expected Result**:
- [ ] Redirect to Forgot Password page
- [ ] Page loads without errors

---

---

## Test Suite 3: Login Flow - OTP Method

### TC-3.1: Valid OTP Login
**Objective**: Complete login using OTP verification

**Prerequisites**: Test account test3@example.com exists

**Steps**:
1. Navigate to Login page
2. Click "Verification Code" tab
3. Enter Email: "test3@example.com"
4. Click "Send Verification Code"

**Expected Results**:
- [ ] Tab switches to OTP tab
- [ ] Button shows loading spinner
- [ ] Success message: "Verification code sent to your email"
- [ ] OTP input field becomes visible
- [ ] Resend timer starts (60s)
- [ ] Email received with OTP

**Step 2: Verify OTP**
5. Copy OTP from email
6. Enter OTP in 6-digit field
7. Click "Sign In" button

**Expected Results**:
- [ ] Button shows loading spinner
- [ ] Success message: "Logged in successfully"
- [ ] Redirect to My Account page
- [ ] User logged in
- [ ] Session created

---

### TC-3.2: Invalid OTP for Login
**Objective**: Error handling for wrong OTP

**Steps**:
1. Request login OTP (as above)
2. Enter "999999" as OTP
3. Click "Sign In"

**Expected Result**:
- [ ] Error: "Invalid or expired code"
- [ ] User not logged in
- [ ] Remain on OTP field

---

### TC-3.3: Tab Switching
**Objective**: Test switching between login methods

**Steps**:
1. On Login page
2. Click "Email & Password" tab
3. Verify form switches to password form
4. Click "Verification Code" tab
5. Verify form switches to OTP form

**Expected Result**:
- [ ] Forms switch smoothly
- [ ] Tab indication (underline) updates
- [ ] Previous form data clears
- [ ] No console errors

---

### TC-3.4: OTP Expired During Input
**Objective**: Test OTP expiry while user is entering code

**Prerequisites**: Modify OTP expiry to 30 seconds for testing

**Steps**:
1. Request OTP
2. Wait 35 seconds
3. Enter OTP
4. Click "Sign In"

**Expected Result**:
- [ ] Error: "Code has expired. Please request a new one."

---

### TC-3.5: Rate Limiting - Daily Attempts
**Objective**: Verify 5 requests per day limit

**Steps**:
1. Request OTP (1st time)
2. Wait 65 seconds
3. Request OTP again (2nd time)
4. Repeat until 5 total requests made
5. Request OTP 6th time immediately

**Expected Result**:
- [ ] Requests 1-5: Success
- [ ] Request 6: Error "Too many attempts. Try again tomorrow."
- [ ] Attempts reset next day (in dev: after 24 hours)

---

### TC-3.6: Rate Limiting - Resend Cooldown
**Objective**: Verify 60-second cooldown between requests

**Steps**:
1. Request OTP (sent successfully)
2. Immediately click "Resend Code" button
3. Wait 30 seconds
4. Click "Resend Code" again
5. Wait 30 more seconds (total 60s)
6. Click "Resend Code" again

**Expected Result**:
- [ ] Request 2 (immediate): Error "Please wait 60 seconds..."
- [ ] Request 3 (after 30s): Error "Please wait..."
- [ ] Request 4 (after 60s): Success

---

## Test Suite 4: Forgot Password Flow

### TC-4.1: Complete Password Reset Flow
**Objective**: Successfully reset password using OTP

**Steps - Step 1: Email**:
1. Navigate to Forgot Password page
2. Verify page loads
3. Enter Email: "test2@example.com"
4. Click "Send Verification Code"

**Expected Results**:
- [ ] Button shows loading spinner
- [ ] Success: "Verification code sent to your email"
- [ ] OTP field becomes visible
- [ ] Resend timer starts
- [ ] Email received with OTP

**Steps - Step 2: Verify OTP**:
5. Enter OTP from email
6. Click "Verify Code"

**Expected Results**:
- [ ] OTP field hidden
- [ ] Password reset form appears
- [ ] Success message: "Code verified"
- [ ] Focus on password field

**Steps - Step 3: Reset Password**:
7. New Password: "NewPass@123456"
8. Confirm Password: "NewPass@123456"
9. Click "Reset Password"

**Expected Results**:
- [ ] Success message: "Password reset successfully"
- [ ] Success screen with checkmark icon
- [ ] "Back to Sign In" button visible
- [ ] Password changed in database

**Verification**:
1. Click "Back to Sign In"
2. Log in with old password - should fail
3. Log in with new password - should succeed

---

### TC-4.2: Password Mismatch
**Objective**: Verify password confirmation validation

**Steps**:
1. Go through steps 1-2 (get OTP)
2. New Password: "NewPass@123456"
3. Confirm Password: "DifferentPass@123456"
4. Click "Reset Password"

**Expected Result**:
- [ ] Error: "Passwords do not match"
- [ ] Form remains on password step
- [ ] Password not changed

---

### TC-4.3: Invalid OTP on Reset
**Objective**: Error handling for wrong OTP

**Steps**:
1. Email: "test2@example.com"
2. Click "Send Verification Code"
3. Enter "999999" as OTP
4. Click "Verify Code"

**Expected Result**:
- [ ] Error: "Invalid or expired code"
- [ ] Remain on OTP field
- [ ] Option to "Use Different Email"

---

### TC-4.4: Use Different Email
**Objective**: Return to email entry and try different account

**Steps**:
1. On OTP entry step
2. Click "Use Different Email" button
3. Enter different email
4. Send new OTP

**Expected Result**:
- [ ] Email field clears
- [ ] New OTP sent to different email
- [ ] Resend timer resets

---

### TC-4.5: Short Password
**Objective**: Verify password length requirement

**Steps**:
1. Complete OTP verification (Step 2)
2. New Password: "Pass1"
3. Confirm Password: "Pass1"
4. Click "Reset Password"

**Expected Result**:
- [ ] Error: "Password must be at least 6 characters"

---

### TC-4.6: Success Redirect
**Objective**: Verify redirect after successful reset

**Steps**:
1. Complete full password reset flow
2. Success screen appears
3. Wait 3-5 seconds OR click "Back to Sign In"

**Expected Result**:
- [ ] Redirect to Login page (if waited)
- [ ] OR manual redirect on button click
- [ ] Can log in with new password

---

## Test Suite 5: Security & Rate Limiting

### TC-5.1: Brute Force Protection - Email Enumeration
**Objective**: Verify users cannot enumerate registered emails

**Test: Multiple invalid attempts**:
1. Try login with 10 different non-existent emails
2. Error message should be generic

**Expected Result**:
- [ ] All errors show: "Invalid email or password"
- [ ] No indication whether email exists
- [ ] No IP blocking (yet)

---

### TC-5.2: Brute Force Protection - Password Attempts
**Objective**: Verify protection against password guessing

**Setup**: Configure rate limiting in auth.php

**Steps**:
1. Attempt login with correct email
2. Try 5+ wrong passwords rapidly

**Expected Result**:
- [ ] After N attempts: Temporary lockout
- [ ] Error: "Too many failed attempts. Try again in X minutes."
- [ ] Session IP logged

---

### TC-5.3: CSRF Protection - Nonce Validation
**Objective**: Verify AJAX requests are nonce-protected

**Steps**:
1. Open DevTools (F12)
2. Go to Network tab
3. Make AJAX request (e.g., request OTP)
4. Inspect request body

**Expected Result**:
```
POST data includes:
- action: aurora_request_registration_otp
- nonce: [valid WordPress nonce]
- Other form fields
```

**Verification - Invalid Nonce**:
1. Manually modify nonce in JavaScript
2. Send OTP request
3. Should fail with "Invalid nonce"

---

### TC-5.4: SQL Injection Prevention
**Objective**: Verify inputs are sanitized

**Test Case 1: Email field**:
1. Email: "test' OR '1'='1"
2. Try to register

**Expected Result**:
- [ ] Error: "Valid email is required"
- [ ] SQL injection prevented

**Test Case 2: Password field**:
1. Password: "Pass123'; DROP TABLE users; --"
2. Try to register

**Expected Result**:
- [ ] Account created with literal password
- [ ] No SQL executed
- [ ] Database tables intact

---

### TC-5.5: XSS Prevention
**Objective**: Verify JavaScript injection is prevented

**Test Case 1: Email field**:
1. Email: "<script>alert('xss')</script>@test.com"
2. Try to register

**Expected Result**:
- [ ] Invalid email error
- [ ] No alert appears
- [ ] Input escaped in database

**Test Case 2: Name field**:
1. First Name: "<img src=x onerror=alert('xss')>"
2. Try to register

**Expected Result**:
- [ ] Stored safely in database
- [ ] Displayed as plain text (not HTML)
- [ ] No alert appears

---

### TC-5.6: Password Hashing
**Objective**: Verify passwords are hashed, not stored plaintext

**Steps**:
1. Register user with password "TestPassword123"
2. Check WordPress database: wp_users table

**Expected Result**:
```
In wp_users.user_pass:
✓ NOT: TestPassword123
✓ IS: $P$... (WordPress hash format)
✓ Hash is salted and unique
```

---

## Test Suite 6: Email Delivery

### TC-6.1: Registration OTP Email
**Objective**: Verify email format and delivery

**Email Content Check**:
1. Subject line includes: "verification code" or similar
2. Body includes:
   - [ ] 6-digit OTP code clearly displayed
   - [ ] Expiration time (10 minutes)
   - [ ] Note not to share code
   - [ ] Company name/branding
3. HTML formatting looks good
4. Code is readable

**Email Metadata Check**:
- [ ] From: WordPress admin email
- [ ] To: Correct user email
- [ ] Reply-To: Business email
- [ ] Content-Type: text/html

---

### TC-6.2: Login OTP Email
**Objective**: Verify login OTP email

**Email Content**:
1. Subject line includes: "verification code" or "sign in"
2. Body includes:
   - [ ] 6-digit OTP code
   - [ ] Expiration time
   - [ ] Security note
3. Clearly states this is for login

---

### TC-6.3: Password Reset Email
**Objective**: Verify password reset email

**Email Content**:
1. Subject line: "reset password" or similar
2. Body includes:
   - [ ] 6-digit OTP code
   - [ ] Instructions to reset password
   - [ ] Security notice
   - [ ] Link back to reset page (optional)

---

### TC-6.4: Email Delivery Failure
**Objective**: Test behavior when email cannot be sent

**Setup**: Disable email sending temporarily

**Steps**:
1. Try to register
2. Check for error message

**Expected Result**:
- [ ] Error: "Unable to send verification code. Please try again."
- [ ] User data not stored
- [ ] Clear error message to user

---

## Test Suite 7: Mobile & Responsive

### TC-7.1: Mobile (375px width)
**Objective**: Full functionality on mobile devices

**Test All Workflows On**:
- [ ] iPhone SE (375x667)
- [ ] iPhone XR (414x896)
- [ ] Android (360x800)

**Checks**:
- [ ] Single-column layout
- [ ] Touch targets ≥44px
- [ ] No horizontal scroll
- [ ] Text readable
- [ ] Forms fully functional
- [ ] Buttons clickable
- [ ] Messages display clearly

---

### TC-7.2: Tablet (768px width)
**Objective**: Tablet experience

**Test On**:
- [ ] iPad (768x1024)
- [ ] Android tablet

**Checks**:
- [ ] Two-column layout (if applicable)
- [ ] Touch-friendly spacing
- [ ] Buttons appropriately sized
- [ ] Full functionality works

---

### TC-7.3: Desktop (1920px width)
**Objective**: Desktop experience

**Checks**:
- [ ] Card centered on page
- [ ] Max-width applied (≤500px)
- [ ] Proper spacing around content
- [ ] All elements accessible

---

## Test Suite 8: Browser Compatibility

### Supported Browsers
Test on:
- [ ] Chrome/Chromium (latest 2 versions)
- [ ] Firefox (latest 2 versions)
- [ ] Safari (latest 2 versions)
- [ ] Edge (latest 2 versions)

### Checks for Each Browser
- [ ] Page loads without errors
- [ ] Styling renders correctly
- [ ] Forms functional
- [ ] AJAX requests work
- [ ] Email input accepts @ symbol
- [ ] OTP input accepts numbers
- [ ] Loading spinners animate
- [ ] Messages display properly
- [ ] Redirects work

---

## Test Suite 9: Performance

### TC-9.1: Page Load Time
**Objective**: Verify acceptable load times

**Measurement**:
1. Open DevTools Performance tab
2. Load auth page
3. Measure:
   - DOM Content Loaded
   - Full page load

**Expected Results**:
- [ ] DOM Content Loaded: < 1 second
- [ ] Full load: < 3 seconds
- [ ] First Paint: < 800ms

---

### TC-9.2: AJAX Request Time
**Objective**: Verify AJAX responses are fast

**Steps**:
1. Open Network tab
2. Make AJAX request (e.g., request OTP)
3. Check request time

**Expected Result**:
- [ ] Request time: < 2 seconds
- [ ] Response time: < 1 second
- [ ] Status: 200 OK

---

### TC-9.3: CSS Load Time
**Objective**: Verify CSS doesn't block rendering

**Steps**:
1. Performance tab → Styles
2. Check CSS loading

**Expected Result**:
- [ ] CSS loads asynchronously
- [ ] No render-blocking
- [ ] First paint before CSS complete

---

## Test Suite 10: Accessibility

### TC-10.1: Keyboard Navigation
**Objective**: Full functionality with keyboard only

**Steps**:
1. Start with Tab key (don't use mouse)
2. Navigate through form:
   - Tab to each input
   - Enter data
   - Tab to button
   - Submit with Enter key

**Expected Result**:
- [ ] All inputs accessible via Tab
- [ ] Submit with Enter key works
- [ ] Focus indicators visible
- [ ] Logical tab order

---

### TC-10.2: Screen Reader Compatibility
**Objective**: Form usable with screen readers

**Tools**: NVDA (Windows), VoiceOver (Mac)

**Steps**:
1. Enable screen reader
2. Navigate registration form
3. Verify announcements

**Expected Result**:
- [ ] Form labels announced
- [ ] Field types announced (text, email, password)
- [ ] Error messages announced
- [ ] Success messages announced
- [ ] Button purpose clear

---

### TC-10.3: Color Contrast
**Objective**: Text readable for users with vision issues

**Tool**: WebAIM Contrast Checker

**Checks**:
- [ ] Button text on button background: ≥4.5:1
- [ ] Form labels vs background: ≥4.5:1
- [ ] Error message text: ≥4.5:1
- [ ] Link text: ≥4.5:1

---

## Test Suite 11: Edge Cases

### TC-11.1: Concurrent Requests
**Objective**: Handle rapid form submission

**Steps**:
1. Fill registration form
2. Click "Send Verification Code" multiple times rapidly

**Expected Result**:
- [ ] Only 1 OTP generated
- [ ] Button disabled during request
- [ ] No duplicate entries

---

### TC-11.2: Network Failure During OTP Request
**Objective**: Test offline/network error handling

**Setup**: DevTools → Throttling → Offline

**Steps**:
1. Try to request OTP with offline connection

**Expected Result**:
- [ ] Error: "Network error"
- [ ] Button re-enabled
- [ ] User can retry

---

### TC-11.3: Long Email Addresses
**Objective**: Handle maximum length emails

**Steps**:
1. Email: "veryverylongemailaddress+withextensiontesting@subdomain.example.co.uk"
2. Try to register

**Expected Result**:
- [ ] Accepted (if valid)
- [ ] Stored correctly
- [ ] OTP sent successfully

---

### TC-11.4: Special Characters in Password
**Objective**: Support special characters

**Steps**:
1. Password: "P@ss!w0rd#123&More"
2. Register and login

**Expected Result**:
- [ ] Password accepted
- [ ] Login works
- [ ] Characters handled safely

---

### TC-11.5: Unicode Characters in Name
**Objective**: Support international names

**Steps**:
1. First Name: "José"
2. Last Name: "García"
3. Register

**Expected Result**:
- [ ] Accepted
- [ ] Stored correctly
- [ ] Displays properly

---

## Test Suite 12: Integration

### TC-12.1: WooCommerce Integration
**Objective**: Verify integration with WooCommerce

**Steps**:
1. Register via auth system
2. Check WooCommerce Customers list
3. Verify user appears

**Expected Result**:
- [ ] User listed as Customer
- [ ] Email correct
- [ ] Creation date correct
- [ ] User can purchase products

---

### TC-12.2: WordPress Admin Integration
**Objective**: Registered users appear in WordPress

**Steps**:
1. Register via auth system
2. Go to WordPress Admin → Users

**Expected Result**:
- [ ] User listed
- [ ] Email correct
- [ ] Role: "Customer" (or "Subscriber")
- [ ] Registration date correct
- [ ] User profile complete

---

### TC-12.3: Menu Visibility
**Objective**: Navigation links update based on login status

**Test Case - Guest**:
1. Logged out
2. Check header/menu

**Expected Result**:
- [ ] Sign In link visible
- [ ] Register link visible
- [ ] My Account link NOT visible

**Test Case - Logged In**:
1. Log in
2. Check header/menu

**Expected Result**:
- [ ] Sign In link NOT visible (or hidden)
- [ ] My Account link visible
- [ ] Sign Out link visible

---

## Test Suite 13: Database

### TC-13.1: Table Creation
**Objective**: Verify database tables auto-created

**Steps**:
1. First time OTP is requested
2. Check database

**Expected Result**:
- [ ] Table `wp_aurora_otps` created
- [ ] Table `wp_aurora_otp_attempts` created
- [ ] Proper indexes created
- [ ] Columns have correct types

---

### TC-13.2: OTP Data Storage
**Objective**: Verify OTP is stored correctly

**Steps**:
1. Request OTP
2. Check `wp_aurora_otps` table

**Expected Result**:
```sql
SELECT * FROM wp_aurora_otps WHERE email = 'test@example.com';
```
Results show:
- [ ] otp_code: 6-digit number
- [ ] email: correct email
- [ ] action_type: "registration" or "login"
- [ ] created_at: recent timestamp
- [ ] expires_at: 10 minutes from created_at
- [ ] is_used: 0 (until verified)

---

### TC-13.3: Attempt Tracking
**Objective**: Verify attempt counts tracked

**Steps**:
1. Request OTP 3 times
2. Check `wp_aurora_otp_attempts` table

**Expected Result**:
```sql
SELECT * FROM wp_aurora_otp_attempts WHERE email = 'test@example.com';
```
Results show:
- [ ] attempt_date: today's date
- [ ] attempt_count: 3
- [ ] Increments on each request

---

## Test Results Summary

### Overall Status
- [ ] All Critical tests PASSED
- [ ] All High priority tests PASSED
- [ ] All Medium priority tests PASSED
- [ ] No critical bugs found

### Known Issues
(List any issues found during testing)
1. ________________
2. ________________

### Recommendations
(List any improvements or enhancements)
1. ________________
2. ________________

---

## Sign-Off

**Tested By**: _____________________
**Date**: _____________________
**Environment**: _____________________
**Bugs Found**: _____ (Critical: _____, High: _____, Medium: _____)
**Status**: [ ] PASSED [ ] FAILED [ ] CONDITIONAL PASS

**Comments**:
_________________________________________________________________
_________________________________________________________________

---

**The Aurora Authentication System is ready for production deployment!** 🚀
