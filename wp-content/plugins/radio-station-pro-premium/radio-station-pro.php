<?php

/*
Plugin Name: Radio Station Pro
Plugin URI: https://radiostation.pro
Description: Adds all Pro Features to the Radio Station Plugin
Author: Tony Zeoli, Tony Hayes
Version: 2.4.1.10
Requires at least: 4.0
Text Domain: radio-station
Domain Path: /languages
Author URI: https://netmix.com
*/

// === Load Check ===
// - Define Pro Constants
// - Filter Plugin Options
// - Update Pro Settings
// - Plugin Activation
// - Plugin Deactivation
// - Filter Activation URL
// - Include Freemius Loader
// - Include Teleporter Loader
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
// - Get User Shows for Type
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

// ----------------------
// Check for Current Load
// ----------------------
if ( defined( 'RADIO_STATION_PRO_SLUG' ) ) {

	// --- add an admin notice ---
	// 2.4.1.8: fix to action hook admin_notice
	add_action( 'admin_notices', 'radio_station_pro_conflict_notice' );
	
	// --- bug out as either Plus or Pro is already loaded ---
	return;
}

// ------------------------
// Pro/Plus Conflict Notice
// ------------------------
function radio_station_pro_conflict_notice() {

	// --- open notice wrapper ---
	echo '<div id="radio-station-pro-conflict-notice" class="notice notice-warning" style="position:relative; text-align:center;">';

		// --- output conflict notice ---
		echo __( 'You have multiple conflicting versions of Radio Station Pro activated.', 'radio-station' ) . '<br>';
		echo '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">';
		echo __( 'Please visit your Plugins page to deactivate the incorrect one.', 'radio-station' );
		echo '</a>';

	echo '</div>';
}

// --------------------
// Define Pro Constants
// --------------------
define( 'RADIO_STATION_PRO_SLUG', 'radio-station-pro' );
define( 'RADIO_STATION_PRO_NAME', __( 'Radio Station Pro', 'radio-station' ) );
define( 'RADIO_STATION_PRO_FILE', __FILE__ );
define( 'RADIO_STATION_PRO_DIR', dirname( __FILE__ ) );
define( 'RADIO_STATION_PRO_BASENAME', plugin_basename( __FILE__ ) );
define( 'RADIO_STATION_PRO_VERSION', '2.4.1.10' );
define( 'RADIO_STATION_PRO_FREE_MINIMUM', '2.4.0.7' );
define( 'RADIO_STATION_PRO_HOME_URL', 'https://radiostation.pro/' );
define( 'RADIO_STATION_PRO_DOCS_URL', 'https://radiostation.pro/docs/pro/' );

// --- define custom post type and taxonomy slugs ---
define( 'RADIO_STATION_EPISODE_SLUG', 'rs-episode' );
define( 'RADIO_STATION_TOPICS_SLUG', 'rs-topics' );
define( 'RADIO_STATION_GUESTS_SLUG', 'rs-guests' );

// -----------------------
// Set Debug Mode Constant
// -----------------------
if ( !defined( 'RADIO_STATION_PRO_DEBUG' ) ) {
	$rsp_debug = false;
	if ( isset( $_REQUEST['rsp-debug'] ) && ( '1' == $_REQUEST['rsp-debug'] ) ) {
		$rsp_debug = true;
	}
	define( 'RADIO_STATION_PRO_DEBUG', $rsp_debug );
}

// -------------------
// Update Pro Settings
// -------------------
// 2.4.1.4: added for Pro specific special settings
function radio_station_pro_update_settings() {

	// 2.4.1.4: trigger role interface updates
	radio_station_pro_update_roles();

}

// -----------------
// Plugin Activation
// -----------------
// (run on plugin activation, and thus also after a plugin update)
register_activation_hook( RADIO_STATION_PRO_FILE, 'radio_station_pro_plugin_activation' );
function radio_station_pro_plugin_activation() {

	// --- flag to flush rewrite rules ---
	add_option( 'radio_station_flush_rewrite_rules', true );

	// --- clear schedule transients ---
	// 2.4.1.2: add function check for pro-only activation edge case
	if ( function_exists( 'radio_station_clear_cached_data') ) {
		radio_station_clear_cached_data( false );
	}
	
	// --- clear plugins transient ---
	// 2.4.1.8: clear plugin updates transient on activation
	delete_site_transient( 'update_plugins' );
}

