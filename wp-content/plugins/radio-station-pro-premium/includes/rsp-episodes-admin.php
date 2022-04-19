<?php

// ----------------------
// === Episodes Admin ===
// ----------------------
// - Add Taxonomy Submenu Items
// - Filter Taxonomy Menu Heighlights
// - Add Episodes Submenu Item
// - Add Show Episode Metabox
// - Show Episode Metabox
// - Update Show Episode
// - AJAX Audio AutoSave
// - Add Show Episode Count Column
// - Show Episode Count Column

// --------------------------
// Add Taxonomy Submenu Items
// --------------------------
// 2.4.1.6.1: added submenus for editing guests and topics taxonomy terms
add_action( 'radio_station_admin_submenu_middle', 'radio_station_pro_add_taxonomy_submenus', 13 );
function radio_station_pro_add_taxonomy_submenus() {
	$rs = __( 'Radio Station', 'radio-station' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Guests', 'radio-station' ), __( 'Guests', 'radio-station' ), 'edit_episodes', 'edit-tags.php?taxonomy=rs-guests' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Topics', 'radio-station' ), __( 'Topics', 'radio-station' ), 'edit_episodes', 'edit-tags.php?taxonomy=rs-topics' );
}

// -------------------------------
// Filter Taxonomy Menu Highlights
// -------------------------------
// 2.4.1.6.1: added filter to highlight correct submenu items
add_filter( 'parent_file', 'radio_station_pro_add_taxonomy_highlights' );
function radio_station_pro_add_taxonomy_highlights( $parent_file ) {
    global $plugin_page, $submenu_file, $taxonomy;
	if ( RADIO_STATION_TOPICS_SLUG == $taxonomy ) {
		$plugin_page = 'edit-tags.php?taxonomy=' . RADIO_STATION_TOPICS_SLUG;
		$submenu_file = 'edit-tags.php?taxonomy=' . RADIO_STATION_TOPICS_SLUG;
		$parent_file = 'radio-station';
	}
	if ( RADIO_STATION_GUESTS_SLUG == $taxonomy ) {
		$plugin_page = 'edit-tags.php?taxonomy=' . RADIO_STATION_GUESTS_SLUG;
		$submenu_file = 'edit-tags.php?taxonomy=' . RADIO_STATION_GUESTS_SLUG;
		$parent_file = 'radio-station';
	}
    return $parent_file;
}

// -------------------------
// Add Episodes Submenu Item
// -------------------------
add_action( 'radio_station_admin_submenu_top', 'radio_station_pro_add_episodes_submenu', 10 );
function radio_station_pro_add_episodes_submenu() {
	$rs = __( 'Radio Station', 'radio-station' );
	add_submenu_page( 'radio-station', $rs . ' ' .  __( 'Episodes', 'radio-station' ), __( 'Episodes', 'radio-station' ), 'publish_episodes', 'episodes' );
}

// ------------------------
// Add Show Episode Metabox
// ------------------------
add_action( 'add_meta_boxes', 'radio_station_pro_add_show_episode_metabox' );
function radio_station_pro_add_show_episode_metabox() {
	$position = apply_filters( 'radio_station_metabox_position', 'rstop', 'profiles' );
	add_meta_box(
		'radio-station-show-episode-metabox',
		__( 'Episode Information', 'radio-station' ),
		'radio_station_pro_show_episode_metabox',
		RADIO_STATION_EPISODE_SLUG,
		$position,
		'high'
	);
}

