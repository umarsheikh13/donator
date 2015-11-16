<?php
/**
 * functions.php
 *
 * All the helper functions.
 *
 * @author		Umar Sheikh <umarsheikh.co.uk>
 * @copyright	2015 Umar Sheikh
 * @since		1.0
 */


/*-----------------------------------------------------------------------------------*/
/*	Format price function
/*-----------------------------------------------------------------------------------*/

function don_format_price( $num ) {
	$dec = 2;
	$result = round( $num * pow( 10, $dec ) ) / pow( 10, $dec ) + '';
	$result = round( $num * pow( 10, $dec ) ) / pow( 10, $dec );
	if ( preg_match( '/\./', $result . '' ) ) {
		if ( preg_match( '/\.[0-9]$/', $result ) ) {
			return $result . '0';
		} else {
			return $result;
		}
	} else {
		return $result . '.00';
	}
}

/*-----------------------------------------------------------------------------------*/
/*	Check transaction ID
/*-----------------------------------------------------------------------------------*/

function don_check_txnid() {
	$valid_txnid = true;
	$donation_id = $_POST['custom'];
	if ( is_numeric( $donation_id ) ) {
		$donation = get_post( $donation_id * 1 );
		if ( $donation ) {
			$valid_txnid = true;
		} else {
			$valid_txnid = false;
		}
	} else {
		$valid_txnid = false;
	}
	return $valid_txnid;
}

?>