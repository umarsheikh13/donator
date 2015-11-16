<?php
/**
 * donate-admin.php
 *
 * Options and stats page.
 *
 * @author		Umar Sheikh <umarsheikh.co.uk>
 * @copyright	2015 Umar Sheikh
 * @since		1.0
 */


function don_add_page() {
	add_submenu_page( 'edit.php?post_type=donation', __( 'Stats', 'donator' ), __( 'Stats', 'donator' ), 'manage_options', 'donator-stats', 'don_options_stats_page' );
	add_submenu_page( 'edit.php?post_type=donation', __( 'Settings', 'donator' ), __( 'Settings', 'donator' ), 'manage_options', 'donator-options', 'don_options_page' );
}
add_action( 'admin_menu', 'don_add_page' );

function don_options_page() {

	global $table_prefix, $wpdb;
	
	// Pull all the pages into an array
	$options_pages = array();  
	$options_pages_obj = get_pages( 'sort_column=post_parent,menu_order' );
	$options_pages[''] = __( 'Select a page:', 'els' );
	foreach ( $options_pages_obj as $page ) {
    	$options_pages[$page->ID] = $page->post_title;
	}

	if ( isset( $_POST['submit'] ) ) {
		foreach ( $_POST as $key => $value ) {
			if ( $key !== 'submit' ) {
				update_option( $key, trim( $value ) );
			}
		}
		$successful = "<div id='message' class='updated fade'><p>" . __( 'Settings saved successfully', 'donator' ) . "</p></div>";
	}

?>

<div class="wrap">

	<?php echo $successful; ?>

	<h2><?php _e( 'Settings', 'donator' ); ?></h2>

	<form action="#" method="post">

		<table class="form-table">
			
			<tr valign="top">
				<th scope="row"><label for="don-d-admin-email"><?php _e( 'Admin E-mail', 'donator' ); ?></label></th>
				<td><input id="don-d-admin-email" style="width:200px" type="text" name="don-d-admin-email" value="<?php echo get_option( 'don-d-admin-email' ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="don-d-paypal-email"><?php _e( 'Paypal E-mail', 'donator' ); ?></label></th>
				<td><input id="don-d-paypal-email" style="width:200px" type="text" name="don-d-paypal-email" value="<?php echo get_option( 'don-d-paypal-email' ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="don-d-paypal-status"><?php _e( 'Paypal Status', 'donator' ); ?></label></th>
				<td>
					<select name="don-d-paypal-status">
						<option <?php echo ( ( get_option( 'don-d-paypal-status' ) == 'live' ) ? 'selected="selected"' : '' ); ?> value="live"><?php _e( 'Live', 'donator' ); ?></option>
						<option <?php echo ( ( get_option( 'don-d-paypal-status' ) == 'sandbox' ) ? 'selected="selected"' : '' ); ?> value="sandbox"><?php _e( 'Sandbox', 'donator' ); ?></option>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="don-d-paypal-confirm-page"><?php _e( 'Confirmation Page', 'donator' ); ?></label></th>
				<td>
					<select name="don-d-paypal-confirm-page">
						<?php
						foreach ( $options_pages as $opt_pg_key => $opt_pg_value ) {
							echo sprintf( __( '<option %s value="%s">%s</option>', 'donator' ),
								( ( get_option( 'don-d-paypal-confirm-page' ) == $opt_pg_key ) ? 'selected="selected"' : '' ),
								$opt_pg_key,
								$opt_pg_value
							);
						}
						?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="don-d-paypal-cancel-page"><?php _e( 'Cancellation Page', 'donator' ); ?></label></th>
				<td>
					<select name="don-d-paypal-cancel-page">
						<?php
						foreach ( $options_pages as $opt_pg_key => $opt_pg_value ) {
							echo sprintf( __( '<option %s value="%s">%s</option>', 'donator' ),
								( ( get_option( 'don-d-paypal-cancel-page' ) == $opt_pg_key ) ? 'selected="selected"' : '' ),
								$opt_pg_key,
								$opt_pg_value
							);
						}
						?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="don-d-thankyou-email"><?php _e( 'Thank You Email:', 'donator' ); ?> (Use %NAME%, %DONATIONNO%, %AMOUNT%)</label></th>
				<td><textarea id="don-d-thankyou-email" name="don-d-thankyou-email" style="height:300px; width:99%;" rows="1" cols="1"><?php echo htmlentities( utf8_decode( stripslashes( get_option( 'don-d-thankyou-email' ) ) ) ); ?></textarea></td>
			</tr>
			
		</table>

		<p class="submit"><input type="submit" value="<?php esc_attr_e( 'Save Changes', 'donator' ); ?>" class="button button-primary" id="submit" name="submit"></p>

	</form>

</div>

<?php
}