// --------------------
// Show Episode Metabox
// --------------------
function radio_station_pro_show_episode_metabox() {

	global $post;
	$post_id = 0;
	if ( property_exists( $post, 'ID' ) ) {
		$post_id = $post->ID;
	}

	// --- add nonce field for verification ---
	wp_nonce_field( 'radio-station', 'episode_show_nonce' );

	// --- open metabox div ---
	echo '<div class="meta_inner">' . PHP_EOL;

	echo '<ul class="episode-input-list">' . PHP_EOL;

		echo '<li class="episode-item first-item">' . PHP_EOL;

			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'Show', 'radio-station' ) ) . ':' . PHP_EOL;
			echo '</label></div>' . PHP_EOL;

			// -- get all shows ---
			$args = array(
				'numberposts' => - 1,
				'offset'      => 0,
				'orderby'     => 'post_title',
				'order'       => 'ASC',
				'post_type'   => RADIO_STATION_SHOW_SLUG,
				'post_status' => array( 'publish', 'draft', 'pending', 'future' ),
			);
			$shows = get_posts( $args );

			// --- get current selection ---
			$selected = get_post_meta( $post->ID, 'episode_show_id', true );
			if ( !$selected ) {
				$selected = '';
			}

			echo '<div class="input-field">' . PHP_EOL;
			if ( count( $shows ) > 0 ) {

				// --- select related show input ---
				echo '<select name="episode_show">';
				echo '<option value="">' . esc_html( __( 'Select Show', 'radio-station') ) . '</option>';

				// --- loop shows for selection options ---
				foreach ( $shows as $show ) {

					echo '<option value="' . esc_attr( $show->ID ) . '"';
					if ( $show->ID == $selected ) {
						echo ' selected="selected"';
					}
					echo '>' . esc_html( $show->post_title );
					if ( 'draft' == $show->post_status ) {
						echo ' (' . esc_html( __( 'Draft', 'radio-station' ) ) . ')';
					}
					echo '</option>';
				}
				echo '</select>';

			} else {
				// --- no shows message ---
				echo esc_html( __( 'No Shows to Select.', 'radio-station' ) );
			}
			echo '</div>' . PHP_EOL;

		echo '</li>' . PHP_EOL;

		// --- episode media type ---
		$episode_type = get_post_meta( $post->ID, 'episode_type', true );
		echo '<li class="episode-item">' . PHP_EOL;

			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'Media Type', 'radio-station' ) ) . ': ' . PHP_EOL;
			echo '</label></div>' . PHP_EOL;
			echo '<div class="input-field">'  . PHP_EOL;
			echo '<input type="radio" id="episode-type-url" class="episode-type" name="episode_type" value="url" onclick="radio_episode_type(\'url\');"';
			if ( !$episode_type || ( 'url' == $episode_type ) ) {
				echo ' checked="checked"';
			}
			echo '> <a href="javascript:void(0);" class="radio-link" onclick="radio_episode_type(\'url\');">';
			echo esc_html( __( 'File URL', 'radio-station' ) ) . '</a>' . PHP_EOL;
			echo ' <input type="radio" id="episode-type-media" class="episode-type" name="episode_type" value="media" onclick="radio_episode_type(\'media\');"';
			if ( 'media' == $episode_type ) {
				echo ' checked="checked"';
			}
			echo '> <a href="javascript:void(0);" class="radio-link" onclick="radio_episode_type(\'media\');">' . PHP_EOL;
			echo esc_html( __(  'Media Library', 'radio-station' ) ) . '</a>' . PHP_EOL;
			echo '<input type="hidden" id="show-episode-type" value="' . esc_attr( $episode_type ) . '">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

			echo '<div class="input-helper">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

		echo '</li>' . PHP_EOL;

		// --- episode media URL ---
		$episode_file_url = get_post_meta( $post->ID, 'episode_file_url', true );
		echo '<li id="episode-file" class="episode-item"';
		if ( 'url' != $episode_type ) {
			echo ' style="display:none;"';
		}
		echo '>' . PHP_EOL;

			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'File URL', 'radio-station' ) ) . ': ' . PHP_EOL;
			echo '</label></div>' . PHP_EOL;

			echo '<div class="input-field">' . PHP_EOL;
			echo '<input type="text" id="episode-file-url" class="episode-file" name="episode_file_url" value="' . esc_attr( $episode_file_url ) . '">';
			echo '<input type="hidden" id="show-episode-file" value="' . esc_attr( $episode_file_url ) . '">';
			echo '</div>' . PHP_EOL;

			echo '<div class="input-helper">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

		echo '</li>' . PHP_EOL;

		// --- episode media ID ---
		$episode_media_id = get_post_meta( $post->ID, 'episode_media_id', true );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Media ID: ' . $episode_media_id . '</span>';
		}
		echo '<li id="episode-media" class="episode-item"';
		if ( !$episode_type || ( 'media' != $episode_type ) ) {
			echo ' style="display:none;"';
		}
		echo '>' . PHP_EOL;

			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'Media Library', 'radio-station' ) ) . ': ' . PHP_EOL;
			echo '</label></div>' . PHP_EOL;

			echo '<div class="input-field">' . PHP_EOL;

			radio_station_pro_media_libary_audio( $post->ID, $episode_media_id );

			echo '<input type="hidden" id="episode-media-id" class="episode-media-id" name="episode_media_id" value="' . esc_attr( $episode_media_id ) . '">';
			echo '<input type="hidden" id="show-episode-media" value="' . esc_attr( $episode_media_id ) . '">';
			echo '</div>' . PHP_EOL;

			echo '<div class="input-helper">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

		echo '</li>' . PHP_EOL;

		// --- check for existing highest episode number ---
		$highest = 0;
		$latest = radio_staton_pro_get_latest_show_episode( $post->ID );
		if ( $latest && ( $latest != $post->ID ) ) {
			$highest = get_post_meta( $latest, 'episode_number', true );
		}

		// --- episode number ---
		// TODO: improve like loader.php number field ?
		$episode_number = get_post_meta( $post->ID, 'episode_number', true );
		if ( !$episode_number ) {
			$episode_number = '';
		}
		echo '<li class="episode-item">' . PHP_EOL;

			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'Episode Number', 'radio-station' ) ) . ': ' . PHP_EOL;
			echo '</div>' . PHP_EOL;

			echo '<div class="input-field">' . PHP_EOL;
			echo '<input type="text" id="episode-number" class="episode-number" name="episode_number" value="' . esc_attr( $episode_number ) . '" style="width:50px;">' . PHP_EOL;
			if ( 0 != $highest ) {
				echo ' (' . esc_html( __( 'Current Highest', 'radio-station' ) ) . ': ' . esc_attr( $highest ) . ' )' . PHP_EOL;
			}
			echo '<input type="hidden" id="show-episode-number" value="' . esc_attr( $episode_number ) . '">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

			echo '<div class="input-helper">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

		echo '</li>' . PHP_EOL;

		// --- aired date ---
		$episode_date = get_post_meta( $post->ID, 'episode_air_date', true );
		if ( !$episode_date ) {
			$episode_date = '';
		}
		echo '<li class="episode-item">' . PHP_EOL;

			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'Broadcast Date', 'radio-station' ) ) . ': ' . PHP_EOL;
			echo '</label></div>' . PHP_EOL;

			echo '<div class="input-field">' . PHP_EOL;
			echo '<input type="text" id="episode-date" class="episode-date" name="episode_air_date" value="' . esc_attr( $episode_date ) . '">';
			echo '<input type="hidden" id="show-episode-date" value="' . esc_attr( $episode_date ) . '">';
			echo '</div>' . PHP_EOL;

			echo '<div class="input-helper">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

		echo '</li>' . PHP_EOL;

		// --- aired time ---
		$episode_time = get_post_meta( $post->ID, 'episode_air_time', true );
		$times = array( '01', '00', '' );
		if ( $episode_time ) {
			$times = explode( ':', $episode_time );
		}
		echo '<li class="episode-item">' . PHP_EOL;

			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'Broadcast Time', 'radio-station' ) ) . ':' . PHP_EOL;
			echo '</label></div>' . PHP_EOL;

			// --- air time hour ---
			echo '<div class="input-field">' . PHP_EOL;
			echo '<select id="episode-hour" name="episode_hour">' . PHP_EOL;
			for ( $i = 1; $i < 13; $i++ ) {
				if ( $i < 10 ) {
					$i = '0' . $i;
				}
				echo '<option value="' . esc_attr( $i ) . '"';
				if ( (string) $i == (string) $times[0] ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $i ) . '</option>' . PHP_EOL;
			}
			echo '</select>' . PHP_EOL;
			echo '<input type="hidden" id="show-episode-hour" value="">' . PHP_EOL;

			// --- air time minutes ---
			echo '<select id="episode-mins" name="episode_mins">' . PHP_EOL;
			echo '<option value="00">00</option>' . PHP_EOL;
			echo '<option value="15">15</option>' . PHP_EOL;
			echo '<option value="30">30</option>' . PHP_EOL;
			echo '<option value="45">45</option>' . PHP_EOL;
			for ( $i = 0; $i < 60; $i++ ) {
				if ( $i < 10 ) {
					$i = '0' . $i;
				}
				echo '<option value="' . esc_attr( $i ) . '"';
				if ( (string) $i == (string) $times[1] ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $i ) . '</option>' . PHP_EOL;
			}
			echo '</select>' . PHP_EOL;
			echo '<input type="hidden" id="show-episode-mins" value="">' . PHP_EOL;

			// --- air time meridian ---
			$am = radio_station_translate_meridiem( 'am' );
			$pm = radio_station_translate_meridiem( 'pm' );
			echo '<select id="episode-meridian" name="episode_meridian">' . PHP_EOL;
				echo '<option value=""></option>' . PHP_EOL;
				echo '<option value="am" ' . selected( $times[2], 'am', false ) . '>' . esc_html( $am ) . '</option>' . PHP_EOL;
				echo '<option value="pm" ' . selected( $times[2], 'pm', false ) . '>' . esc_html( $pm ) . '</option>' . PHP_EOL;
			echo '</select>';
			echo '<input type="hidden" id="show-episode-meridian" value="">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

			echo '<div class="input-helper">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

		echo '</li>' . PHP_EOL;

		// --- episode length ---
		/* $episode_length = get_post_meta( $post->ID, 'episode_air_length', true );
		echo '<li class="episode-item">' . PHP_EOL;

			echo '<div class="input-label">' . PHP_EOL;
			echo esc_html( __( 'Episode Length', 'radio-station' ) ) . ':' . PHP_EOL;
			echo '</div>' . PHP_EOL;

			echo '<div class="input-field">'  . PHP_EOL;
			$date = ( !empty( $episode_length ) ) ? trim( $episode_length ) : '';
			echo '<input type="text" id="episode-length" name="episode_length" value="' . esc_attr( $episode_length ) . '">';
			echo ' ' . esc_html( __( 'minutees', 'radio-station' ) );
			echo '<input type="hidden" id="show-episode-length" value="' . esc_attr( $episode_length ) . '">';

			// TODO: autodetect length from file button
			echo '</div>' . PHP_EOL;

			echo '<div class="input-helper">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

		echo '</li>'; */


		// --- episode playlist ---
		echo '<li class="episode-item">' . PHP_EOL;

			echo '<div class="input-label">' . PHP_EOL;
			echo esc_html( __( 'Playlist', 'radio-station' ) ) . ':';
			echo '</div>' . PHP_EOL;

			// -- get all playlists ---
			$args = array(
				'numberposts' => - 1,
				'offset'      => 0,
				'orderby'     => 'post_title',
				'order'       => 'ASC',
				'post_type'   => RADIO_STATION_PLAYLIST_SLUG,
				'post_status' => array( 'publish', 'draft' ),
			);
			$playlists = get_posts( $args );

			// --- get current selection ---
			$selected = get_post_meta( $post->ID, 'episode_playlist', true );
			if ( !$selected ) {
				$selected = '';
			}

			echo '<div class="input-field">' . PHP_EOL;
			if ( count( $playlists ) > 0 ) {

				// --- select related show input ---
				echo '<select name="episode_playlist">' . PHP_EOL;
				echo '<option value="">' . esc_html( __( 'Select Playlist', 'radio-station') ) . '</option>' . PHP_EOL;

				// --- loop shows for selection options ---
				foreach ( $playlists as $playlist ) {

					echo '<option value="' . esc_attr( $playlist->ID ) . '"';
					if ( $playlist->ID == $selected ) {
						echo ' selected="selected"';
					}
					echo '>' . esc_html( $playlist->post_title );
					if ( 'draft' == $playlist->post_status ) {
						echo ' (' . esc_html( __( 'Draft', 'radio-station' ) ) . ')';
					}
					echo '</option>' . PHP_EOL;
				}
				echo '</select>' . PHP_EOL;

			} else {
				// --- no playlists message ---
				echo esc_html( __( 'No Playlists to Select.', 'radio-station' ) ) . PHP_EOL;
			}
			echo '</div>' . PHP_EOL;

			echo '<div class="input-helper">' . PHP_EOL;
			echo '</div>' . PHP_EOL;

		echo '</li>' . PHP_EOL;

		do_action( 'radio_station_episode_fields', $post_id, 'episode' );

	// --- close input list ---
	echo '</ul>' . PHP_EOL;

	// --- close metabox div ---
	echo '</div>' . PHP_EOL;

	// --- enqueue datepicker script and styles ---
	radio_station_enqueue_datepicker();

	// --- episode type switcher ---
	$js = "function radio_episode_type(etype) {
		if (etype == 'url') {
			jQuery('#episode-type-url').prop('checked', true);
			jQuery('#episode-type-media').prop('checked', false);
			jQuery('#episode-media').hide(); jQuery('#episode-file').show();
		} else if (etype == 'media') {
			jQuery('#episode-type-media').prop('checked', true);
			jQuery('#rpisode-type-url').prop('checked', false);
			jQuery('#episode-file').hide(); jQuery('#episode-media').show();
		}
	}" . PHP_EOL;

	// --- initialize datepicker fields ---
	$js .= "jQuery(document).ready(function() {
		jQuery('.episode-date').datepicker({dateFormat : 'yy-mm-dd'});
	});" . PHP_EOL;

	// --- enqueue inline script ---
	wp_add_inline_script( 'radio-station-admin', $js );

	// --- episode info styles ---
	// 2.4.1.6: added min width to episode air time inputs
	$css = 'body.post-type-rs-episode #ui-datepicker-div, .media-modal-close {z-index: 1001 !important;}
	.input-label, .input-field, .input-helper {display: inline-block;}
	.input-label {font-weight: bold; width: 120px;}
	.input-field {margin-left: 20px; max-width: 50%;}
	.input-helper {margin-left: 20px; font-style: italic;}
	.episode-date {width: 100px; text-align: center;}
	#episode-hour, #episode-mins, #episode-meridian {min-width: 40px;}
	#upload-episode-audio.hidden, #delete-episode-audio.hidden {display:none;}
	.radio-link {text-decoration: none;}
	.radio-link:hover {text-decoration: underline;}
	' . PHP_EOL;

	// --- filter and output styles ---
	$css = apply_filters( 'radio_station_episode_edit_styles', $css );
	echo '<style>' . $css . '</style>';

}

