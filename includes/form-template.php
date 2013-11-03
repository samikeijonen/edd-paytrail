<?php

/**
 * PayPal Remove CC Form
 *
 * PayPal Standard does not need a CC form, so remove it.
 *
 * @access private
 * @since 1.0
 */
add_action( 'edd_paytrail_cc_form', '__return_false' );

/**
 * Renders the Paytrail form.
 *
 * @since 1.0
 * @return void
 */
function edd_paytrail_get_cc_form() {
	ob_start(); ?>

	<?php do_action( 'edd_paytrail_before_cc_fields' ); ?>
	
	<fieldset id="">
	<p id="payment">
		<a href="https://payment.verkkomaksut.fi/payment/load/token/0123456789abcdefg">Go to payments</a>
	</p>
	</fieldset>
	<?php
	
	do_action( 'edd_paytrail_after_cc_fields' );

	echo ob_get_clean();
}
add_action( 'edd_after_purchase_form', 'edd_paytrail_get_cc_form' );

?>