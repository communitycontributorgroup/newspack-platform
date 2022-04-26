<?php

// =========================
// === Radio Station Pro ===
// =========================
// --------- Admin ---------
// =========================
// - Add Pro Suffix to Admin Menu
// - Add Export Playlist Submenu Item
// - Add Pro Plugin Page Links
// - Filter Activation URL
// - Filter Meta Input Types


// -------------
// === Admin ===
// -------------

// ----------------------------
// Add Pro Suffix to Admin Menu
// ----------------------------
// 2.4.1.8: filter admin menu to add Pro suffix
add_action( 'admin_menu', 'radio_station_pro_admin_menu', 11 );
function radio_station_pro_admin_menu() {
	global $menu;
	foreach ( $menu as $i => $item ) {
		if ( 'radio-station' == $item[2] ) {
			$item[0] = RADIO_STATION_PRO_NAME;
			$item[3] = RADIO_STATION_PRO_NAME . ' ' . __( 'Settings', 'radio-station' );
			$menu[$i] = $item;
		}
	}
	if ( RADIO_STATION_PRO_DEBUG ) {
		echo "Admin Menus: " . print_r( $menu, true ) . PHP_EOL;
	}
}

// ---------------------------------
// Add Export Playlists Submenu Item
// ---------------------------------
// add_action( 'radio_station_admin_submenu_middle', 'radio_station_pro_add_export_playlist_submenu', 13 );
function radio_station_pro_add_export_playlist_submenu() {
	$rs = __( 'Radio Station', 'radio-station' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Export Playlists', 'radio-station' ), __( 'Export Playlists', 'radio-station' ), $settingscap, 'playlist-export', 'radio_station_playlist_export_page' );
}

// ------------------------
// Filter Free Plugin Links
// ------------------------
// 2.4.1.8: filter free plugin links in Pro
add_action( 'admin_init', 'radio_station_pro_free_plugin_link_check' );
function radio_station_pro_free_plugin_link_check() {
	// --- add filter whether Free version is active or not ---
	$basename = defined( 'RADIO_STATION_BASENAME' ) ? RADIO_STATION_BASENAME : 'radio-station/radio-station.php';
	add_filter( 'plugin_action_links_' . $basename, 'radio_station_pro_free_plugin_links', 19, 2 );	
}
function radio_station_pro_free_plugin_links( $links, $file ) {
	
	global $radio_station_plugin_links;
	
	foreach ( $links as $key => $link ) {

		// --- Pro opt in/out link ---
		if ( 'opt-in-or-opt-out radio-station-pro' == $key )  {
			$radio_station_plugin_links[$key] = $link;
			unset( $links[$key] );
		}

		// --- remove Free activate license link ---
		if ( 'activate-license radio-station' == $key ) {
			unset( $links[$key] );
		}

		// --- Pro activate license link ---
		if ( 'activate-license radio-station-pro' == $key ) {
			$radio_station_plugin_links[$key] = $link;
			unset( $links[$key] );
		}
		
		// --- duplicate settings link ---
		if ( 'settings' == $key ) {
			$radio_station_plugin_links[$key] = $link;
		}
	}

	return $links;
}

// -------------------------
// Add Pro Plugin Page Links
// -------------------------
// 2.4.1.5: added link to radio station Account page
add_filter( 'plugin_action_links_' . RADIO_STATION_PRO_BASENAME , 'radio_station_pro_plugin_links', 20, 2 );
function radio_station_pro_plugin_links( $links, $file ) {

	if ( RADIO_STATION_PRO_DEBUG ) {
		echo '<span style="display:none;">RSP Plugin Links A: ' . print_r( $links, true ) . '</span>' . PHP_EOL;
	}

	global $radio_station_freemius, $radio_station_plugin_links, $radio_station_activation_link;

	// --- settings link ---
	// 2.4.1.8: added settings link for Pro
	if ( isset( $radio_station_plugin_links['settings'] ) ) {
		$link = array( 'settings' => $radio_station_plugin_links['settings'] );
		$links = array_merge( $link, $links );
	}

	// --- maybe move opt in/out link from Free ---
	// 2.4.1.8: changed as these do different things for Free / Pro
	if ( isset( $radio_station_plugin_links['opt-in-or-opt-out radio-station-pro'] ) ) {
		$links['opt-in-or-opt-out radio-station-pro'] = $radio_station_plugin_links['opt-in-or-opt-out radio-station-pro'];
	}

	// --- bug out here if free Freemius not loaded ---	
	if ( !isset( $radio_station_freemius ) || !is_object( $radio_station_freemius ) ) {
		return $links;
	}

	// --- license activation link ---
	// 2.4.1.8: maybe add license activation link
	if ( isset( $radio_station_activation_link ) ) {
		// note: for free 2.4.0.7 support only
		$activate_license_link = str_replace( 'radio-station', RADIO_STATION_PRO_SLUG, $radio_station_activation_link );
		$links['activate-license'] = $activate_license_link;
	} elseif ( isset( $radio_station_plugin_links['activate-licence radio-station-pro'] ) ) {
		// note: for free 2.4.0.8+ support
		$activate_license_link = str_replace( 'radio-station', RADIO_STATION_PRO_SLUG, $radio_station_plugin_links['activate-licence'] );
		$links['activate-license radio-station-pro'] = $activate_license_link;
	} else {
		// --- account page link ---
		$account_url = add_query_arg( 'page', 'radio-station-account', admin_url( 'admin.php' ) );
		$account_link = '<a class="account radio-station" href="' . esc_url( $account_url ) . '">' . __( 'Account', 'radio-station' ) . '</a>';
		$links['account'] = $account_link;
	}

	// --- move deactivate to last link item ---
	foreach ( $links as $key => $link ) {
		if ( strstr( $link, 'deactivate' ) ) {
			unset( $links[$key] );
			$links['deactivate'] = $link;
		}
	}

	if ( RADIO_STATION_PRO_DEBUG ) {
		echo '<span style="display:none;">RSP Plugin Links B: ' . print_r( $links, true ) . '</span>' . PHP_EOL;
	}

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

