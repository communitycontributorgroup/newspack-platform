<?php

/*
Plugin Name: Radio Station Pro
Plugin URI: https://radiostation.pro
Description: Adds all Pro Features to the Radio Station Plugin
Author: Tony Zeoli, Tony Hayes
Version: 2.4.1.7.1
Requires at least: 4.0
Text Domain: radio-station
Domain Path: /languages
Author URI: https://netmix.com
*/
// === Load Check ===
// - Define Pro Constants
// - Filter Plugin Options
// - Plugin Activation
// - Plugin Deactivation
// - Initialize Freemius
// - Pro Premium Freemius Settings Init
// - Pro Addon Freemius Settings Init
// - Check if Parent Plugin Active and Loaded
// - Check if Parent Plugin Active
// - Maybe Initialize Freemius
// - Freemius Load Check
// - Installation Check
// - Installation Check Notice
// - Free Minimum Notice
// - Load Teleporter for Player Bar
// - Update Pro Settings
// - Filter Premium Freemius Init
// - Filter Addons Freemius Init
// - Fix Plugin Update Notices
// - Modify Pro Plugin Update Row
// === OPEN LOADER WRAPPER ===
// === Setup ===
// - Include Pro Files
// - Include Pro Features
// - Enqeueue Pro Script
// - Enqeueue Pro Scripts
// === Helper Functions ===
// - Get Host Profile Pages
// - Get Producer Profile Pages
// - Set Thickbox Loading Image
// - Thickbox Styles
// === Settings Filters ===
// - Filter Settings Menu Title
// - Filter Settings Page Title
// - Filter Settings Page Icon URL
// - Teleporter Settings Page Note
// === Template Filters ===
// - Episode Content Template Filter
// - DJ / Host Content Template Filter
// - Producer Content Template Filter
// - Content Template Paths Filter
// - Enqueue Template Styles
// * Host Archive Page Content
// * Producer Archive Page Content
// - Archive Template Hierarchy
// === Shortcodes ===
// - Archive List Shortcode
// === Role and Capabilities ===
// - Set Roles and Capabilities
// - Filter Edit Episode Capabilities
// - Filter Edit Profile Capabilities
// === Transient Caching ===
// - Cache Result Data
// - Clear Cached Data
// - Get Cached Data
// === CLOSE LOADER WRAPPER ===
// Development TODOs
// -----------------
// - test admin notices for Free/Free minimum
// - get latest show episode file URL
// ------------------
// === Load Check ===
// ------------------
// --------------------
// Define Pro Constants
// --------------------
define( 'RADIO_STATION_PRO_FILE', __FILE__ );
define( 'RADIO_STATION_PRO_DIR', dirname( __FILE__ ) );
define( 'RADIO_STATION_PRO_BASENAME', plugin_basename( __FILE__ ) );
define( 'RADIO_STATION_PRO_HOME_URL', 'https://radiostation.pro/' );
define( 'RADIO_STATION_PRO_DOCS_URL', 'https://radiostation.pro/docs/pro/' );
define( 'RADIO_STATION_PRO_VERSION', '2.4.1.7' );
define( 'RADIO_STATION_PRO_FREE_MINIMUM', '2.4.0' );
// --- define custom post type and taxonomy slugs ---
define( 'RADIO_STATION_EPISODE_SLUG', 'rs-episode' );
define( 'RADIO_STATION_TOPICS_SLUG', 'rs-topics' );
define( 'RADIO_STATION_GUESTS_SLUG', 'rs-guests' );
// -----------------------
// Set Debug Mode Constant
// -----------------------

if ( !defined( 'RADIO_STATION_PRO_DEBUG' ) ) {
    $rsp_debug = false;
    if ( isset( $_REQUEST['rsp-debug'] ) && '1' == $_REQUEST['rsp-debug'] ) {
        $rsp_debug = true;
    }
    define( 'RADIO_STATION_PRO_DEBUG', $rsp_debug );
}

// ---------------------
// Filter Plugin Options
// ---------------------
// Important Note: do not move to a hook, this must be loaded straight away
add_filter( 'radio_station_options', 'radio_station_pro_options' );
function radio_station_pro_options( $options )
{
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
    // --- [Pro] Time Spaced Grid View ---
    // 2.4.1.6: added grid view time spacing option
    if ( !isset( $options['schedule_timegrid'] ) ) {
        $options['schedule_timegrid'] = array(
            'type'    => 'checkbox',
            'label'   => __( 'Time Spaced Grid', 'radio-station' ),
            'default' => '',
            'value'   => 'yes',
            'helper'  => __( 'Enable Grid View option for equalized time spacing and background imsges.', 'radio-station' ),
            'tab'     => 'pages',
            'section' => 'schedule',
            'pro'     => true,
        );
    }
    // --- Player Bar Height ---
    // 2.4.1.3: added in case of free 2.4.0.2 or less
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
    // 2.4.1.3: added in case of free 2.4.0.2 or less
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
    // 2.4.1.3: added in case of free 2.4.0.2 or less
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
    // 2.4.1.3: added in case of free 2.4.0.2 or less
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
    // 2.4.1.4: added in case of free 2.4.0.2 or less
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

// -----------------
// Plugin Activation
// -----------------
// (run on plugin activation, and thus also after a plugin update)
register_activation_hook( RADIO_STATION_PRO_FILE, 'radio_station_pro_plugin_activation' );
function radio_station_pro_plugin_activation()
{
    // --- flag to flush rewrite rules ---
    add_option( 'radio_station_flush_rewrite_rules', true );
    // --- clear schedule transients ---
    // 2.4.1.2: add function check for pro-only activation edge case
    if ( function_exists( 'radio_station_clear_cached_data' ) ) {
        radio_station_clear_cached_data( false );
    }
}

// -------------------
// Plugin Deactivation
// -------------------
register_deactivation_hook( RADIO_STATION_PRO_FILE, 'flush_rewrite_rules' );
// ----------------------
// Check Pro Version Type
// ----------------------
// 2.4.1.5: distinguish premium from add-on via plugin header
function radio_station_pro_check_version_type()
{
    global  $radio_station_pro_data ;
    if ( isset( $radio_station_pro_data['pro_version_type'] ) ) {
        return $radio_station_pro_data['pro_version_type'];
    }
    $fp = fopen( __FILE__, 'r' );
    $plugin_header = fread( $fp, 1024 );
    fclose( $fp );
    
    if ( strstr( $plugin_header, 'Radio Station Pro (Premium)' ) ) {
        $version_type = 'premium';
    } else {
        $version_type = 'addon';
    }
    
    if ( !is_array( $radio_station_pro_data ) ) {
        $radio_station_pro_data = array();
    }
    $radio_station_pro_data['pro_version_type'] = $version_type;
    return $version_type;
}

// -------------------
// Initialize Freemius
// -------------------
function radio_station_pro_freemius()
{
    global  $radio_station_pro_freemius ;
    
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
            'id'               => '7984',
            'slug'             => 'radio-station-pro',
            'type'             => 'plugin',
            'public_key'       => 'pk_0995ae72abbc099c158bea14564a9',
            'is_premium'       => true,
            'has_paid_plans'   => true,
            'is_premium_only'  => true,
            'is_org_compliant' => false,
            'parent_id'        => 4526,
            'parent'           => array(
            'id'         => '4526',
            'slug'       => 'radio-station',
            'public_key' => 'pk_aaf375c4fb42e0b5b3831e0b8476b',
            'name'       => 'Radio Station',
        ),
            'menu'             => array(
            'first-path' => $first_path,
            'support'    => 'https://radiostation.pro/support/',
        ),
        );
        // --- maybe set the SDK to work in a sandbox mode (for development) ---
        // 2.4.1.5: added secret key constant for local dev testing
        if ( defined( 'RADIO_STATION_PRO_SECRET_KEY' ) ) {
            $pro_settings['secret_key'] = RADIO_STATION_PRO_SECRET_KEY;
        }
        $radio_station_pro_freemius = fs_dynamic_init( $pro_settings );
        return $radio_station_pro_freemius;
    }

}

