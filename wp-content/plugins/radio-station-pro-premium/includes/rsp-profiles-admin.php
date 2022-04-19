<?php

// ----------------------
// === Profiles Admin ===
// ----------------------
// - Turn on Profile Editing Interfaces
// - Add User Profile Submenu Links
// - Add Profile Edit Redirects
// - Add Profile Fields Metabox
// - Profile Info Metabox
// - Save Profile Info
// - User Assigned Error Message
// - Profile Error Notice Fix
// === Hosts ===
// - Add Host Submenu Item
// - Add Assign Host User Metabox
// - Assign Host User Metabox
// - Save Host User
// === Producers ===
// - Add Producer Submenu Item
// - Add Assign Producer User Metabox
// - Assign Producer User Metabox
// - Save Producer User


// ----------------------------------
// Turn on Profile Editing Interfaces
// ----------------------------------
add_filter( 'radio_station_host_interface', '__return_true' );
add_filter( 'radio_station_producer_interface', '__return_true' );

// ------------------------------
// Add User Profile Submenu Links
// ------------------------------
// 2.4.1.4: link to user host/producer profile page
add_action( 'admin_menu', 'radio_station_pro_add_user_profile_links' );
function radio_station_pro_add_user_profile_links() {

	$user = wp_get_current_user();
	if ( in_array( 'dj', $user->roles ) ) {
		add_users_page( __( 'Edit Your Host Profile', 'radio-station' ), __( 'Host Profile', 'radio-station' ), 'publish_hosts', 'edit_host_profile', 'radio_station_dummy_function' );
	}
	if ( in_array( 'producer', $user->roles ) ) {
		add_users_page( __( 'Edit Your Producer Profile', 'radio-station' ), __( 'Producer Profile', 'radio-station' ), 'publish_producers', 'edit_producer_profile', 'radio_station_dummy_function' );
	}

}

// --------------------------
// Add Profile Edit Redirects
// --------------------------
add_action( 'admin_init', 'radio_station_pro_user_profile_redirects' );
function radio_station_pro_user_profile_redirects() {
	if ( !isset( $_REQUEST['page'] ) ) {
		return;
	}
	if ( 'edit_host_profile' == $_REQUEST['page'] ) {
		$meta_key = 'host_user_id';
		$post_type = RADIO_STATION_HOST_SLUG;
	} elseif ( 'edit_producer_profile' == $_REQUEST['page'] ) {
		$meta_key = 'producer_user_id';
		$post_type = RADIO_STATION_PRODUCER_SLUG;
	} else {
		return;
	}

	// --- maybe get existing profile ---
	global $wpdb;
	$user = wp_get_current_user();
	$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta";
	$query .= " WHERE meta_key = '" . $meta_key . "' AND meta_value = %d";
	$query = $wpdb->prepare( $query, $user->ID );
	$post_id = $wpdb->get_var( $query );
	if ( !$post_id ) {
		// --- create draft profile ---
		$post = array(
			'post_title'  => $user->display_name,
			'post_type'   => $post_type,
			'post_status' => 'draft',
			'meta_input'  => array(
				// 2.4.1.6: fix to use meta_key variable
				$meta_key => $user->ID,
			),
		);
		$post_id = wp_insert_post( $post );
	}

	// --- redirect to edit profile page ---
	if ( $post_id ) {
		$redirect_to = admin_url( 'post.php' );
		$redirect_to = add_query_arg( 'post', $post_id, $redirect_to );
		$redirect_to = add_query_arg( 'action', 'edit', $redirect_to );
		wp_redirect( $redirect_to );
		exit;
	}
}

// --------------------------
// Add Profile Fields Metabox
// --------------------------
add_action( 'add_meta_boxes', 'radio_station_pro_add_profile_metabox' );
function radio_station_pro_add_profile_metabox() {
	$position = apply_filters( 'radio_station_metabox_position', 'rstop', 'profiles' );
	add_meta_box(
		'radio-station-profile-info-metabox',
		__( 'Profile Information', 'radio-station' ),
		'radio_station_pro_profile_info_metabox',
		array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG ),
		$position,
		'high'
	);
}

