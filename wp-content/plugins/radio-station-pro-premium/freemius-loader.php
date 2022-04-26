<?php 

// =======================
// === Freemius Loader ===
// =======================
// - Filter Plugin Options
// - Check Pro Version Type
// - Initialize Freemius
// - Pro Premium Freemius Settings Init
// - Pro Addon Freemius Settings Init
// - Check if Parent Plugin Active and Loaded
// - Check if Parent Plugin Active
// - Maybe Initialize Freemius
// - Filter Activation URL
// - Freemius Load Check
// - Installation Check
// - Installation Check Notice
// - Free Minimum Notice
// - Filter Premium Freemius Init
// - Filter Addons Freemius Init
// - Fix Plugin Update Notices
// - Modify Pro Plugin Update Row


// ---------------------
// Filter Plugin Options
// ---------------------
// Important Note: do not move to a hook, this must be loaded straight away
add_filter( 'radio_station_options', 'radio_station_pro_options', 11 );
function radio_station_pro_options( $options ) {

	foreach ( $options as $key => $option ) {
		// --- just disable the Pro switch so option is handled as normal ---
		if ( isset( $option['pro'] ) && $option['pro'] ) {
			unset( $options[$key]['pro'] );
		}
	}

	// 2.4.1: temporarily disable autoresume feature
	if ( isset( $options['player_autoresume'] ) ) {
		unset( $options['player_autoresume'] );
	}

	// === Newly Added Options ===
	// 2.4.1.3: added in case of older free versions

	// --- Teams Archive Page ---
	// 2.4.1.8: added team archive page (free 2.4.0.6+)
	if ( !isset( $options['team_archive_page'] ) ) {
		$options['team_archive_page'] = array(
			'label'   => __( 'Team Archive Page', 'radio-station' ),
			'type'    => 'select',
			'options' => 'PAGEID',
			'default' => '',
			'helper'  => __( 'Select the Page for displaying the Team archive list.', 'radio-station' ),
			'tab'     => 'archives',
			'section' => 'archives',
			'pro'     => true,
		);
	}

	// --- Automatic Display ---
	// 2.4.1.8: added teams archive automatic page (free 2.4.0.6+)
	if ( !isset( $options['team_archive_auto'] ) ) {
		$options['team_archive_auto'] = array(
			'label'   => __( 'Automatic Display', 'radio-station' ),
			'type'    => 'checkbox',
			'value'   => 'yes',
			'default' => 'yes',
			'helper'  => __( 'Replaces selected page content with default Team Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [teams-archive]',
			'tab'     => 'archives',
			'section' => 'archives',
			'pro'     => true,
		);
	}

	// --- [Pro] Combined Team Tab ---
	// 2.4.1.8: added combined team tab option (2.4.0.7+)
	if ( !isset( $options['combined_team_tab'] ) ) {
		$options['combined_team_tab'] = array(
			'type'    => 'select',
			'label'   => __( 'Combined Team Tab', 'radio-station' ),
			'default' => 'yes',
			'options' => array(
				''     => __( 'Do Not Combine', 'radio-station' ),
				'yes'  => __( 'Combined List', 'radio-station' ),
				// 'grid' => __( 'Combined Grid', 'radio-station' ),
			),
			'helper'  => __( 'Combine team members (eg. hosts, producers) into a single display tab.', 'radio-station' ),
			'tab'     => 'pages',
			'section' => 'show',
			'pro'     => true,
		);
	}

	// --- [Pro] Time Spaced Grid View ---
	// 2.4.1.6: added grid view time spacing option (free 2.4.0.4+)
	if ( !isset( $options['schedule_timegrid'] ) ) {
		$options['schedule_timegrid'] = array(
			'type'    => 'checkbox',
			'label'   => __( 'Time Spaced Grid', 'radio-station' ),
			'default' => '',
			'value'	  => 'yes',
			'helper'  => __( 'Enable Grid View option for equalized time spacing and background imsges.', 'radio-station' ),
			'tab'     => 'pages',
			'section' => 'schedule',
			'pro'     => true,
		);
	}

	// --- Player Bar Height ---
	// 2.4.1.3: added player height setting (free 2.4.0.2+)
	if ( !isset( $options['player_bar_height'] ) ) {
		$options['player_bar_height'] = array(
			'type'    => 'number',
			'min'     => 40,
			'max'     => 400,
			'step'    => 1,
			'label'   => __( 'Player Bar Height', 'radio-station' ),
			'default' => 80,
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => __( 'Set the height of the Sitewide Player Bar in pixels.', 'radio-station' ),
		);
	}

	// --- Display Current Show ---
	// 2.4.1.3: added current show display in player bar (free 2.4.0.3+)
	if ( !isset( $options['player_bar_currentshow'] ) ) {
		$options['player_bar_currentshow'] = array(
			'type'    => 'checkbox',
			'label'   => __( 'Display Current Show', 'radio-station' ),
			'value'   => 'yes',
			'default' => 'yes',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => __( 'Display the Current Show in the Player Bar.', 'radio-station' ),
		);
	}

	// --- Display Metadata ---
	// 2.4.1.3: added display metadata in player bar (free 2.4.0.3+)
	if ( !isset( $options['player_bar_nowplaying'] ) ) {
		$options['player_bar_nowplaying'] = array(
			'type'    => 'checkbox',
			'label'   => __( 'Display Now Playing', 'radio-station' ),
			'value'   => 'yes',
			'default' => 'yes',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => __( 'Display the currently playing song in the Player Bar, if a supported metadata format is available. (Icy Meta, Icecast, Shoutcast 1/2, Current Playlist)', 'radio-station' ),
		);
	}

	// --- Metadata URL ---
	// 2.4.1.3: added alternative metadata URL (free 2.4.0.3+)
	if ( !isset( $options['player_bar_metadata'] ) ) {
		$options['player_bar_metadata'] = array(
			'type'    => 'url',
			'label'   => __( 'Metadata URL', 'radio-station' ),
			'default' => '',
			'tab'     => 'player',
			'section' => 'bar',
			'helper'  => __( 'Now playing metadata is normally retrieved via the Stream URL. Use this setting if you need to provide an alternative metadata location.', 'radio-station' ),
		);
	}

	// --- Page Load Timeout ---
	// 2.4.1.4: added page load timeout setting (free 2.4.0.2+)
	if ( !isset( $options['player_bar_timeout'] ) ) {
		$options['player_bar_timeout'] = array(
			'type'    => 'number',
			'label'   => __( 'Page Load Timeout', 'teleporter' ),
			'default' => 7000,
			'min'     => 0,
			'step'    => 500,
			'max'     => 20000,
			'helper'  => __( 'Number of milliseconds to wait for new Page to load before fading in anyway. Use 0 for instant display.', 'teleporter' ),
			'tab'     => 'player',
			'section' => 'bar',
		);
	}

	return $options;
}

// ----------------------
// Check Pro Version Type
// ----------------------
// 2.4.1.5: distinguish premium from add-on via plugin header
function radio_station_pro_check_version_type() {

	global $radio_station_pro_data;
	if ( isset( $radio_station_pro_data['pro_version_type'] ) ) {
		return $radio_station_pro_data['pro_version_type'];
	}

	// --- read Pro file plugin header ---
	$fp = fopen( RADIO_STATION_PRO_FILE, 'r' );
	$plugin_header = fread( $fp, 1024 );
	fclose( $fp );
	if ( strstr( $plugin_header, 'Radio Station Pro (Premium)' ) ) {
		$version_type = 'premium';
	} else {
		$version_type = 'addon';
	}
	
	if ( RADIO_STATION_PRO_DEBUG ) {
		echo '<span style="display:none;">Pro Version Type: ' . $version_type . '</span>' . PHP_EOL;
	}
	
	// --- set plugin version type ---
	if ( !is_array( $radio_station_pro_data ) ) {
		$radio_station_pro_data = array();
	}
	$radio_station_pro_data['pro_version_type'] = $version_type;
	return $version_type;
}

// -------------------
// Initialize Freemius
// -------------------
function radio_station_pro_freemius() {

	global $radio_station_pro_freemius;

	if ( !isset( $radio_station_pro_freemius ) ) {

		// if ( file_exists( dirname( dirname( __FILE__ ) ) . '/radio-station/freemius/start.php' ) ) {
		//	require_once dirname( dirname( __FILE__ ) ) . '/radio-station/freemius/start.php';
		// } else {
			if ( file_exists( RADIO_STATION_PRO_DIR . '/freemius/start.php' ) ) {
				require_once RADIO_STATION_PRO_DIR . '/freemius/start.php';
			} else {
				// test: do not initialize Freemius if not present
				return;
			}
		// }

		// --- Addon Settings ---
		// 2.4.1.3: update support submenu link
		$first_path = add_query_arg( 'page', 'radio-station', 'admin.php' );
		$first_path = add_query_arg( 'welcome', 'true', $first_path );
		$pro_settings = array(
			'id'                  => '7984',
			'slug'                => 'radio-station-pro',
			// 'premium_slug'        => 'radio-station-pro',
			'type'                => 'plugin',
			'public_key'          => 'pk_0995ae72abbc099c158bea14564a9',
			'is_premium'          => true,
			'has_paid_plans'      => true,
			'is_premium_only'     => true,
			// 'has_affiliation'     => 'selected',
			'is_org_compliant'    => false,
			'parent_id'           => 4526,
			'parent'              => array(
				'id'              => '4526',
				'slug'            => 'radio-station',
				'public_key'      => 'pk_aaf375c4fb42e0b5b3831e0b8476b',
				'name'            => 'Radio Station',
			),
			'menu' => array(
				'first-path'      => $first_path,
				'support'         => 'https://radiostation.pro/support/',
			),
			// 2.4.1.8: add bundles configuration
			'bundle_id'           => '9521',
			'bundle_public_key'   => 'pk_a2650f223ef877e87fe0fdfc4442b',
			'bundle_license_auto_activation' => true,
		);

		// --- maybe set the SDK to work in a sandbox mode (for development) ---
		// 2.4.1.5: added secret key constant for local dev testing
		if ( defined( 'RADIO_STATION_PRO_SECRET_KEY' ) ) {
			$pro_settings['secret_key'] = RADIO_STATION_PRO_SECRET_KEY;
		}

		$radio_station_pro_freemius = fs_dynamic_init( $pro_settings );

		// if ( RADIO_STATION_DEBUG ) {
		// 	echo "RSP Freemius Slug: " . $radio_station_pro_freemius->get_slug() . PHP_EOL;
		// }

		return $radio_station_pro_freemius;
	}
}

// ----------------------------------
// Pro Premium Freemius Settings Init
// ----------------------------------
function radio_station_pro_premium_freemius_settings( $settings ) {

	// --- Pro Premium Settings ---
	$settings['id']                  = '4526';
	$settings['slug']                = 'radio-station';
	$settings['premium_slug']        = 'radio-station-pro';
	$settings['type']                = 'plugin';
	$settings['public_key']          = 'pk_aaf375c4fb42e0b5b3831e0b8476b';
	$settings['is_premium']          = true;
	$settings['premium_suffix']      = '';
	$settings['has_premium_version'] = true;
	$settings['has_addons']          = false;
	$settings['has_paid_plans']      = true;
	// 2.4.1.8: remove trial (not available for bundles)
	// $settings['trial']               = array(
	//	'days'               => 7,
	// 	'is_require_payment' => true,
	// );
	// $settings['has_affiliation']     = 'selected';

	// $first_path = add_query_arg( 'page', 'radio-station', 'admin.php' );
	// $first_path = add_query_arg( 'welcome', 'true', $first_path );
	// $settings['menu']['first-path'] = $first_path,
	$settings['menu']['support']     = 'https://radiostation.pro/support/';

	// --- maybe set the SDK to work in a sandbox mode (for development) ---
	// 2.4.1.5: added secret key constant for local dev testing
	if ( defined( 'RADIO_STATION_PRO_SECRET_KEY' ) ) {
		$settings['secret_key'] = RADIO_STATION_PRO_SECRET_KEY;
	}

	return $settings;
}

// --------------------------------
// Pro Addon Freemius Settings Init
// --------------------------------
function radio_station_pro_addon_freemius_settings( $settings ) {

	// --- force free plugin to non-premium with Pro add-on ---
	// (hides Upgrade menus/links etc from existing addon users)
	$settings['is_premium']          = false;
	$settings['has_premium_version'] = false;
	$settings['has_paid_plans']      = false;
	if ( isset( $settings['trial'] ) ) {
		unset( $settings['trial'] );
	}
	return $settings;
}


// -----------------------------
// Check if Parent Plugin Loaded
// -----------------------------
function radio_station_pro_is_parent_loaded() {
	// --- Check if the parent's init SDK method exists ---
	return class_exists( 'radio_station_loader' );
}

// -----------------------------
// Check if Parent Plugin Active
// -----------------------------
function radio_station_pro_is_parent_active() {

	$active_plugins = get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
		$active_plugins         = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
	}

	foreach ( $active_plugins as $basename ) {
		if ( ( 0 === strpos( $basename, 'radio-station/' ) ) || strstr( $basename, 'radio-station.php' ) ) {
			return true;
		}
	}

	return false;
}

