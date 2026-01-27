<?php
/**
 * My Account Dashboard
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="aurora-my-account-dashboard">
    <div class="my-account-container">
        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-card">
                <div class="profile-image-container">
                    <img id="profile-image-display" class="profile-image" src="<?php echo esc_url( aurora_get_user_profile_image( get_current_user_id() ) ); ?>" alt="Profile">
                    <label for="profile-image-upload" class="upload-button">
                        <span class="upload-icon">📷</span>
                        <span class="upload-text"><?php esc_html_e( 'Change Photo', 'aurora' ); ?></span>
                    </label>
                    <input type="file" id="profile-image-upload" class="hidden-upload" accept="image/*">
                </div>

                <div class="profile-info">
                    <h2><?php echo esc_html( wp_get_current_user()->display_name ); ?></h2>
                    <p class="user-email"><?php echo esc_html( wp_get_current_user()->user_email ); ?></p>
                    <p class="member-since">
                        <?php 
                        $user = wp_get_current_user();
                        $user_registered = new DateTime( $user->user_registered );
                        echo sprintf(
                            esc_html__( 'Member since %s', 'aurora' ),
                            esc_html( $user_registered->format( 'F Y' ) )
                        );
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Account Settings Section -->
        <div class="settings-section">
            <div class="settings-card">
                <h3><?php esc_html_e( 'Account Settings', 'aurora' ); ?></h3>

                <!-- Tabs -->
                <div class="settings-tabs">
                    <button class="tab-button active" data-tab="email"><?php esc_html_e( 'Email Address', 'aurora' ); ?></button>
                    <button class="tab-button" data-tab="password"><?php esc_html_e( 'Password', 'aurora' ); ?></button>
                </div>

                <!-- Email Tab -->
                <div id="email-tab" class="tab-content active">
                    <div class="setting-item">
                        <label><?php esc_html_e( 'Current Email', 'aurora' ); ?></label>
                        <input type="email" class="form-input" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" disabled>
                    </div>

                    <button class="button button-primary" id="change-email-btn" style="display: block; margin-top: 15px;"><?php esc_html_e( 'Change Email Address', 'aurora' ); ?></button>

                    <div id="email-change-form" style="display: none; margin-top: 20px;">
                        <div class="setting-item">
                            <label><?php esc_html_e( 'New Email', 'aurora' ); ?></label>
                            <input type="email" id="new-email" class="form-input" placeholder="<?php esc_attr_e( 'Enter new email address', 'aurora' ); ?>">
                        </div>

                        <div id="email-otp-section" style="display: none; margin-top: 15px;">
                            <div class="setting-item">
                                <label><?php esc_html_e( 'Enter OTP (sent to your current email)', 'aurora' ); ?></label>
                                <input type="text" id="email-otp-code" class="form-input" placeholder="<?php esc_attr_e( '000000', 'aurora' ); ?>" maxlength="6">
                            </div>
                        </div>

                        <button class="button button-primary" id="change-email-submit"><?php esc_html_e( 'Change Email Address', 'aurora' ); ?></button>
                        <button class="button" id="cancel-email-change"><?php esc_html_e( 'Cancel', 'aurora' ); ?></button>
                    </div>
                </div>

                <!-- Password Tab -->
                <div id="password-tab" class="tab-content" style="display: none;">
                    <div id="password-change-form">
                        <div class="setting-item">
                            <label><?php esc_html_e( 'Current Password', 'aurora' ); ?></label>
                            <input type="password" id="current-password" class="form-input" placeholder="<?php esc_attr_e( 'Enter your current password', 'aurora' ); ?>">
                        </div>

                        <div class="setting-item">
                            <label><?php esc_html_e( 'New Password', 'aurora' ); ?></label>
                            <input type="password" id="new-password" class="form-input" placeholder="<?php esc_attr_e( 'Enter new password', 'aurora' ); ?>">
                            <small><?php esc_html_e( 'Password must be at least 8 characters long', 'aurora' ); ?></small>
                        </div>

                        <button class="button button-primary" id="send-password-otp"><?php esc_html_e( 'Send OTP', 'aurora' ); ?></button>

                        <div id="password-otp-section" style="display: none; margin-top: 15px;">
                            <div class="setting-item">
                                <label><?php esc_html_e( 'Enter OTP (sent to your email)', 'aurora' ); ?></label>
                                <input type="text" id="password-otp-code" class="form-input" placeholder="<?php esc_attr_e( '000000', 'aurora' ); ?>" maxlength="6">
                            </div>
                            <button class="button button-success" id="verify-password-otp"><?php esc_html_e( 'Verify & Update Password', 'aurora' ); ?></button>
                        </div>
                    </div>

                    <div id="forgot-password-section" style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #e5e7eb;">
                        <h4><?php esc_html_e( 'Forgot Password?', 'aurora' ); ?></h4>
                        <p><?php esc_html_e( 'If you forgot your current password, we can help you reset it.', 'aurora' ); ?></p>
                        <button class="button button-secondary" id="forgot-password-btn"><?php esc_html_e( 'Reset Password', 'aurora' ); ?></button>

                        <div id="reset-password-form" style="display: none; margin-top: 15px;">
                            <div class="setting-item">
                                <label><?php esc_html_e( 'Email Address', 'aurora' ); ?></label>
                                <input type="email" id="reset-email" class="form-input" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>">
                            </div>
                            <button class="button button-primary" id="send-reset-otp"><?php esc_html_e( 'Send Reset OTP', 'aurora' ); ?></button>
                            <button class="button" id="cancel-reset"><?php esc_html_e( 'Cancel', 'aurora' ); ?></button>

                            <div id="reset-otp-section" style="display: none; margin-top: 15px;">
                                <div class="setting-item">
                                    <label><?php esc_html_e( 'Enter OTP (sent to your email)', 'aurora' ); ?></label>
                                    <input type="text" id="reset-otp-code" class="form-input" placeholder="<?php esc_attr_e( '000000', 'aurora' ); ?>" maxlength="6">
                                </div>

                                <div class="setting-item">
                                    <label><?php esc_html_e( 'New Password', 'aurora' ); ?></label>
                                    <input type="password" id="reset-new-password" class="form-input" placeholder="<?php esc_attr_e( 'Enter new password', 'aurora' ); ?>">
                                </div>

                                <button class="button button-success" id="confirm-reset-password"><?php esc_html_e( 'Reset Password', 'aurora' ); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Display -->
<div id="account-message" class="account-message" style="display: none;"></div>

<style>
.aurora-my-account-dashboard {
    padding: 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.my-account-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

/* Profile Section */
.profile-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.profile-image-container {
    position: relative;
    width: 150px;
    height: 150px;
}