// -------------------
// Plugin Deactivation
// -------------------
// 2.4.1.8: clear plugin updates transient on deactivation
register_deactivation_hook( RADIO_STATION_PRO_FILE, 'radio_station_pro_plugin_deactivation' );
function radio_station_pro_deactivation() {
	flush_rewrite_rules();
	delete_site_transient( 'update_plugins' );
}


// -----------------------
// Include Freemius Loader
// -----------------------
include RADIO_STATION_PRO_DIR . '/freemius-loader.php';

// -------------------------
// Include Teleporter Loader
// -------------------------
// 2.4.1.8: added conditional load to support Plus version
$teleporter_loader = RADIO_STATION_PRO_DIR . '/teleporter-loader.php';
if ( file_exists( $teleporter_loader ) ) {
	include $teleporter_loader;
}


// ========================
// Start Pro Loader Wrapper
// ========================
function radio_station_pro_load_plugin() {
// ========================

// -------------
// === Setup ===
// -------------

// -----------------
// Include Pro Files
// -----------------
// 2.4.1.6: separated out feature admin to separate files
if ( is_admin() ) {
	require RADIO_STATION_PRO_DIR . '/radio-station-pro-admin.php';
}
// 2.4.1.6: move taxonomy declarations to individual files
require RADIO_STATION_PRO_DIR . '/includes/rsp-post-types.php';
require RADIO_STATION_PRO_DIR . '/includes/rsp-shortcodes.php';
require RADIO_STATION_PRO_DIR . '/includes/rsp-data-feeds.php';

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
	// 'role-interface' (admin)
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
function radio_station_pro_enqueue_script( $scriptkey, $filename = false, $deps = array(), $version = false ) {

	// --- set stylesheet filename and child theme path ---
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
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
	wp_enqueue_script( $scriptkey, $url, $deps, $version );
}

// -------------------
// Enqueue Pro Scripts
// -------------------
add_action( 'wp_enqueue_scripts', 'radio_station_pro_enqueue_scripts' );
function radio_station_pro_enqueue_scripts() {

	// -- JS JODA --
	// ref: https://js-joda.github.io/js-joda/manual/ZonedDateTime.html

	// --- Moment.js ---
	// ref: https://momentjs.com
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix = ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) ? '' : $suffix;
	radio_station_pro_enqueue_script( 'momentjs', 'moment' . $suffix . '.js' );
	radio_station_pro_enqueue_script( 'moment-timezone', 'moment-timezone-with-data-10-year-range' . $suffix . '.js', array( 'momentjs' ) );
}

// -----------------------
// Interim Relogin Message
// -----------------------
function radio_station_pro_relogin_message() {

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
	echo '<script>' . $js . '</script>';
	exit;

}


// ------------------------
// === Helper Functions ===
// ------------------------

// ----------------------
// Get Host Profile Pages
// ----------------------
function radio_station_pro_get_host_pages() {
	global $wpdb;
	$query = "SELECT post_id FROM " . $wpdb->prefix ."postmeta WHERE meta_key = 'host_user_id'";
	$post_ids = $wpdb->get_results( $query, ARRAY_A );
	$host_pages = array();
	if ( $post_ids && is_array( $post_ids ) && ( count( $post_ids ) > 0 ) ) {
		foreach ( $post_ids as $post_id ) {$host_pages[] = $post_id;}
	}
	return $host_pages;
}

// --------------------------
// Get Producer Profile Pages
// --------------------------
function radio_station_pro_get_producer_pages() {
	global $wpdb;
	$query = "SELECT post_id FROM " . $wpdb->prefix ."postmeta WHERE meta_key = 'producer_user_id'";
	$post_ids = $wpdb->get_results( $query, ARRAY_A );
	$producer_pages = array();
	if ( $post_ids && is_array( $post_ids ) && ( count( $post_ids ) > 0 ) ) {
		foreach ( $post_ids as $post_id ) {$producer_pages[] = $post_id;}
	}
	return $producer_pages;
}

