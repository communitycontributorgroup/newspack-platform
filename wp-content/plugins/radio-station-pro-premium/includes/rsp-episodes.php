<?php

// =========================
// === Radio Station Pro ===
// =========================
// ------- Episodes --------
// =========================
// - Register Episode Taxonomies
// * Show Episode File URL
// - Get Latest Episode
// - Get Show Episode IDs
// - Get Episode URL
// - Get Show Episodes
// - Get Episode Avatar
// - Get Episode Avatar ID
// - Show Episode List Filter
// - Get Show Page Episodes
// - Show Episode Archive Avatar
// - Show Episodes Tab
// * Episode Archive Shortcode
// - Filter Archive Shortcode Meta
// - Show Episodes List Shortcode


// ---------------------------
// Register Episode Taxonomies
// ---------------------------
add_action( 'init', 'radio_station_pro_register_taxonomies' );
function radio_station_pro_register_taxonomies() {

	// --------------
	// Topic Taxonomy
	// --------------

	// --- Topic taxonomy labels ---
	$labels = array(
		'name'              => _x( 'Topics', 'taxonomy general name', 'radio-station' ),
		'singular_name'     => _x( 'Topic', 'taxonomy singular name', 'radio-station' ),
		'search_items'      => __( 'Search Topics', 'radio-station' ),
		'all_items'         => __( 'All Topics', 'radio-station' ),
		'parent_item'       => __( 'Parent Topic', 'radio-station' ),
		'parent_item_colon' => __( 'Parent Topic:', 'radio-station' ),
		'edit_item'         => __( 'Edit Topic', 'radio-station' ),
		'update_item'       => __( 'Update Topic', 'radio-station' ),
		'add_new_item'      => __( 'Add New Topic', 'radio-station' ),
		'new_item_name'     => __( 'New Topic Name', 'radio-station' ),
		'menu_name'         => __( 'Topic', 'radio-station' ),
	);

	// --- register the topic taxonomy ---
	$args = array(
		'hierarchical'       => true,
		'labels'             => $labels,
		'public'             => true,
		'show_tagcloud'      => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'topic' ),
		'show_ui'            => true,
		'show_in_menu'       => false,
		'show_in_rest'       => true,
		'show_admin_column'  => true,
		'show_in_quick_edit' => true,
		'capabilities'       => array(
			'manage_terms' => 'edit_episodes',
			'edit_terms'   => 'edit_episodes',
			'delete_terms' => 'edit_episodes',
			'assign_terms' => 'edit_episodes',
		),
	);
	$post_types = array( RADIO_STATION_EPISODE_SLUG );
	$args = apply_filters( 'radio_station_topic_taxonomy_args', $args );
	register_taxonomy( RADIO_STATION_TOPICS_SLUG, $post_types, $args );

	// --------------
	// Guest Taxonomy
	// --------------

	// --- Guest taxonomy labels ---
	$labels = array(
		'name'              => _x( 'Guests', 'taxonomy general name', 'radio-station' ),
		'singular_name'     => _x( 'Guest', 'taxonomy singular name', 'radio-station' ),
		'search_items'      => __( 'Search Guests', 'radio-station' ),
		'all_items'         => __( 'All Guests', 'radio-station' ),
		'parent_item'       => __( 'Parent Guest', 'radio-station' ),
		'parent_item_colon' => __( 'Parent Guest:', 'radio-station' ),
		'edit_item'         => __( 'Edit Guest', 'radio-station' ),
		'update_item'       => __( 'Update Guest', 'radio-station' ),
		'add_new_item'      => __( 'Add New Guest', 'radio-station' ),
		'new_item_name'     => __( 'New Guest Name', 'radio-station' ),
		'menu_name'         => __( 'Guest', 'radio-station' ),
	);

	// --- register the guest taxonomy ---
	$args = array(
		'hierarchical'       => true,
		'labels'             => $labels,
		'public'             => true,
		'show_tagcloud'      => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'guest' ),
		'show_ui'            => true,
		'show_in_menu'       => false,
		'show_in_rest'       => true,
		'show_admin_column'  => true,
		'show_in_quick_edit' => true,
		'capabilities'       => array(
			'manage_terms' => 'edit_episodes',
			'edit_terms'   => 'edit_episodes',
			'delete_terms' => 'edit_episodes',
			'assign_terms' => 'edit_episodes',
		),
	);
	$post_types = array( RADIO_STATION_EPISODE_SLUG );
	$args = apply_filters( 'radio_station_guest_taxonomy_args', $args );
	register_taxonomy( RADIO_STATION_GUESTS_SLUG, $post_types, $args );

}

