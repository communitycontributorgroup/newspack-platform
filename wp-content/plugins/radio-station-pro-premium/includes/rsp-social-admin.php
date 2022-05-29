<?php 

// --------------------------
// === Social Icons Admin ===
// --------------------------
// - Add Override Fields
// - Filter Override Edit Scripts
// - Filter Override Edit Styles
// - Save Linked Fields for Override


// -------------------
// Add Override Fields
// -------------------
// 2.4.1.4: added field for selective metadata display
add_action( 'radio_station_show_fields', 'radio_station_pro_override_social_icons_fields', 12, 2 );
function radio_station_pro_override_social_icons_fields( $post_id, $context ) {

	if ( 'override' == $context ) {

		// --- social icons linked field ---
		// if ( function_exists( 'radio_station_pro_social_icons_inputs' ) ) {
		echo '<tr><td style="vertical-align:top; padding-top:20px;">' . PHP_EOL;
			echo '<input type="checkbox" class="override-checkbox" id="override-show-social-icons" name="social_icons_link" value="yes" onclick="radio_check_linked(\'social-icons\');"';
			if ( isset( $linked_fields['social_icons'] ) && $linked_fields['social_icons'] ) {
				echo ' checked="checked"';
			}
			echo '> <label>' . esc_html( __( 'Social Icons', 'radio-station' ) ) . '</label>' . PHP_EOL;
		echo '</td><td width="30"></td><td>' . PHP_EOL;
			echo '<div id="override-input-social-icons" class="override-input"';
			if ( !isset( $linked_fields['social_icons'] ) || !$linked_fields['social_icons'] ) {
				echo ' style="display:none;"';
			}
				echo '>';

			// --- get social icon inputs ---
			// note: overrides plural is intentional here!
			radio_station_pro_social_icons_inputs( $post_id, 'overrides' );

			echo '<div id="override-data-social-icons"';
			if ( isset( $linked_fields['social_icons'] ) && $linked_fields['social_icons'] ) {
				echo ' style="display:none;"';
			}
			echo '>';
			// TODO: display social icon list from linked Show ?
			echo '</div>' . PHP_EOL;
		echo '</td></tr>' . PHP_EOL;
		// }

		// --- modify override edit script ---
		add_filter( 'radio_station_override_show_script', 'radio_station_pro_override_social_icons_script' );

		// --- modify override edit styles ---
		add_filter( 'radio_station_override_edit_styles', 'radio_station_pro_override_social_icons_styles' );
	}

}

// ----------------------------
// Filter Override Edit Scripts
// ----------------------------
function radio_station_pro_override_social_icons_script( $js ) {
	$find = "keys = ['";
	$replace = "keys = ['social-icons', '";
	$js = str_replace( $find, $replace, $js );
	return $js;
}

// ---------------------------
// Filter Override Edit Styles
// ---------------------------
// 2.4.1.4: added to remove duplicate label on override social icons
function radio_station_pro_override_social_icons_styles( $css ) {
	$css .= "#override-input-social-icons .input-label {display: none;}" . PHP_EOL;
	$css .= "#override-input-social-icons .input-field {margin-left: 0;}" . PHP_EOL;
	return $css;
}

// -------------------------------
// Save Linked Fields for Override
// -------------------------------
// 2.4.1.4: added for Pro override fields
add_filter( 'save_post', 'radio_station_pro_save_social_icons_override', 12 );
add_filter( 'publish_post', 'radio_station_pro_save_social_icons_override', 12 );
function radio_station_pro_save_social_icons_override( $post_id ) {

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
	$social_link = false;
	if ( isset( $_POST['social_icons_link'] ) && ( 'yes' == $_POST['social_icons_link'] ) ) {
		$social_link = true;
	}

	// --- save to linked fields settings ---
	$linked_fields = get_post_meta( $post_id, 'linked_show_fields', true );
	if ( !is_array( $linked_fields ) ) {
		$linked_fields = array();
	}
	$linked_fields['social_icons'] = $social_link;
	update_post_meta( $post_id, 'linked_show_fields', $linked_fields );
}