.profile-image {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #0b57d0;
    display: block;
}

.upload-button {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 50px;
    height: 50px;
    background: #0b57d0;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    gap: 2px;
}

.upload-button:hover {
    background: #0942a6;
    transform: scale(1.1);
}

.upload-icon {
    font-size: 20px;
}

.upload-text {
    font-size: 9px;
    color: white;
    font-weight: 600;
}

.hidden-upload {
    display: none;
}

.profile-info h2 {
    margin: 0;
    font-size: 24px;
    color: #1a1a1a;
}

.user-email {
    margin: 5px 0;
    color: #6b7280;
    font-size: 14px;
}

.member-since {
    margin: 10px 0 0 0;
    font-size: 13px;
    color: #9ca3af;
}

/* Settings Section */
.settings-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
}

.settings-card h3 {
    margin: 0 0 25px 0;
    font-size: 22px;
    color: #1a1a1a;
}

.settings-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    border-bottom: 2px solid #e5e7eb;
}

.tab-button {
    padding: 12px 20px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 600;
    color: #6b7280;
    transition: all 0.3s ease;
    font-size: 14px;
    margin-bottom: -2px;
}

.tab-button:hover {
    color: #0b57d0;
}

.tab-button.active {
    color: #0b57d0;
    border-bottom-color: #0b57d0;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.setting-item {
    margin-bottom: 20px;
}