// --------------------
// Profile Info Metabox
// --------------------
function radio_station_pro_profile_info_metabox() {

	// --- setup data ---
	global $post;
	$post_id = 0;
	if ( property_exists( $post, 'ID' ) ) {
		$post_id = $post->ID;
	}
	$post_type = $post->post_type;
	$type = str_replace( 'rs-', '', $post_type );
	$post_type_object = get_post_type_object( $post_type );
	$singular_label = $post_type_object->labels->singular_name;
	$plural_label = $post_type_object->labels->name;

	// --- nonce field ---
	wp_nonce_field( 'radio-station', $type . '_meta_nonce' );

	// --- get show meta ---
	$link = get_post_meta( $post_id, 'profile_link', true );
	$email = get_post_meta( $post_id, 'profile_email', true );
	$phone = get_post_meta( $post_id, 'profile_phone', true );
	$patreon_id = get_post_meta( $post_id, 'profile_patreon', true );
	// $file = get_post_meta( $post_id, 'profile_file', true );
	// $download = get_post_meta( $post_id, 'profile_download', true );

	// --- open meta inner box ---
	echo '<div class="meta_inner">' . PHP_EOL;

		// --- start profile input list ---
		echo '<ul class="profile-input-list">' . PHP_EOL;

			// --- show website link ---
			echo '<li class="profile-item">' . PHP_EOL;
				echo '<div class="input-label"><label>';
				echo esc_html( __( 'Website Link', 'radio-station' ) ) . ':' . PHP_EOL;
				echo '</label></div>';
				echo '<div class="input-field">' . PHP_EOL;
				echo '<input type="text" name="profile_link" size="100" style="max-width:80%;" value="' . esc_url( $link ) . '">' . PHP_EOL;
				echo '</div>' . PHP_EOL;
			echo '</li>' . PHP_EOL;

			// --- show email address ---
			echo '<li class="profile-item">' . PHP_EOL;
				echo '<div class="input-label"><label>';
				$label = __( '%s Email', 'radio-station' );
				$label = sprintf( $label, $singular_label );
				echo esc_html( $label ) . ':' . PHP_EOL;
				echo '</label></div>' . PHP_EOL;
				echo '<div class="input-field">' . PHP_EOL;
				echo '<input type="text" name="profile_email" size="100" style="max-width:80%;" value="' . esc_attr( $email ) . '">' . PHP_EOL;
				echo '</div>'. PHP_EOL;
			echo '</li>' . PHP_EOL;

			// --- show phone number ---
			echo '<li class="profile-item">' . PHP_EOL;
				echo '<div class="input-label"><label>' . PHP_EOL;
				echo esc_html( __( 'Show Phone', 'radio-station' ) ) . ':' . PHP_EOL;
				echo '</label></div>' . PHP_EOL;
				echo '<div class="input-field">' . PHP_EOL;
				echo '<input type="text" name="profile_phone" size="100" style="max-width:80%;" value="' . esc_attr( $phone ) . '">' . PHP_EOL;
				echo '</div>' . PHP_EOL;
			echo '</li>' . PHP_EOL;

			// --- Patreon ID ---
			echo '<li class="profile-item">' . PHP_EOL;
				echo '<div class="input-label"><label>' . PHP_EOL;
				echo esc_html( __( 'Patreon Page ID', 'radio-station' ) ) . ':' . PHP_EOL;
				echo '</label></div>' . PHP_EOL;
				echo '<div class="input-field">' . PHP_EOL;
				echo ' https://patreon.com/<input type="text" name="profile_patreon" size="80" style="max-width:50%;" value="' . esc_attr( $patreon_id ) . '">' . PHP_EOL;
				echo '</div>' . PHP_EOL;
			echo '</li>' . PHP_EOL;

			// --- do additional fields action ---
			do_action( 'radio_station_' . $type . '_fields', $post_id, $type );

		// --- close porfile input list ---
		echo '</ul>' . PHP_EOL;

	// --- close meta inner box ---
	echo '</div>' . PHP_EOL;

	// --- set metabox styles ---
	$css = ".profile-input-list, .profile-item {list-style: none;}
	.profile-item .input-label, .profile-item .input-field, .profile-item .input-helper {display: inline-block;}
	.profile-item .input-label {width: 120px;}
	.profile-item .input-field, .profile-item .input-helper {margin-left: 20px;}
	.profile-item .input-field {max-width: 80%;}" . PHP_EOL;

	// --- filter and output styles ---
	$css = apply_filters( 'radio_station_profile_edit_styles', $css );
	// phpcs:ignore WordPress.Security.OutputNotEscaped
	echo '<style>' . $css . '</style>';
}

