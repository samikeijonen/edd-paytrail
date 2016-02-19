<?php

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add own address form.
 *
 * @access      private
 * @since       1.0.0
 * @return      void
 */
function edd_paytrail_address_fields() {
	
	if ( 'paytrail' != edd_get_chosen_gateway() ) {
		return;
	}
	
	$logged_in = is_user_logged_in();
	$customer  = EDD()->session->get( 'customer' );
	$customer  = wp_parse_args( $customer, array( 'address' => array(
		'line1'   => '',
		'line2'   => '',
		'city'    => '',
		'zip'     => '',
		'state'   => '',
		'country' => ''
	) ) );
	
	$customer['address'] = array_map( 'sanitize_text_field', $customer['address'] );
	
	if( $logged_in ) {
		$user_address = get_user_meta( get_current_user_id(), '_edd_user_address', true );
		foreach( $customer['address'] as $key => $field ) {
			if ( empty( $field ) && ! empty( $user_address[ $key ] ) ) {
				$customer['address'][ $key ] = $user_address[ $key ];
			} else {
				$customer['address'][ $key ] = '';
			}
		}
	}
	
	ob_start(); ?>
	<fieldset id="edd_cc_address" class="cc-address">
		<span><legend><?php _e( 'Billing Details', 'edd-paytrail' ); ?></legend></span>
		<?php do_action( 'edd_cc_billing_top' ); ?>
		<p id="edd-card-address-wrap">
			<label for="card_address" class="edd-label">
				<?php _e( 'Billing Address', 'edd-paytrail' ); ?>
				<?php if( edd_field_is_required( 'card_address' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'This is your billing address.', 'edd-paytrail' ); ?></span>
			<input type="text" id="card_address" name="card_address" class="card-address edd-input<?php if( edd_field_is_required( 'card_address' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Address line', 'edd-paytrail' ); ?>" value="<?php echo $customer['address']['line1']; ?>"<?php if( edd_field_is_required( 'card_address' ) ) {  echo ' required '; } ?> />
		</p>
		<p id="edd-card-zip-wrap">
			<label for="card_zip" class="edd-label">
				<?php _e( 'Billing Zip / Postal Code', 'edd-paytrail' ); ?>
				<?php if( edd_field_is_required( 'card_zip' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The zip or postal code for your billing address.', 'edd-paytrail' ); ?></span>
			<input type="text" size="4" name="card_zip" class="card-zip edd-input<?php if( edd_field_is_required( 'card_zip' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Zip / Postal code', 'edd-paytrail' ); ?>" value="<?php echo $customer['address']['zip']; ?>"<?php if( edd_field_is_required( 'card_zip' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="edd-card-city-wrap">
			<label for="card_city" class="edd-label">
				<?php _e( 'Billing City', 'edd-paytrail' ); ?>
				<?php if( edd_field_is_required( 'card_city' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The city for your billing address.', 'edd-paytrail' ); ?></span>
			<input type="text" id="card_city" name="card_city" class="card-city edd-input<?php if( edd_field_is_required( 'card_city' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'City', 'edd-paytrail' ); ?>" value="<?php echo $customer['address']['city']; ?>"<?php if( edd_field_is_required( 'card_city' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="edd-card-country-wrap">
			<label for="billing_country" class="edd-label">
				<?php _e( 'Billing Country', 'edd-paytrail' ); ?>
				<?php if( edd_field_is_required( 'billing_country' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The country for your billing address.', 'edd-paytrail' ); ?></span>
			<select id="billing_country" name="billing_country" id="billing_country" class="billing_country edd-select<?php if( edd_field_is_required( 'billing_country' ) ) { echo ' required'; } ?>"<?php if( edd_field_is_required( 'billing_country' ) ) {  echo ' required '; } ?>>
				<?php

				$selected_country = edd_get_shop_country();

				if( ! empty( $customer['address']['country'] ) && '*' !== $customer['address']['country'] ) {
					$selected_country = $customer['address']['country'];
				}

				$countries = edd_get_country_list();
				foreach( $countries as $country_code => $country ) {
				  echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
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
if ( true === edd_paytrail_show_extra_address_fields() ) {
	remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_tax_fields', 999 ); // Remove original address fields.
	add_action( 'edd_purchase_form_after_cc_form', 'edd_paytrail_address_fields', 999 ); // Add own address fields.
}
add_action( 'edd_paytrail_cc_form', '__return_false' ); // Remove credit card info from paytrail gateway.

/**
 * Remove card state from required address fields. Also add last name and address as required.
 *
 * @access      private
 * @since       1.0.0
 * @return      array
 */
function edd_paytrail_required_fields( $required_fields ) {

	/* If paytrail is chosen payment gateway and use finnish address field, remove card_state. And add last name. */
	if ( 'paytrail' == edd_get_chosen_gateway() && true === edd_paytrail_show_extra_address_fields() ) {
		
		/* Unset card_state from required fields. */
		unset( $required_fields['card_state'] );
	
		/* In this case we also need to make last name and address required because they needs to be send to Paytrail. */
		$required_fields['edd_last'] = array(   
			'error_id'      => 'invalid_last_name',
			'error_message' => __( 'Please enter your last name.', 'edd-paytrail' )
		);
		$required_fields['card_address'] = array(   
			'error_id'      => 'invalid_card_address',
			'error_message' => __( 'Please enter your address.', 'edd-paytrail' )
		);
	
	}

	/* Return required fields. */
	return $required_fields;

}
add_filter( 'edd_purchase_form_required_fields', 'edd_paytrail_required_fields' );

/**
 * Address fields should be required when finnish address fields are selected.
 *
 * @access      private
 * @since       1.0.0
 * @return      void
 */
function edd_paytrail_require_address_fields( $required_address_fields ) {

	/* If paytrail is chosen payment gateway and use finnish address field, all address fields are required. */
	if ( 'paytrail' == edd_get_chosen_gateway() && true === edd_paytrail_show_extra_address_fields() ) {
		return ( $required_address_fields ) || edd_paytrail_show_extra_address_fields();
	} else {
		return $required_address_fields;
	}

}
add_filter( 'edd_require_billing_address', 'edd_paytrail_require_address_fields' );