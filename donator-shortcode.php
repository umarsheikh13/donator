<?php

/*-----------------------------------------------------------------------------------*/
/*	Donate form shortcode
/*-----------------------------------------------------------------------------------*/

function don_donate_shortcode() {
	add_shortcode( 'donator', 'don_donate_section_shortcode' );
}
add_action( 'init', 'don_donate_shortcode' );

function don_donate_section_shortcode( $atts, $content = null ) {
	global $post;
	?>

	<!-- Donator Form - Begin -->

	<form id="donator-form" action="<?php echo get_permalink($post->ID); ?>" method="post">
		
		<?php if( isset( $_SESSION['don_donate_error_msg'] ) ): ?>
		<p class="donator-msg-error"><?php echo $_SESSION['don_donate_error_msg']; ?></p>
		<?php endif; ?>

		<div id="donator-da">
			
			<label class="donator-da-currency">
				<select name="don-currency">
					<?php
					foreach ( unserialize( DON_CURRENCIES ) as $don_curr_key => $don_curr_value ) {
						echo '<option ' . ( ( $_POST['don-currency'] == $don_curr_key ) ? 'selected="selected"' : '' ) . ' value="' . $don_curr_key . '">' . $don_curr_value[1] . '</option>';
					}
					?>
				</select>
			</label>

			<label class="donator-da-fixed-amount">
				<select name="don-fixed-amount">
					<option <?php echo ( ( $_POST['don-fixed-amount'] == '10' ) ? 'selected="selected"' : '' ); ?> value="10">10</option>
					<option <?php echo ( ( $_POST['don-fixed-amount'] == '20' ) ? 'selected="selected"' : '' ); ?> value="20">20</option>
					<option <?php echo ( ( $_POST['don-fixed-amount'] == '50' ) ? 'selected="selected"' : '' ); ?> value="50">50</option>
				</select>
			</label>

			<span class="donator-da-or"><?php _e( 'or', 'donator' ); ?></span>

			<label class="donator-da-amount"><input placeholder="0.00" type="text" name="don-amount" value="<?php echo ( ( is_numeric( $_POST['don-amount'] ) ) ? $_POST['don-amount'] : '' ); ?>"></label>
			
			<?php
			$don_types = get_terms( 'donation-type', 'hide_empty=0' );
			if ( count( $don_types ) ) {
				echo '<label class="donator-da-type"><select name="don-type"><option value="0">' . __( 'Select donation type', 'donator' ) . '</option>';
					foreach ( $don_types as $don_type ) {
						echo '<option ' . ( ( $_POST['don-type'] == $don_type->id ) ? 'selected="selected"' : '' ) . ' value="' . $don_type->id . '">' . $don_type->name . '</option>';
					}
				echo '</select></label>';
			}
			?>

			<div style="clear: both;"></div>

		</div>

		<p id="donator-occurrence">
			<label><input type="radio" <?php if ( $_POST['don-occurrence'] == 'monthly' || !isset( $_POST['don-occurrence'] ) ) { echo 'checked="checked"'; } ?> name="don-occurrence" value="monthly"> <?php _e( 'Monthly', 'donator' ); ?></label>&nbsp;&nbsp;&nbsp;
			<label><input type="radio" <?php if ( $_POST['don-occurrence'] == 'oneoff' ) { echo 'checked="checked"'; } ?> name="don-occurrence" value="oneoff"> <?php _e( 'One-off', 'donator' ); ?></label>
		</p>

		<div id="donator-fields">

			<div class="donator-left-col">

				<p><label><select name="don-title">
					<option <?php echo ( ( $_POST['don-title'] == esc_attr__( 'Mr', 'donator' ) ) ? 'selected="selected"' : '' ); ?> value="<?php esc_attr_e( 'Mr', 'donator' ); ?>"><?php _e( 'Mr', 'donator' ); ?></option>
					<option <?php echo ( ( $_POST['don-title'] == esc_attr__( 'Mrs', 'donator' ) ) ? 'selected="selected"' : '' ); ?> value="<?php esc_attr_e( 'Mrs', 'donator' ); ?>"><?php _e( 'Mrs', 'donator' ); ?></option>
					<option <?php echo ( ( $_POST['don-title'] == esc_attr__( 'Ms', 'donator' ) ) ? 'selected="selected"' : '' ); ?> value="<?php esc_attr_e( 'Ms', 'donator' ); ?>"><?php _e( 'Ms', 'donator' ); ?></option>
					<option <?php echo ( ( $_POST['don-title'] == esc_attr__( 'Miss', 'donator' ) ) ? 'selected="selected"' : '' ); ?> value="<?php esc_attr_e( 'Miss', 'donator' ); ?>"><?php _e( 'Miss', 'donator' ); ?></option>
					<option <?php echo ( ( $_POST['don-title'] == esc_attr__( 'Dr', 'donator' ) ) ? 'selected="selected"' : '' ); ?> value="<?php esc_attr_e( 'Dr', 'donator' ); ?>"><?php _e( 'Dr', 'donator' ); ?></option>
				</select></label></p>

				<p><label><input type="text" placeholder="<?php _e( 'First name', 'donator' ); ?>" name="don-firstname" value="<?php echo ( ( strlen( $_POST['don-firstname'] ) ) ? htmlentities( $_POST['don-firstname'] ) : '' ); ?>"></label></p>
				
				<p><label><input type="text" placeholder="<?php _e( 'Last name', 'donator' ); ?>" name="don-lastname" value="<?php echo ( ( strlen( $_POST['don-lastname'] ) ) ? htmlentities( $_POST['don-lastname'] ) : '' ); ?>"></label></p>

				<p><label><input type="text" placeholder="<?php _e( 'House name/no.', 'donator' ); ?>" name="don-houseno" value="<?php echo ( ( strlen( $_POST['don-houseno'] ) ) ? htmlentities( $_POST['don-houseno'] ) : '' ); ?>"></label></p>

				<p><label><input type="text" placeholder="<?php _e( 'Street', 'donator' ); ?>" name="don-street" value="<?php echo ( ( strlen( $_POST['don-street'] ) ) ? htmlentities( $_POST['don-street'] ) : '' ); ?>"></label></p>
			
			</div><div class="donator-right-col">

				<p><label><input type="text" placeholder="<?php _e( 'Town', 'donator' ); ?>" name="don-town" value="<?php echo ( ( strlen( $_POST['don-town'] ) ) ? htmlentities( $_POST['don-town'] ) : '' ); ?>"></label></p>
				
				<p><label><input type="text" placeholder="<?php _e( 'City', 'donator' ); ?>" name="don-city" value="<?php echo ( ( strlen( $_POST['don-city'] ) ) ? htmlentities( $_POST['don-city'] ) : '' ); ?>"></label></p>

				<p><label><input type="text" placeholder="<?php _e( 'Zip/Post code', 'donator' ); ?>" name="don-postcode" value="<?php echo ( ( strlen( $_POST['don-postcode'] ) ) ? htmlentities( $_POST['don-postcode'] ) : '' ); ?>"></label></p>

				<p><label><select name="don-country" onchange="if(this.value == 'GB'){document.getElementById('donator-giftaid').style.display = 'block';}else{document.getElementById('donator-giftaid').style.display = 'none';}">
					<?php
					foreach ( unserialize( DON_COUNTRIES ) as $don_country_key => $don_country_value ) {
						echo sprintf( __( '<option %s value="%s">%s</option>', 'donator' ),
							( ( $_POST['don-country'] == $don_country_key ) ? 'selected="selected"' : ( ($don_country_key == 'GB') ? 'selected="selected"' : '' ) ),
							$don_country_key,
							$don_country_value
						);
					}
					?>
				</select></label></p>

				<p><label><input type="email" placeholder="<?php _e( 'E-mail', 'donator' ); ?>" name="don-email" value="<?php echo ( ( strlen( $_POST['don-email'] ) ) ? htmlentities( $_POST['don-email'] ) : '' ); ?>"></label></p>

			</div>

		</div>

		<p id="donator-giftaid" style="display: <?php echo ( ( $_POST['don-country'] == 'GB' || !isset( $_POST['don-country'] ) ) ? 'block' : 'none' ); ?>"><label>
			<input type="checkbox" name="don-giftaid" value="true">&nbsp; <img alt="<?php esc_attr_e( 'Gift aid', 'donator' ); ?>" src="<?php echo plugins_url( 'images/giftaid.gif', __FILE__ ); ?>">
			<span><?php sprintf( _e( 'Yes, I am a UK taxpayer and would like %s to treat all donations I have made over the past four years and all donations I make in the future (unless I notify you otherwise) as Gift Aid donations. The tax reclaimed will be used to help fund the whole of our work.', 'donator' ), get_bloginfo( 'blogname' ) ); ?></span>
			<br><br>
			<span><?php _e( 'If this is a personal donation and you will pay enough UK income or capital gains tax (this does not include VAT or council tax) this year, we will be able to claim an additional 25p in Gift Aid from the Government for every £1 you donate, at no extra cost to you.', 'donator' ); ?></span>
		</label></p>

		<p id="donator-submit"><label><input type="submit" value="<?php _e( 'Donate', 'donator' ); ?>" name="don-submit"></label></p>

	</form>

	<!-- Donator Form - End -->

	<?php
}