// --------------------
// Add Images Metaboxes
// --------------------
add_action( 'add_meta_boxes', 'radio_station_pro_add_image_metaboxes' );
function radio_station_pro_add_image_metaboxes() {
	add_meta_box(
		'radio-station-images-metabox',
		__( 'Host Image', 'radio-station' ),
		'radio_station_pro_profile_images_metabox',
		RADIO_STATION_HOST_SLUG,
		'side',
		'high'
	);
	add_meta_box(
		'radio-station-images-metabox',
		__( 'Producer Image', 'radio-station' ),
		'radio_station_pro_profile_images_metabox',
		RADIO_STATION_PRODUCER_SLUG,
		'side',
		'high'
	);
	add_meta_box(
		'radio-station-images-metabox',
		__( 'Episode Image', 'radio-station' ),
		'radio_station_pro_profile_images_metabox',
		RADIO_STATION_EPISODE_SLUG,
		'side',
		'high'
	);
}

// --------------
// Images Metabox
// --------------
function radio_station_pro_profile_images_metabox() {

	global $post;
	$post_type = $post->post_type;
	$post_type_object = get_post_type_object( $post_type );
	$type = ( RADIO_STATION_EPISODE_SLUG == $post_type ) ? 'episode' : 'profile';
	$singular_label = $post_type_object->labels->singular_name;

	wp_nonce_field( 'radio-station', $type . '_images_nonce' );
	$upload_link = get_upload_iframe_src( 'image', $post->ID );

	// --- get avatar image info ---
	$avatar = get_post_meta( $post->ID, $type . '_avatar', true );
	$avatar_src = wp_get_attachment_image_src( $avatar, 'full' );
	$has_avatar = is_array( $avatar_src );

	// --- show avatar image ---
	echo '<div id="profile-avatar-image">' . PHP_EOL;

	// --- image container ---
	echo '<div class="custom-image-container">' . PHP_EOL;
	if ( $has_avatar ) {
		echo '<img src="' . esc_url( $avatar_src[0] ) . '" alt="" style="max-width:100%;">' . PHP_EOL;
	}
	echo '</div>' . PHP_EOL;

	// --- add and remove links ---
	echo '<p class="hide-if-no-js">' . PHP_EOL;
	$hidden = '';
	if ( $has_avatar ) {
		$hidden = ' hidden';
	}
	echo '<a class="upload-custom-image' . esc_attr( $hidden ) . '" href="' . esc_url( $upload_link ) . '">' . PHP_EOL;
	$set_text = __( 'Set %s Avatar Image', 'radio-station' );
	$set_text = sprintf( $set_text, $singular_label );
	echo esc_html( $set_text ) . PHP_EOL;
	echo '</a>' . PHP_EOL;
	$hidden = '';
	if ( !$has_avatar ) {
		$hidden = ' hidden';
	}
	echo '<a class="delete-custom-image' . esc_attr( $hidden ) . '" href="#">' . PHP_EOL;
	$remove_text = __( 'Remove %s Avatar Image', 'radio-station' );
	$remove_text = sprintf( $remove_text, $singular_label );
	echo esc_html( $remove_text ) . PHP_EOL;
	echo '</a>' . PHP_EOL;
	echo '</p>' . PHP_EOL;

	// --- hidden input for image ID ---
	echo '<input class="custom-image-id" name="' . esc_js( $type ) . '_avatar" type="hidden" value="' . esc_attr( $avatar ) . '">' . PHP_EOL;

	echo '</div>' . PHP_EOL;

	// --- set images autosave nonce and iframe ---
	$images_autosave_nonce = wp_create_nonce( 'profile-images-autosave' );
	echo '<input type="hidden" id="profile-images-save-nonce" value="' . esc_attr( $images_autosave_nonce ) . '">' . PHP_EOL;
	echo '<iframe src="javascript:void(0);" name="profile-images-save-frame" id="profile-images-save-frame" style="display:none;"></iframe>' . PHP_EOL;

	// --- script text strings ---
	$confirm_remove = __( 'Are you sure you want to remove this image?', 'radio-station' );
	$media_title_text = __( 'Select or Upload Image' ,'radio-station' );
	$media_button_text = __( 'Use this Image', 'radio-station' );

	// --- image selection script ---
	$js = "var mediaframe, parentdiv,
	imagesmetabox = jQuery('#radio-station-images-metabox'),
	addimagelink = imagesmetabox.find('.upload-custom-image'),
	deleteimagelink = imagesmetabox.find('.delete-custom-image'),
	imageconfirmremove = '" . esc_js( $confirm_remove ) . "',
	wpmediatitletext = '" . esc_js( $media_title_text ). "',
	wpmediabuttontext = '" . esc_js( $media_button_text ) . "';

	/* Add Image on Click */
	addimagelink.on( 'click', function( event ) {

		event.preventDefault();
		parentdiv = jQuery(this).parent().parent();

		if (mediaframe) {mediaframe.open(); return;}
		mediaframe = wp.media({
			title: wpmediatitletext,
			button: {text: wpmediabuttontext},
			library: {order: 'DESC', orderby: 'date', type: 'image', search: null, uploadedTo: null},
			multiple: false
		});

		mediaframe.on( 'select', function() {
			var attachment = mediaframe.state().get('selection').first().toJSON();
			image = '<img src=\"'+attachment.url+'\" alt=\"\" style=\"max-width:100%;\"/>';
			parentdiv.find('.custom-image-container').append(image);
			parentdiv.find('.custom-image-id').val(attachment.id);
			parentdiv.find('.upload-custom-image').addClass('hidden');
			parentdiv.find('.delete-custom-image').removeClass('hidden');

			/* auto-save image via AJAX */
			postid = jQuery('#post_ID').val(); imgid = attachment.id;
			imagessavenonce = jQuery('#profile-images-save-nonce').val();
			framesrc = ajaxurl+'?action=radio_station_profile_images_save';
			framesrc += '&post_id='+postid+'&image_id='+imgid+'&nonce='+imagessavenonce;
			jQuery('#profile-images-save-frame').attr('src', framesrc);
		});

		mediaframe.open();
	});

	/* Delete Image on Click */
	deleteimagelink.on( 'click', function( event ) {
		event.preventDefault();
		agree = confirm(imageconfirmremove);
		if (!agree) {return;}
		parentdiv = jQuery(this).parent().parent();
		parentdiv.find('.custom-image-container').html('');
		parentdiv.find('.custom-image-id').val('');
		parentdiv.find('.upload-custom-image').removeClass('hidden');
		parentdiv.find('.delete-custom-image').addClass('hidden');
	});
	" . PHP_EOL;

	// --- enqueue script inline ---
	wp_add_inline_script( 'radio-station-admin', $js );

	// --- media modal close button style fix ---
	echo '<style>.media-modal-close {z-index: 1001 !important;</style>';
}