// ---------------------
// Set Slug for Data API
// ---------------------
// 2.4.1.6: added to separate out episode data availability
add_filter( 'radio_station_route_slug_episodes', 'radio_station_pro_set_data_slug' );
function radio_station_pro_set_data_slug( $slug ) {
	return 'episodes';
}

// ---------------------
// Show Episode File URL
// ---------------------
// TODO: add use_episodes Pro setting to Radio Station free version
// add_filter( 'radio_station_show_file', 'radio_station_pro_show_episode_file_url', 10, 2 );
function radio_station_pro_show_episode_file_url( $show_file, $post_id ) {

	// --- get latest show episode file URL ---
	$use_episodes = radio_station_get_setting( 'use_episodes' );
	if ( 'yes' == $use_episodes ) {
		$latest = radio_staton_pro_get_latest_show_episode( $post_id );
		if ( $latest ) {
			$show_file = radio_station_pro_get_episode_url( $latest );
		}
	}

	return $show_file;
}

// ------------------
// Get Latest Episode
// ------------------
function radio_staton_pro_get_latest_show_episode( $show_id ) {

	$latest = false;
	$episode_ids = radio_station_pro_get_show_episode_ids( $show_id );
	if ( $episode_ids && is_array( $episode_ids ) && ( count( $episode_ids ) > 0 ) ) {

		// --- order by episode number ---
		$highest = 0;
		foreach ( $episode_ids as $episode_id ) {
			$number = get_post_meta( $episode_id, 'episode_number', true );
			if ( $number && ( $number > $highest ) ) {
				$highest = $number;
				$latest = $episode_id;
			}
		}

		// --- fallback to episode published date ordering ---
		if ( !isset( $latest ) ) {
			$latest_datetime = 0;
			foreach ( $repisode_ids as $episode_id ) {
				$query = "SELECT post_date FROM " . $wpdb->prefix . "posts WHERE ID = %d";
				$query = $wpdb->prepare( $query, $episode_id );
				$post_date = $wpdb->get_var( $query );
				$post_datetime = strtotime( $post_date );
				if ( $post_datetime > $latest_datetime ) {
					$latest_datetime = $post_datetime;
					$latest = $episode_id;
				}
			}
		}
	}

	return $latest;
}

// --------------------
// Get Show Episode IDs
// --------------------
function radio_station_pro_get_show_episode_ids( $show_id )  {

	// --- get episodes for show ---
	global $wpdb;
	$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = %s AND meta_value = %d";
	$query = $wpdb->prepare( $query, array( 'episode_show_id', $show_id ) );
	$results = $wpdb->get_results( $query, ARRAY_A );

	$episode_ids = array();
	if ( $results && is_array( $results ) && ( count( $results ) > 0 ) ) {

		// --- recheck episode is published ---
		foreach ( $results as $i => $result ) {
			$query = "SELECT post_status FROM " . $wpdb->prefix . "posts WHERE ID = %d";
			$query = $wpdb->prepare( $query, $result['post_id'] );
			$status = $wpdb->get_var( $query );
			if ( 'publish' == $status ) {
				$episode_ids[] = $result['post_id'];
			}
		}
	}
	return $episode_ids;
}

// ---------------
// Get Episode URL
// ---------------
function radio_station_pro_get_episode_url( $episode_id ) {

	$episode_type = get_post_meta( $episode_id, 'episode_type', true );
	if ( 'url' == $episode_type ) {
		$episode_url = get_post_meta( $episode_id, 'episode_file_url', true );
	} elseif ( 'media' == $episode_type ) {
		$media_id = get_post_meta( $episode_id, 'episode_media_id', true );
		$episode_url = wp_get_attachment_url( $media_id );
	} // elseif ( 'podcast' == $episode_type ) {
		// TODO: get URL via podcast post ?
	// }

	// --- filter and return ---
	$episode_url = apply_filters( 'radio_station_episode_url', $episode_url, $episode_id );
	return $episode_url;
}