// --------------------------
// Set Thickbox Loading Image
// --------------------------
function radio_station_pro_thickbox_loading_image() {
	$thickbox_loading_url = plugins_url( 'images/antenna-loading.gif', RADIO_STATION_PRO_FILE );
	$thickbox_loading_url = apply_filters( 'radio_station_thickbox_loading_icon_url', $thickbox_loading_url );
	$js = "var tb_pathToImage = '" . esc_url( $thickbox_loading_url ) . "';";
	wp_add_inline_script( 'thickbox', $js, 'before' );
}

// ---------------
// Thickbox Styles
// ---------------
function radio_station_pro_thickbox_styles() {

	$css = "body #TB_title {font-size: 15px;}
	body #TB_load {padding: 10px; margin: -48px 0 0 -48px; background-color: #FAFAFA;}
	body #TB_load img {width: 128px; height: auto;}
	body #TB_ajaxContent {font-size: 14px; margin: 0 auto; max-width: 1000px; text-align: center;}" . PHP_EOL;

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
function radio_station_pro_settings_menu_title( $title ) {
	$title = RADIO_STATION_PRO_NAME;
	return $title;
}

// --------------------------
// Filter Settings Page Title
// --------------------------
// 2.4.1.3: add filter for changing settings page title for Pro
add_filter( 'radio_station_settings_page_title', 'radio_station_pro_settings_page_title', 11 );
function radio_station_pro_settings_page_title( $title ) {
	$title = str_replace( 'Pro', 'PRO', RADIO_STATION_PRO_NAME );
	return $title;
}

// -----------------------------
// Filter Settings Page Icon URL
// -----------------------------
// 2.4.1.3: add filter for changing settings page icon for Pro
add_filter( 'radio_station_settings_page_icon_url', 'radio_station_settings_page_icon_url', 11 );
function radio_station_settings_page_icon_url( $icon_url ) {
	$icon_url = plugins_url( 'images/' . RADIO_STATION_PRO_SLUG . '.png', RADIO_STATION_PRO_FILE );
	return $icon_url;
}

// -----------------------------
// Filter Settings Page Icon URL
// -----------------------------
// 2.4.1.3: add filter for changing settings page version
// 2.4.1.8: fix to add missing _pro in function prefix
add_filter( 'radio_station_settings_page_version', 'radio_station_pro_settings_page_version', 11 );
function radio_station_pro_settings_page_version( $version ) {
	$version = 'v' . RADIO_STATION_PRO_VERSION;
	return $version;
}

// -----------------------------
// Filter Settings Page Subtitle
// -----------------------------
// 2.4.1.3: add filter to output free version as subtitle
// 2.4.1.8: fix to add missing _pro in function prefix
add_filter( 'radio_station_settings_page_subtitle', 'radio_station_pro_settings_page_subtitle', 11);
function radio_station_pro_settings_page_subtitle( $subtitle ) {
	$version = radio_station_plugin_version();
	// 2.4.1.4: add missing partial translation wrapper
	$subtitle = '(' . __( 'Extending', 'radio-station' ) . ' Radio Station v' . $version . ')';
	return $subtitle;
}


// ------------------------
// === Template Filters ===
// ------------------------

// -------------------------------
// Episode Content Template Filter
// -------------------------------
add_filter( 'the_content', 'radio_station_pro_episode_content_template', 11 );
function radio_station_pro_episode_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_pro_episode_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_EPISODE_SLUG );
	add_filter( 'the_content', 'radio_station_pro_episode_content_template', 11 );
	return $output;
}

// ---------------------------------
// DJ / Host Content Template Filter
// ---------------------------------
add_filter( 'the_content', 'radio_station_host_content_template', 11 );
function radio_station_host_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_host_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_HOST_SLUG );
	add_filter( 'the_content', 'radio_station_host_content_template', 11 );
	return $output;
}