// -----------------
// Save Profile Info
// -----------------
add_action( 'save_post', 'radio_station_pro_profile_save_data', 11 );
add_action( 'publish_post', 'radio_station_pro_profile_save_data', 11 );
function radio_station_pro_profile_save_data( $post_id ) {

	// --- verify if this is an auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- setup data ---
	$post = get_post( $post_id );
	$post_type = $post->post_type;
	$type = str_replace( 'rs-', '', $post_type );

	// --- check post type ---
	// 2.4.1.2: bug out early for mismatching post types
	// 2.4.1.6: fix to backwards conditional logic on save
	$profile_post_types = array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
	if ( !in_array( $post_type, $profile_post_types ) ) {
		return;
	}

	// --- set show meta changed flags ---
	$profile_meta_changed = false;

	// --- save show meta data ---
	if ( isset( $_POST[$type . '_meta_nonce'] ) && wp_verify_nonce( $_POST[$type . '_meta_nonce'], 'radio-station' ) ) {

		// --- get the meta data to be saved ---
		$link = radio_station_sanitize_input( 'profile' , 'link' );
		$email = radio_station_sanitize_input( 'profile', 'email' );
		$phone = radio_station_sanitize_input( 'profile', 'phone' );
		$patreon_id = radio_station_sanitize_input( 'profile', 'patreon' );

		// --- get existing values and check if changed ---
		$prev_link = get_post_meta( $post_id, 'profile_link', true );
		$prev_email = get_post_meta( $post_id, 'profile_email', true );
		$prev_phone = get_post_meta( $post_id, 'profile_phone', true );
		$prev_patreon_id = get_post_meta( $post_id, 'profile_patreon', true );
		if ( ( $prev_link != $link ) || ( $prev_email != $email ) || ( $prev_phone != $phone )
		     || ( $prev_patreon_id != $patreon_id ) ) {
			$profile_meta_changed = true;
		}

		// --- update the profile metadata ---
		update_post_meta( $post_id, 'profile_link', $link );
		update_post_meta( $post_id, 'profile_email', $email );
		update_post_meta( $post_id, 'profile_phone', $phone );
		update_post_meta( $post_id, 'profile_patreon', $patreon_id );

	}

	// --- update the profile avatar ---
	$profile_avatar_changed = false;
	if ( isset( $_POST['profile_images_nonce'] ) && wp_verify_nonce( $_POST['profile_images_nonce'], 'radio-station' ) ) {
		$prev_avatar = get_post_meta( $post_id, 'profile_avatar', true );
		$avatar = absint( $_POST['profile_avatar'] );
		if ( $avatar > 0 ) {
			update_post_meta( $post_id, 'profile_avatar', $avatar );
			if ( $prev_avatar != $avatar ) {
				$profile_avatar_changed = true;
			}
		}
	}

	// --- maybe clear transient data ---
	if ( $profile_meta_changed || $profile_avatar_changed ) {
		radio_station_clear_cached_data( $post_id );
	}

}

