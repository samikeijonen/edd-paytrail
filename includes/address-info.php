<?php
/**
 * Add own address form.
 *
 * @access private
 * @since 1.0.0
 */
function edd_paytrail_address_fields() {

	$logged_in = is_user_logged_in();

	if( $logged_in ) {
		$user_address = get_user_meta( get_current_user_id(), '_edd_user_address', true );
	}
	$line1 = $logged_in && ! empty( $user_address['line1'] ) ? $user_address['line1'] : '';
	$city  = $logged_in && ! empty( $user_address['city']  ) ? $user_address['city']  : '';
	$zip   = $logged_in && ! empty( $user_address['zip']   ) ? $user_address['zip']   : '';
	
	ob_start(); ?>
	<fieldset id="edd_cc_address" class="cc-address">
		<span><legend><?php _e( 'Billing Details', 'edd-paytrail' ); ?></legend></span>
		<?php do_action( 'edd_cc_billing_top' ); ?>
		<p id="edd-card-address-wrap">
			<label for="card_address" class="edd-label">
				<?php _e( 'Billing Address', 'edd-paytrail' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'This is your billing address.', 'edd-paytrail' ); ?></span>
			<input type="text" id="card_address" name="card_address" class="card-address edd-input required" placeholder="<?php _e( 'Address line', 'edd-paytrail' ); ?>" value="<?php echo $line1; ?>" />
		</p>
		<p id="edd-card-zip-wrap">
			<label for="card_zip" class="edd-label">
				<?php _e( 'Billing Zip / Postal Code', 'edd-paytrail' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The zip or postal code for your billing address.', 'edd-paytrail' ); ?></span>
			<input type="text" size="4" name="card_zip" class="card-zip edd-input required" placeholder="<?php _e( 'Zip / Postal code', 'edd-paytrail' ); ?>" value="<?php echo $zip; ?>" />
		</p>
		<p id="edd-card-city-wrap">
			<label for="card_city" class="edd-label">
				<?php _e( 'Billing City', 'edd-paytrail' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The city for your billing address.', 'edd-paytrail' ); ?></span>
			<input type="text" id="card_city" name="card_city" class="card-city edd-input required" placeholder="<?php _e( 'City', 'edd-paytrail' ); ?>" value="<?php echo $city; ?>" />
		</p>
		<p id="edd-card-country-wrap">
			<label for="billing_country" class="edd-label">
				<?php _e( 'Billing Country', 'edd-paytrail' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The country for your billing address.', 'edd-paytrail' ); ?></span>
			<select id="billing_country" name="billing_country" id="billing_country" class="billing_country edd-select required">
				<?php

				$selected_country = edd_get_shop_country();

				if( $logged_in && ! empty( $user_address['country'] ) && '*' !== $user_address['country'] ) {
					$selected_country = $user_address['country'];
				}
				
				$countries = edd_get_country_list();
				foreach( $countries as $country_code => $country ) {
				  echo '<option value="' . $country_code . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
				}
				?>
			</select>
		</p>
		<?php do_action( 'edd_cc_billing_bottom' ); ?>
	</fieldset>
	<?php
	echo ob_get_clean();
	
}
/* Show finnish type of address fields if paytrail is chosen payment gateway and site owner wants to use these address fields. */
if ( 'paytrail' == edd_get_chosen_gateway() && edd_paytrail_show_extra_address_fields() ) {
	remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_tax_fields', 999 ); // Remove original address fields.
	add_action( 'edd_purchase_form_after_cc_form', 'edd_paytrail_address_fields', 999 ); // Add own address fields.
}
add_action( 'edd_paytrail_cc_form', '__return_false' ); // Remove credit card info from paytrail gateway.

/**
 * Remove card state from from required address fields.
 *
 * @access      private
 * @since       1.0.0
 * @return      array
 */
function edd_paytrail_remove_required_fields( $required_fields ) {

	/* If paytrail is chosen payment gateway and use finnish address field, remove card_state. It doesn't even exists in address fields. */
	if ( 'paytrail' == edd_get_chosen_gateway() && edd_paytrail_show_extra_address_fields() ) {
		unset( $required_fields['card_state'] );
	}

	/* Return required fields. */
	return $required_fields;

}
add_filter( 'edd_purchase_form_required_fields', 'edd_paytrail_remove_required_fields' );

?>