// ---------------------------------
// Media Library Audio Select Script
// ---------------------------------
function radio_station_pro_media_libary_audio( $post_id, $media_id ) {

	global $post;

	// wp_nonce_field( 'radio-station', 'episode_audio_nonce' );
	// $upload_link = get_upload_iframe_src( 'audio', $post->ID, $tab );
	$upload_link = get_upload_iframe_src( 'audio', $post_id );

	// --- get audio source URL ---
	$media_src = wp_get_attachment_url( $media_id );

	// --- show avatar image ---
	echo '<div id="episode-audio">' . PHP_EOL;

	// --- image container ---
	echo '<div id="episode-audio-display" style="font-style: italic;">' . PHP_EOL;
	if ( $media_src ) {
		echo $media_id . ' : ' . $media_src;
	}
	echo '</div>' . PHP_EOL;

	// --- add and remove links ---
	echo '<p class="hide-if-no-js">' . PHP_EOL;
	if ( $media_src ) {
	 	$upload_hidden = ' hidden';
	 	$delete_hidden = '';
	} else {
	 	$upload_hidden = '';
	 	$delete_hidden = ' hidden';
	}

	// --- upload
	echo '<a id="upload-episode-audio" class="radio-link' . esc_attr( $upload_hidden ) . '" href="' . esc_url( $upload_link ) . '">' . PHP_EOL;
	echo esc_html( __( 'Select Audio File', 'radio-station' ) ) . PHP_EOL;
	echo '</a>' . PHP_EOL;

	// --- delete audio attachment ---
	echo '<a id="delete-episode-audio" class="radio-link ' . esc_attr( $delete_hidden ) . '" href="javascript:void(0);">' . PHP_EOL;
	echo esc_html( __( 'Remove Selected Audio', 'radio-station' ) ) . PHP_EOL;
	echo '</a>' . PHP_EOL;
	echo '</p>' . PHP_EOL;

	echo '</div>' . PHP_EOL;

	// --- set autosave nonce and iframe ---
	$audio_autosave_nonce = wp_create_nonce( 'episode-audio-autosave' );
	echo '<input type="hidden" id="episode-audio-save-nonce" value="' . esc_attr( $audio_autosave_nonce ) . '">' . PHP_EOL;
	echo '<iframe src="javascript:void(0);" name="episode-audio-save-frame" id="episode-audio-save-frame" style="display:none;"></iframe>' . PHP_EOL;

	// --- audio selection script ---
	// ref: https://dev.to/kelin1003/utilising-wordpress-media-library-for-uploading-files-2b01
	$confirm_remove = __( 'Are you sure you want to remove this audio?', 'radio-station' );
	$js = "var mediaframe;

	/* Add Audio on Click */
	jQuery('#upload-episode-audio').on( 'click', function( event ) {

		event.preventDefault();

		if (mediaframe) {mediaframe.open(); return;}
		mediaframe = wp.media({
			frame: 'select',
			title: 'Select or Upload Audio File',
			button: {text: 'Use this Audio'},
			library: {order: 'DESC', orderby: 'date', type: 'audio', search: null, uploadedTo: null},
			multiple: false
		});

		mediaframe.on( 'select', function() {
			var attachment = mediaframe.state().get('selection').first().toJSON();
			jQuery('#episode-audio-display').html(attachment.id+' : '+attachment.url);
			jQuery('#episode-media-id').val(attachment.id);
			jQuery('#upload-episode-audio').addClass('hidden');
			jQuery('#delete-episode-audio').removeClass('hidden');

			/* auto-save audio via AJAX */
			postid = jQuery('#post_ID').val(); audioid = attachment.id;
			savenonce = jQuery('#episode-audio-save-nonce').val();
			framesrc = ajaxurl+'?action=radio_station_episode_audio_save';
			framesrc += '&post_id='+postid+'&audio_id='+audioid+'&nonce='+savenonce;
			jQuery('#episode-audio-save-frame').attr('src', framesrc);
		});

		mediaframe.open();
	});

	/* Delete Audio on Click */
	jQuery('#delete-episode-audio').on( 'click', function( event ) {
		event.preventDefault();
		agree = confirm('" . esc_js( $confirm_remove ) . "');
		if (!agree) {return;}
		jQuery('#episode-audio-display').html('');
		jQuery('#episode-media-id').val('');
		jQuery('#upload-episode-audio').removeClass('hidden');
		jQuery('#delete-episode-audio').addClass('hidden');
	});" . PHP_EOL;

	// --- enqueue script inline ---
	wp_add_inline_script( 'radio-station-admin', $js );

}