// ---------------------------
// User Assigned Error Message
// ---------------------------
// (single profile per user)
add_filter( 'post_updated_messages', 'radio_station_pro_profile_error_message' );
function radio_station_pro_profile_error_message( $messages ) {

	global $pagenow, $post;

	if ( !isset( $pagenow ) || ( 'post.php' != $pagenow ) ) {
		return;
	}
	$profile_post_types = array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
	if ( !property_exists( $post, 'post_type' ) || !in_array( $post->post_type, $profile_post_types ) ) {
		return;
	}
	$error = get_post_meta( $post->ID, 'profile_id_error', true );
	if ( !$error ) {
		return;
	}

	// --- set error message ---
	$post_type_object = get_post_type_object( $post->post_type );
	$singular_label = $post_type_object->labels->singular_name;
	$message = __( 'User Save Error! The selected User is already assigned to a %s Profile.', 'radio-station' );
	$message = sprintf( $message, $singular_label );
	$message = '<span id="user-assigned-error">' . $message . '</span>';
	delete_post_meta( $post->ID, 'profile_id_error' );
	$messages[$post->post_type][11] = $message;

	// --- override with error message ---
	// $ignore_codes = array( );
	// if ( !isset( $_GET['message'] ) || !in_array $_GET['message'], $ignore_codes ) {
		$_GET['message'] = 11;
	// }

	add_action( 'admin_footer', 'radio_station_pro_profile_error_fix' );

	return $messages;
}

// ------------------------
// Profile Error Notice Fix
// ------------------------
function radio_station_pro_profile_error_fix() {
	echo "<script>jQuery('#user-assigned-error').parent().parent().removeClass('updated').removeClass('notice-success').addClass('notice-error');</script>";
}


// -------------
// === Hosts ===
// -------------

