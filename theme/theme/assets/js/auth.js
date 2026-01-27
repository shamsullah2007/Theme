/**
 * Aurora Authentication Frontend Handler
 * Manages form interactions and AJAX communication with backend
 */

(function ($) {
    'use strict';

    window.AuroraAuth = {
        ajaxUrl: auroraTheme.ajaxUrl,
        authNonce: auroraTheme.authNonce,
        profileNonce: auroraTheme.profileNonce,
        isLoading: false,

        /**
         * Initialize all auth pages
         */
        init: function () {
            // Add body class
            $('body').addClass('aurora-auth-page');

            // Detect current page and initialize
            const page = $('body').data('page') || this.detectAuthPage();

            if (page === 'registration') {
                this.initRegistration();
            } else if (page === 'login') {
                this.initLogin();
            } else if (page === 'forgot-password') {
                this.initForgotPassword();
            }
        },

        /**
         * Detect which auth page we're on
         */
        detectAuthPage: function () {
            if ($('#aurora-registration-form').length) return 'registration';
            if ($('#aurora-password-login-form').length || $('#aurora-otp-login-form').length) return 'login';
            if ($('#aurora-forgot-email-form').length || $('#aurora-forgot-otp-form').length) return 'forgot-password';
            return 'unknown';
        },

        /**
         * Initialize registration page
         */
        initRegistration: function () {
            const self = this;

            // Request OTP button
            $(document).on('click', '#btn-request-reg-otp', function (e) {
                e.preventDefault();
                self.requestRegistrationOTP();
            });

            // Complete registration (verify OTP)
            $(document).on('click', '#btn-complete-registration', function (e) {
                e.preventDefault();
                self.completeRegistration();
            });

            // Back to registration button
            $(document).on('click', '#btn-back-to-registration', function (e) {
                e.preventDefault();
                self.showRegistrationForm();
            });

            // Resend OTP timer
            $(document).on('click', '#btn-resend-reg-otp', function (e) {
                e.preventDefault();
                self.requestRegistrationOTP();
            });

            // Handle Enter key in registration form
            $(document).on('keypress', '#aurora-registration-form .form-control', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    if ($('#reg-otp-input').is(':visible')) {
                        $('#btn-complete-registration').click();
                    } else {
                        $('#btn-request-reg-otp').click();
                    }
                }
            });
        },

        /**
         * Request registration OTP
         */
        requestRegistrationOTP: function () {
            const self = this;
            const form = $('#aurora-registration-form');
            const messagesContainer = $('#reg-messages');

            // Validate form
            const validation = self.validateRegistrationForm();
            if (!validation.valid) {
                self.showMessage(messagesContainer, validation.error, 'error');
                return;
            }

            // Get form data
            const formData = {
                action: 'aurora_request_registration_otp',
                auth_nonce: self.authNonce,
                first_name: $('#reg-first-name').val().trim(),
                last_name: $('#reg-last-name').val().trim(),
                email: $('#reg-email').val().trim(),
                username: $('#reg-username').val().trim() || null,
                password: $('#reg-password').val(),
                password_confirm: $('#reg-password-confirm').val(),
                agree_terms: $('#reg-agree-terms').is(':checked') ? 1 : 0
            };

            self.setLoadingState($('#btn-request-reg-otp'), true);
            self.clearMessages(messagesContainer);

            $.ajax({
                type: 'POST',
                url: self.ajaxUrl,
                data: formData,
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    self.setLoadingState($('#btn-request-reg-otp'), false);

                    if (response.success) {
                        // Store token for verification
                        if (response.data.token) {
                            $('#reg-otp-token').val(response.data.token);
                        }

                        let successMsg = response.data.message || 'OTP sent.';
                        if (typeof response.data.attempts_left !== 'undefined') {
                            successMsg += ' Attempts left today: ' + response.data.attempts_left + '.';
                        }

                        self.showMessage(messagesContainer, successMsg, 'success');
                        self.showOTPForm();

                        // Stop resend if limit reached
                        if (response.data.attempts_left !== undefined && response.data.attempts_left <= 0) {
                            $('#btn-resend-reg-otp').prop('disabled', true).html('Max OTP limit reached');
                        } else {
                            self.startResendTimer('reg', 60);
                        }

                        // Store state for later
                        sessionStorage.setItem('aurora_reg_email', $('#reg-email').val());
                    } else {
                        self.showMessage(messagesContainer, response.data.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    self.setLoadingState($('#btn-request-reg-otp'), false);
                    self.showMessage(messagesContainer, 'Network error. Please try again.', 'error');
                    console.error('Registration OTP Error:', error);
                }
            });
        },

        /**
         * Complete registration with OTP
         */
        completeRegistration: function () {
            const self = this;
            const messagesContainer = $('#reg-messages');

            // Validate OTP
            const otp = $('#reg-otp-input').val().replace(/\s/g, '').trim();
            if (!otp || otp.length !== 6 || !/^\d+$/.test(otp)) {
                self.showMessage(messagesContainer, 'Please enter a valid 6-digit code', 'error');
                return;
            }

            const formData = {
                action: 'aurora_complete_registration',
                auth_nonce: self.authNonce,
                otp: otp,
                token: $('#reg-otp-token').val(),
                email: $('#reg-email').val().trim()
            };

            self.setLoadingState($('#btn-complete-registration'), true);
            self.clearMessages(messagesContainer);

            $.ajax({
                type: 'POST',
                url: self.ajaxUrl,
                data: formData,
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    self.setLoadingState($('#btn-complete-registration'), false);

                    if (response.success) {
                        self.showMessage(messagesContainer, response.data.message, 'success');
                        self.showSuccessMessage('registration');
                        sessionStorage.removeItem('aurora_reg_email');

                        // Redirect after delay
                        setTimeout(function () {
                            window.location.href = auroraTheme.dashboardUrl || '/my-account/';
                        }, 2000);
                    } else {
                        self.showMessage(messagesContainer, response.data.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    self.setLoadingState($('#btn-complete-registration'), false);
                    self.showMessage(messagesContainer, 'Verification failed. Please try again.', 'error');
                    console.error('Registration Completion Error:', error);
                }
            });
        },

        /**
         * Validate registration form
         */
        validateRegistrationForm: function () {
            const firstName = $('#reg-first-name').val().trim();
            const lastName = $('#reg-last-name').val().trim();
            const email = $('#reg-email').val().trim();
            const password = $('#reg-password').val();
            const passwordConfirm = $('#reg-password-confirm').val();
            const agreeTerms = $('#reg-agree-terms').is(':checked');

            if (!firstName) return { valid: false, error: 'First name is required' };
            if (!lastName) return { valid: false, error: 'Last name is required' };
            if (!email || !this.isValidEmail(email)) return { valid: false, error: 'Valid email is required' };
            if (!password || password.length < 8) return { valid: false, error: 'Password must be at least 8 characters' };
            if (password !== passwordConfirm) return { valid: false, error: 'Passwords do not match' };
            if (!agreeTerms) return { valid: false, error: 'You must agree to the terms and conditions' };

            return { valid: true };
        },

        /**
         * Initialize login page
         */
        initLogin: function () {
            const self = this;

            // Tab switching
            $(document).on('click', '.tab-button', function () {
                const tabId = $(this).data('tab');
                self.switchTab(tabId);
            });

            // Password login form
            $('#aurora-login-password-form').on('submit', function (e) {
                // Let the standard WordPress login form handle it
                // Return true to submit normally
            });

            // Request OTP for login
            $(document).on('click', '#btn-request-login-otp', function (e) {
                e.preventDefault();
                self.requestLoginOTP();
            });

            // Login with OTP
            $(document).on('click', '#btn-verify-login-otp', function (e) {
                e.preventDefault();
                self.loginWithOTP();
            });

            // Skip password reset and login directly
            $(document).on('click', '#btn-skip-password-reset', function (e) {
                e.preventDefault();
                self.skipPasswordResetAndLogin();
            });

            // Show password reset form
            $(document).on('click', '#btn-reset-password-prompt', function (e) {
                e.preventDefault();
                $('#password-reset-buttons').hide();
                $('#password-reset-form').show();
                $('#reset-new-password').focus();
            });

            // Hide password reset form
            $(document).on('click', '#btn-toggle-password-form', function (e) {
                e.preventDefault();
                $('#password-reset-form').hide();
                $('#password-reset-buttons').show();
            });

            // Reset password and login
            $(document).on('click', '#btn-reset-password-and-login', function (e) {
                e.preventDefault();
                self.resetPasswordAndLogin();
            });

            // Resend OTP timer for login
            $(document).on('click', '#btn-resend-login-otp', function (e) {
                e.preventDefault();
                self.requestLoginOTP();
            });

            // Handle Enter key
            $(document).on('keypress', '#aurora-otp-login-form .form-control', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    if ($('#otp-login-section').is(':visible')) {
                        $('#btn-verify-login-otp').click();
                    } else {
                        $('#btn-request-login-otp').click();
                    }
                }
            });
        },

        /**
         * Switch login tabs
         */
        switchTab: function (tabId) {
            const tabs = $('.tab-button');
            const contents = $('.tab-content');

            tabs.removeClass('active');
            contents.removeClass('active').hide();

            tabs.filter('[data-tab="' + tabId + '"]').addClass('active');
            contents.filter('[data-tab="' + tabId + '"]').addClass('active').show();
        },

        /**
         * Request login OTP
         */
        requestLoginOTP: function () {
            const self = this;
            const messagesContainer = $('#login-messages');
            const email = $('#otp-login-email').val().trim();

            if (!email || !self.isValidEmail(email)) {
                self.showMessage(messagesContainer, 'Please enter a valid email address', 'error');
                return;
            }

            const formData = {
                action: 'aurora_request_login_otp',
                auth_nonce: self.authNonce,
                email: email
            };

            self.setLoadingState($('#btn-request-login-otp'), true);
            self.clearMessages(messagesContainer);

            $.ajax({
                type: 'POST',
                url: self.ajaxUrl,
                data: formData,
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    self.setLoadingState($('#btn-request-login-otp'), false);

                    if (response.success) {
                        self.showMessage(messagesContainer, response.data.message, 'success');
                        $('#otp-login-code').val('').focus();
                        $('#otp-login-section').removeClass('display-none').show();
                        self.startResendTimer('login', 60);

                        sessionStorage.setItem('aurora_login_email', email);
                    } else {
                        self.showMessage(messagesContainer, response.data.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    self.setLoadingState($('#btn-request-login-otp'), false);
                    self.showMessage(messagesContainer, 'Network error. Please try again.', 'error');
                }
            });
        },

        /**
         * Login with OTP
         */
        loginWithOTP: function () {
            const self = this;
            const messagesContainer = $('#login-messages');
            const otp = $('#otp-login-code').val().replace(/\s/g, '').trim();
            const email = $('#otp-login-email').val().trim();

            if (!otp || otp.length !== 6 || !/^\d+$/.test(otp)) {
                self.showMessage(messagesContainer, 'Please enter a valid 6-digit code', 'error');
                return;
            }

            const formData = {
                action: 'aurora_verify_login_otp_only',
                auth_nonce: self.authNonce,
                otp: otp,
                email: email
            };

            self.setLoadingState($('#btn-verify-login-otp'), true);
            self.clearMessages(messagesContainer);

            $.ajax({
                type: 'POST',
                url: self.ajaxUrl,
                data: formData,
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    self.setLoadingState($('#btn-verify-login-otp'), false);

                    if (response.success) {
                        // OTP verified successfully - show password reset option
                        $('#otp-login-section').hide();
                        $('#otp-password-reset-section').show();

                        // Store verified email for later use
                        sessionStorage.setItem('aurora_otp_verified_email', email);
                        sessionStorage.setItem('aurora_otp_verified_token', response.data.temp_token || '');

                        self.showMessage(messagesContainer, 'Identity verified! You can now set a new password or sign in directly.', 'success');
                    } else {
                        self.showMessage(messagesContainer, response.data.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    self.setLoadingState($('#btn-verify-login-otp'), false);
                    self.showMessage(messagesContainer, 'Verification failed. Please try again.', 'error');
                }
            });
        },

        /**
         * Skip password reset and login directly
         */
        skipPasswordResetAndLogin: function () {
            const self = this;
            const messagesContainer = $('#login-messages');
            const email = sessionStorage.getItem('aurora_otp_verified_email');
            const tempToken = sessionStorage.getItem('aurora_otp_verified_token');

            if (!email) {
                self.showMessage(messagesContainer, 'Session expired. Please verify your OTP again.', 'error');
                return;
            }

            const formData = {
                action: 'aurora_login_with_verified_otp',
                auth_nonce: self.authNonce,
                email: email,
                temp_token: tempToken
            };

            self.setLoadingState($('#btn-skip-password-reset'), true);
            self.clearMessages(messagesContainer);

            $.ajax({
                type: 'POST',
                url: self.ajaxUrl,
                data: formData,
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    self.setLoadingState($('#btn-skip-password-reset'), false);

                    if (response.success) {
                        self.showMessage(messagesContainer, response.data.message, 'success');
                        sessionStorage.removeItem('aurora_login_email');
                        sessionStorage.removeItem('aurora_otp_verified_email');
                        sessionStorage.removeItem('aurora_otp_verified_token');

                        // Redirect after delay
                        setTimeout(function () {
                            window.location.href = auroraTheme.dashboardUrl || '/my-account/';
                        }, 1500);
                    } else {
                        self.showMessage(messagesContainer, response.data.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    self.setLoadingState($('#btn-skip-password-reset'), false);
                    self.showMessage(messagesContainer, 'Login failed. Please try again.', 'error');
                }
            });
        },

        /**
         * Reset password and login
         */
        resetPasswordAndLogin: function () {
            const self = this;
            const messagesContainer = $('#login-messages');
            const email = sessionStorage.getItem('aurora_otp_verified_email');
            const tempToken = sessionStorage.getItem('aurora_otp_verified_token');
            const newPassword = $('#reset-new-password').val();
            const confirmPassword = $('#reset-confirm-password').val();

            if (!email || !tempToken) {
                self.showMessage(messagesContainer, 'Session expired. Please verify your OTP again.', 'error');
                return;
            }

            if (!newPassword || newPassword.length < 8) {
                self.showMessage(messagesContainer, 'Password must be at least 8 characters', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                self.showMessage(messagesContainer, 'Passwords do not match', 'error');
                return;
            }

            const formData = {
                action: 'aurora_reset_password_and_login',
                auth_nonce: self.authNonce,
                email: email,
                temp_token: tempToken,
                new_password: newPassword
            };

            self.setLoadingState($('#btn-reset-password-and-login'), true);
            self.clearMessages(messagesContainer);

            $.ajax({
                type: 'POST',
                url: self.ajaxUrl,
                data: formData,
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    self.setLoadingState($('#btn-reset-password-and-login'), false);

                    if (response.success) {
                        self.showMessage(messagesContainer, response.data.message, 'success');
                        sessionStorage.removeItem('aurora_login_email');
                        sessionStorage.removeItem('aurora_otp_verified_email');
                        sessionStorage.removeItem('aurora_otp_verified_token');

                        // Redirect after delay
                        setTimeout(function () {
                            window.location.href = auroraTheme.dashboardUrl || '/my-account/';
                        }, 1500);
                    } else {
                        self.showMessage(messagesContainer, response.data.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    self.setLoadingState($('#btn-reset-password-and-login'), false);
                    self.showMessage(messagesContainer, 'Password reset failed. Please try again.', 'error');
                }
            });
        },

        /**
         * Initialize forgot password page
         */
        initForgotPassword: function () {
            const self = this;

            // Request reset OTP
            $(document).on('click', '#btn-forgot-send', function (e) {
                e.preventDefault();
                self.requestPasswordResetOTP();
            });

            // Verify reset OTP
            $(document).on('click', '#btn-forgot-verify', function (e) {
                e.preventDefault();
                self.verifyPasswordResetOTP();
            });

            // Confirm password reset
            $(document).on('click', '#btn-reset-password', function (e) {
                e.preventDefault();
                self.confirmPasswordReset();
            });

            // Back to email button
            $(document).on('click', '#btn-back-forgot-email', function (e) {
                e.preventDefault();
                self.showResetEmailForm();
            });

            // Use different email button
            // Resend OTP for reset
            $(document).on('click', '#btn-forgot-resend', function (e) {
                e.preventDefault();
                self.requestPasswordResetOTP();
            });

            // Handle Enter key
            $(document).on('keypress', '#aurora-forgot-email-form .form-control, #aurora-forgot-otp-form .form-control, #aurora-new-password-form .form-control', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    if ($('#aurora-forgot-otp-form').is(':visible')) {
                        $('#btn-forgot-verify').click();
                    } else if ($('#aurora-new-password-form').is(':visible')) {
                        $('#btn-reset-password').click();
                    } else {
                        $('#btn-forgot-send').click();
                    }
                }
            });
        },

        /**
         * Request password reset OTP
         */
        requestPasswordResetOTP: function () {
            const self = this;
            const messagesContainer = $('#forgot-messages');
            const email = $('#forgot-email').val().trim();

            if (!email || !self.isValidEmail(email)) {
                self.showMessage(messagesContainer, 'Please enter a valid email address', 'error');
                return;
            }

            const formData = {
                action: 'aurora_reset_password',
                auth_nonce: self.authNonce,
                email: email
            };

            self.setLoadingState($('#btn-request-reset-otp'), true);
            self.clearMessages(messagesContainer);

            $.ajax({
                type: 'POST',
                url: self.ajaxUrl,
                data: formData,
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    self.setLoadingState($('#btn-request-reset-otp'), false);

                    if (response.success) {
                        self.showMessage(messagesContainer, response.data.message, 'success');
                        $('#aurora-forgot-email-form').hide();
                        $('#aurora-forgot-otp-form').show();
                        self.startResendTimer('reset', 60, '#btn-forgot-resend');

                        sessionStorage.setItem('aurora_reset_email', email);
                    } else {
                        self.showMessage(messagesContainer, response.data.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    self.setLoadingState($('#btn-request-reset-otp'), false);
                    self.showMessage(messagesContainer, 'Network error. Please try again.', 'error');
                }
            });
        },

        /**
         * Verify password reset OTP
         */
        verifyPasswordResetOTP: function () {
            const self = this;
            const messagesContainer = $('#forgot-messages');
            const otp = $('#forgot-otp').val().replace(/\s/g, '').trim();
            const email = $('#forgot-email').val().trim();

            if (!otp || otp.length !== 6 || !/^\d+$/.test(otp)) {
                self.showMessage(messagesContainer, 'Please enter a valid 6-digit code', 'error');
                return;
            }

            self.setLoadingState($('#btn-forgot-verify'), true);
            self.clearMessages(messagesContainer);

            // Store OTP state
            sessionStorage.setItem('aurora_reset_otp_verified', 'true');
            sessionStorage.setItem('aurora_reset_otp', otp);

            self.setLoadingState($('#btn-forgot-verify'), false);
            self.showMessage(messagesContainer, 'Code verified! Now set your new password.', 'success');

            // Show password form
            $('#aurora-forgot-otp-form').hide();
            $('#aurora-new-password-form').show();
            $('#new-password').focus();
        },

        /**
         * Confirm password reset
         */
        confirmPasswordReset: function () {
            const self = this;
            const messagesContainer = $('#forgot-messages');
            const newPassword = $('#new-password').val();
            const confirmPassword = $('#confirm-password').val();
            const otp = sessionStorage.getItem('aurora_reset_otp');

            // Validate passwords
            if (!newPassword || newPassword.length < 8) {
                self.showMessage(messagesContainer, 'Password must be at least 8 characters', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                self.showMessage(messagesContainer, 'Passwords do not match', 'error');
                return;
            }

            const formData = {
                action: 'aurora_confirm_password_reset',
                auth_nonce: self.authNonce,
                email: $('#forgot-email').val().trim(),
                new_password: newPassword,
                otp: otp
            };

            self.setLoadingState($('#btn-reset-password'), true);
            self.clearMessages(messagesContainer);

            $.ajax({
                type: 'POST',
                url: self.ajaxUrl,
                data: formData,
                dataType: 'json',
                timeout: 15000,
                success: function (response) {
                    self.setLoadingState($('#btn-reset-password'), false);

                    if (response.success) {
                        self.showMessage(messagesContainer, response.data.message, 'success');
                        self.showResetSuccessMessage();

                        // Clear session storage
                        sessionStorage.removeItem('aurora_reset_email');
                        sessionStorage.removeItem('aurora_reset_otp');
                        sessionStorage.removeItem('aurora_reset_otp_verified');
                    } else {
                        self.showMessage(messagesContainer, response.data.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    self.setLoadingState($('#btn-reset-password'), false);
                    self.showMessage(messagesContainer, 'Password reset failed. Please try again.', 'error');
                }
            });
        },

        /**
         * Show registration OTP form
         */
        showOTPForm: function () {
            $('[data-step="registration-form"]').hide();
            $('[data-step="registration-otp"]').removeClass('display-none').show();
            $('#reg-otp-input').focus();
        },

        /**
         * Show registration form
         */
        showRegistrationForm: function () {
            $('[data-step="registration-otp"]').hide();
            $('[data-step="registration-form"]').show();
        },

        /**
         * Show reset email form
         */
        showResetEmailForm: function () {
            $('#aurora-new-password-form').hide();
            $('#aurora-forgot-otp-form').hide();
            $('#aurora-forgot-email-form').show();
            $('#forgot-success-message').hide();
            $('#forgot-email').val('').focus();

            sessionStorage.removeItem('aurora_reset_otp_verified');
            sessionStorage.removeItem('aurora_reset_otp');
        },

        /**
         * Show success message
         */
        showSuccessMessage: function (type) {
            if (type === 'registration') {
                $('[data-step="registration-otp"]').hide();
                $('#registration-success-message').show();
            } else if (type === 'reset') {
                $('#aurora-new-password-form').hide();
                $('#aurora-forgot-otp-form').hide();
                $('#aurora-forgot-email-form').hide();
                $('#forgot-success-message').show();
            }
        },

        /**
         * Show reset success message
         */
        showResetSuccessMessage: function () {
            $('#aurora-new-password-form').hide();
            $('#aurora-forgot-otp-form').hide();
            $('#aurora-forgot-email-form').hide();
            $('#forgot-success-message').show();
        },

        /**
         * Start resend OTP timer
         */
        startResendTimer: function (type, seconds, selector) {
            const self = this;
            let remaining = seconds;
            const buttonSelector = selector || ('#btn-resend-' + type + '-otp');

            $(buttonSelector).prop('disabled', true);

            const interval = setInterval(function () {
                remaining--;
                $(buttonSelector).html('Resend in ' + remaining + 's');

                if (remaining <= 0) {
                    clearInterval(interval);
                    $(buttonSelector).prop('disabled', false).html('Resend Code');
                }
            }, 1000);
        },

        /**
         * Show message
         */
        showMessage: function (container, message, type) {
            const html = '<div class="auth-message ' + type + '">' + message + '</div>';
            container.prepend(html);

            // Auto-remove after 5 seconds
            setTimeout(function () {
                container.find('.auth-message:first').fadeOut(function () {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Clear messages
         */
        clearMessages: function (container) {
            container.find('.auth-message').remove();
        },

        /**
         * Set loading state
         */
        setLoadingState: function (button, isLoading) {
            if (isLoading) {
                button.prop('disabled', true).html('<span class="spinner"></span> Loading...');
            } else {
                const originalText = button.data('original-text') || button.html();
                button.prop('disabled', false).html(originalText);
            }
        },

        /**
         * Validate email format
         */
        isValidEmail: function (email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    };

    // Initialize on document ready
    $(document).ready(function () {
        AuroraAuth.init();
    });

})(jQuery);