// --------------------------------
// Producer Content Template Filter
// --------------------------------
add_filter( 'the_content', 'radio_station_producer_content_template', 11 );
function radio_station_producer_content_template( $content ) {
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
function radio_station_pro_template_paths( $templates ) {
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
add_action( 'radio_station_enqueue_template_styles', 'radio_station_pro_enqueue_template_styles', 10, 1 );
function radio_station_pro_enqueue_template_styles( $post_type ) {
	$profile_templates = array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG, RADIO_STATION_EPISODE_SLUG );
	if ( in_array( $post_type, $profile_templates ) ) {
		radio_station_enqueue_style( 'profiles' );
	}
}

// -------------------------
// Host Archive Page Content
// -------------------------
function radio_station_pro_host_archive( $content ) {
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
function radio_station_pro_producer_archive( $content ) {
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
function radio_station_pro_archive_template_hierarchy( $templates ) {

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
function radio_station_pro_get_user_shows( $user_id, $type ) {

	// 2.4.1.8: add check for valid user ID
	if ( !$user_id ) {
		return false;
	}

	// --- set meta key for type ---
	if ( 'host' == $type ) {
		$meta_key = 'show_user_list';
	} elseif ( 'producer' == $type ) {
		$meta_key = 'show_producer_list';
	} else {
		// 2.4.1.8: bug out if not a valid type
		return false;
	}

	// --- query shows for user ---
	global $wpdb;
	$query = "SELECT post_id,meta_value FROM " . $wpdb->prefix . "postmeta WHERE meta_key = %s AND meta_value LIKE '%" . (string) $user_id . "%'";
	$query = $wpdb->prepare( $query, $meta_key );
	$results = $wpdb->get_results( $query, ARRAY_A );
	if ( !$results || !is_array( $results ) ) {
		return false;
	}
	foreach ( $results as $result ) {
		$meta_value = maybe_unserialize( $result['meta_value'] );
		if ( ( $user_id == $result['meta_value'] ) || in_array( $user_id, $meta_value ) ) {
			$shows[] = $result['post_id'];
		}
	}

	// --- filter and return ---
	$shows = apply_filters( 'radio_station_user_shows', $shows, $type, $user_id );
	return $shows;
}


// ------------------------------
// === Roles and Capabilities ===
// ------------------------------

// --------------------------
// Set Roles and Capabilities
// --------------------------
if ( is_multisite() ) {
	add_action( 'init', 'radio_station_pro_set_roles', 10, 0 );
} else {
	add_action( 'admin_init', 'radio_station_pro_set_roles', 10, 0 );
}
function radio_station_pro_set_roles() {

	global $wp_roles;

	/// --- add episode editing capabilities to all plugin roles ---
	$roles = array( 'dj', 'producer', 'show-editor', 'author', 'editor', 'administrator' );
	$episode_caps = array( 'edit_episodes', 'edit_published_episodes', 'delete_episodes', 'read_episodes', 'publish_episodes' );
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
/* function radio_station_pro_episode_capabilities( $allcaps, $caps, $args, $user ) {

	global $post, $wp_roles;

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
					if ( ( 'edit_episodes' === $capname ) && (bool) $capstatus ) {
						if ( !in_array( $name, $edit_show_roles ) ) {
							$edit_show_roles[] = $name;
						}
					}
					if ( ( 'edit_others_episodes' === $capname ) && (bool) $capstatus ) {
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
				if ( !$related_show || empty( $related_show ) ) {
					return $allcaps;
				}

				$hosts = get_post_meta( $related_show, 'show_user_list', true );
				$producers = get_post_meta( $related_show, 'show_producer_list', true );
				if ( !$hosts || empty( $hosts ) ) {
					$hosts = array();
				}
				if ( !$producers || empty( $producers ) ) {
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
} */

// ------------------------
// Filter User Capabllities
// ------------------------
// 2.4.1.7: new edit permissions filter
add_filter( 'user_has_cap', 'radio_station_pro_user_caps', 10, 4 );
function radio_station_pro_user_caps( $allcaps, $caps, $args, $user ) {

	if ( !isset( $args[2] ) ) {
		return $allcaps;
	}
	if ( RADIO_STATION_DEBUG ) {
		echo "Needed Caps: " . print_r( $caps, true ) . PHP_EOL;
		echo "User Caps: " . print_r( $allcaps, true ) . PHP_EOL;
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
				} elseif ( !is_array( $hosts ) && ( $user_id == (int) $hosts ) ) {
					$add_permissions = true;
				}
			}
		} elseif ( RADIO_STATION_PRODUCER_SLUG == $post_type ) {
			// 2.4.1.7: check for (old) possible non-array value
			$producers = get_post_meta( $post_id, 'producer_user_id', true );
			if ( $producers ) {
				if ( is_array( $producers ) && in_array( $user_id, $producers ) ) {
					$add_permissions = true;
				} elseif ( !is_array( $producers ) && ( $user_id == (int) $producers ) ) {
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
						} elseif ( !is_array( $hosts ) && ( $user_id == (int) $hosts ) ) {
							$add_permissions = true;
						}
					}
					$producers = get_post_meta( $show_post->ID, 'show_producer_list', true );
					if ( $producers ) {
						if ( is_array( $producers ) && in_array( $user_id, $producers ) ) {
							$add_permissions = true;
						} elseif ( !is_array( $producers ) && ( $user_id == (int) $producers ) ) {
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
		echo "Filtered User Caps: " . print_r( $allcaps, true ) . PHP_EOL;
	}

	return $allcaps;
}

// --------------------------------
// Filter Edit Profile Capabilities
// --------------------------------
add_action( 'load-post-new.php', 'radio_station_pro_edit_profiles_check' );
add_action( 'load-post.php', 'radio_station_pro_edit_profiles_check' );
add_action( 'load-edit.php', 'radio_station_pro_edit_profiles_check' );
function radio_station_pro_edit_profiles_check() {

	global $post, $pagenow;

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
		// 2.4.1.9: fix to undefined index warning for post type
		if ( ( $pagenow == 'post-new.php' ) && isset( $_GET['post_type'] ) && ( $_GET['post_type'] == $post_type ) ) {
			if ( current_user_can( 'edit_others_' . $profile . 's') ) {
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

	// 2.4.1.6: removed this redirect as handled by capabilities
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
add_action( 'radio_station_cache_data', 'radio_station_pro_cache_data', 10, 3 );
function radio_station_pro_cache_data( $datatype, $id, $results ) {

	if ( 'yes' != radio_station_get_setting( 'transient_caching' ) )  {
		return false;
	}
	if ( $results && is_array( $results ) && ( count( $results ) > 0 ) ) {
		$expiry = 12 * 60 * 60; // temp test time
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
add_action( 'radio_station_clear_data', 'radio_station_pro_clear_data', 10, 2 );
function radio_station_pro_clear_data( $datatype, $id ) {

	delete_transient( '_radio_show_' . $datatype . '_' . $id );

	// --- maybe clear related show transient ---
	// 2.4.1.4: fix to clear related show transient
	if ( 'playlist' == $datatype ) {
		$show_ids = get_post_meta( $id, 'playlist_show_id', true );
	} elseif ( 'post' == $datatype ) {
		$show_ids = get_post_meta( $id, 'post_showblog_id', true );
	}
	if ( isset( $show_ids ) && $show_ids && is_array( $show_ids ) && ( count( $show_ids ) > 0 ) ) {
		foreach ( $show_ids as $show_id ) {
			do_action( 'radio_station_clear_data', RADIO_STATION_SHOW_SLUG, $show_id );
		}
	}
}

// ---------------
// Get Cached Data
// ---------------
add_filter( 'radio_station_cached_data', 'radio_station_pro_get_cached', 10, 3 );
function radio_station_pro_get_cached( $data, $datatype, $id ) {

	if ( 'yes' != radio_station_get_setting( 'transient_caching' ) )  {
		return false;
	}
	return get_transient( '_radio_show_' . $datatype . '_' . $id );
}


// ======================
// End Pro Loader Wrapper
// ======================
}
// ======================