// -------------------------
// Maybe Initialize Freemius
// -------------------------
function radio_station_pro_addon_freemius_init() {

	// --- Init Freemius for Add-on ---
	radio_station_pro_freemius();

	// --- Signal that the add-on's SDK was initiated ---

	do_action( 'radio_station_pro_loaded' );
}

// ---------------------
// Filter Activation URL
// ---------------------
// 2.4.1.8: added to correct link URL in admin activation notice
add_filter( 'fs_connect_url_radio-station-pro', 'radio_station_pro_activation_url', 11 );
function radio_station_pro_activation_url( $url ) {
	$url = str_replace( 'page=radio-station-pro', 'page=radio-station-account', $url );
	return $url;
}

// -------------------
// Freemius Load Check
// -------------------
$version_type = radio_station_pro_check_version_type();
if ( 'premium' == $version_type ) {

	// --- filter settings for the Freemius init on free ---
	add_filter( 'freemius_init_settings_radio_station', 'radio_station_pro_premium_freemius_settings' );

} elseif ( 'addon' == $version_type ) {

	// --- filter settings for the Freemius init on free ---
	add_filter( 'freemius_init_settings_radio_station', 'radio_station_pro_addon_freemius_settings' );

	// --- handle add-on version loading ---
	if ( radio_station_pro_is_parent_loaded() ) {

		// --- If parent already included, init Add-on ---
		radio_station_pro_addon_freemius_init();

	} elseif ( radio_station_pro_is_parent_active() ) {

		// --- init add-on version only after the parent is loaded ---
		// 2.4.1.3: function typo radio_station_pro_init
		add_action( 'radio_station_loaded', 'radio_station_pro_addon_freemius_init' );

	} else {

		// --- Even though the parent is not activated, execute add-on for activation / uninstall hooks ---
		radio_station_pro_addon_freemius_init();

	}
}