// -----------------
// Get Show Episodes
// -----------------
function radio_station_pro_get_show_episodes( $show_id, $args = array() ) {
	return radio_station_get_show_data( 'episodes', $show_id, $args );
}

// ------------------
// Get Episode Avatar
// ------------------
function radio_station_pro_get_episode_avatar( $episode_id, $size = 'thumbnail', $attr = array() ) {

	$avatar_id = radio_station_pro_get_episode_avatar_id( $episode_id );

	// --- get the attachment image tag ---
	$avatar = false;
	if ( $avatar_id ) {
		$avatar = wp_get_attachment_image( $avatar_id, $size, false, $attr );
	}

	// --- filter and return ---
	$avatar = apply_filters( 'radio_station_episode_avatar_output', $avatar, $episode_id );
	return $avatar;
}

// ---------------------
// Get Episode Avatar ID
// ---------------------
function radio_station_pro_get_episode_avatar_id( $episode_id ) {

	$avatar_id = get_post_meta( $episode_id, 'episode_avatar', true );

	// --- filter and return ---
	$avatar_id = apply_filters( 'radio_station_episode_avatar_id', $avatar_id, $episode_id );
	return $avatar_id;
}

// ------------------------
// Show Episode List Filter
// ------------------------
add_filter( 'radio_station_get_show_episodes', 'radio_station_pro_get_show_page_episodes', 10, 3 );
function radio_station_pro_get_show_page_episodes( $posts, $show_id, $args = array() ) {
	return radio_station_get_show_data( 'episodes', $show_id, $args );
}

// ----------------------
// Get Show Page Episodes
// ----------------------
add_filter( 'radio_station_show_page_episodes', 'radio_station_pro_show_page_episodes', 10, 2 );
// add_filter( 'radio_station_host_page_episodes', 'radio_station_pro_show_page_episodes', 10, 2 );
// add_filter( 'radio_station_producer_page_episodes', 'radio_station_pro_show_page_episodes', 10, 2 );
function radio_station_pro_show_page_episodes( $show_episodes, $post_id ) {
	$episodes_per_page = radio_station_get_setting( 'show_episodes_per_page' );
	if ( absint( $episodes_per_page ) > 0 ) {
		$limit = apply_filters( 'radio_station_show_page_episodes_limit', false, $post_id );
		$show_episodes = radio_station_pro_get_show_episodes( $post_id, array( 'limit' => $limit ) );
	}
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Episodes: ' . print_r( $show_episodes, true ) . '</span>';
	}
	return $show_episodes;
}

// ---------------------------
// Show Episode Archive Avatar
// ---------------------------
add_filter( 'radio_station_show_list_archive_avatar', 'radio_station_pro_episode_archive_avatar', 10, 3 );
function radio_station_pro_episode_archive_avatar( $thumbnail, $episode_id, $type ) {

	if ( 'episode' == $type ) {
		$avatar = radio_station_pro_get_episode_avatar( $episode_id );
		if ( $avatar ) {
			return $avatar;
		} else {
			return false;
		}
	}

	return $thumbnail;
}

// -----------------
// Show Episodes Tab
// -----------------
add_filter( 'radio_station_show_page_sections', 'radio_station_pro_show_page_episodes_section', 10, 2 );
function radio_station_pro_show_page_episodes_section( $sections, $post_id ) {

	$newline = RADIO_STATION_DEBUG ? "\n" : '';

	// --- check for show episodes ---
	$episodes_per_page = radio_station_get_setting( 'show_episodes_per_page' );
	$show_episodes = apply_filters( 'radio_station_show_page_episodes', false, $post_id );

	if ( $show_episodes ) {

		// --- add show episodes tab/section ---
		$episode_type = get_post_type_object( RADIO_STATION_EPISODE_SLUG );
		$episodes_label = $episode_type->labels->name;
		$anchor = $episodes_label;
		$sections['episodes']['heading'] = '<a name="show-episodes"></a>' . $newline;
		$label = __( 'Show %s', 'radio-station' );
		$label = sprintf( $label, $anchor );
		$label = apply_filters( 'radio_station_show_episodes_label', $label, $post_id );
		$sections['episodes']['heading'] .= '<h3 id="show-section-episodes">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = apply_filters( 'radio_station_show_episodes_anchor', $anchor, $post_id );
		$sections['episodes']['anchor'] = $anchor;

		$sections['episodes']['content'] = '<div id="show-episodes" class="show-section-content"><br>' . $newline;
		$radio_station_data['show-episodes'] = $show_episodes;
		$shortcode = '[show-episodes-archive per_page="' . $episodes_per_page . '" show="' . $post_id . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_episodes_shortcode', $shortcode, $post_id );
		$sections['episodes']['content'] .= do_shortcode( $shortcode );
		$sections['episodes']['content'] .= '</div>' . $newline;
	}

	return $sections;
}

