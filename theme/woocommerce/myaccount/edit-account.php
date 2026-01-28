<?php
/**
 * Edit Account Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/edit-account.php.
 */

defined( 'ABSPATH' ) || exit;

?>

<style>
    .woocommerce-MyAccount-wrapper {
        display: grid !important;
        grid-template-columns: 280px 1fr !important;
        gap: 40px !important;
        align-items: start !important;
        max-width: none !important;
        margin: 0 !important;
        width: 100% !important;
        padding: 0 !important;
        background: transparent !important;
    }

    .woocommerce-MyAccount-wrapper > div:not(.aurora-myaccount-sidebar) {
        grid-column: 2 !important;
    }

    .aurora-myaccount-sidebar {
        grid-column: 1 !important;
        grid-row: 1 / -1 !important;
    }

    @media (max-width: 768px) {
        .woocommerce-MyAccount-wrapper {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }

        .woocommerce-MyAccount-wrapper > div:not(.aurora-myaccount-sidebar) {
            grid-column: 1 !important;
        }
    }
</style>

<div class="woocommerce-MyAccount-wrapper">
    <?php
    // Include sidebar
    $sidebar_file = dirname( __FILE__ ) . '/sidebar-navigation.php';
    if ( file_exists( $sidebar_file ) ) {
        include $sidebar_file;
    }
    ?>
    
    <div style="grid-column: 2;">

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>

	<fieldset>
		<legend><?php esc_html_e( 'Password change', 'woocommerce' ); ?></legend>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
		</p>
	</fieldset>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button button-primary" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
	</p>

</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

    </div>
</div>