// ------------------
// Installation Check
// ------------------
add_action( 'plugins_loaded', 'radio_station_pro_installation_check', 9 );
function radio_station_pro_installation_check() {

	// --- check if Free version is loaded ---
	if ( !defined( 'RADIO_STATION_FILE' ) ) {

		// --- admin notice that Free version is required ---
		// 2.4.1.8: fix to action hook admin_notice
		add_action( 'admin_notices', 'radio_station_pro_check_notice' );

	} else {

		// --- check if Free version is required minimum for Pro ---
		// 2.3.1: fix to ensure get_plugin_data functino is available
		if ( !function_exists( 'get_plugin_data' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugin_data = get_plugin_data( RADIO_STATION_FILE );
		if ( version_compare( RADIO_STATION_PRO_FREE_MINIMUM, $plugin_data['Version'], '>' ) ) {

			// --- add minimum required version admin notice ---
			// 2.4.1.8: fix to action hook admin_notice
			add_action( 'admin_notices', 'radio_station_pro_free_minimum_notice' );

		} else {

			if ( RADIO_STATION_PRO_DEBUG ) {
				echo '<span style="display:none;">Radio Station Pro Loading...</span>' . PHP_EOL;
			}

			// --- load all Pro Version functions ---
			radio_station_pro_load_plugin();

		}
	}
}

// -------------------------
// Installation Check Notice
// -------------------------
function radio_station_pro_check_notice() {

	// --- open notice wrapper ---
	echo '<div id="radio-station-pro-check-notice" class="notice notice-warning" style="position:relative; text-align:center; padding: 10px 0;">';

	// --- notice message ---
	// 2.4.1.8: fix $s to %s in string translation
	echo sprintf( __( 'Radio Station needs to be installed and activated to use %s.', 'radio-station' ), RADIO_STATION_PRO_NAME ) . '<br><br>';

	// --- check if Free version is installed ---
	// 2.4.1.8: fix to undefined variable version
	$version = 0;
	$plugin = false;
	$plugins = get_plugins();
	foreach ( $plugins as $plugin_path => $data ) {
		if ( $data['Title'] == 'Radio Station' ) {
			if ( $plugin ) {
				// --- get the highest version of the plugin ---
				if ( version_compare( $version, $data['Version'], '<' ) ) {
					$plugin = $plugin_path;
					$version = $data['Version'];
				}
			} else {
				$plugin = $plugin_path;
				$version = $data['Version'];
			}
		}
	}

	if ( $plugin ) {

		// --- plugin activation link ---
		global $radio_station_plugin_basename;
		$radio_station_plugin_basename = $plugin;
		$activate_url = admin_url('plugins.php').'?action=activate&plugin=' . $plugin;
		$activate_url = wp_nonce_url( $activate_url, 'activate-plugin_' . $plugin );
		echo '<a href="' . esc_url( $activate_url ) . '" class="button button-primary">' . esc_html( 'Activate Radio Station', 'radio-station' ) . '</a>';
		// 2.4.1.8: add activate free link on plugin page
		add_filter( 'plugin_action_links_' . RADIO_STATION_PRO_BASENAME, 'radio_station_pro_activate_free_link', 19, 2 );

	} else {

		// --- plugin (re)installation button ---
		$slug = 'radio-station';
		$install_url = self_admin_url( 'update.php' ) . '?action=install-plugin&plugin=' . $slug;
		$install_url = wp_nonce_url( $install_url, 'install-plugin_' . $slug );
		echo '<a href="' . esc_url( $install_url ) . '" class="button button-primary">' . esc_html( 'Install Radio Station', 'radio-station' ) . '</a>';
		// 2.4.1.8: add install free link on plugin page
		add_filter( 'plugin_action_links_' . RADIO_STATION_PRO_BASENAME, 'radio_station_pro_install_free_link', 19, 2 );

	}

	// --- close notice wrapper ---
	echo '</div>';

}

// -------------------------
// Install Free Version Link
// -------------------------
// 2.4.1.8: add plugin action link on plugins page
function radio_station_pro_install_free_link( $links, $file ) {
	
	// --- add install link to plugin links ---
	$slug = 'radio-station';
	$install_url = self_admin_url( 'update.php' ) . '?action=install-plugin&plugin=' . $slug;
	$install_url = wp_nonce_url( $install_url, 'install-plugin_' . $slug );
	$install_link = '<a href="' . esc_url( $install_url ) . '">' . esc_html( 'Install Radio Station', 'radio-station' ) . '</a>';
	$links[] = $install_link;
	return $links;
}

// --------------------------
// Activate Free Version link
// --------------------------
// 2.4.1.8: add plugin action link on plugins page
function radio_station_pro_activate_free_link( $links, $file ) {
	
	// --- add activation link to plugin links ---
	global $radio_station_plugin_basename;
	$plugin = $radio_station_plugin_basename;
	$activate_url = admin_url( 'plugins.php' ) . '?action=activate&plugin=' . urlencode( $plugin );
	$activate_url = wp_nonce_url( $activate_url, 'activate-plugin_' . $plugin );
	$activate_link = '<a href="' . esc_url( $activate_url ) . '">' . esc_html( 'Activate Radio Station', 'radio-station' ) . '</a>';
	$links[] = $activate_link;
	return $links;
}

// ---------------------------
// Free Minimum Version Notice
// ---------------------------
function radio_station_pro_free_minimum_notice() {

	// --- open notice wrapper ---
	echo '<div id="radio-station-pro-check-notice" class="notice notice-warning" style="position:relative; text-align:center; padding: 10px 0;">';

	// --- notice message ---
	echo sprintf( __( 'Your Radio Station plugin needs to updated to work with %s.', 'radio-station' ), RADIO_STATION_PRO_NAME ) . '<br>';
	echo __( 'The required minimum version of Radio Station plugin is:', 'radio-station' ) . ' ' . RADIO_STATION_PRO_FREE_MINIMUM . '<br><br>';

	// --- update plugin button ---
	$plugin = 'radio-station/radio-station.php';
	$update_link = admin_url( 'update.php' ) .'?action=upgrade-plugin&plugin=' . $plugin;
	$update_link = wp_nonce_url( $update_link, 'upgrade-plugin_' . $plugin );
	echo '<a href="' . esc_url( $update_link ) . '" class="button button-primary">' . esc_html( 'Update Radio Station', 'radio-station' ) . '</a>';

	// --- close notice wrapper ---
	echo '</div>';
}

// ----------------------------
// Filter Premium Freemius Init
// ----------------------------
// 2.4.1.5: filter premium flag in free version init
add_filter( 'freemius_init_premium_radio_station', 'radio_station_pro_freemius_premium_init' );
function radio_station_pro_freemius_premium_init( $premium ) {
	$version_type = radio_station_pro_check_version_type();
	if ( 'premium' == $version_type ) {
		$premium = true;
	} elseif ( 'addon' == $version_type ) {
		$premium = false;
	}
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Premium Menus Init? ' . ( $premium ? '1' : '0' ) . '</span>';
	}
	return $premium;
}

// ---------------------------
// Filter Addons Freemius Init
// ---------------------------
// 2.4.1.5: filter addons flag in free version init
add_filter( 'freemius_init_addons_radio_station', 'radio_station_pro_freemius_addons_init' );
function radio_station_pro_freemius_addons_init( $addons ) {
	$version_type = radio_station_pro_check_version_type();
	if ( 'addon' == $version_type ) {
		$addons = true;
	}
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Addons Menus Init? ' . ( $addons ? '1' : '0' ) . '</span>';
	}
	return $addons;
}