// -------------------
// Update Show Episode
// -------------------
add_action( 'wp_ajax_nopriv_radio_station_episode_save_segments', 'radio_station_pro_relogin_message' );
add_action( 'wp_ajax_radio_station_episode_save_segments', 'radio_station_pro_episode_save_data' );
add_action( 'save_post', 'radio_station_pro_episode_save_data' );
function radio_station_pro_episode_save_data( $post_id ) {

	// --- do not save when doing autosaves ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- make sure we have a post ID for AJAX save ---
	// 2.3.2: added AJAX segment saving checks
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		// 2.3.3: added double check for AJAX action match
		if ( !isset( $_REQUEST['action'] ) || ( 'radio_station_episode_save_segments' != $_REQUEST['action'] ) ) {
			return;
		}

		$error = false;
		if ( !current_user_can( 'edit_episodes' ) ) {
			$error = __( 'Failed. Use manual Publish or Update instead.', 'radio-station' );
		} elseif ( !isset( $_POST['episode_show_nonce'] ) || !wp_verify_nonce( $_POST['episode_show_nonce'], 'radio-station' ) ) {
			$error = __( 'Expired. Publish or Update instead.', 'radio-station' );
		} elseif ( !isset( $_POST['episode_id'] ) || ( '' == $_POST['episode_id'] ) ) {
			$error = __( 'Failed. No Episode ID provided.', 'radio-station' );
		} else {
			$post_id = absint( $_POST['episode_id'] );
			$post = get_post( $post_id );
			if ( !$post ) {
				$error = __( 'Failed. Invalid Episode ID.', 'radio-station' );
			}
		}

		// --- send error message to parent window ---
		if ( $error ) {
			echo "<script>parent.document.getElementById('segment-saving-message').style.display = 'none';
			parent.document.getElementById('segment-error-message').style.display = '';
			parent.document.getElementById('segment-error-message').innerHTML = '" . esc_js( $error ) . "';
			form = parent.document.getElementById('segment-save-form'); form.parentNode.removeChild(form);
			</script>";

			exit;
		}
	}

	// --- check matching post type ---
	$post = get_post( $post_id );
	$post_type = $post->post_type;
	if ( RADIO_STATION_EPISODE_SLUG != $post_type ) {
		return;
	}

	// ---  verify field save nonce ---
	$meta_changed = false;
	if ( isset( $_POST['episode_show_nonce'] ) && wp_verify_nonce( $_POST['episode_show_nonce'], 'radio-station' ) ) {

		// --- episode show ID ---
		$prev_show = get_post_meta( $post_id, 'episode_show_id', true );
		$show_id = radio_station_sanitize_input( 'episode', 'show' );
		$check = get_post( $show_id );
		if ( $check ) {
			update_post_meta( $post_id, 'episode_show_id', $show_id );
		} else {
			$show_id = false;
			delete_post_meta( $post_id, 'episode_show_id' );
		}

		// --- episode_media_type ---
		$episode_type = $_POST['episode_type'];
		$prev_type = get_post_meta( $post_id, 'episode_type', true );
		if ( in_array( $episode_type, array( 'url', 'media' ) ) ) {
			update_post_meta( $post_id, 'episode_type', $episode_type );
		}

		// --- episode_file_url ---
		$file_url = radio_station_sanitize_input( 'episode', 'file_url' );
		$prev_file_url = get_post_meta( $post_id, 'episode_file_url', true );
		update_post_meta( $post_id, 'episode_file_url', $file_url );

		// --- episode media id ---
		$media_id = radio_station_sanitize_input( 'episode', 'media_id' );
		$prev_media_id = get_post_meta( $post_id, 'episode_media_id', true );
		update_post_meta( $post_id, 'episode_media_id', $media_id );

		// --- episode_number ---
		$number = radio_station_sanitize_input( 'episode', 'number' );
		$prev_number = get_post_meta( $post_id, 'episode_number', true );
		update_post_meta( $post_id, 'episode_number', $number );

		// --- episode date ---
		$date = radio_station_sanitize_input( 'episode', 'air_date' );
		$prev_date = get_post_meta( $post_id, 'episode_air_date', true );
		update_post_meta( $post_id, 'episode_air_date', $date );

		// --- episode time ---
		$hour = radio_station_sanitize_input( 'episode', 'hour' );
		$mins = radio_station_sanitize_input( 'episode', 'mins' );
		$mer = radio_station_sanitize_input( 'episode', 'meridian' );
		$prev_time = get_post_meta( $post_id, 'episode_air_time', true );
		$time = $hour . ':' . $mins . ':' . $mer;
		update_post_meta( $post_id, 'episode_air_time', $time );

		// --- episode length ---
		// $length = radio_station_sanitize_value( 'episode', 'length' );
		// $prev_length = get_post_meta( $post_id, 'episode_length', true );
		// update_post_meta( $post_id, 'episode_length', $length );

		// --- playlist ---
		$playlist_id = radio_station_sanitize_input( 'episode', 'playlist' );
		$prev_playlist = get_post_meta( $post_id, 'episode_playlist', true );
		$check = get_post( $playlist_id );
		if ( $check ) {
			update_post_meta( $post_id, 'episode_playlist', $playlist_id );
		} else {
			$playlist_id = false;
			delete_post_meta( $post_id, 'episode_playlist' );
		}

		// --- check if changed ---
		if ( ( $prev_show != $show_id )
		  || ( $episode_type != $prev_type )
		  || ( $file_url != $prev_file_url )
		  || ( $media_id != $prev_media_id )
		  || ( $number != prev_number )
		  || ( $date != $prev_date )
		  || ( $time != $prev_time )
		  // || ( $length != $prev_length )
		  || ( $prev_playlist != $playlist_id ) ) {
			$meta_changed = true;
		}
	}

	// --- save episode segments ---
	$segments_changed = apply_filters( 'radio_station_update_segments', false, $post_id );

	// --- save episode avatar ---
	$avatar_changed = false;
	if ( isset( $_POST['episode_images_nonce'] ) && wp_verify_nonce( $_POST['episode_images_nonce'], 'radio-station' ) ) {
		$prev_avatar = get_post_meta( $post_id, 'episode_avatar', true );
		$avatar = absint( $_POST['episode_avatar'] );
		if ( $avatar > 0 ) {
			update_post_meta( $post_id, 'episode_avatar', $avatar );
			if ( $prev_avatar != $avatar ) {
				$avatar_changed = true;
			}
		}
	}

	// --- maybe clear episode cached data ---
	if ( $meta_changed || $segments_changed || $avatar_changed ) {
		radio_station_clear_cached_data( $post_id );
	}

	// --- AJAX saving ---
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		if ( isset( $_POST['action'] ) && ( 'radio_station_episode_save_segments' == $_POST['action'] ) ) {

			// --- display segments saved message ---
			$episode_segments_nonce = wp_create_nonce( 'radio-station' );
			echo "<script>parent.document.getElementById('segments-saving-message').style.display = 'none';
			parent.document.getElementById('segments-saved-message').style.display = '';
			if (typeof parent.jQuery == 'function') {parent.jQuery('#segments-saved-message').fadeOut(3000);}
			else {setTimeout(function() {parent.document.getElementById('segments-saved-message').style.display = 'none';}, 3000);}
			form = parent.document.getElementById('segments-save-form'); form.parentNode.removeChild(form);
			parent.document.getElementById('episode_segments_nonce').value = '" . esc_js( $episodes_segments_nonce ) . "';
			</script>";

			// --- refresh segment list table ---
			if ( $segments_changed ) {
				echo radio_station_pro_segments_table( $post_id );
				echo "<script>segmenttable = parent.document.getElementById('segments-table');
				segmenttable.innerHTML = document.getElementById('segments-table').innerHTML;</script>";
			}

			exit;
		}
	}

}