/*-----------------------------------------------------------------------------------*/
/*	Donate form processing
/*-----------------------------------------------------------------------------------*/

function don_donate_process() {

	if ( isset( $_POST['don-submit'] ) ) {

		$don_donate_error = false;

		$don_currency = trim( $_POST['don-currency'] );
		$don_fixedamount = (int)$_POST['don-fixed-amount'];
		$don_amount = (int)$_POST['don-amount'];
		$don_occurrence = trim( $_POST['don-occurrence'] );
		if ( isset( $_POST['don-type'] ) ) {
			$don_type = (int)$_POST['don-type'];
		} else {
			$don_type = 0;
		}
		$don_title = trim( $_POST['don-title'] );
		$don_firstname = trim( preg_replace( '/\PL/u', '', $_POST['don-firstname'] ) );
		$don_lastname = trim( preg_replace( '/\PL/u', '', $_POST['don-lastname'] ) );
		$don_houseno = trim( $_POST['don-houseno'] );
		$don_street = trim( $_POST['don-street'] );
		$don_town = trim( $_POST['don-town'] );
		$don_city = trim( $_POST['don-city'] );
		$don_postcode = trim( $_POST['don-postcode'] );
		$don_country = trim( $_POST['don-country'] );
		$don_email = trim( $_POST['don-email'] );
		$don_giftaid = trim( $_POST['don-giftaid'] );

		// Validation
		
		if ( strlen( $don_title ) < 10 && 
			strlen( $don_firstname ) && 
			strlen( $don_lastname ) && 
			preg_match( '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $don_email ) && 
			strlen( $don_houseno ) && 
			strlen( $don_street ) && 
			strlen( $don_town ) && 
			strlen( $don_city ) && 
			strlen( $don_postcode ) && 
			strlen( $don_country ) == 2 ) {

			// Amount

			if ( is_numeric( $don_amount ) ) {
				if ( $don_amount > 0 ) {
					$donate_amount = $don_amount;
				} else {
					if ( is_numeric( $don_fixedamount ) ) {
						if ( $don_fixedamount > 0 ) {
							$donate_amount = $don_fixedamount;
						} else {
							$don_donate_error_msg = __( 'Please enter a correct donation amount', 'donator' );
							$don_donate_error = true;
						}
					} else {
						$don_donate_error_msg = __( 'Please enter a correct donation amount', 'donator' );
						$don_donate_error = true;
					}
				}
			}

			// Currency

			if ( in_array( $don_currency, unserialize( DON_CURRENCIES_SINGLE ) ) ) {
				$donate_currency = $don_currency;
			} else {
				$donate_currency = 'GBP';
			}

			// Occurrence

			if ( in_array( $don_occurrence, array( 'monthly', 'oneoff' ) ) ) {
				$donate_occurrence = $don_occurrence;
			} else {
				$donate_occurrence = 'monthly';
			}

			// Check type

			if ( $don_type == 0 ) {
				// Do nothing
			} else {
				if ( !get_term( $don_type, 'donation-type' ) ) {
					$don_type = 0;
				}
			}

			// Gift aid

			if ( $don_giftaid == 'true' ) {
				$donate_giftaid = 'true';
			} else {
				$donate_giftaid = 'false';
			}

			// Process donation

			if ( !$don_donate_error ) {

				// Donor details

				$donor_title = $don_title;
				$donor_firstname = $don_firstname;
				$donor_lastname = $don_lastname;
				$donor_houseno = $don_houseno;
				$donor_street = $don_street;
				$donor_town = $don_town;
				$donor_city = $don_city;
				$donor_postcode = $don_postcode;
				$donor_country = $don_country;
				$donor_email = $don_email;

				// Create new donation post

				$don_donation_details = array(
					'post_title'    => ( ( $donate_occurrence == 'monthly' ) ? __( 'Monthly', 'donator' ) : __( 'One-off', 'donator' ) ) . ' - ' . $donor_firstname . ' ' . $donor_lastname,
					'post_content'  => '',
					'post_status'   => 'publish',
					'post_author'   => 1,
					'post_type'		=> 'donation'
				);

				if ( $don_type == 0 ) {
					// Don't do anything
				} else {
					$don_donation_details['tax_input'] = array( $don_type );
				}

				$don_donation_id = wp_insert_post( $don_donation_details );

				// Save details in post

				update_post_meta( $don_donation_id, 'donation_status', 'Incomplete' );
				update_post_meta( $don_donation_id, 'donation_currency', $donate_currency );
				update_post_meta( $don_donation_id, 'donation_amount', $donate_amount );
				update_post_meta( $don_donation_id, 'donation_occurrence', $donate_occurrence );
				update_post_meta( $don_donation_id, 'donation_giftaid', $donate_giftaid );
				update_post_meta( $don_donation_id, 'donor_title', $donor_title );
				update_post_meta( $don_donation_id, 'donor_firstname', $donor_firstname );
				update_post_meta( $don_donation_id, 'donor_lastname', $donor_lastname );
				update_post_meta( $don_donation_id, 'donor_houseno', $donor_houseno );
				update_post_meta( $don_donation_id, 'donor_street', $donor_street );
				update_post_meta( $don_donation_id, 'donor_town', $donor_town );
				update_post_meta( $don_donation_id, 'donor_city', $donor_city );
				update_post_meta( $don_donation_id, 'donor_postcode', $donor_postcode );
				update_post_meta( $don_donation_id, 'donor_country', $donor_country );
				update_post_meta( $don_donation_id, 'donor_email', $donor_email );

				// Send to paypal

				if ( preg_match( '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', get_option( 'don-d-paypal-email' ) ) ) {
					
					// Paypal setup
					
					$paypal_email = trim( get_option( 'don-d-paypal-email' ) );
					$paypal_return_url = get_page( (int)get_option( 'don-d-paypal-confirm-page' ) );
					$paypal_return_url = get_permalink( $paypal_return_url->ID );
					$paypal_cancel_url = get_page( (int)get_option( 'don-d-paypal-cancel-page' ) );
					$paypal_cancel_url = get_permalink( $paypal_cancel_url->ID );
					$paypal_notify_url = plugins_url( 'donator-ipn.php', __FILE__ );
					
					// Variables
					
					$paypal_name = get_bloginfo( 'name' ) . ': ' . __( 'Donation', 'donator' );
					$paypal_amount = don_format_price( $donate_amount );
					$paypal_id = 'DON' . str_pad( $don_donation_id, 8, '0', STR_PAD_LEFT );
					$paypal_custom = $don_donation_id;

					// Build query string
					
					$paypal_querystring = array(
						'business' => $paypal_email,
						'return' => $paypal_return_url,
						'cancel_return' => $paypal_cancel_url,
						'notify_url' => $paypal_notify_url,
						'item_name' => $paypal_name,
						'item_number' => $paypal_id,
						'custom' => $paypal_custom,
						'currency_code' => $donate_currency,
						'no_note' => 1,
						'bn' => get_bloginfo( 'name' ) . '_Donation_Website',
						'payer_email' => $don_email,
						'email' => $don_email,
						'first_name' => $don_firstname,
						'last_name' => $don_lastname,
						'city' => $don_city,
						'address1' => $don_houseno . ' ' . $don_street,
						'country' => $don_country
					);

					if ( $donate_occurrence == 'monthly' ) {
						$paypal_querystring['a3'] = $paypal_amount;
						$paypal_querystring['p3'] = 1;
						$paypal_querystring['t3'] = 'M';
						$paypal_querystring['src'] = 1;
						$paypal_querystring['sra'] = 1;
						$paypal_querystring['cmd'] = '_xclick-subscriptions';
					} else {
						$paypal_querystring['cmd'] = '_donations';
						$paypal_querystring['amount'] = $paypal_amount;

					}

					$paypal_querystring = http_build_query($paypal_querystring);
					
					// Send to Paypal
					
					if ( get_option( 'don-d-paypal-status' ) == 'live' ) {
						header( 'Location: https://www.paypal.com/cgi-bin/webscr?' . $paypal_querystring );
						exit;
					} else {
						header( 'Location: https://www.sandbox.paypal.com/cgi-bin/webscr?' . $paypal_querystring );
						exit;
					}
				
				}

			} else {
				$don_donate_error_msg = __( 'Please fill in the form correctly', 'donator' );
				$don_donate_error = true;
			}

		} else {
			$don_donate_error_msg = __( 'Please fill in the form correctly', 'donator' );
			$don_donate_error = true;
		}

		if ( $don_donate_error ) {
			session_start();
			$_SESSION['don_donate_error_msg'] = $don_donate_error_msg;
		}

	}

}
add_action( 'init', 'don_donate_process' );

?>