function don_options_stats_page() {

	// Date vars

	if ( isset( $_POST['don-month'] ) ) {
		$don_month = $_POST['don-month'];
	} else {
		$don_month = ( date( 'm' ) * 1 );
	}
	if ( isset( $_POST['don-year'] ) ) {
		$don_year = $_POST['don-year'];
	} else {
		$don_year = ( date( 'Y' ) * 1 );
	}

	// Total donations

	$don_meta_query = new WP_Query( 'post_type=donation&meta_value=Completed&year=' . $don_year . '&monthnum=' . $don_month );
	$don_total_donations = $don_meta_query->found_posts;
	wp_reset_postdata();

	// Get total donation amounts

	$don_donations = get_posts( 'meta_value=Completed&posts_per_page=9999999&post_type=donation&year=' . $don_year . '&monthnum=' . $don_month );
	if ( count( $don_donations ) ) {
		$don_totals = array();
		foreach ( unserialize( DON_CURRENCIES ) as $don_curr_key => $don_curr_value ) {
			$don_totals[$don_curr_key] = 0;
		}
		foreach ( $don_donations as $don_donation ) {
			foreach ( unserialize( DON_CURRENCIES ) as $don_curr_key => $don_curr_value ) {
				if ( get_post_meta( $don_donation->ID, 'donation_currency', true ) == $don_curr_key ) {
					$don_totals[$don_curr_key] += (int)get_post_meta( $don_donation->ID, 'donation_amount', true );
				}
			}
		}
	}

?>

<div class="wrap">

	<?php echo $successful; ?>

	<h2><?php _e( 'Stats', 'donator' ); ?></h2>

	<form action="#" method="post">

		<table class="form-table">
			
			<tr valign="top">
				<th scope="row"><label><?php _e( 'Month/Year', 'donator' ); ?></label></th>
				<td>
					<select name="don-month">
						<option value="1" <?php echo ( ( $_POST['don-month'] == '1' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 1 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'January', 'donator' ); ?></option>
						<option value="2" <?php echo ( ( $_POST['don-month'] == '2' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 2 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'February', 'donator' ); ?></option>
						<option value="3" <?php echo ( ( $_POST['don-month'] == '3' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 3 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'March', 'donator' ); ?></option>
						<option value="4" <?php echo ( ( $_POST['don-month'] == '4' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 4 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'April', 'donator' ); ?></option>
						<option value="5" <?php echo ( ( $_POST['don-month'] == '5' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 5 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'May', 'donator' ); ?></option>
						<option value="6" <?php echo ( ( $_POST['don-month'] == '6' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 6 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'June', 'donator' ); ?></option>
						<option value="7" <?php echo ( ( $_POST['don-month'] == '7' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 7 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'July', 'donator' ); ?></option>
						<option value="8" <?php echo ( ( $_POST['don-month'] == '8' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 8 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'August', 'donator' ); ?></option>
						<option value="9" <?php echo ( ( $_POST['don-month'] == '9' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 9 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'September', 'donator' ); ?></option>
						<option value="10" <?php echo ( ( $_POST['don-month'] == '10' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 10 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'October', 'donator' ); ?></option>
						<option value="11" <?php echo ( ( $_POST['don-month'] == '11' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 11 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'November', 'donator' ); ?></option>
						<option value="12" <?php echo ( ( $_POST['don-month'] == '12' ) ? 'selected="selected"' : ( ( ( date( 'm' ) * 1 ) == 12 && !isset( $_POST['don-month'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php _e( 'December', 'donator' ); ?></option>
					</select>&nbsp;<select name="don-year">
						<option value="<?php echo date( 'Y' ) - 1; ?>" <?php echo ( ( $_POST['don-year'] == ( date( 'Y' ) - 1 ) ) ? 'selected="selected"' : '' ); ?>><?php echo date( 'Y' ) - 1; ?></option>
						<option value="<?php echo date( 'Y' ); ?>" <?php echo ( ( $_POST['don-year'] == ( date( 'Y' ) ) ) ? 'selected="selected"' : ( ( !isset( $_POST['don-year'] ) ) ? 'selected="selected"' : '' ) ); ?>><?php echo date( 'Y' ); ?></option>
						<option value="<?php echo date( 'Y' ) + 1; ?>" <?php echo ( ( $_POST['don-year'] == ( date( 'Y' ) + 1 ) ) ? 'selected="selected"' : '' ); ?>><?php echo date( 'Y' ) + 1; ?></option>
					</select>&nbsp;<input type="submit" value="Submit" class="button button-primary" id="submit" name="submit">
				</td>
			</tr>

			<?php
			foreach ( unserialize( DON_CURRENCIES ) as $don_curr_key => $don_curr_value ) {
				?>
				<tr valign="top">
					<th scope="row"><label><?php echo sprintf( __( 'Total %s', 'donator' ), $don_curr_key ); ?></label></th>
					<td><?php echo don_format_price( $don_totals[$don_curr_key] ); ?></td>
				</tr>
				<?php
			}
			?>

			<tr valign="top">
				<th scope="row"><label><?php _e( 'Total donations', 'donator' ); ?></label></th>
				<td><?php echo $don_total_donations; ?></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label><?php _e( 'Export donations', 'donator' ); ?></label></th>
				<td><a href="<?php echo plugins_url( 'donator-export.php?export=1&amp;month=' . $don_month . '&amp;year=' . $don_year, __FILE__ ); ?>" class="button button-primary"><?php _e( 'Export', 'donator' ); ?></a></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label><?php _e( 'Export gift aid donations', 'donator' ); ?></label></th>
				<td><a href="<?php echo plugins_url( 'donator-export-ga.php?export=1&amp;month=' . $don_month . '&amp;year=' . $don_year, __FILE__ ); ?>" class="button button-primary"><?php _e( 'Export', 'donator' ); ?></a></td>
			</tr>

	</form>

</div>

<?php
}

?>