// -------------------------
// Fix Plugin Update Notices
// -------------------------
// 2.4.1.7: fix conflicting upgrade messages for free/pro/addon on plugin page
add_action( 'init', 'radio_station_pro_remove_free_upgrade_message' );
function radio_station_pro_remove_free_upgrade_message() {

	if ( !is_admin() ) {
		return;
	}
	if ( RADIO_STATION_PRO_DEBUG ) {
		delete_site_transient( 'update_plugins' );
	}
	$type = radio_station_pro_check_version_type();

	// --- filter Free Freemius actions ---
	// $freemius = radio_station_freemius_instance();
	global $radio_station_freemius;
	if ( isset( $radio_station_freemius ) ) {
		$freemius = $radio_station_freemius;
		if ( is_object( $freemius ) ) {
			$basename = $freemius->get_plugin_basename();
			$updater = FS_Plugin_Updater::instance( $freemius );
			remove_action( 'after_plugin_row_' . $basename, array( $updater, 'catch_plugin_update_row'), 9 );
			remove_action( 'after_plugin_row_' . $basename, array( $updater, 'edit_and_echo_plugin_update_row' ), 11 );
			if ( 'addon' == $type ) {
				remove_filter( 'pre_set_site_transient_update_plugins', array( $updater, 'pre_set_site_transient_update_plugins_filter' ) );
			} elseif ( 'premium' == $type ) {
				// TODO: recheck if need to remove from addon version too ?
				remove_filter( 'plugins_api', array( $updater, 'plugins_api_filter' ), 10 );
			}
			// remove_action( 'admin_footer', array( $freemius, '_add_premium_version_upgrade_selection_dialog_box' ) );
		}
	}

	// --- filter Pro Freemius actions ---
	/* if ( 'addon' == $type ) {
		global $radio_station_pro_freemius;
		if ( is_object( $radio_station_pro_freemius ) ) {
			$pro_basename = $radio_station_pro_freemius->get_plugin_basename();
			$pro_updater = FS_Plugin_Updater::instance( $radio_station_pro_freemius );
			// remove_action( 'after_plugin_row_' . $pro_basename, array( $pro_updater, 'catch_plugin_update_row'), 9 );
			// remove_action( 'after_plugin_row_' . $pro_basename, array( $pro_updater, 'edit_and_echo_plugin_update_row' ), 11 );
			// remove_filter( 'pre_set_site_transient_update_plugins', array( $pro_updater, 'pre_set_site_transient_update_plugins_filter' ) );
			// remove_filter( 'plugins_api', array( $pro_updater, 'plugins_api_filter' ), 10 );
			// remove_action( 'admin_footer', array( $radio_station_pro_freemius, '_add_premium_version_upgrade_selection_dialog_box' ) );
		} elseif ( RADIO_STATION_DEBUG ) {
			echo 'Freemius Pro Object: ' . print_r( $radio_station_pro_freemius, true );
		}
	} */
	if ( 'premium' == $type ) {
		$pro_basename = RADIO_STATION_PRO_BASENAME;
		// note: output buffering intentionally started twice here!
		add_action( 'after_plugin_row_' . $pro_basename, 'radio_station_pro_catch_plugin_update_row', 8 );
		add_action( 'after_plugin_row_' . $pro_basename, 'radio_station_pro_catch_plugin_update_row', 9 );
		add_action( 'after_plugin_row_' . $pro_basename, 'radio_station_pro_edit_and_echo_plugin_update_row', 11, 2 );
		add_filter( 'plugins_api', 'radio_station_pro_plugins_api_filter', 11, 3 );
	}

	// --- filter updates transient ---
	add_filter( 'pre_set_site_transient_update_plugins', 'radio_station_pro_capture_transient_update_plugins', 0 );
	add_filter( 'pre_set_site_transient_update_plugins', 'radio_station_pro_capture_transient_update_plugins', 9 );
	add_filter( 'pre_set_site_transient_update_plugins', 'radio_station_pro_edit_transient_update_plugins', 11 );
}