// -------------------------
// Episode Archive Shortcode
// -------------------------
// 2.4.1.7: added episode archive shortcode
add_shortcode( 'episode-archive', 'radio_station_pro_episode_archive_list' );
add_shortcode( 'episodes-archive', 'radio_station_pro_episode_archive_list' );
function radio_station_pro_episode_archive_list( $atts ) {
	return radio_station_pro_archive_list_shortcode( RADIO_STATION_EPISODE_SLUG, $atts );
}

// -----------------------------
// Filter Archive Shortcode Meta
// -----------------------------
// 2.4.0.4: added to display episode meta in archive shortcode
add_filter( 'radio_station_archive_shortcode_meta', 'radio_station_pro_archive_meta_episodes', 10, 4 );
function radio_station_pro_archive_meta_episodes( $meta, $post_id, $post_type, $atts ) {

	$type = str_replace( 'rs-', '', $post_type );
	if ( RADIO_STATION_EPISODE_SLUG == $post_type ) {

		$show_id = get_post_meta( $post_id, 'episode_show_id', true );
		$type = get_post_meta( $post_id, 'episode_type', true );
		if ( 'url' == $type ) {
			$url = get_post_meta( $post_id, 'episode_file_url', true );
		} elseif ( 'media' == $type ) {
			$media_id = get_post_meta( $post_id, 'episode_media_id', true );
			$url = wp_get_attachment_url( $media_id );
		}
		$number = get_post_meta( $post_id, 'episode_number', true );
		$air_date = get_post_meta( $post_id, 'episode_air_date', true );
		$air_time = get_post_meta( $post_id, 'episode_air_time', true );
		// $download = get_post_meta( $post_id, 'episode_download', true );
		// $download = ( 'on' == $download ) ? false : true;
		// $patreon = get_post_meta( $show_id, 'show_patreon', true );

		// TODO: add episode meta display
		$parts = array();
		if ( $number ) {
			$parts[] = esc_html( __( 'Episode', 'radio-station' ) ) . ' #' . (int) $number;
		}
		if ( $air_date ) {
			$date_format = apply_filters( 'radio_station_archive_meta_date_format', 'jS M Y' );
			$date = date( $date_format, strtotime( $air_date ) );
			$parts[] = esc_html( __( 'Aired on', 'radio-station') ) . ': ' . esc_html( $date );
		}
		
		// TODO: maybe display episode player ?
		// if ( isset( $atts['player'] && ( '1' == $atts['player'] ) ) {}


		$sep = apply_filters( 'radio_station_archive_meta_separator', ' | ', $post_type );
		$meta = '<div class="' . $type . '-meta">' . implode( $sep, $parts ) . '</div>';
	}

	return $meta;
}

// ----------------------------
// Show Episodes List Shortcode
// ----------------------------
// requires: show shortcode attribute, eg. [show-episodes-list show="1"]
add_shortcode( 'show-episodes-archive',  'radio_station_pro_show_episodes_archive' );
add_shortcode( 'show-episode-archive',  'radio_station_pro_show_episodes_archive' );
add_shortcode( 'show-episodes-list',  'radio_station_pro_show_episodes_archive' );
add_shortcode( 'show-episode-list',  'radio_station_pro_show_episodes_archive' );
function radio_station_pro_show_episodes_archive( $atts ) {
	$output = radio_station_show_list_shortcode( RADIO_STATION_EPISODE_SLUG, $atts );
	return $output;
}

