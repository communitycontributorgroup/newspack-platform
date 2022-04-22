<?php

// -------------------------
// === Teleporter Loader ===
// -------------------------
// - Load Teleporter for Player Bar
// - Teleporter Settings Page Note

// ------------------------------
// Load Teleporter for Player Bar
// ------------------------------
// 2.4.1.8: moved Teleporter loading to separate file
add_action( 'plugins_loaded', 'radio_station_pro_teleporter_loader' );
function radio_station_pro_teleporter_loader() {

	// --- bug out if free version is not active ---
	// 2.4.1.2: added for pro-only activation edge case
	if ( !function_exists( 'radio_station_get_setting' ) ) {
		return;
	}

	// --- get player bar position ---
	$position = radio_station_get_setting( 'player_bar' );
	if ( ( '' == $position ) || ( 'off' == $position ) ) {
		return;
	}

	// --- check if separate teleporter plugin is already active ---
	if ( function_exists( 'teleporter_enqueue_scripts' ) ) {
		return;
	}

	// --- check for continuous player setting ---
	$continuous = radio_station_get_setting( 'player_bar_continuous' );
	if ( ( '1' == $continuous ) || ( 'yes' == $continuous ) ) {
		// --- include teleporter plugin for pageload transitions ---
		// 2.4.1.8: allow for Radio Station Pro/Radio Player Pro dirs
		$dir = defined( 'STREAM_PLAYER_PRO_DIR' ) ? STREAM_PLAYER_PRO_DIR : RADIO_STATION_PRO_DIR;
		$teleporter = $dir . '/teleporter/teleporter.php';
		include_once $teleporter;
		// 2.4.1.5: fix to misssing teleporter loader action
		teleporter_load_plugin();
	}

	if ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Continuous Player? ' . $continuous . PHP_EOL;
		echo 'Loaded Teleporter Path: ' . $teleporter . '</span>';
	}
}

// -----------------------------
// Teleporter Settings Page Note
// -----------------------------
// 2.4.1.4: added in case Teleporter plugin activated separately
// 2.4.1.8: moved to separate file with Teleporter loader
add_action( 'teleporter_admin_page_tab_general_top', 'radio_station_pro_teleporter_settings_note' );
function radio_station_pro_teleporter_settings_note() {

	$continuous = radio_station_get_setting( 'player_bar_continuous' );
	if ( ( '1' == $continuous ) || ( 'yes' == $continuous ) ) {
		// 2.4.1.8: allow for Radio Station Pro/Radio Player Pro name
		$name = defined( 'STREAM_PLAYER_PRO_NAME' ) ? STREAM_PLAYER_PRO_NAME : RADIO_STATION_PRO_NAME;
		echo sprintf( __( 'Important: %s continuous player bar is active.', 'radio-station' ), $name ) . '<br>';
		echo __( 'This overrides the page fade in, load timeout and loader bar settings here.' ) . '<br>';
		echo __( 'Adjust those settings via your Radio Station settings page instead.', 'radio-station' ) . '<br><br>';
	}
}

