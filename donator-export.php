<?php
define( 'WP_USE_THEMES', false );
require_once '../../../wp-load.php';

/**
 * donate-export.php
 *
 * Export donation data.
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

	$don_donations = get_posts( 'meta_key=donation_status&meta_value=Completed&posts_per_page=-1&post_type=donation&year=' . $don_year . '&monthnum=' . $don_month );

	if ( count( $don_donations ) > 0 ) {

		$results = array();
		$results[] = array(
			__( 'Donation no.', 'donator' ),
			__( 'Donation date', 'donator' ),
			__( 'Transaction ID', 'donator' ),
			__( 'Amount', 'donator' ),
			__( 'Currency', 'donator' ),
			__( 'Occurrence', 'donator' ),
			__( 'Type', 'donator' ),
			__( 'Title', 'donator' ),
			__( 'First name', 'donator' ),
			__( 'Last name', 'donator' ),
			__( 'House name/no.', 'donator' ),
			__( 'Street', 'donator' ),
			__( 'Town', 'donator' ),
			__( 'City', 'donator' ),
			__( 'Post code', 'donator' ),
			__( 'Country', 'donator' ),
			__( 'E-mail', 'donator' )
		);

		$r = 1;
		foreach ( $don_donations as $don_donation ) {

			$don_types = get_the_terms( $don_donation->ID, 'donation-type' );

			$results[$r][] = get_post_meta( $don_donation->ID, 'donation_no', true );
			$results[$r][] = date( 'd/m/y', strtotime( $don_donation->post_date ) );
			$results[$r][] = "\t" . get_post_meta( $don_donation->ID, 'donation_txn_id', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donation_amount', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donation_currency', true );
			$results[$r][] = ( get_post_meta( $don_donation->ID, 'donation_occurrence', true ) == 'monthly' ) ? __( 'Monthly', 'donator' ) : __( 'One-off', 'donator' );
			if ( count( $don_types ) ) {
				$donation_type = '';
				foreach ( $don_types as $don_type ) {
					$donation_type = $don_type->name;
					break;
				}
				if ( strlen( $donation_type ) ) {
					$results[$r][] = $donation_type;
				} else {
					$results[$r][] = 'N/A';
				}
			} else {
				$results[$r][] = 'N/A';
			}
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_title', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_firstname', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_lastname', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_houseno', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_street', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_town', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_city', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_postcode', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_country', true );
			$results[$r][] = get_post_meta( $don_donation->ID, 'donor_email', true );

		$r++;
		}

		// Export as CSV
		
		header( 'Content-Encoding: UTF-8' );
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment;filename=Export-' . date( 'd-m-y' ) . '.csv' );
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