// ----------------------------------
// Pro Premium Freemius Settings Init
// ----------------------------------
function radio_station_pro_premium_freemius_settings( $settings )
{
    // --- Pro Premium Settings ---
    $settings['id'] = '4526';
    $settings['slug'] = 'radio-station';
    $settings['premium_slug'] = 'radio-station-pro';
    $settings['type'] = 'plugin';
    $settings['public_key'] = 'pk_aaf375c4fb42e0b5b3831e0b8476b';
    $settings['is_premium'] = true;
    $settings['premium_suffix'] = '';
    $settings['has_premium_version'] = true;
    $settings['has_addons'] = false;
    $settings['has_paid_plans'] = true;
    $settings['trial'] = array(
        'days'               => 7,
        'is_require_payment' => true,
    );
    // $settings['has_affiliation']     = 'selected';
    // $first_path = add_query_arg( 'page', 'radio-station', 'admin.php' );
    // $first_path = add_query_arg( 'welcome', 'true', $first_path );
    // $settings['menu']['first-path'] = $first_path,
    $settings['menu']['support'] = 'https://radiostation.pro/support/';
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
function radio_station_pro_addon_freemius_settings( $settings )
{
    // --- force free plugin to non-premium with Pro add-on ---
    // (hides Upgrade menus/links etc from existing addon users)
    $settings['is_premium'] = false;
    $settings['has_premium_version'] = false;
    $settings['has_paid_plans'] = false;
    if ( isset( $settings['trial'] ) ) {
        unset( $settings['trial'] );
    }
    return $settings;
}

// -----------------------------
// Check if Parent Plugin Loaded
// -----------------------------
function radio_station_pro_is_parent_loaded()
{
    // --- Check if the parent's init SDK method exists ---
    return class_exists( 'radio_station_loader' );
}

// -----------------------------
// Check if Parent Plugin Active
// -----------------------------
function radio_station_pro_is_parent_active()
{
    $active_plugins = get_option( 'active_plugins', array() );
    
    if ( is_multisite() ) {
        $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
        $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
    }
    
    foreach ( $active_plugins as $basename ) {
        if ( 0 === strpos( $basename, 'radio-station/' ) || strstr( $basename, 'radio-station.php' ) ) {
            return true;
        }
    }
    return false;
}

// -------------------------
// Maybe Initialize Freemius
// -------------------------
function radio_station_pro_addon_freemius_init()
{
    // --- Init Freemius for Add-on ---
    radio_station_pro_freemius();
    // --- Signal that the add-on's SDK was initiated ---
    do_action( 'radio_station_pro_loaded' );
}

// -------------------
// Freemius Load Check
// -------------------
$version_type = radio_station_pro_check_version_type();
if ( RADIO_STATION_PRO_DEBUG ) {
    echo  '<span style="display:none;">Radio Station Pro Version Type: ' . $version_type . '</span>' . PHP_EOL ;
}

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
function radio_station_pro_installation_check()
{
    // --- check if Free version is loaded ---
    
    if ( !defined( 'RADIO_STATION_FILE' ) ) {
        // --- admin notice that Free version is required ---
        add_action( 'admin_notice', 'radio_station_pro_check_notice' );
    } else {
        // --- check if Free version is required minimum for Pro ---
        // 2.3.1: fix to ensure get_plugin_data functino is available
        if ( !function_exists( 'get_plugin_data' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugin_data = get_plugin_data( RADIO_STATION_FILE );
        
        if ( version_compare( RADIO_STATION_PRO_FREE_MINIMUM, $plugin_data['Version'], '>' ) ) {
            // --- add minimum required version admin notice ---
            add_action( 'admin_notice', 'radio_station_pro_free_minimum_notice' );
        } else {
            // --- load all Pro Version functions ---
            radio_station_pro_load_plugin();
        }
    
    }

}

// -------------------------
// Installation Check Notice
// -------------------------
function radio_station_pro_check_notice()
{
    // --- open notice wrapper ---
    echo  '<div id="radio-station-pro-check-notice" class="notice notice-warning" style="position:relative; text-align:center;">' ;
    // --- notice message ---
    echo  __( 'Radio Station needs to be installed and activated to use Radio Station Pro.', 'radio-station' ) . '<br><br>' ;
    // --- check if Free version is installed ---
    $plugin = false;
    $plugins = get_plugins();
    foreach ( $plugins as $plugin_path => $data ) {
        if ( $data['Title'] == 'Radio Station' ) {
            
            if ( $plugin ) {
                
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
        $activate_link = admin_url( 'plugins.php' ) . '?action=activate&plugin=' . $plugin;
        $activate_link = wp_nonce_url( $activate_link, 'activate-plugin_' . $plugin );
        echo  '<a href="' . esc_url( $activate_link ) . '" class="button button-primary">' . esc_html( 'Activate Radio Station', 'radio-station' ) . '</a>' ;
    } else {
        // --- plugin (re)installation button ---
        $slug = 'radio-station';
        $install_link = self_admin_url( 'update.php' ) . '?action=install-plugin&plugin=' . $slug;
        $install_link = wp_nonce_url( $install_link, 'install-plugin_' . $slug );
        echo  '<a href="' . esc_url( $install_link ) . '" class="button button-primary">' . esc_html( 'Install Radio Station', 'radio-station' ) . '</a>' ;
    }
    
    // --- close notice wrapper ---
    echo  '</div>' ;
}

// ---------------------------
// Free Minimum Version Notice
// ---------------------------
function radio_station_pro_free_minimum_notice()
{
    // --- open notice wrapper ---
    echo  '<div id="radio-station-pro-check-notice" class="notice notice-warning" style="position:relative; text-align:center;">' ;
    // --- notice message ---
    echo  __( 'Your Radio Station plugin needs to updated to work with Radio Station Pro.', 'radio-station' ) . '<br>' ;
    echo  __( 'The required minimum version of Radio Station plugin is:', 'radio-station' ) . ' ' . RADIO_STATION_FREE_MINIMUM . '<br><br>' ;
    // --- update plugin button ---
    $plugin = 'radio-station/radio-station.php';
    $update_link = admin_url( 'update.php' ) . '?action=upgrade-plugin&plugin=' . $plugin;
    $update_link = wp_nonce_url( $update_link, 'upgrade-plugin_' . $plugin );
    echo  '<a href="' . esc_url( $update_link ) . '" class="button button-primary">' . esc_html( 'Update Radio Station', 'radio-station' ) . '</a>' ;
    // --- close notice wrapper ---
    echo  '</div>' ;
}

// ------------------------------
// Load Teleporter for Player Bar
// ------------------------------
add_action( 'plugins_loaded', 'radio_station_pro_teleporter_loader' );
function radio_station_pro_teleporter_loader()
{
    // --- bug out if free version is not active ---
    // 2.4.1.2: added for pro-only activation edge case
    if ( !function_exists( 'radio_station_get_setting' ) ) {
        return;
    }
    // --- get player bar position ---
    $position = radio_station_get_setting( 'player_bar' );
    if ( '' == $position || 'off' == $position ) {
        return;
    }
    // --- check if separate teleporter plugin is already active ---
    if ( function_exists( 'teleporter_enqueue_scripts' ) ) {
        return;
    }
    // --- check for continuous player setting ---
    $continuous = radio_station_get_setting( 'player_bar_continuous' );
    
    if ( '1' == $continuous || 'yes' == $continuous ) {
        // --- include teleporter plugin for pageload transitions ---
        $teleporter = RADIO_STATION_PRO_DIR . '/teleporter/teleporter.php';
        include_once $teleporter;
        // 2.4.1.5: fix to misssing teleporter loader action
        teleporter_load_plugin();
    }
    
    
    if ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) {
        echo  '<span style="display:none;">Continuous Player? ' . $continuous . PHP_EOL ;
        echo  'Loaded Teleporter Path: ' . $teleporter . '</span>' ;
    }

}

// -------------------
// Update Pro Settings
// -------------------
// 2.4.1.4: added for Pro specific special settings
function radio_station_pro_update_settings()
{
    // 2.4.1.4: trigger role interface updates
    radio_station_pro_update_roles();
}

// ----------------------------
// Filter Premium Freemius Init
// ----------------------------
// 2.4.1.5: filter premium flag in free version init
add_filter( 'freemius_init_premium_radio_station', 'radio_station_pro_freemius_premium_init' );
function radio_station_pro_freemius_premium_init( $premium )
{
    $version_type = radio_station_pro_check_version_type();
    
    if ( 'premium' == $version_type ) {
        $premium = true;
    } elseif ( 'addon' == $version_type ) {
        $premium = false;
    }
    
    if ( RADIO_STATION_DEBUG ) {
        echo  '<span style="display:none;">Premium Menus Init? ' . (( $premium ? '1' : '0' )) . '</span>' ;
    }
    return $premium;
}

// ---------------------------
// Filter Addons Freemius Init
// ---------------------------
// 2.4.1.5: filter addons flag in free version init
add_filter( 'freemius_init_addons_radio_station', 'radio_station_pro_freemius_addons_init' );
function radio_station_pro_freemius_addons_init( $addons )
{
    $version_type = radio_station_pro_check_version_type();
    if ( 'addon' == $version_type ) {
        $addons = true;
    }
    if ( RADIO_STATION_DEBUG ) {
        echo  '<span style="display:none;">Addons Menus Init? ' . (( $addons ? '1' : '0' )) . '</span>' ;
    }
    return $addons;
}

// -------------------------
// Fix Plugin Update Notices
// -------------------------
// 2.4.1.7: fix conflicting upgrade messages for free/pro/addon on plugin page
add_action( 'init', 'radio_station_pro_remove_free_upgrade_message' );
function radio_station_pro_remove_free_upgrade_message()
{
    if ( !is_admin() ) {
        return;
    }
    if ( RADIO_STATION_DEBUG ) {
        delete_site_transient( 'update_plugins' );
    }
    $type = radio_station_pro_check_version_type();
    // --- filter Free Freemius actions ---
    // $freemius = radio_station_freemius_instance();
    
    if ( isset( $GLOBALS['radio_station_freemius'] ) ) {
        $freemius = $GLOBALS['radio_station_freemius'];
        
        if ( is_object( $freemius ) ) {
            $basename = $freemius->get_plugin_basename();
            $updater = FS_Plugin_Updater::instance( $freemius );
            remove_action( 'after_plugin_row_' . $basename, array( $updater, 'catch_plugin_update_row' ), 9 );
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
        add_action(
            'after_plugin_row_' . $pro_basename,
            'radio_station_pro_edit_and_echo_plugin_update_row',
            11,
            2
        );
        add_filter( 'pre_set_site_transient_update_plugins', 'radio_station_pro_capture_transient_update_plugins', 9 );
        add_filter( 'pre_set_site_transient_update_plugins', 'radio_station_pro_edit_transient_update_plugins', 11 );
        add_filter(
            'plugins_api',
            'radio_station_pro_plugins_api_filter',
            11,
            3
        );
    }

}

// -------------------------------------
// Capture Update Plugins Transient Data
// -------------------------------------
function radio_station_pro_capture_transient_update_plugins( $transient_data )
{
    global  $radio_station_free_updates ;
    
    if ( property_exists( $transient_data, 'response' ) ) {
        if ( !defined( 'RADIO_STATION_BASENAME' ) ) {
            define( 'RADIO_STATION_BASENAME', 'radio-station/radio-station.php' );
        }
        $response = $transient_data->response;
        $radio_station_free_updates = $response[RADIO_STATION_BASENAME];
        // if ( RADIO_STATION_DEBUG ) {
        //	echo 'Radio Station Update Data: ' . print_r( $radio_station_free_updates, true ) . PHP_EOL;
        // }
    }
    
    return $transient_data;
}

// ----------------------------------
// Edit Update Plugins Transient Data
// ----------------------------------
function radio_station_pro_edit_transient_update_plugins( $transient_data )
{
    global  $radio_station_free_updates ;
    
    if ( property_exists( $transient_data, 'response' ) ) {
        $response = $transient_data->response;
        $response[RADIO_STATION_PRO_BASENAME] = $response[RADIO_STATION_BASENAME];
        $response[RADIO_STATION_BASENAME] = $radio_station_free_updates;
        $transient_data->response = $response;
        // if ( RADIO_STATION_DEBUG ) {
        // 	echo 'Plugins Update Transient Modified: ' . print_r( $transient_data, true ) . PHP_EOL;
        // }
    }
    
    return $transient_data;
}

// -----------------------------
// Capture Pro Plugin Update Row
// -----------------------------
function radio_station_pro_catch_plugin_update_row( $file )
{
    if ( $file != RADIO_STATION_PRO_BASENAME ) {
        return;
    }
    ob_start();
}

// --------------------------
// Edit Pro Plugin Update Row
// --------------------------
function radio_station_pro_edit_and_echo_plugin_update_row( $file, $plugin_data )
{
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
        echo  "FIND: " . $find . PHP_EOL ;
        echo  "REPLACE: " . $replace . PHP_EOL ;
        echo  "<textarea>" . $plugin_row . "</textarea>" . PHP_EOL ;
    }
    
    $plugin_row = str_replace( $find, $replace, $plugin_row );
    echo  $plugin_row ;
}

// ------------------
// Plugins API Filter
// ------------------
function radio_station_pro_plugins_api_filter( $data, $action = '', $args = null )
{
    if ( 'plugin_information' !== $action || !isset( $args->slug ) || 'radio-station-pro' != $args->slug ) {
        return $data;
    }
    // --- override with premium plugin information ---
    if ( !isset( $GLOBALS['radio_station_freemius'] ) ) {
        return $data;
    }
    $freemius = $GLOBALS['radio_station_freemius'];
    
    if ( is_object( $freemius ) ) {
        $basename = $freemius->get_plugin_basename();
        $updater = FS_Plugin_Updater::instance( $freemius );
        $args->slug = 'radio-station';
        $new_data = $updater->plugins_api_filter( $data, $action, $args );
        
        if ( RADIO_STATION_DEBUG ) {
            echo  "ARGS: " . print_r( $args, true ) . '<br>' . PHP_EOL ;
            echo  "OLD DATA: " . print_r( $data, true ) . '<br>' . PHP_EOL ;
            echo  "NEW DATA: " . print_r( $new_data, true ) . '<br>' . PHP_EOL ;
        }
        
        return $new_data;
    }
    
    return $data;
}

// ========================
// Start Pro Loader Wrapper
// ========================
function radio_station_pro_load_plugin()
{
    // ========================
    // -------------
    // === Setup ===
    // -------------
    // -----------------
    // Include Pro Files
    // -----------------
    // 2.4.1.6: move taxonomy declarations to individual files
    require RADIO_STATION_PRO_DIR . '/includes/rsp-post-types.php';
    require RADIO_STATION_PRO_DIR . '/includes/rsp-shortcodes.php';
    require RADIO_STATION_PRO_DIR . '/includes/rsp-data-feeds.php';
    if ( is_admin() ) {
        // 2.4.1.6: separated out feature admin to separate files
        require RADIO_STATION_PRO_DIR . '/radio-station-pro-admin.php';
    }
    // --------------------
    // Include Pro Features
    // --------------------
    // --- Completed Pro Features ---
    // 2.3.1: added schedule view switcher feature
    // 2.3.3: added schedule editor feature
    // 2.3.3: added finished widget reloader feature
    // 2.4.1.3: streamline feature includes
    // 2.4.1.4: move role interface to admin only load
    $pro_features = array(
        // --- core features ---
        'player',
        'metadata',
        'schedule-editor',
        'episodes',
        // --- enhancements ---
        'profiles',
        'schedule-views',
        'import-export',
        'social',
        'timezones',
        'widget-reloader',
        'genre-images',
    );
    // --- Admin Interfaces ---
    // 2.4.1.4: only load role interface in admin
    
    if ( is_admin() ) {
        // 2.4.1.6: use individual feature admin files
        $pro_features[] = 'role-interface';
        $pro_features[] = 'profiles-admin';
        $pro_features[] = 'metadata-admin';
        $pro_features[] = 'episodes-admin';
        $pro_features[] = 'social-admin';
    }
    
    // --- Upcoming Development Features ---
    $pro_features[] = 'segments';
    $pro_features[] = 'subscribers';
    $pro_features[] = 'show-feeds';
    $pro_features[] = 'ical-feeds';
    // --- include all Pro features ---
    foreach ( $pro_features as $pro_feature ) {
        $pro_filepath = RADIO_STATION_PRO_DIR . '/includes/rsp-' . $pro_feature . '.php';
        if ( file_exists( $pro_filepath ) ) {
            include $pro_filepath;
        }
    }
    // ------------------
    // Enqueue Pro Script
    // ------------------
    function radio_station_pro_enqueue_script(
        $scriptkey,
        $filename = false,
        $deps = array(),
        $version = false
    )
    {
        // --- set stylesheet filename and child theme path ---
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        if ( !$filename ) {
            $filename = $scriptkey . $suffix . '.js';
        }
        // --- check for script using template hierarchy ---
        $template = radio_station_get_template( 'both', $filename, 'js' );
        
        if ( $template ) {
            $url = $template['url'];
            if ( !$version ) {
                $version = filemtime( $template['file'] );
            }
        }
        
        // --- enqueue script ---
        wp_enqueue_script(
            $scriptkey,
            $url,
            $deps,
            $version
        );
    }
    
    // -------------------
    // Enqueue Pro Scripts
    // -------------------
    add_action( 'wp_enqueue_scripts', 'radio_station_pro_enqueue_scripts' );
    function radio_station_pro_enqueue_scripts()
    {
        // -- JS JODA --
        // ref: https://js-joda.github.io/js-joda/manual/ZonedDateTime.html
        // --- Moment.js ---
        // ref: https://momentjs.com
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        $suffix = ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ? '' : $suffix );
        radio_station_pro_enqueue_script( 'momentjs', 'moment' . $suffix . '.js' );
        radio_station_pro_enqueue_script( 'moment-timezone', 'moment-timezone-with-data-10-year-range' . $suffix . '.js', array( 'momentjs' ) );
    }
    
    // -----------------------
    // Interim Relogin Message
    // -----------------------
    function radio_station_pro_relogin_message()
    {
        // --- get the requested action ---
        $action = $_REQUEST['action'];
        $edit_shift_actions = array( 'radio_station_edit_show', 'radio_station_edit_shift' );
        
        if ( in_array( $action, $edit_shift_actions ) ) {
            $type = 'edit';
        } elseif ( 'radio_station_add_shift' == $action ) {
            $type = 'add';
        } elseif ( 'radio_station_add_show_shift' == $action ) {
            $type = 'shift';
        } elseif ( 'radio_station_add_override_time' == $action ) {
            $type = 'override';
        } elseif ( 'radio_station_episode_save_segments' == $action ) {
            $type = 'segment';
        } elseif ( 'radio_station_episode_audio_save' == $action ) {
            $type == 'audio';
        }
        
        // TODO: maybe cache the posted data for restore ?
        // --- trigger interim login thickbox ---
        // 2.3.3.9: maybe close existing thickbox
        $js = "if (parent && parent.tb_remove) {try {parent.tb_remove();} catch(e) {} }" . PHP_EOL;
        // 2.3.3.9: trigger WP interim login screen thickbox
        $js .= "if (parent) {parent.jQuery(document).trigger('heartbeat-tick.wp-auth-check', [{'wp-auth-check': false}]);}" . PHP_EOL;
        // --- output script ---
        $js = apply_filters( 'radio_station_editor_relogin_script', $js, $type );
        echo  '<script>' . $js . '</script>' ;
        exit;
    }
    
    // ------------------------
    // === Helper Functions ===
    // ------------------------
    // ----------------------
    // Get Host Profile Pages
    // ----------------------
    function radio_station_pro_get_host_pages()
    {
        global  $wpdb ;
        $query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'host_user_id'";
        $post_ids = $wpdb->get_results( $query, ARRAY_A );
        $host_pages = array();
        if ( $post_ids && is_array( $post_ids ) && count( $post_ids ) > 0 ) {
            foreach ( $post_ids as $post_id ) {
                $host_pages[] = $post_id;
            }
        }
        return $host_pages;
    }
    
    // --------------------------
    // Get Producer Profile Pages
    // --------------------------
    function radio_station_pro_get_producer_pages()
    {
        global  $wpdb ;
        $query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = 'producer_user_id'";
        $post_ids = $wpdb->get_results( $query, ARRAY_A );
        $producer_pages = array();
        if ( $post_ids && is_array( $post_ids ) && count( $post_ids ) > 0 ) {
            foreach ( $post_ids as $post_id ) {
                $producer_pages[] = $post_id;
            }
        }
        return $producer_pages;
    }
    
    // --------------------------
    // Set Thickbox Loading Image
    // --------------------------
    function radio_station_pro_thickbox_loading_image()
    {
        $thickbox_loading_url = plugins_url( 'images/antenna-loading.gif', RADIO_STATION_PRO_FILE );
        $thickbox_loading_url = apply_filters( 'radio_station_thickbox_loading_icon_url', $thickbox_loading_url );
        $js = "var tb_pathToImage = '" . esc_url( $thickbox_loading_url ) . "';";
        wp_add_inline_script( 'thickbox', $js, 'before' );
    }
    
    // ---------------
    // Thickbox Styles
    // ---------------
    function radio_station_pro_thickbox_styles()
    {
        $css = "body #TB_title {font-size: 15px;}\r\n\tbody #TB_load {padding: 10px; margin: -48px 0 0 -48px; background-color: #FAFAFA;}\r\n\tbody #TB_load img {width: 128px; height: auto;}\r\n\tbody #TB_ajaxContent {font-size: 14px; margin: 0 auto; max-width: 1000px; text-align: center;}" . PHP_EOL;
        $css = apply_filters( 'radio_station_thickbox_styles', $css );
        return $css;
    }
    
    // ------------------------
    // === Settings Filters ===
    // ------------------------
    // --------------------------
    // Filter Settings Menu Title
    // --------------------------
    // 2.4.1.3: add filter for changing settings page title for Pro
    add_filter( 'radio_station_settings_menu_title', 'radio_station_pro_settings_menu_title' );
    function radio_station_pro_settings_menu_title( $title )
    {
        $title = __( 'Radio Station Pro', 'radio-station' );
        return $title;
    }
    
    // --------------------------
    // Filter Settings Page Title
    // --------------------------
    // 2.4.1.3: add filter for changing settings page title for Pro
    add_filter( 'radio_station_settings_page_title', 'radio_station_pro_settings_page_title' );
    function radio_station_pro_settings_page_title( $title )
    {
        $title = __( 'Radio Station PRO', 'radio-station' );
        return $title;
    }
    
    // -----------------------------
    // Filter Settings Page Icon URL
    // -----------------------------
    // 2.4.1.3: add filter for changing settings page icon for Pro
    add_filter( 'radio_station_settings_page_icon_url', 'radio_station_settings_page_icon_url' );
    function radio_station_settings_page_icon_url( $icon_url )
    {
        $icon_url = plugins_url( 'images/radio-station-pro.png', RADIO_STATION_PRO_FILE );
        return $icon_url;
    }
    
    // -----------------------------
    // Filter Settings Page Icon URL
    // -----------------------------
    // 2.4.1.3: add filter for changing settings page version
    add_filter( 'radio_station_settings_page_version', 'radio_station_settings_page_version' );
    function radio_station_settings_page_version( $version )
    {
        $version = 'v' . RADIO_STATION_PRO_VERSION;
        return $version;
    }
    
    // -----------------------------
    // Filter Settings Page Subtitle
    // -----------------------------
    // 2.4.1.3: add filter to output free version as subtitle
    add_filter( 'radio_station_settings_page_subtitle', 'radio_station_settings_page_subtitle' );
    function radio_station_settings_page_subtitle( $subtitle )
    {
        $version = radio_station_plugin_version();
        // 2.4.1.4: add missing partial translation wrapper
        $subtitle = '(' . __( 'Extending', 'radio-station' ) . ' Radio Station v' . $version . ')';
        return $subtitle;
    }
    
    // -----------------------------
    // Teleporter Settings Page Note
    // -----------------------------
    // 2.4.1.4: added in case Teleporter plugin activated separately
    add_action( 'teleporter_admin_page_tab_general_top', 'radio_station_pro_teleporter_settings_note' );
    function radio_station_pro_teleporter_settings_note()
    {
        $continuous = radio_station_get_setting( 'player_bar_continuous' );
        
        if ( '1' == $continuous || 'yes' == $continuous ) {
            echo  __( 'Important: Radio Station Pro continuous player bar is active.', 'radio-station' ) . '<br>' ;
            echo  __( 'This overrides the page fade in, load timeout and loader bar settings here.' ) . '<br>' ;
            echo  __( 'Adjust those settings via your Radio Station settings page instead.', 'radio-station' ) . '<br><br>' ;
        }
    
    }
    
    // ------------------------
    // === Template Filters ===
    // ------------------------
    // -------------------------------
    // Episode Content Template Filter
    // -------------------------------
    add_filter( 'the_content', 'radio_station_pro_episode_content_template', 11 );
    function radio_station_pro_episode_content_template( $content )
    {
        remove_filter( 'the_content', 'radio_station_pro_episode_content_template', 11 );
        $output = radio_station_single_content_template( $content, RADIO_STATION_EPISODE_SLUG );
        add_filter( 'the_content', 'radio_station_pro_episode_content_template', 11 );
        return $output;
    }
    
    // ---------------------------------
    // DJ / Host Content Template Filter
    // ---------------------------------
    add_filter( 'the_content', 'radio_station_host_content_template', 11 );
    function radio_station_host_content_template( $content )
    {
        remove_filter( 'the_content', 'radio_station_host_content_template', 11 );
        $output = radio_station_single_content_template( $content, RADIO_STATION_HOST_SLUG );
        add_filter( 'the_content', 'radio_station_host_content_template', 11 );
        return $output;
    }
    
    // --------------------------------
    // Producer Content Template Filter
    // --------------------------------
    add_filter( 'the_content', 'radio_station_producer_content_template', 11 );
    function radio_station_producer_content_template( $content )
    {
        remove_filter( 'the_content', 'radio_station_producer_content_template', 11 );
        $output = radio_station_single_content_template( $content, RADIO_STATION_PRODUCER_SLUG );
        add_filter( 'the_content', 'radio_station_producer_content_template', 11 );
        return $output;
    }
    
    // -----------------------------
    // Content Template Paths Filter
    // -----------------------------
    // (changes the default plugin template path to Pro plugin path)
    add_filter( 'radio_station_' . RADIO_STATION_HOST_SLUG . '_content_templates', 'radio_station_pro_template_paths' );
    add_filter( 'radio_station_' . RADIO_STATION_PRODUCER_SLUG . '_content_templates', 'radio_station_pro_template_paths' );
    add_filter( 'radio_station_' . RADIO_STATION_EPISODE_SLUG . '_content_templates', 'radio_station_pro_template_paths' );
    function radio_station_pro_template_paths( $templates )
    {
        $host = $producer = false;
        foreach ( $templates as $i => $template ) {
            $templates[$i] = str_replace( RADIO_STATION_DIR, RADIO_STATION_PRO_DIR, $template );
            if ( strstr( $template, 'single-' . RADIO_STATION_HOST_SLUG . '-content' ) ) {
                $host = true;
            }
            if ( strstr( $template, 'single-' . RADIO_STATION_PRODUCER_SLUG . '-content' ) ) {
                $producer = true;
            }
        }
        // --- use profile template for hosts and producers ---
        
        if ( $host || $producer ) {
            $theme_dir = get_stylesheet_directory();
            $templates[] = $theme_dir . '/templates/single-profile-content.php';
            $templates[] = $theme_dir . '/single-profile-content.php';
            $templates[] = RADIO_STATION_PRO_DIR . '/templates/single-profile-content.php';
        }
        
        return $templates;
    }
    
    // -----------------------
    // Enqueue Template Styles
    // -----------------------
    add_action(
        'radio_station_enqueue_template_styles',
        'radio_station_pro_enqueue_template_styles',
        10,
        1
    );
    function radio_station_pro_enqueue_template_styles( $post_type )
    {
        $profile_templates = array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG, RADIO_STATION_EPISODE_SLUG );
        if ( in_array( $post_type, $profile_templates ) ) {
            radio_station_enqueue_style( 'profiles' );
        }
    }
    
    // -------------------------
    // Host Archive Page Content
    // -------------------------
    function radio_station_pro_host_archive( $content )
    {
        $auto = radio_station_get_setting( 'host_archive_auto' );
        if ( $auto != 'yes' ) {
            return $content;
        }
        $shortcode = '[host-archive';
        // TODO: add archive grid view support ?
        // $view = radio_station_get_setting( 'host_archive_view' );
        // if ( $view == 'grid' ) {$shortcode .= ' view="grid"';}
        $shortcode .= ']';
        $content = do_shortcode( $shortcode );
        return $content;
    }
    
    // -----------------------------
    // Producer Archive Page Content
    // -----------------------------
    function radio_station_pro_producer_archive( $content )
    {
        $auto = radio_station_get_setting( 'producer_archive_auto' );
        if ( $auto != 'yes' ) {
            return $content;
        }
        $shortcode = '[producer-archive';
        // TODO: add archive grid view support ?
        // $view = radio_station_get_setting( 'producer_archive_view' );
        // if ( $view == 'grid' ) {$shortcode .= ' view="grid"';}
        $shortcode .= ']';
        $content = do_shortcode( $shortcode );
        return $content;
    }
    
    // --------------------------
    // Archive Template Hierarchy
    // --------------------------
    add_filter( 'archive_template_hierarchy', 'radio_station_pro_archive_template_hierarchy' );
    function radio_station_pro_archive_template_hierarchy( $templates )
    {
        // --- add extra template search path of /templates/ ---
        $post_types = array_filter( (array) get_query_var( 'post_type' ) );
        
        if ( count( $post_types ) == 1 ) {
            $post_type = reset( $post_types );
            $post_types = array( RADIO_STATION_EPISODE_SLUG );
            
            if ( in_array( $post_type, $post_types ) ) {
                $template = array( 'templates/archive-' . $post_type . '.php' );
                $templates = array_merge( $template, $templates );
            }
        
        }
        
        return $templates;
    }
    
    // -----------------------
    // Get User Shows for Type
    // -----------------------
    function radio_station_pro_get_user_shows( $user_id, $type )
    {
        // --- set meta key for type ---
        
        if ( 'host' == $type ) {
            $meta_key = 'show_user_list';
        } elseif ( 'producer' == $type ) {
            $meta_key = 'show_producer_list';
        }
        
        // --- query shows for user ---
        global  $wpdb ;
        $query = "SELECT post_id,meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key = %s AND meta_value LIKE '%" . (string) $user_id . "%'";
        $query = $wpdb->prepare( $query, $meta_key );
        $results = $wpdb->get_results( $query, ARRAY_A );
        if ( !$results || !is_array( $results ) ) {
            return false;
        }
        foreach ( $results as $result ) {
            $meta_value = maybe_unserialize( $result['meta_value'] );
            if ( $user_id == $result['meta_value'] || in_array( $user_id, $meta_value ) ) {
                $shows[] = $result['post_id'];
            }
        }
        // --- filter and return ---
        $shows = apply_filters(
            'radio_station_user_shows',
            $shows,
            $type,
            $user_id
        );
        return $shows;
    }
    
    // ------------------------------
    // === Roles and Capabilities ===
    // ------------------------------
    // --------------------------
    // Set Roles and Capabilities
    // --------------------------
    
    if ( is_multisite() ) {
        add_action(
            'init',
            'radio_station_pro_set_roles',
            10,
            0
        );
    } else {
        add_action(
            'admin_init',
            'radio_station_pro_set_roles',
            10,
            0
        );
    }
    
    function radio_station_pro_set_roles()
    {
        global  $wp_roles ;
        /// --- add episode editing capabilities to all plugin roles ---
        $roles = array(
            'dj',
            'producer',
            'show-editor',
            'author',
            'editor',
            'administrator'
        );
        $episode_caps = array(
            'edit_episodes',
            'edit_published_episodes',
            'delete_episodes',
            'read_episodes',
            'publish_episodes'
        );
        foreach ( $roles as $role ) {
            $role_caps = $wp_roles->roles[$role]['capabilities'];
            if ( !is_array( $role_caps ) ) {
                $role_caps = array();
            }
            foreach ( $episode_caps as $cap ) {
                if ( !array_key_exists( $cap, $role_caps ) || !$role_caps[$cap] ) {
                    $wp_roles->add_cap( $role, $cap, true );
                }
            }
        }
    }
    
    // --------------------------------
    // Filter Edit Episode Capabilities
    // --------------------------------
    // 2.4.1.7: deprecated in favour of new capability filtering approach
    // add_filter( 'user_has_cap', 'radio_station_pro_episode_capabilities', 10, 4 );
    function radio_station_pro_episode_capabilities(
        $allcaps,
        $caps,
        $args,
        $user
    )
    {
        global  $post, $wp_roles ;
        // --- check for editor
        $editor_roles = array( 'administrator', 'editor', 'show-editor' );
        foreach ( $editor_roles as $role ) {
            if ( in_array( $role, $user->roles ) ) {
                return $allcaps;
            }
        }
        // --- get roles with edit episodes capability ---
        $edit_show_roles = $edit_others_shows_roles = array();
        if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {
            foreach ( $wp_roles->roles as $name => $role ) {
                if ( isset( $role['capabilities'] ) ) {
                    foreach ( $role['capabilities'] as $capname => $capstatus ) {
                        if ( 'edit_episodes' === $capname && (bool) $capstatus ) {
                            if ( !in_array( $name, $edit_show_roles ) ) {
                                $edit_show_roles[] = $name;
                            }
                        }
                        if ( 'edit_others_episodes' === $capname && (bool) $capstatus ) {
                            if ( !in_array( $name, $edit_others_shows_roles ) ) {
                                $edit_others_shows_roles[] = $name;
                            }
                        }
                    }
                }
            }
        }
        foreach ( $edit_others_shows_roles as $role ) {
            if ( in_array( $role, $user->roles ) ) {
                return $allcaps;
            }
        }
        $found = false;
        foreach ( $edit_show_roles as $role ) {
            if ( in_array( $role, $user->roles ) ) {
                $found = true;
            }
        }
        // --- maybe revoke edit episode capability ---
        if ( $found ) {
            // --- limit this to published shows ---
            if ( isset( $post ) && is_object( $post ) && property_exists( $post, 'post_type' ) && isset( $post->post_type ) ) {
                
                if ( RADIO_STATION_EPISODE_SLUG == $post->post_type ) {
                    // --- get show hosts and producers ---
                    $related_show = get_post_meta( $post->ID, 'episode_show_id', true );
                    if ( !$related_show || empty($related_show) ) {
                        return $allcaps;
                    }
                    $hosts = get_post_meta( $related_show, 'show_user_list', true );
                    $producers = get_post_meta( $related_show, 'show_producer_list', true );
                    if ( !$hosts || empty($hosts) ) {
                        $hosts = array();
                    }
                    if ( !$producers || empty($producers) ) {
                        $producers = array();
                    }
                    // ---- revoke editing capability if not assigned to this show ---
                    
                    if ( !in_array( $user->ID, $hosts ) && !in_array( $user->ID, $producers ) ) {
                        // --- remove the edit_shows capability ---
                        $allcaps['edit_episodes'] = false;
                        if ( 'publish' == $post->post_status ) {
                            $allcaps['edit_published_episodes'] = false;
                        }
                    }
                
                }
            
            }
        }
        return $allcaps;
    }
    
    // ------------------------
    // Filter User Capabllities
    // ------------------------
    // 2.4.1.7: new edit permissions filter
    add_filter(
        'user_has_cap',
        'radio_station_pro_user_caps',
        10,
        4
    );
    function radio_station_pro_user_caps(
        $allcaps,
        $caps,
        $args,
        $user
    )
    {
        if ( !isset( $args[2] ) ) {
            return $allcaps;
        }
        
        if ( RADIO_STATION_DEBUG ) {
            echo  "Needed Caps: " . print_r( $caps, true ) . PHP_EOL ;
            echo  "User Caps: " . print_r( $allcaps, true ) . PHP_EOL ;
        }
        
        $post_id = $args[2];
        $post = get_post( $post_id );
        if ( !is_object( $post ) ) {
            return $allcaps;
        }
        $post_type = $post->post_type;
        $post_types = array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG, RADIO_STATION_EPISODE_SLUG );
        if ( !in_array( $post_type, $post_types ) ) {
            return $allcaps;
        }
        $post_type_object = get_post_type_object( $post_type );
        $user_id = $user->ID;
        $add_permissions = false;
        // --- check for editor role ---
        $editor_roles = array( 'administrator', 'show-editor' );
        $editor_role_caps = radio_station_get_setting( 'add_editor_capabilities' );
        if ( 'yes' == $editor_role_caps ) {
            $editor_roles[] = 'editor';
        }
        foreach ( $editor_roles as $role ) {
            if ( in_array( $role, $user->roles ) ) {
                $addpermissions = true;
            }
        }
        // --- check if assigned to profile / episode's show ---
        if ( !$addpermissions ) {
            
            if ( RADIO_STATION_HOST_SLUG == $post_type ) {
                // 2.4.1.7: check for (old) possible non-array value
                $hosts = get_post_meta( $post_id, 'host_user_id', true );
                if ( $hosts ) {
                    
                    if ( is_array( $hosts ) && in_array( $user_id, $hosts ) ) {
                        $add_permissions = true;
                    } elseif ( !is_array( $hosts ) && $user_id == (int) $hosts ) {
                        $add_permissions = true;
                    }
                
                }
            } elseif ( RADIO_STATION_PRODUCER_SLUG == $post_type ) {
                // 2.4.1.7: check for (old) possible non-array value
                $producers = get_post_meta( $post_id, 'producer_user_id', true );
                if ( $producers ) {
                    
                    if ( is_array( $producers ) && in_array( $user_id, $producers ) ) {
                        $add_permissions = true;
                    } elseif ( !is_array( $producers ) && $user_id == (int) $producers ) {
                        $add_permissions = true;
                    }
                
                }
            } elseif ( RADIO_STATION_EPISODE_SLUG == $post_type ) {
                $episode_show = get_post_meta( $post_id, 'episode_show_id', true );
                
                if ( $episode_show ) {
                    $show_post = get_post( $episode_show );
                    
                    if ( $show_post->post_author ) {
                        $add_permissions = true;
                    } else {
                        // 2.4.1.7: check for (old) possible non-array value
                        $hosts = get_post_meta( $show_post->ID, 'show_user_list', true );
                        if ( $hosts ) {
                            
                            if ( is_array( $hosts ) && in_array( $user_id, $hosts ) ) {
                                $add_permissions = true;
                            } elseif ( !is_array( $hosts ) && $user_id == (int) $hosts ) {
                                $add_permissions = true;
                            }
                        
                        }
                        $producers = get_post_meta( $show_post->ID, 'show_producer_list', true );
                        if ( $producers ) {
                            
                            if ( is_array( $producers ) && in_array( $user_id, $producers ) ) {
                                $add_permissions = true;
                            } elseif ( !is_array( $producers ) && $user_id == (int) $producers ) {
                                $add_permissions = true;
                            }
                        
                        }
                    }
                
                }
            
            }
        
        }
        if ( !$add_permissions ) {
            return $allcaps;
        }
        // --- give the extra post type capabilities ---
        
        if ( $user_id != $post->post_author ) {
            $addcaps = array();
            $addcaps[] = $post_type_object->cap->edit_posts;
            $addcaps[] = $post_type_object->cap->edit_others_posts;
            $addcaps[] = $post_type_object->cap->edit_published_posts;
            $addcaps[] = $post_type_object->cap->delete_others_posts;
            $addscap[] = $post_type_object->cap->read_private_posts;
            foreach ( $addcaps as $addcap ) {
                $allcaps[$addcap] = true;
            }
        }
        
        if ( RADIO_STATION_DEBUG ) {
            echo  "Filtered User Caps: " . print_r( $allcaps, true ) . PHP_EOL ;
        }
        return $allcaps;
    }
    
    // --------------------------------
    // Filter Edit Profile Capabilities
    // --------------------------------
    add_action( 'load-post-new.php', 'radio_station_pro_edit_profiles_check' );
    add_action( 'load-post.php', 'radio_station_pro_edit_profiles_check' );
    add_action( 'load-edit.php', 'radio_station_pro_edit_profiles_check' );
    function radio_station_pro_edit_profiles_check()
    {
        global  $post, $pagenow ;
        // --- get the current user ---
        $current_user = wp_get_current_user();
        // (dev debugging)
        // print_r($current_user);
        // print_r(get_option('wp_user_roles'));
        // $post_type_object = get_post_type_object('host');
        // print_r($post_type_object->cap);
        // --- new host and producer profiles ---
        $post_types = array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
        foreach ( $post_types as $post_type ) {
            $profile = str_replace( 'rs-', '', $post_type );
            
            if ( $pagenow == 'post-new.php' && $_GET['post_type'] == $post_type ) {
                if ( current_user_can( 'edit_others_' . $profile . 's' ) ) {
                    return;
                }
                
                if ( current_user_can( 'publish_' . $profile . 's' ) ) {
                    // 2.4.1.6: fix to old unprefixed function name
                    $profile_id = radio_station_pro_get_profile_id( $profile, $current_user->ID );
                    
                    if ( $profile_id ) {
                        // --- redirect to existing profile ---
                        $location = add_query_arg( 'post', $profile_id, admin_url( 'post.php' ) );
                        $location = add_query_arg( 'action', 'edit', $location );
                        wp_redirect( $location );
                        exit;
                    } else {
                        // --- return to allow profile creation ---
                        return;
                    }
                
                } else {
                    wp_die( __( 'You do not have permission to do that.', 'radio-station' ) );
                }
            
            }
            
            // 2.4.1.6: remove edit list redirect as handled by capabilities
            /* if ( ( $pagenow == 'edit.php' ) && isset( $_GET['post_type'] ) && ( $_GET['post_type'] == $profile ) ) {
              			if ( current_user_can( 'edit_others_' . $profile . 's' ) ) {
              				return;
              			}
              			if ( !current_user_can( 'publish_' . $profile . 's' ) ) {
              				wp_die( __( 'You do not have permission to do that.', 'radio-station' ) );
              			}
              			// 2.4.1.6: fix to old unprefixed function name
              			$profile_id = radio_station_pro_get_profile_id( $profile, $current_user->ID );
              			if ( $profile_id ) {
              				// 2.4.1.6: maybe check for profile draft with this user as author
              				$profile = get_post( $profile_id );
              				$unpublished_statuses = array( 'draft', 'pending', 'future' );
              				if ( in_array( $profile->post_status, $unpublished_statuses ) ) {
              					$location = add_query_arg( 'post', $profile_id, admin_url( 'post.php' ) );
              					$location = add_query_arg( 'action', 'edit', $location );
              				} else {
              					// --- redirect to create new profile ---
              					$location = add_query_arg( 'post_type', $post_type, admin_url( 'post-new.php' ) );
              				}
              			}
              			wp_redirect( $location );
              			exit;
              
              		} */
        }
        // 2.4.1.6: removed redirect as handled by capabilities
        /* foreach ( $post_types as $post_type ) {
          		if ( isset( $post ) && ( $post->post_type == $post_type ) ) {
          
          			$profile = str_replace( 'rs-', '', $post_type );
          
          			// if ( ( $pagenow == 'post.php' ) && ( !current_user_can( 'publish_' . $profile . 's' ) ) ) {
          			//	wp_die( __( 'You do not have permission to do that.', 'radio-station' ) );
          			// }
          
          			if ( ( $pagenow == 'post.php' ) && ( !current_user_can( 'edit_others_' . $profile . 's' ) ) ) {
          				$profile_id = get_post_meta( $post->ID, $profile . '_user_id', true );
          				if ( $profile_id && ( $profile_id != $current_user->ID ) ) {
          					wp_die( __( 'You do not have permission to do that.', 'radio-station' ) );
          				}
          			}
          		}
          	} */
    }
    
    // -------------------------
    // === Transient Caching ===
    // -------------------------
    // -----------------
    // Cache Result Data
    // -----------------
    add_action(
        'radio_station_cache_data',
        'radio_station_pro_cache_data',
        10,
        3
    );
    function radio_station_pro_cache_data( $datatype, $id, $results )
    {
        if ( 'yes' != radio_station_get_setting( 'transient_caching' ) ) {
            return false;
        }
        
        if ( $results && is_array( $results ) && count( $results ) > 0 ) {
            $expiry = 12 * 60 * 60;
            // temp test time
            set_transient( '_radio_show_' . $datatype . '_' . $id, $results, $expiry );
            return true;
        } else {
            radio_station_pro_clear_data( $datatype, $id );
            return false;
        }
    
    }
    
    // -----------------
    // Clear Cached Data
    // -----------------
    add_action(
        'radio_station_clear_data',
        'radio_station_pro_clear_data',
        10,
        2
    );
    function radio_station_pro_clear_data( $datatype, $id )
    {
        delete_transient( '_radio_show_' . $datatype . '_' . $id );
        // --- maybe clear related show transient ---
        // 2.4.1.4: fix to clear related show transient
        
        if ( 'playlist' == $datatype ) {
            $show_ids = get_post_meta( $id, 'playlist_show_id', true );
        } elseif ( 'post' == $datatype ) {
            $show_ids = get_post_meta( $id, 'post_showblog_id', true );
        }
        
        if ( isset( $show_ids ) && $show_ids && is_array( $show_ids ) && count( $show_ids ) > 0 ) {
            foreach ( $show_ids as $show_id ) {
                do_action( 'radio_station_clear_data', RADIO_STATION_SHOW_SLUG, $show_id );
            }
        }
    }
    
    // ---------------
    // Get Cached Data
    // ---------------
    add_filter(
        'radio_station_cached_data',
        'radio_station_pro_get_cached',
        10,
        3
    );
    function radio_station_pro_get_cached( $data, $datatype, $id )
    {
        if ( 'yes' != radio_station_get_setting( 'transient_caching' ) ) {
            return false;
        }
        return get_transient( '_radio_show_' . $datatype . '_' . $id );
    }
    
    // ======================
    // End Pro Loader Wrapper
    // ======================
}

// ======================