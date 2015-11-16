<?php
define( 'WP_USE_THEMES', false );
require_once '../../../wp-load.php';

/**
 * donate-ipn.php
 *
 * Paypal IPN processing script.
 *
 * @author		Umar Sheikh <umarsheikh.co.uk>
 * @copyright	2015 Umar Sheikh
 * @since		1.0
 */


require_once 'functions.php';

if ( isset( $_POST['txn_id'] ) && isset( $_POST['txn_type'] ) ) {

	global $table_prefix;
	
	// Paypal transaction details
	
	$paypal_txn_id = $_POST['txn_id'];
	$paypal_item_number = $_POST['item_number'];
	$paypal_first_name = $_POST['first_name'];
	$paypal_last_name = $_POST['last_name'];
	$paypal_amount = $_POST['mc_gross'];
	$paypal_custom = $_POST['custom'];
	
	// Paypal request
	
    $paypal_req = 'cmd=_notify-validate';
	foreach ( $_POST as $pkey => $pval ) {
		$pval = urlencode( stripslashes( $pval ) );
		$pval = preg_replace( '/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $pval );
		$paypal_req .= "&$pkey=$pval";
	}
	
	// Validate with Paypal
	
	$paypal_header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
	if ( get_option( 'don-d-paypal-status' ) == 'true' ) {
		$paypal_header .= "Host: www.paypal.com\r\n";
	} else {
		$paypal_header .= "Host: www.sandbox.paypal.com\r\n";
	}
	$paypal_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$paypal_header .= "Content-Length: " . strlen( $paypal_req ) . "\r\n\r\n";
	if ( get_option( 'don-d-paypal-status' ) == 'live' ) {
		$paypal_fp = fsockopen( 'ssl://www.paypal.com', 443, $errno, $errstr, 30 );
	} else {
		$paypal_fp = fsockopen( 'ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30 );
	}

	if ( !$paypal_fp ) {
		
		// Cannot connect to paypal
		
	} else {
		
		// Check response
		fputs( $paypal_fp, $paypal_header.$paypal_req );
		
		while( !feof( $paypal_fp ) ) {
		
			$paypal_res = @fgets( $paypal_fp, 1024 );

			if ( preg_match( '/VERIFIED/', $paypal_res ) ) {
				
				// Payment validated!
				if ( don_check_txnid() ) {
										
					// Get donation
					
					$donation = get_post( $paypal_custom * 1 );
					
					// Update order
					
					if ( get_post_meta( $donation->ID, 'donation_status', true ) == 'Incomplete' ) {
					
						// Set to completed

						update_post_meta( $donation->ID, 'donation_status', 'Completed' );
						update_post_meta( $donation->ID, 'donation_txn_id', $paypal_txn_id );
						update_post_meta( $donation->ID, 'donation_no', $paypal_item_number );
						
						// Send email
						
						if ( strlen( get_option( 'don-d-thankyou-email' ) ) ) {
							
							$send_email_customer_body = stripslashes( get_option( 'don-d-thankyou-email' ) );
							$send_email_customer_body = str_replace( '%NAME%', get_post_meta( $donation->ID, 'donor_firstname', true ), $send_email_customer_body );
							$send_email_customer_body = str_replace( '%DONATIONNO%', $paypal_item_number, $send_email_customer_body );
							$send_email_customer_body = str_replace( '%AMOUNT%', $paypal_amount . ' ' . get_post_meta( $donation->ID, 'donation_currency', true ), $send_email_customer_body );
							$send_email_customer_headers = "MIME-Version: 1.0\r\n";
							$send_email_customer_headers .= "From: " . get_bloginfo( 'name' ) . " <" . get_option( 'don-d-admin-email' ) . ">\r\n";
							
							if ( wp_mail( get_post_meta( $donation->ID, 'donor_email', true ), __( 'Donation Confirmation', 'donator' ) . ' - ' . get_bloginfo( 'name' ), $send_email_customer_body, $send_email_customer_headers ) ) {
								// Sent
							} else {
								// E-mail could not be sent
							}
						
						}
						
						// Send admin email
						
						if ( preg_match( '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', get_option( 'don-d-admin-email' ) ) ) {
						
							$send_email_admin_body = sprintf( __( "Name: %s\nE-mail: %s\n\nTransaction ID: %s\nDonation ID: %s\nDonation No: %s\nAmount: %s\n\n%s\n%s", 'donator' ),
								get_post_meta( $donation->ID, 'donor_firstname', true ) . ' ' . get_post_meta( $donation->ID, 'donor_lastname', true ),
								get_post_meta( $donation->ID, 'donor_email', true ),
								$paypal_txn_id,
								$donation->ID,
								$paypal_item_number,
								$paypal_amount . ' ' . get_post_meta( $donation->ID, 'donation_currency', true ),
								get_bloginfo( 'name' ),
								home_url( '/' )
							);

							$send_email_admin_headers = "MIME-Version: 1.0\r\n";
							$send_email_admin_headers .= "From: " . get_bloginfo( 'name' ) . " <" . get_option( 'don-d-admin-email' ) . ">\r\n";
							
							if ( mail( get_option( 'don-d-admin-email' ), __( 'New Donation', 'donator' ) . ' - ' . get_bloginfo( 'name' ), $send_email_admin_body, $send_email_admin_headers ) ) {
								// Sent
							} else {
								// E-mail could not be sent
							}
							
						} else {
							// Admin e-mail not set
						}
					
					}
					
				} else {
					
					// Txn ID already exists
					
				}

			} else if ( preg_match( '/INVALID/', $paypal_res ) ) {

				// Paypal request invalid
			
			}
			
		}
		
		fclose( $paypal_fp );
		
	}
	
}

?>