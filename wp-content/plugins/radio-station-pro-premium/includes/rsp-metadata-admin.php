<?php

// ----------------------
// === Metadata Admin ===
// ----------------------
// - Add Metadata Fields
// - Filter Override Edit Scripts
// - Save Metadata Fields for Show
// - Save Linked Fields for Override


// -------------------
// Add Metadata Fields
// -------------------
// 2.4.1.4: added field for selective metadata display
add_action( 'radio_station_show_fields', 'radio_station_pro_show_metadata_fields', 11, 2 );
function radio_station_pro_show_metadata_fields( $post_id, $context ) {

	// --- get current values ---
	$metadata = get_post_meta( $post_id, 'show_metadata', true );
	if ( !$metadata ) {
		$metadata = '';
	}
	/* $metadata_url = get_post_meta( $post_id, 'show_metadata_url', true );
	if ( !$metadata_url ) {
		$metadata_url = '';
	} */

	// --- metadata fields for shows ---
	if ( 'show' == $context ) {

		// --- metadata source override ---
		echo '<li class="show-item" id="show-metadata">' . PHP_EOL;
			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'Metadata Source', 'radio-station' ) ) . ':' . PHP_EOL;
			echo '</label></div>' . PHP_EOL;
			echo '<div class="input-field">' . PHP_EOL;
			$options = array(
				''         => __( 'Default Plugin Setting', 'radio-station' ),
				'on'       => __( 'Override: Metadata On', 'radio-station' ),
				'off'      => __( 'Override: Metadata Off', 'radio-station' ),
				// 'url'      => __( 'Override: Metadata URL', 'radio-station' ),
				'playlist' => __( 'Override: Current Playlist', 'radio-station' ),
			);
			echo '<select name="show_metadata" id="show-metadata-select">' . PHP_EOL;
			// onchange="radio_check_metadata_select();"
			foreach ( $options as $value => $label ) {
				echo '<option value="' . esc_attr( $value ) . '"';
				if ( $value == $metadata ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $label ) . '</option>' . PHP_EOL;
			}
			echo '</select>' . PHP_EOL;
			echo '</div>' . PHP_EOL;
		echo '</li>' . PHP_EOL;

		// --- metadata URL ---
		// TODO: custom metadata URL for show option?
		/* echo '<li class="show-item" id="show-metadata-url"';
		if ( 'url' != $metadata ) {
			style="display:none;"
		}
		echo '>' . PHP_EOL;
			echo '<div class="input-label"><label>' . PHP_EOL;
			echo esc_html( __( 'Metadata Override URL', 'radio-station' ) ) . ':' . PHP_EOL;
			echo '</label></div>' . PHP_EOL;
			echo '<div class="input-field">' . PHP_EOL;
			echo '<input type="text" name="show_metadata_url" size="80" style="max-width:50%;" value="' . esc_attr( $metadata_url ) . '">' . PHP_EOL;
			echo '</div>' . PHP_EOL;
		echo '</li>' . PHP_EOL; */

		// --- enqueue select field script in footer ---
		// add_action( 'admin_footer', 'radio_station_pro_metadata_field_script' );

	} elseif ( 'override' == $context ) {

		// --- get linked show fields ---
		$linked_fields = get_post_meta( $post_id, 'linked_show_fields', true );

		// --- show metadata source override for overrides ---
		echo '<tr><td>' . PHP_EOL;
			echo '<input type="checkbox" class="override-checkbox" id="override-show-metadata" name="show_metadata_link" value="yes" onclick="radio_check_linked(\'metadata\');"';
			if ( isset( $linked_fields['show_metadata'] ) && $linked_fields['show_metadata'] ) {
				echo ' checked="checked"';
			}
			echo '> <label>' . esc_html( __( 'Metadata Source', 'radio-station' ) ) . '</label>' . PHP_EOL;
		echo '</td><td width="30"></td><td>' . PHP_EOL;
			echo '<div id="override-input-metadata" class="override-input"';
			if ( !isset( $linked_fields['show_metadata'] ) || !$linked_fields['show_metadata'] ) {
				echo ' style="display:none;"';
			}
			echo '>';
			$options = array(
				''         => __( 'Default Plugin Setting', 'radio-station' ),
				'on'       => __( 'Override: Metadata On', 'radio-station' ),
				'off'      => __( 'Override: Metadata Off', 'radio-station' ),
				// 'url'      => __( 'Override: Metadata URL', 'radio-station' ),
				'playlist' => __( 'Override: Current Playlist', 'radio-station' ),
			);
			echo '<select name="show_metadata" id="show-metadata-select">' . PHP_EOL;
			//  onchange="radio_check_metadata_select();"
			foreach ( $options as $value => $label ) {
				echo '<option value="' . esc_attr( $value ) . '"';
				if ( $value == $metadata ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $label ) . '</option>' . PHP_EOL;
			}
			echo '</select>' . PHP_EOL;
			echo '</div>' . PHP_EOL;
			echo '<div id="override-data-metadata"';
			if ( isset( $linked_fields['show_metadata'] ) && $linked_fields['show_metadata'] ) {
				echo ' style="display:none;"';
			}
			echo '</div>' . PHP_EOL;
		echo '</td></tr>' . PHP_EOL;

		// TODO: metadata URL source override for overrides?

		// --- modify override edit script ---
		add_filter( 'radio_station_override_show_script', 'radio_station_pro_override_metadata_script' );

	}
}

