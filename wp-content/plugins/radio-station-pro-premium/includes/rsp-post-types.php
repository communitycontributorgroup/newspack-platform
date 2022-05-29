<?php

// =========================
// === Radio Station Pro ===
// =========================
// ------- Post Types ------
// =========================

// === Post Types ===
// - Register Post Types
// - Add Theme Thumbnail Support
// === Shortcode Filters ===
// - Archives Shortcode No Records Message
// - Filter Archive Shortcode Meta
// === Admin Frontend ===
// - Set Post Type Editing to Classic Editor
// - Filter Admin Bar Post Types


// ------------------
// === Post Types ===
// ------------------

// -------------------
// Register Post Types
// -------------------
add_action( 'init', 'radio_station_pro_register_post_types', 11 );
function radio_station_pro_register_post_types() {

	// --- Register Episodes Post Type ---
	$episodes = array(
		'labels'          => array(
			'name'               => __( 'Episodes', 'radio-station' ),
			'singular_name'      => __( 'Episode', 'radio-station' ),
			'add_new'            => __( 'Add Episode', 'radio-station' ),
			'add_new_item'       => __( 'Add Episode', 'radio-station' ),
			'edit_item'          => __( 'Edit Episode', 'radio-station' ),
			'new_item'           => __( 'New Episode', 'radio-station' ),
			'view_item'          => __( 'View Episode', 'radio-station' ),
			'archive_title'	     => __( 'Episodes', 'radio-station' ),
			'search_items'       => __( 'Search Episodes', 'radio-station' ),
			'not_found'          => __( 'No Episodes found', 'radio-station' ),
			'not_found_in_trash' => __( 'No Episodes found in Trash', 'radio-station' ),
			'all_items'          => __( 'All Episodes', 'radio-station' ),
		),
		'show_ui'		=> true,
		'show_in_menu'		=> false,
		'show_in_admin_bar'	=> false,
		'description'		=> __( 'Post Type for Show Episodes', 'radio-station' ),
		'public'		=> true,
		// 'taxonomies'		=> false,
		'hierarchical'		=> false,
		// 2.4.1.4: added revisions and author support
		'supports'		=> array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt', 'custom-fields', 'revisions', 'author' ),
		'can_export'		=> true,
		'has_archive'		=> 'episodes',
		'rewrite'           => array(
			'slug'       => 'episodes',
			'with_front' => false,
			'feeds'      => true,
		),
		'capability_type'	=> 'episode',
		'map_meta_cap'		=> true,
	);
	$episodes = apply_filters( 'radio_station_pro_post_type_episodes', $episodes );
	register_post_type( RADIO_STATION_EPISODE_SLUG, $episodes );

	// --- maybe trigger flush of rewrite rules ---
	if ( get_option( 'radio_station_flush_rewrite_rules' ) ) {
		add_action( 'init', 'flush_rewrite_rules', 20 );
		delete_option( 'radio_station_flush_rewrite_rules' );
	}
}

// ---------------------------
// Add Theme Thumbnail Support
// ---------------------------
// (probably no longer necessary as declared in register_post_type(s))
add_action( 'init', 'radio_station_pro_add_featured_image_support', 11 );
function radio_station_pro_add_featured_image_support() {

	$supported_types = get_theme_support( 'post-thumbnails' );
	if ( false === $supported_types ) {
		$post_types = array( RADIO_STATION_EPISODE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
		add_theme_support( 'post-thumbnails', $post_types );
	} elseif ( is_array( $supported_types ) ) {
		$supported_types[0][] = RADIO_STATION_EPISODE_SLUG;
		$supported_types[0][] = RADIO_STATION_HOST_SLUG;
		$supported_types[0][] = RADIO_STATION_PRODUCER_SLUG;
		add_theme_support( 'post-thumbnails', $supported_types[0] );
	}
}


// ----------------------
// === Admin Frontend ===
// ----------------------
// (admin code for frontend eg. Admin Bar - see radio-station-pro.php for Post Type Admin)

// ---------------------------------------
// Set Post Type Editing to Classic Editor
// ---------------------------------------
add_filter( 'gutenberg_can_edit_post_type', 'radio_station_pro_post_type_editor', 20, 2 );
add_filter( 'use_block_editor_for_post_type', 'radio_station_pro_post_type_editor', 20, 2 );
function radio_station_pro_post_type_editor( $can_edit, $post_type ) {
	$post_types = array( RADIO_STATION_EPISODE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
	if ( in_array( $post_type, $post_types ) ) {
		return false;
	}
	return $can_edit;
}

// ---------------------------
// Filter Admin Bar Post Types
// ---------------------------
add_filter( 'radio_station_admin_bar_post_types', 'radio_station_pro_admin_bar_post_types' );
function radio_station_pro_admin_bar_post_types( $post_types ) {

	$post_types[] = RADIO_STATION_EPISODE_SLUG;
	$post_types[] = RADIO_STATION_HOST_SLUG;
	$post_types[] = RADIO_STATION_PRODUCER_SLUG;
	
	return $post_types;
}

