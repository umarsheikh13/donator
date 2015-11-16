<?php
/**
 * donate-post-type.php
 *
 * Adds donation post type.
 *
 * @author		Umar Sheikh <umarsheikh.co.uk>
 * @copyright	2015 Umar Sheikh
 * @since		1.0
 */


function don_donation_register() {
	register_taxonomy( 'donation-type', array( 'donation' ),
		array(
			'labels' => array(
				'name' => __( 'Donation Type', 'donator' ),
				'menu_name' => __( 'Types', 'donator' ),
				'singular_name' => __( 'Type', 'donator' ),
				'add_new_item' => __( 'Add New Donation Type', 'donator' ),
				'all_items' => __( 'All Donation Types', 'donator' )
			),
			'public' => false,
			'hierarchical' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => 'donation-type',           
			'show_in_nav_menus' => true,
			'rewrite' => array(
				'slug' => 'donation-type',
				'hierarchical' => true,
				'with_front' => false
			)
		)
	);
	$labels = array(
		'name' => _x( 'Donations', 'donator' ),
		'singular_name' => _x( 'Donations', 'donator' ),
		'search_items' => _x( 'Search Donations', 'donator' ),
		'popular_items' => _x( 'Popular Donations', '' ),
		'all_items' => _x( 'All Donations', 'donator' ),
		'edit_item' => _x( 'Edit Donation', 'donator' ),
		'update_item' => _x( 'Update Donation', 'donator' ),
		'new_item' => _x( 'New Donation', 'donator' ),
		'add_new' => _x( 'Add New', 'donator' ),
		'add_new_item' => _x( 'Add New Donation', 'donator' ),
		'new_item_name' => _x( 'New Donation', 'donator' ),
		'view_item' => _x( 'View Donation', 'donator' ),
		'not_found' =>  _x( 'No donations found.', 'donator' ),
		'not_found_in_trash' => _x( 'No donations found in Trash', 'donator' )
	);
	$args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'taxonomies' => array( 'donation-type' ),
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-cart',
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title' )
	); 
	register_post_type( 'donation', $args );
}
add_action( 'init', 'don_donation_register' );

// Remove donation links

function don_custom_wp_link_query_args( $query ) {
    $pt_new = array();
    $exclude_types = array( 'donation' );
    foreach ( $query['post_type'] as $pt ) {
        if ( in_array( $pt, $exclude_types ) ) continue;
        $pt_new[] = $pt;
    }
    $query['post_type'] = $pt_new;
    return $query;
}
add_filter( 'wp_link_query_args', 'don_custom_wp_link_query_args' );

// Hide donations page

function don_remove_donations_admin_menu() {
    if ( !current_user_can( 'activate_plugins' ) ) {
        remove_menu_page( 'edit.php?post_type=donation' );
    }
}
add_action( 'admin_menu', 'don_remove_donations_admin_menu', 9999 );

// Change donation messages

