<?php

// =========================
// === Radio Station Pro ===
// =========================
// --------- Admin ---------
// =========================
// - Add Export Playlist Submenu Item
// - Add Pro Plugin Page Links
// - Filter Meta Input Types


// -------------
// === Admin ===
// -------------

// ---------------------------------
// Add Export Playlists Submenu Item
// ---------------------------------
// add_action( 'radio_station_admin_submenu_middle', 'radio_station_pro_add_export_playlist_submenu', 13 );
function radio_station_pro_add_export_playlist_submenu() {
	$rs = __( 'Radio Station', 'radio-station' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Export Playlists', 'radio-station' ), __( 'Export Playlists', 'radio-station' ), $settingscap, 'playlist-export', 'radio_station_playlist_export_page' );
}

// -------------------------
// Add Pro Plugin Page Links
// -------------------------
// 2.4.1.5: added link to radio station Account page
add_filter( 'plugin_action_links_' . RADIO_STATION_PRO_BASENAME, 'radio_station_pro_plugin_links', 20, 2 );
function radio_station_pro_plugin_links( $links, $file ) {

	// echo '<span style="display:none;">Radio Station Pro Plugin Links: ' . print_r( $links, true ) . '</span>' . PHP_EOL;

	// --- remove optin / optout link in Pro ---
	// (this is handled by the Free version)
	foreach ( $links as $key => $link ) {
		if ( strstr( $key, 'opt-in-or-opt-out' ) ) {
			unset( $links[$key] );
		}
	}

	// --- add account page link ---
	$account_url = add_query_arg( 'page', 'radio-station-account', admin_url( 'admin.php' ) );
	$account_link = array( 'account' => '<a class="account radio-station" href="' . esc_url( $account_url ) . '">' . __( 'Account', 'radio-station' ) . '</a>' );
	$links = array_merge( $account_link, $links );

	return $links;
}

// -----------------------
// Filter Meta Input Types
// -----------------------
add_filter( 'radio_station_meta_input_types', 'radio_station_pro_meta_input_types' );
function radio_station_pro_meta_input_types( $types ) {

	// --- episode meta fields ---
	$types['numeric'][] = 'show';
	// $types['numeric'][] = 'show_id';
	$types['numeric'][] = 'media_id';
	$types['numeric'][] = 'length';
	$types['numeric'][] = 'override';
	$types['numeric'][] = 'playlist';
	$types['url'][] = 'file_url';
	$types['date'][] = 'air_date';

	return $types;
}