// ----------------------------
// Select Metadata Field Script
// ----------------------------
// 2.4.1.4: add onchange script to check/display extra URL field
/* function radio_station_pro_metadata_field_script() {
	echo "<script>function radio_check_metadata_select() {
		select = document.getElementById('show-metadata-select');
		value = select.options[select.selectedIndex].value;
		if (value == 'url') {document.getElementById('show-metadata-url').style.display = '';}
		else {document.getElementById('show-metadata-url').style.display = 'none';}
	}</script>";
} */

// ----------------------------
// Filter Override Edit Scripts
// ----------------------------
// 2.4.1.4: added for Pro override field display toggling
function radio_station_pro_override_metadata_script( $js ) {
	$find = "keys = ['";
	$replace = "keys = ['metadata', '";
	$js = str_replace( $find, $replace, $js );
	return $js;
}


// -----------------------------
// Save Metadata Fields for Show
// -----------------------------
// 2.4.1.4: added for Pro metadata field
add_action( 'save_post', 'radio_station_pro_save_metadata_show', 11 );
add_action( 'publish_post', 'radio_station_pro_save_metadata_show', 11 );
function radio_station_pro_save_metadata_show( $post_id ) {

	// --- skip if doing auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- check show data nonce ---
	// if ( !isset( $_POST['show_data_nonce'] ) || !wp_verify_nonce( $_POST['show_data_nonce'], 'radio-station' ) ) {
	//	return;
	// }

	// --- check post type ---
	$post = get_post( $post_id );
	if ( !in_array( $post->post_type, array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG ) ) ) {
		return;
	}

	// --- check posted value ---
	if ( isset( $_POST['show_metadata'] ) ) {
		$show_metadata = $_POST['show_metadata'];
		$valid = array( '', 'on', 'off', 'playlist' );
		if ( !in_array( $show_metadata, $valid ) ) {
			return;
		}
		update_post_meta( $post_id, 'show_metadata', $show_metadata );
	}
}

// -------------------------------
// Save Linked Fields for Override
// -------------------------------
// 2.4.1.4: added for Pro override fields
add_filter( 'save_post', 'radio_station_pro_save_metadata_override', 11 );
add_filter( 'publish_post', 'radio_station_pro_save_metadata_override', 11 );
function radio_station_pro_save_metadata_override( $post_id ) {

	// --- skip if doing auto save routine ---
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// --- check show data nonce ---
	if ( !isset( $_POST['show_data_nonce'] ) || !wp_verify_nonce( $_POST['show_data_nonce'], 'radio-station' ) ) {
		return;
	}

	// --- check post type ---=
	$post = get_post( $post_id );
	if ( RADIO_STATION_OVERRIDE_SLUG != $post->post_type ) {
		return;
	}

	// --- get linked field value ---
	$metadata_link = false;
	if ( isset( $_POST['show_metadata_link'] ) && ( 'yes' == $_POST['show_metadata_link'] ) ) {
		$metadata_link = true;
	}

	// --- save to linked fields settings ---
	$linked_fields = get_post_meta( $post_id, 'linked_show_fields', true );
	if ( !is_array( $linked_fields ) ) {
		$linked_fields = array();
	}
	$linked_fields['show_metadata'] = $metadata_link;
	update_post_meta( $post_id, 'linked_show_fields', $linked_fields );
}


