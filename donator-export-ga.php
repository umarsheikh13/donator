<?php
define( 'WP_USE_THEMES', false );
require_once '../../../wp-load.php';

/**
 * donate-export-ga.php
 *
 * Export gift aid donations.
 *
 * @author		Umar Sheikh <umarsheikh.co.uk>
 * @copyright	2015 Umar Sheikh
 * @since		1.0
 */


if ( $_GET['export'] == '1' && is_user_logged_in() ) {
	
	// Check if admin
	
	if ( !current_user_can( 'activate_plugins' ) ) {
		exit;
	}

	// Date vars

	if ( isset( $_GET['month'] ) ) {
		$don_month = $_GET['month'];
	} else {
		$don_month = ( date( 'm' ) * 1 );
	}
	if ( isset( $_GET['year'] ) ) {
		$don_year = $_GET['year'];
	} else {
		$don_year = ( date( 'Y' ) * 1 );
	}

	// Donations

	$don_donations = get_posts( 'meta_key=donation_status&meta_value=Completed&posts_per_page=9999999&post_type=donation&year=' . $don_year . '&monthnum=' . $don_month );

	if ( count( $don_donations ) > 0 ) {

		$results = array();
		$results[] = array(
			__( 'Title', 'donator' ),
			__( 'First Name', 'donator' ),
			__( 'Last Name', 'donator' ),
			__( 'House name or Number.', 'donator' ),
			__( 'Postcode', 'donator' ),
			__( 'Aggregated Donations', 'donator' ),
			__( 'Sponsored Event', 'donator' ),
			__( 'Last Date', 'donator' ),
			__( 'Donated Amount', 'donator' )
		);

		$r = 1;
		foreach ( $don_donations as $don_donation ) {

			if ( get_post_meta( $don_donation->ID, 'donation_giftaid', true ) == 'true' ) {

				$results[$r][] = get_post_meta( $don_donation->ID, 'donor_title', true );
				$results[$r][] = get_post_meta( $don_donation->ID, 'donor_firstname', true );
				$results[$r][] = get_post_meta( $don_donation->ID, 'donor_lastname', true );
				$results[$r][] = get_post_meta( $don_donation->ID, 'donor_houseno', true );
				$results[$r][] = get_post_meta( $don_donation->ID, 'donor_postcode', true );
				$results[$r][] = '';
				$results[$r][] = '';
				$results[$r][] = date( 'd/m/y', strtotime( $don_donation->post_date ) );
				$results[$r][] = get_post_meta( $don_donation->ID, 'donation_amount', true );

			}

		$r++;
		}

		// Export as CSV
		
		header( 'Content-Encoding: UTF-8' );
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment;filename=Export-GA-' . date( 'd-m-y' ) . '.csv' );
		$fp = fopen( 'php://output', 'w' );
		foreach ( $results as $result ) {
			fputcsv( $fp, $result );
		}
		fclose( $fp );
		exit;

	} else {
		exit;
	}
	
}

?>