function don_donation_type_updated_messages( $messages ) {
	global $post, $post_ID;  
	$messages['donation'] = array( 
		0 => '', 
		1 => sprintf( __( 'Donation updated. <a href="%s">View item</a>', 'donator' ), esc_url( get_permalink( $post_ID ) ) ),  
		2 => __( 'Custom field updated.', 'donator' ),  
		3 => __( 'Custom field deleted.', 'donator' ),  
		4 => __( 'Donation updated.', 'donator' ),  
		5 => isset( $_GET['revision'] ) ? sprintf( __( 'Donation restored to revision from %s', 'donator' ), wp_post_revision_title( (int)$_GET['revision'], false ) ) : false,  
		6 => sprintf( __( 'Donation published. <a href="%s">View donation</a>', 'donator' ), esc_url( get_permalink( $post_ID ) ) ),  
		7 => __( 'Donation saved.', 'donator' ),  
		8 => sprintf( __( 'Donation submitted. <a target="_blank" href="%s">Preview donation</a>', 'donator' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),  
		9 => sprintf( __( 'Donation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview donation</a>', 'donator' ),  
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),  
		10 => sprintf( __( 'Donation draft updated. <a target="_blank" href="%s">Preview donation</a>', 'donator' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),  
	);  
	return $messages;  
}
add_filter( 'post_updated_messages', 'don_donation_type_updated_messages' );

// Rejig the post layout

function don_donation_edit_columns( $columns ) {
	$columns = array(
		"cb" => '<input type="checkbox">',
		"title" => __( "Title", 'donator' ),
		"amount" => __( "Amount", 'donator' ),
		"status" => __( "Status", 'donator' ),		
		"email" => __( "E-mail", 'donator' ),		
		"date" => __( "Date", 'donator' ),
	);
	return $columns;
}
function don_donation_custom_columns( $column ) {
	global $post;
	switch ( $column ) {
		case "amount":
			foreach ( unserialize( DON_CURRENCIES ) as $don_curr_key => $don_curr_value ) {
				if ( get_post_meta( $post->ID, 'donation_currency', true ) == $don_curr_key ) {
					echo $don_curr_value[1];
				}
			}
			echo don_format_price(get_post_meta( $post->ID, 'donation_amount', true ) );
			break;
		case "status":
			echo htmlentities( get_post_meta( $post->ID, 'donation_status', true ) );
			break;
		case "email":
			echo htmlentities( get_post_meta( $post->ID, 'donor_email', true ) );
			break;
	}
}
add_filter( 'manage_edit-donation_columns', 'don_donation_edit_columns' );
add_action( 'manage_posts_custom_column',  'don_donation_custom_columns' );

// Add donation meta boxes

function don_donation_custom_boxes() {
	add_meta_box( 'don_donation_custom_boxes', __( 'Donation details', 'donator' ), 'don_donation_custom_boxes_html', 'donation', 'normal', 'high' );
}
function don_donation_custom_boxes_html( $post ) {
	$don_fields = array(
		array( 'donation_status', __( 'Donation status', 'donator' ), array( 'Incomplete' => 'Incomplete', 'Completed' => 'Completed' ) ),
		array( 'donation_txn_id', __( 'Transaction ID', 'donator' ) ),
		array( 'donation_currency', __( 'Currency', 'donator' ), unserialize( DON_CURRENCIES_SELECT ) ),
		array( 'donation_amount', __( 'Amount', 'donator' ) ),
		array( 'donation_occurrence', __( 'Occurrence', 'donator' ), array( 'monthly' => 'Monthly', 'oneoff' => 'One-off' ) ),
		array( 'donation_giftaid', __( 'Gift aid', 'donator' ), array( 'true' => 'Yes', 'false' => 'No' ) ),
		array( 'break' ),
		array( 'donor_title', __( 'Title', 'donator' ) ),
		array( 'donor_firstname', __( 'First name', 'donator' ) ),
		array( 'donor_lastname', __( 'Last name', 'donator' ) ),
		array( 'donor_houseno', __( 'House name/no.', 'donator' ) ),
		array( 'donor_street', __( 'Street', 'donator' ) ),
		array( 'donor_town', __( 'Town', 'donator' ) ),
		array( 'donor_city', __( 'City', 'donator' ) ),
		array( 'donor_postcode', __( 'Post code', 'donator' ) ),
		array( 'donor_country', __( 'Country', 'donator' ), unserialize( DON_COUNTRIES ) ),
		array( 'donor_email', __( 'E-mail', 'donator' ) )
	);
	?>
	<div style="float: left; width: 48.5%;">
	<p><strong><?php _e( 'Donation no:', 'donator' ); ?> </strong><?php echo get_post_meta( $post->ID, 'donation_no', true ); ?></p>
	<?php
	foreach ( $don_fields as $don_field ) {
		if ( $don_field[0] == 'break' ) {
			?></div><div style="float: right; width: 48.5%;"><?php
		} else if ( isset( $don_field[2] ) ) {
		?>
			<p><strong><?php echo $don_field[1]; ?></strong>
			<select name="don_<?php echo $don_field[0]; ?>">
			<?php
			foreach ( $don_field[2] as $don_select_field_key => $don_select_field_value ) {
				echo '<option value="' . $don_select_field_key . '" ' . ( ( get_post_meta( $post->ID, $don_field[0], true ) == $don_select_field_key ) ? 'selected="selected"' : '' ) . '>' . $don_select_field_value . '</option>';
			}
			?>
			</select>
			</p>
		<?php
		} else {
		?>
		<p><strong><?php echo $don_field[1]; ?></strong><input spellcheck="false" style="width:100%;" type="text" id="don_<?php echo $don_field[0]; ?>" name="don_<?php echo $don_field[0]; ?>" value="<?php echo htmlentities( get_post_meta( $post->ID, $don_field[0], true ) ); ?>"></p>
		<?php
		}
	}
	?>
	</div><div style="clear: both;"></div>
	<?php wp_nonce_field( 'don_donation_item_nonce', 'don_donation_item' ); ?>
	<?php
}
function don_donation_custom_boxes_save_postdata( $post_id ) {
	if ( wp_verify_nonce( $_POST['don_donation_item'], 'don_donation_item_nonce' ) ) {
		$don_fields = array(
			'donation_status',
			'donation_txn_id',
			'donation_currency',
			'donation_amount',
			'donation_occurrence',
			'donation_giftaid',
			'donor_title',
			'donor_firstname',
			'donor_lastname',
			'donor_houseno',
			'donor_street',
			'donor_town',
			'donor_city',
			'donor_postcode',
			'donor_country',
			'donor_email'
		);
		foreach ( $don_fields as $don_field ) {
			update_post_meta( $post_id, $don_field, $_POST['don_'.$don_field] );
		}
	}
}
add_action( 'add_meta_boxes', 'don_donation_custom_boxes' );
add_action( 'save_post', 'don_donation_custom_boxes_save_postdata' );

?>