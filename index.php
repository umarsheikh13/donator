<?php
/**
 * Plugin Name: Donator
 * Plugin URI: https://github.com/umarsheikh13/donator
 * Description: Donation plugin for WordPress.
 * Version: 1.0
 * Author: Umar Sheikh
 * Author URI: http://umarsheikh.co.uk/
 */


/*-----------------------------------------------------------------------------------*/
/*	Set currencies and countries
/*-----------------------------------------------------------------------------------*/

define( 'DON_CURRENCIES', serialize( array(
	'GBP' => array( 'British Pound', '&pound;' ),
	'USD' => array( 'US Dollar', '$' ),
	'EUR' => array( 'EURO', '&euro;' )
) ) );

$don_currencies_select = array();

foreach ( unserialize( DON_CURRENCIES ) as $don_curr_key => $don_curr_value ) {
	$don_currencies_select[$don_curr_key] = $don_curr_value[0];
}

define( 'DON_CURRENCIES_SELECT', serialize( $don_currencies_select ) );

$don_currencies_single = array();

foreach ( unserialize( DON_CURRENCIES ) as $don_curr_key => $don_curr_value ) {
	$don_currencies_single[] = $don_curr_key;
}

define( 'DON_CURRENCIES_SINGLE', serialize( $don_currencies_single ) );


/*-----------------------------------------------------------------------------------*/
/*	Include files
/*-----------------------------------------------------------------------------------*/

require_once 'functions.php';
require_once 'donator-countries.php';
require_once 'donator-post-type.php';
require_once 'donator-admin.php';
require_once 'donator-shortcode.php';

?>