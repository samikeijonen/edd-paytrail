<?php
/**
 * Add Paytrail image to checkout page.
 *
 * @access      public
 * @since       1.0
 * @return      string
 */
function edd_paytrail_add_image( $gateways ) {
	
	/* Return if paytrail is not chosen payment gateway. */
	if ( 'paytrail' !== edd_get_chosen_gateway() ) {
		return;
	}
	
	global $edd_options;
	
	/* Return if show image is not set in Settings >> Extensions. */
	if ( ! isset( $edd_options['edd_paytrail_show_image'] ) ) {
		return;
	}
	
	/* Use test credentials if test mode is on. */
	if( edd_is_test_mode() ) {
		$paytrail_merchant_id = $edd_options['edd_paytrail_test_merchant_id'];
		$paytrail_merchant_secret = $edd_options['edd_paytrail_test_merchant_secret'];
	} else {
		$paytrail_merchant_id = $edd_options['edd_paytrail_merchant_id'];
		$paytrail_merchant_secret = $edd_options['edd_paytrail_merchant_secret'];
	}
	
	/* Combine merchant id and merchant secret. @link: http://docs.paytrail.com/files/method-images-api-fi.pdf. */
	$auth_code = $paytrail_merchant_id . $paytrail_merchant_secret;
	
	/* Calculate MD5 and take first 16 characters. */
	$auth_code = substr( md5( $auth_code ), 0, 16 );
	
	/* Image defaults arguments. */
	$image_args = apply_filters( 'edd_paytrail_image_args', array(
		'type' => 'horizontal',
		'cols' => 10,
		'text' => 1
		)
	);
	?>
	<fieldset id="edd_paytrail_image">
		<span><legend><?php echo apply_filters( 'edd_paytrail_checkout_before_image_text', __( 'You can use Paytrail account or finnish banks.', 'edd-paytrail' ) ); ?></legend></span>
		<?php echo '<p><img src="https://img.verkkomaksut.fi/index.svm?id=' . esc_attr( $paytrail_merchant_id ) . '&type=' . esc_attr( $image_args['type'] ) . '&cols=' . absint( $image_args['cols'] ) . '&text=' . absint( $image_args['text'] ) . '&auth=' . esc_attr( $auth_code ) . '" alt="' . _x( 'Paytrail', 'Alt tag for Paytrail image', 'edd-paytrail' ) . '" title="' . _x( 'Paytrail', 'Title tag for Paytrail image', 'edd-paytrail' ) . '"/></p>'; ?>
	</fieldset>
	<?php
	
}
add_action( 'edd_purchase_form_top', 'edd_paytrail_add_image' );

?>