// -------------------
// AJAX Audio AutoSave
// -------------------
add_action( 'wp_ajax_nopriv_radio_station_episode_audio_save', 'radio_station_pro_relogin_message' );
add_action( 'wp_ajax_radio_station_episode_audio_save', 'radio_station_pro_episode_audio_save' );
function radio_station_pro_episode_audio_save() {

	global $post;

	// --- sanitize posted values ---
	if ( isset( $_GET['post_id'] ) ) {
		$post_id = absint( $_GET['post_id'] );
		if ( $post_id < 1 ) {
			unset( $post_id );
		}
	}
	$post = get_post( $post_id );
	if ( !$post ) {
		exit;
	}

	// --- check edit capability ---
	if ( !current_user_can( 'edit_episodes' ) ) {
		exit;
	}

	// --- verify nonce value ---
	if ( !isset( $_GET['nonce'] ) || !wp_verify_nonce( $_GET['nonce'], 'episode-audio-autosave' ) ) {
		exit;
	}

	// --- get audio attachment ID ---
	if ( isset( $_GET['audio_id'] ) ) {
		$audio_id = absint( $_GET['audio_id'] );
		if ( $audio_id < 1 ) {
			unset( $audio_id );
		}
	}

	if ( isset( $post_id ) && isset( $audio_id ) ) {

		// --- save episode type ---
		update_post_meta( $post_id, 'episode_type', 'media' );

		// --- save audio media ID ---
		update_post_meta( $post_id, 'episode_media_id', $audio_id );

		// --- refresh parent frame nonce ---
		$images_save_nonce = wp_create_nonce( 'episode-audio-autosave' );
		echo "<script>parent.document.getElementById('episode-audio-save-nonce').value = '" . esc_js( $images_save_nonce ) . "';</script>";
	}

	exit;
}