// -------------------------------------
// Capture Update Plugins Transient Data
// -------------------------------------
function radio_station_pro_capture_transient_update_plugins( $transient_data ) {
	global $radio_station_updates;
	// 2.4.1.9: fix for PHP8 cannot check property_exists of non-object
	if ( $transient_data && is_object( $transient_data ) &&  property_exists( $transient_data, 'response' ) ) {
		$basename = defined( 'RADIO_STATION_BASENAME' ) ? RADIO_STATION_BASENAME : 'radio-station/radio-station.php';
		$response = $transient_data->response;
		if ( isset( $response[$basename] ) ) {
			if ( strstr( $response[$basename]->url, 'freemius' ) ) {
				// --- make sure the new version is greater than current version ---
				if ( version_compare(  trim( $response[$basename]->new_version ), RADIO_STATION_PRO_VERSION, '>' ) ) {
					$radio_station_updates['pro'] = $response[$basename];
					if ( RADIO_STATION_PRO_DEBUG ) {
						echo 'Radio Station (Pro) Update Data: ' . print_r( $radio_station_updates['pro'], true ) . PHP_EOL;
					}
				}
				unset( $response[$basename] );
				$transient_data->response = $response;
			} else {
				$radio_station_updates['free'] = $response[$basename];
				if ( RADIO_STATION_PRO_DEBUG ) {
					echo 'Radio Station (Free) Update Data: ' . print_r( $radio_station_updates['free'], true ) . PHP_EOL;
				}
			}
		}
	}
	return $transient_data;
}