// ---------------------
// Add Host Submenu Item
// ---------------------
add_action( 'radio_station_admin_submenu_middle', 'radio_station_pro_add_hosts_submenu', 11 );
function radio_station_pro_add_hosts_submenu() {
	$rs = __( 'Radio Station', 'radio-station' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Hosts', 'radio-station' ), __( 'Hosts', 'radio-station' ), 'publish_hosts', 'hosts' );
}

// ----------------------------
// Add Assign Host User Metabox
// ----------------------------
add_action( 'add_meta_boxes', 'radio_station_pro_add_host_metabox' );
function radio_station_pro_add_host_metabox() {
	add_meta_box(
		'radio-station-pro-host-metabox',
		__( 'Host User', 'radio-station' ),
		'radio_station_pro_host_metabox',
		RADIO_STATION_HOST_SLUG,
		'side',
		'high'
	);
}

// ------------------------
// Assign Host User Metabox
// ------------------------
function radio_station_pro_host_metabox() {

	global $post, $wp_roles, $wpdb;

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station-pro', 'host_user_nonce' );

	// --- check for DJ / Host roles ---
	$args = array(
		'role__in' => array( 'dj', 'administrator', 'show-editor' ),
		'orderby'  => 'display_name',
		'order'    => 'ASC'
	);
	$hosts = get_users( $args );

	// --- get current Host user ---
	$current = get_post_meta( $post->ID, 'host_user_id', true );

	// --- move any selected Hosts to the top of the list ---
	foreach ( $hosts as $i => $host ) {
		if ( $current == $host->ID ) {
			unset( $hosts[$i] );
			array_unshift( $hosts, $host );
		}
	}

	// --- Host Selection Input ---
	echo '<div id="meta_inner">';
		echo '<select name="host_user_id" style="width: 100%;">';
			echo '<option value=""></option>';
			foreach ( $hosts as $host ) {
				$display_name = $host->display_name;
				if ( $host->display_name !== $host->user_login ) {
					$display_name .= ' (' . $host->user_login . ')';
				}
				echo '<option value="' . esc_attr( $host->ID ) . '"';
				if ( $current == $host->ID ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $display_name ) . '</option>';
			}
        echo '</select>';
	echo '</div>';
}

// --------------
// Save Host User
// --------------
add_action( 'save_post', 'radio_station_pro_host_save_data' );
add_action( 'publish_post', 'radio_station_pro_host_save_data' );
function radio_station_pro_host_save_data( $post_id ) {

	// --- do not save when doing autosaves ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- check producer user field is set ---
	if ( isset( $_POST['host_user_id'] ) ) {

		// ---  verify field save nonce ---
		if ( !isset( $_POST['host_user_nonce'] ) || !wp_verify_nonce( $_POST['host_user_nonce'], 'radio-station-pro' ) ) {
			return;
		}

		// --- get the user ID ---
		$changed = false;
		$prev_user_id = get_post_meta( $post_id, 'host_user_id', true );
		$user_id = trim( $_POST['host_user_id'] );

		if ( empty( $user_id ) ) {
			// --- remove user ID ---
			delete_post_meta( $post_id, 'host_user_id' );
			if ( $prev_user_id ) {
				$changed = true;
			}
		} elseif ( $user_id != $prev_user_id ) {

			// --- sanitize to numeric before updating ---
			$user_id = absint( $user_id );
			if ( $user_id > 0 ) {
				$user = get_user_by( 'ID', $user_id );
				if ( $user ) {
					// --- check if user already assigned ---
					global $wpdb;
					$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'host_user_id' AND meta_value = %d";
					$query = $wpdb->prepare( $query, $user_id );
					$profile_id = $wpdb->get_var( $query );
					if ( $profile_id ) {
						// --- trigger user overlap error message ---
						update_post_meta( $post_id, 'profile_id_error', 1 );
					} else {
						update_post_meta( $post_id, 'host_user_id', $user_id );
						$changed = true;
					}
				}
			}
		}

		// if ( $changed ) {
		//
		// }
	}
}


// -----------------
// === Producers ===
// -----------------

// -------------------------
// Add Producer Submenu Item
// -------------------------
add_action( 'radio_station_admin_submenu_middle', 'radio_station_pro_add_producers_submenu', 12 );
function radio_station_pro_add_producers_submenu() {
	$rs = __( 'Radio Station', 'radio-station' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Producers', 'radio-station' ), __( 'Producers', 'radio-station' ), 'publish_producers', 'producers' );
}

// --------------------------------
// Add Assign Producer User Metabox
// --------------------------------
add_action( 'add_meta_boxes', 'radio_station_pro_add_producer_metabox' );
function radio_station_pro_add_producer_metabox() {
	add_meta_box(
		'radio-station-pro-producer-metabox',
		__( 'Producer User', 'radio-station' ),
		'radio_station_pro_producer_metabox',
		RADIO_STATION_PRODUCER_SLUG,
		'side',
		'high'
	);
}

// ----------------------------
// Assign Producer User Metabox
// ----------------------------
function radio_station_pro_producer_metabox() {

	global $post, $wp_roles, $wpdb;

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station-pro', 'producer_user_nonce' );

	// --- check for Producer roles ---
	$args = array(
		'role__in' => array( 'producer', 'administrator', 'show-editor' ),
		'orderby'  => 'display_name',
		'order'    => 'ASC'
	);
	$producers = get_users( $args );

	// --- get current Producer user ---
	$current = get_post_meta( $post->ID, 'producer_user_id', true );

	// --- move any selected DJs to the top of the list ---
	foreach ( $producers as $i => $producer ) {
		if ( $current == $producer->ID ) {
			unset( $producers[$i] );
			array_unshift( $producers, $producer );
		}
	}

	// --- Producer Selection Input ---
	echo '<div id="meta_inner">';
		echo '<select name="producer_user_id" style="width: 100%;">';
			echo '<option value=""></option>';
			foreach ( $producers as $producer ) {
				$display_name = $producer->display_name;
				if ( $producer->display_name !== $producer->user_login ) {
					$display_name .= ' (' . $producer->user_login . ')';
				}
				echo '<option value="' . esc_attr( $producer->ID ) . '"';
				if ( $current == $producer->ID ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $display_name ) . '</option>';
			}
		echo '</select>';
	echo '</div>';
}

// ------------------
// Save Producer User
// ------------------
add_action( 'save_post', 'radio_station_pro_producer_save_data' );
add_action( 'publish_post', 'radio_station_pro_producer_save_data' );
function radio_station_pro_producer_save_data( $post_id ) {

	// --- do not save when doing autosaves ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- check producer user field is set ---
	if ( isset( $_POST['producer_user_id'] ) ) {

		// ---  verify field save nonce ---
		if ( !isset( $_POST['producer_user_nonce'] )
		     || !wp_verify_nonce( $_POST['producer_user_nonce'], 'radio-station-pro' ) ) {
			return;
		}

		// --- get the user ID ---
		$changed = false;
		$prev_user_id = get_post_meta( $post_id, 'producer_user_id', true );
		$user_id = trim( $_POST['producer_user_id'] );

		if ( empty( $user_id ) ) {
			// --- remove user ID ---
			delete_post_meta( $post_id, 'producer_user_id' );
			if ( $prev_user_id ) {
				$changed = true;
			}
		} elseif ( $prev_user_id != $user_id ) {

			// --- sanitize to numeric before updating ---
			// 2.4.1.6: add check if different to saved
			$user_id = absint( $user_id );
			if ( $user_id > 0 ) {
				$user = get_user_by( 'ID', $user_id );
				if ( $user ) {
					// --- check if user already assigned ---
					global $wpdb;
					$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'producer_user_id' AND meta_value = %d";
					$query = $wpdb->prepare( $query, $user_id );
					$profile_id = $wpdb->get_var( $query );
					if ( $profile_id ) {
						// --- trigger user overlap error message ---
						update_post_meta( $post_id, 'profile_id_error', 1 );
					} else {
						update_post_meta( $post_id, 'producer_user_id', $user_id );
						$changed = true;
					}
				}
			}
		}

		// if ( $changed ) {}
	}
}