// -----------------------------
// Add Show Episode Count Column
// -----------------------------
add_filter( 'manage_edit-' . RADIO_STATION_SHOW_SLUG . '_columns', 'radio_station_pro_show_episode_columns', 7 );
function radio_station_pro_show_episode_columns( $columns ) {
	$columns['episodes'] = __( 'Episodes', 'radio-station' );
	return $columns;
}

// -------------------------
// Show Episode Count Column
// -------------------------
add_action( 'manage_' . RADIO_STATION_SHOW_SLUG . '_posts_custom_column', 'radio_station_pro_show_episode_column', 5, 2 );
function radio_station_pro_show_episode_column( $column, $post_id ) {

	if ( 'episodes' == $column ) {
		$episode_ids = radio_station_pro_get_show_episode_ids( $post_id );
		$episode_count = count( $episode_ids );
		if ( $episode_count > 0 ) {
			echo esc_html( count( $episode_ids ) );
		} else {
			echo '-';
		}
	}
}

// ----------------------
// Episodes Column Styles
// ----------------------
add_action( 'admin_footer', 'radio_station_pro_show_column_styles' );
function radio_station_pro_show_column_styles() {

	$current_screen = get_current_screen();
	if ( 'edit-' . RADIO_STATION_SHOW_SLUG !== $current_screen->id ) {
		return;
	}

	$css = '#episodes {font-size: 10px;}
	.column-episodes {width: 45px;}';
	echo '<style>' . $css . '</style>';
}