// ----------------------------------
// Edit Update Plugins Transient Data
// ----------------------------------
function radio_station_pro_edit_transient_update_plugins( $transient_data ) {
	global $radio_station_updates;
	// 2.4.1.9: fix for PHP8 cannot check property_exists of non-object
	if ( $transient_data && is_object( $transient_data ) && property_exists( $transient_data, 'response' ) ) {
		$response = $transient_data->response;
		$basename = defined( 'RADIO_STATION_BASENAME' ) ? RADIO_STATION_BASENAME : 'radio-station/radio-station.php';
		if ( isset( $response[$basename] ) ) {
			if ( strstr( $response[$basename]->url, 'freemius' ) ) {
				unset( $response[$basename] );
			}
		}
		if ( isset( $radio_station_updates['pro'] ) ) {
			$response[RADIO_STATION_PRO_BASENAME] = $radio_station_updates['pro'];
		}
		if ( isset( $radio_station_updates['free'] ) ) {		
			$response[$basename] = $radio_station_updates['free'];
		}
		$transient_data->response = $response;
		if ( RADIO_STATION_PRO_DEBUG ) {
			echo 'Plugins Update Transient Modified: ' . print_r( $transient_data, true ) . PHP_EOL;
		}
	}
	return $transient_data;
}

// -----------------------------
// Capture Pro Plugin Update Row
// -----------------------------
function radio_station_pro_catch_plugin_update_row( $file ) {
	if ( $file != RADIO_STATION_PRO_BASENAME ) {
		return;
	}
	ob_start();
}