.setting-item label {
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

.form-input:disabled {
    background: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
}

.setting-item small {
    display: block;
    margin-top: 5px;
    color: #9ca3af;
    font-size: 12px;
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
}

.button-primary {
    background: #0b57d0;
    color: white;
}

.button-primary:hover {
    background: #0942a6;
    transform: translateY(-2px);
}

.button-primary:disabled {
    background: #d1d5db;
    cursor: not-allowed;
    transform: none;
}

.button-success {
    background: #10b981;
    color: white;
}

.button-success:hover {
    background: #059669;
}

.button-secondary {
    background: #f3f4f6;
    color: #374151;
}

.button-secondary:hover {
    background: #e5e7eb;
}

.button:not(.button-primary):not(.button-success):not(.button-secondary) {
    background: #f3f4f6;
    color: #374151;
}

/* Account Message */
.account-message {
    margin: 20px 0;
    padding: 16px 20px;
    border-radius: 8px;
    font-weight: 600;
    animation: slideDown 0.3s ease;
}

.account-message.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.account-message.error {
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

/* Responsive */
@media (max-width: 768px) {
    .my-account-container {
        grid-template-columns: 1fr;
    }

    .settings-tabs {
        flex-wrap: wrap;
    }

    .profile-image-container {
        width: 120px;
        height: 120px;
    }

    .profile-card {
        padding: 20px;
    }

    .settings-card {
        padding: 20px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    const nonce = '<?php echo wp_create_nonce( 'aurora_profile_nonce' ); ?>';
    
    // Tab switching
    $('.tab-button').on('click', function() {
        const tab = $(this).data('tab');
        
        $('.tab-button').removeClass('active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tab + '-tab').addClass('active');
    });

    // Profile image upload
    $('#profile-image-upload').on('change', function() {
        const file = this.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('action', 'aurora_upload_profile_image');
        formData.append('nonce', nonce);
        formData.append('profile_image', file);

        $.ajax({
            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                showMessage('Uploading...', 'info');
            },
            success: function(response) {
                if (response.success) {
                    $('#profile-image-display').attr('src', response.data.image_url);
                    showMessage(response.data.message, 'success');
                } else {
                    showMessage(response.data.message, 'error');
                }
            }
        });
    });

    // Email change
    let emailChangeStep = 'form'; // 'form' or 'otp'

    $('#change-email-btn').on('click', function() {
        $('#email-change-form').show();
        $('#email-otp-section').hide();
        $('#new-email').val('');
        $('#email-otp-code').val('');
        emailChangeStep = 'form';
        $(this).hide();
    });

    $('#cancel-email-change').on('click', function() {
        $('#email-change-form').hide();
        $('#email-otp-section').hide();
        $('#new-email').val('');
        $('#email-otp-code').val('');
        $('#change-email-btn').show();
        emailChangeStep = 'form';
    });

    $('#change-email-submit').on('click', function() {
        if (emailChangeStep === 'form') {
            // Step 1: User enters new email and clicks Change Email Address
            const newEmail = $('#new-email').val();

            if (!newEmail) {
                showMessage('<?php esc_html_e( 'Please enter a new email address', 'aurora' ); ?>', 'error');
                return;
            }

            if (!isValidEmail(newEmail)) {
                showMessage('<?php esc_html_e( 'Please enter a valid email address', 'aurora' ); ?>', 'error');
                return;
            }

            // Request OTP to be sent to existing/current email
            $.ajax({
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                type: 'POST',
                data: {
                    action: 'aurora_request_email_change_otp',
                    nonce: nonce,
                    new_email: newEmail
                },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.data.message, 'success');
                        $('#email-otp-section').show();
                        $('#email-otp-code').focus();
                        emailChangeStep = 'otp';
                        $('#change-email-submit').text('<?php esc_html_e( 'Change Email Address', 'aurora' ); ?>');
                    } else {
                        showMessage(response.data.message, 'error');
                    }
                },
                error: function() {
                    showMessage('<?php esc_html_e( 'Error requesting OTP', 'aurora' ); ?>', 'error');
                }
            });
        } else if (emailChangeStep === 'otp') {
            // Step 2: User enters OTP and clicks Change Email Address again
            const newEmail = $('#new-email').val();
            const otpCode = $('#email-otp-code').val();

            if (!otpCode || otpCode.length !== 6) {
                showMessage('<?php esc_html_e( 'Please enter a valid 6-digit OTP', 'aurora' ); ?>', 'error');
                return;
            }

            $.ajax({
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                type: 'POST',
                data: {
                    action: 'aurora_verify_email_change',
                    nonce: nonce,
                    new_email: newEmail,
                    otp: otpCode
                },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.data.message, 'success');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showMessage(response.data.message, 'error');
                    }
                },
                error: function() {
                    showMessage('<?php esc_html_e( 'Error verifying OTP', 'aurora' ); ?>', 'error');
                }
            });
        }
    });

    // Helper to validate email
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Password change
    $('#send-password-otp').on('click', function() {
        const currentPassword = $('#current-password').val();
        const newPassword = $('#new-password').val();

        if (!currentPassword || !newPassword || newPassword.length < 8) {
            showMessage('<?php esc_html_e( 'Password must be at least 8 characters', 'aurora' ); ?>', 'error');
            return;
        }

        $.ajax({
            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            type: 'POST',
            data: {
                action: 'aurora_request_otp',
                nonce: nonce,
                action_type: 'password_change'
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message, 'success');
                    $('#password-otp-section').show();
                    $('#send-password-otp').prop('disabled', true);
                } else {
                    showMessage(response.data.message, 'error');
                }
            }
        });
    });

    $('#verify-password-otp').on('click', function() {
        const currentPassword = $('#current-password').val();
        const newPassword = $('#new-password').val();
        const otpCode = $('#password-otp-code').val();

        if (!otpCode || otpCode.length !== 6) {
            showMessage('<?php esc_html_e( 'Please enter a valid 6-digit OTP', 'aurora' ); ?>', 'error');
            return;
        }

        $.ajax({
            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            type: 'POST',
            data: {
                action: 'aurora_update_password',
                nonce: nonce,
                current_password: currentPassword,
                new_password: newPassword,
                otp: otpCode
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message, 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showMessage(response.data.message, 'error');
                }
            }
        });
    });

    // Forgot password
    $('#forgot-password-btn').on('click', function() {
        $('#reset-password-form').toggle();
    });

    $('#cancel-reset').on('click', function() {
        $('#reset-password-form').hide();
        $('#reset-otp-section').hide();
    });

    $('#send-reset-otp').on('click', function() {
        const email = $('#reset-email').val();

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

    $('#confirm-reset-password').on('click', function() {
        const email = $('#reset-email').val();
        const otpCode = $('#reset-otp-code').val();
        const newPassword = $('#reset-new-password').val();

        if (!otpCode || otpCode.length !== 6 || !newPassword || newPassword.length < 8) {
            showMessage('<?php esc_html_e( 'Please enter valid OTP and password', 'aurora' ); ?>', 'error');
            return;
        }

        $.ajax({
            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            type: 'POST',
            data: {
                action: 'aurora_confirm_password_reset',
                nonce: nonce,
                email: email,
                otp: otpCode,
                new_password: newPassword
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message, 'success');
                    setTimeout(() => window.location.href = '<?php echo wc_get_page_permalink( 'myaccount' ); ?>', 2000);
                } else {
                    showMessage(response.data.message, 'error');
                }
            }
        });
    });

    // Helper function to show messages
    function showMessage(message, type) {
        const $message = $('#account-message');
        $message.removeClass('success error info');
        $message.addClass(type);
        $message.text(message);
        $message.show();

        if (type !== 'error') {
            setTimeout(() => $message.fadeOut(), 3000);
        }
    }
});
</script>