// --------------------------
// Edit Pro Plugin Update Row
// --------------------------
function radio_station_pro_edit_and_echo_plugin_update_row( $file, $plugin_data ) {

	if ( $file != RADIO_STATION_PRO_BASENAME ) {
		return;
	}

	// -- get freemius update row ---
	if ( !isset( $GLOBALS['radio_station_freemius'] ) ) {
		return;
	}
	$freemius = $GLOBALS['radio_station_freemius'];
	$updater = FS_Plugin_Updater::instance( $freemius );
	$updater->edit_and_echo_plugin_update_row( $file, $plugin_data );

	// --- modify plugin details link ---
	$plugin_row = ob_get_clean();
	$find = 'tab=plugin-information&#038;plugin=radio-station';
	$replace = 'tab=plugin-information&#038;plugin=radio-station-pro';
	if ( RADIO_STATION_DEBUG ) {
		echo "FIND: " . $find . PHP_EOL;
		echo "REPLACE: " . $replace . PHP_EOL;
		echo "<textarea>" . $plugin_row . "</textarea>" . PHP_EOL;
	}
	$plugin_row = str_replace( $find, $replace, $plugin_row );
	echo $plugin_row;
}

// ------------------
// Plugins API Filter
// ------------------
function radio_station_pro_plugins_api_filter( $data, $action = '', $args = null ) {

	if ( ( 'plugin_information' !== $action ) || !isset( $args->slug ) || ( 'radio-station-pro' != $args->slug ) ) {
		return $data;
	}

	// --- override with premium plugin information ---
	if ( isset( $radio_station_freemius ) && is_object( $radio_station_freemius ) ) {
		$freemius = $radio_station_freemius;
		$basename = $freemius->get_plugin_basename();
		$updater = FS_Plugin_Updater::instance( $freemius );
		$args->slug = 'radio-station';
		$new_data = $updater->plugins_api_filter( $data, $action, $args );

		if ( RADIO_STATION_PRO_DEBUG ) {
			echo "ARGS: " . print_r( $args, true ) . '<br>' . PHP_EOL;
			echo "OLD DATA: " . print_r( $data, true ) . '<br>' . PHP_EOL;
			echo "NEW DATA: " . print_r( $new_data, true ) . '<br>' . PHP_EOL;
		}

		return $new_data;
	}

	return $data;
}
