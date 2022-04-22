<?php

// =========================
// === Radio Station Pro ===
// =========================
// ----- User Profiles -----
// =========================

// === User Profiles ===
// - Get Profile ID
// - Get Host Profile URL
// - Get Producer Profile URL
// - Get Profile Data
// - Get Posts for Host
// - Get Posts for Producer
// - Get Shows for Host
// - Get Shows for Producer
// - Get Episodes for Host
// - Get Episodes for Producer
// - Get Profile RSS URL
// - Get Profile Avatar
// - Get Profile Avatar ID
// - Add Profile Sections to Show Page
// - Team Toggle Script
// - Filter Show Page Section Order
// === Shortcodes ===
// - Show Hosts Archive Shortcode
// - Show Producers Archive Shortcode
// - Show Host List Filter
// - Show Producer List Filter
// - Host Archive Shortcode
// - Producer Archive Shortcode
// - Team Archive Shortcode
// - Filter Archive Shortcode Meta


// ---------------------
// === User Profiles ===
// ---------------------

// --------------
// Get Profile ID
// --------------
// 2.3.3.9: moved from Radio Station and reprefixed
function radio_station_pro_get_profile_id( $type, $user_id ) {

	global $radio_station_data;

	if ( isset( $radio_station_data[$type . '-' . $user_id] ) ) {
		$post_id = $radio_station_data[$type . '-' . $user_id];
		return $post_id;
	}

	// --- get the post ID for the profile ---
	// 2.3.3.9: get singular assigned profile post
	global $wpdb;
	$query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta";
	$query .= " WHERE meta_key = '" . $type . "_user_id' AND meta_value = %d";
	$query = $wpdb->prepare( $query, $user_id );
	$post_id = $wpdb->get_var( $query );

	// --- check for and return published profile ID ---
	// 2.3.3.9: fix to single ID column match value
	if ( $post_id ) {
		$query = "SELECT ID FROM " . $wpdb->prefix . "posts";
		$query .= " WHERE post_status = 'publish' AND ID = %d";
		$query = $wpdb->prepare( $query, $post_id );
		$profile_id = $wpdb->get_var( $query );
		if ( $post_id ) {
			$radio_station_data[$type . '-' . $user_id] = $profile_id;
			return $profile_id;
		}
	}

	return false;
}

// --------------------
// Get Host Profile URL
// --------------------
// 2.3.3.9: moved from free to Pro filter
add_filter( 'radio_station_host_url', 'radio_station_pro_host_url', 10, 2 );
function radio_station_pro_host_url( $host_url, $host_id ) {
	$post_id = radio_station_pro_get_profile_id( 'host', $host_id );
	if ( $post_id ) {
		$host_url = get_permalink( $post_id );
	}
	return $host_url;
}

// ------------------------
// Get Producer Profile URL
// ------------------------
// 2.3.3.9: moved from free to Pro filter
add_filter( 'radio_station_producer_url', 'radio_station_pro_producer_url', 10, 2 );
function radio_station_pro_producer_url( $producer_url, $producer_id ) {
	$post_id = radio_station_pro_get_profile_id( 'producer', $producer_id );
	if ( $post_id ) {
		$producer_url = get_permalink( $post_id );
	}
	return $producer_url;
}

// ----------------
// Get Profile Data
// ----------------
function radio_station_pro_get_profile_data( $profile_type, $data_type, $author_id, $args = array() ) {

	// --- we need a data type and show ID ---
	if ( !$data_type ) {
		return false;
	}
	if ( !$author_id ) {
		return false;
	}

	// --- get meta key for valid data types ---
	if ( in_array( $data_type, array( 'shows', 'episodes' ) ) ) {
		if ( 'host' == $profile_type ) {
			$metakey = 'show_user_list';
		} elseif ( 'producer' == $profile_type ) {
			$metakey = 'show_producer_list';
		} // elseif ( 'reporter' == $profile_type ) {
			// $metakey = 'show_reporter_list';
		// }
	} elseif ( 'posts' != $data_type ) {
		return false;
	}

	// --- check for optional arguments ---
	$default = true;
	if ( !isset( $args['limit'] ) ) {
		$args['limit'] = false;
	} elseif ( false !== $args['limit'] ) {
		$default = false;
	}
	if ( !isset( $args['columns'] ) || !is_array( $args['columns'] ) || ( count( $args['columns'] ) < 1 ) ) {
		$columns = 'posts.ID, posts.post_title, posts.post_content, posts.post_excerpt, posts.post_date';
	} else {
		$columns = array();
		$default = false;
		$valid = array(
			'ID',
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_title',
			'post_excerpt',
			'post_status',
			'comment_status',
			'ping_status',
			'post_password',
			'post_name',
			'to_ping',
			'pinged',
			'post_modified',
			'post_modified_gmt',
			'post_content_filtered',
			'post_parent',
			'guid',
			'menu_order',
			'post_type',
			'post_mime_type',
			'comment_count',
		);
		foreach ( $args['columns'] as $i => $column ) {
			if ( in_array( $column, $valid ) ) {
				if ( !isset( $columns ) ) {
					$columns = 'posts.' . $column;
				} else {
					$columns .= ', posts.' . $column;
				}
			}
		}
	}

	// --- get posts for profile author ---
	global $wpdb;
	if ( 'posts' == $data_type ) {

		// --- get posts by this author ---
		$query = "SELECT " . $columns . " FROM " . $wpdb->prefix . "posts AS posts WHERE post_type = 'post'";
		$query .= " AND post_author = %d AND post_status = 'publish' ORDER BY post_date DESC";
		$query = $wpdb->prepare( $query, $author_id );
		if ( $args['limit'] ) {
			$query .= $wpdb->prepare( " LIMIT %d", $args['limit'] );
		}
		$results = $wpdb->get_results( $query, ARRAY_A );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Query: ' . $query . '</span>';
			echo '<span style="display:none;">Related Results: ' . print_r( $results, true ) . '</span>';
		}
		if ( !$results || !is_array( $results ) || ( count( $results ) < 1 ) ) {
			return false;
		}

	} else {

		// --- check host or producer user list ---
		$id_length = strlen( (string) $author_id );
		$query = "SELECT post_id,meta_value FROM " . $wpdb->prefix . "postmeta";
		$query .= " WHERE meta_key = '" . $metakey . "' AND meta_value LIKE '%s:" . $id_length . ":\"" . $author_id . "\";%'";
		$results = $wpdb->get_results( $query, ARRAY_A );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Related Query: ' . $query . '</span>';
			echo '<span style="display:none;">Related Results: ' . print_r( $results, true ) . '</span>';
		}
		if ( !$results || !is_array( $results ) || ( count( $results ) < 1 ) ) {
			return false;
		}

		// --- get/check post IDs in post meta ---
		$post_ids = array();
		foreach ( $results as $result ) {
			$author_ids = maybe_unserialize( $result['meta_value'] );
			if ( $author_id == $result['meta_value'] || in_array( $author_id, $author_ids ) ) {
				$post_ids[] = $result['post_id'];
			}
		}

		// --- check for post IDs ---
		if ( count( $post_ids ) < 1 ) {
			return false;
		}

		// --- get posts from post IDs ---
		$post_id_list = implode( ',', $post_ids );
		$query = "SELECT " . $columns . " FROM " . $wpdb->prefix . "posts AS posts";
		$query .= " WHERE posts.ID IN(" . $post_id_list . ") AND posts.post_status = 'publish'";
		$query .= " ORDER BY posts.post_date DESC";
		if ( $args['limit'] ) {
			$query .= $wpdb->prepare( " LIMIT %d", $args['limit'] );
		}
		$results = $wpdb->get_results( $query, ARRAY_A );

	}

	// --- filter and return ---
	$results = apply_filters( 'radio_station_' . $profile_type . '_' . $data_type, $results, $author_id, $args );
	return $results;

}

// ---------------------
// Get Posts for Profile
// ---------------------
function radio_station_pro_get_profile_posts( $author_id = false, $args = array() ) {
	return radio_station_pro_get_profile_data( '', 'posts', $author_id, $args );
}
// function radio_station_pro_get_host_posts( $author_id = false, $args = array() ) {
// 	return radio_station_pro_get_profile_data( 'host', 'posts', $author_id, $args );
// }
// function radio_station_pro_get_producer_posts( $author_id = false, $args = array() ) {
// 	return radio_station_pro_get_profile_data( 'producer', 'posts', $author_id, $args );
// }

// ------------------
// Get Shows for Host
// ------------------
function radio_station_pro_get_host_shows( $author_id = false, $args = array() ) {
	return radio_station_pro_get_profile_data( 'host', 'shows', $author_id, $args );
}

// ----------------------
// Get Shows for Producer
// ----------------------
function radio_station_pro_get_producer_shows( $author_id = false, $args = array() ) {
	return radio_station_pro_get_profile_data( 'producer', 'shows', $author_id, $args );
}

// ---------------------
// Get Episodes for Host
// ---------------------
function radio_station_pro_get_host_episodes( $author_id = false, $args = array() ) {
	return radio_station_pro_get_profile_data( 'host', 'episodes', $author_id, $args );
}

// -------------------------
// Get Episodes for Producer
// -------------------------
function radio_station_pro_get_producer_episodes( $author_id = false, $args = array() ) {
	return radio_station_pro_get_profile_data( 'producer', 'episodes', $author_id, $args );
}

// -------------------
// Get Profile RSS URL
// -------------------
function radio_station_get_profile_rss_url( $author_id ) {
	// TODO: RSS feed for profile/author ?
	return '';
}

// ------------------
// Get Profile Avatar
// ------------------
function radio_station_pro_get_profile_avatar( $profile_id, $size = 'thumbnail', $attr = array(), $type = false ) {

	// --- get the avatar ID ---
	$avatar_id = radio_station_pro_get_profile_avatar_id( $profile_id, $type );

	// --- get the attachment image tag ---
	$avatar = false;
	if ( $avatar_id ) {
		$avatar = wp_get_attachment_image( $avatar_id, $size, false, $attr );
	}

	// --- filter and return ---
	$avatar = apply_filters( 'radio_station_show_avatar_output', $avatar, $profile_id, $type );
	return $avatar;
}

// ---------------------
// Get Profile Avatar ID
// ---------------------
function radio_station_pro_get_profile_avatar_id( $profile_id, $type = false ) {

	// --- maybe get profile type ---
	if ( !$type ) {
		global $radio_station_data;
		if ( isset( $radio_station_data['profile-type'] ) ) {
			$type = $radio_station_data['profile-type'];
		}
	}

	// --- get avatar ID for profile type ---
	$avatar_id = get_post_meta( $profile_id, 'profile_avatar', true );

	// --- filter and return ---
	$avatar_id = apply_filters( 'radio_station_profile_avatar_id', $avatar_id, $profile_id, $type );

	return $avatar_id;

}

// ---------------------------------
// Add Profile Sections to Show Page
// ---------------------------------
add_filter( 'radio_station_show_page_sections', 'radio_station_pro_show_profile_sections', 10, 2 );
function radio_station_pro_show_profile_sections( $sections, $post_id ) {

	// --- get post data ---
	$post = get_post( $post_id );
	$type = str_replace( 'rs-', '', $post->post_type );
	$post_type_object = get_post_type_object( $post->post_type);
	$singular_label = $post_type_object->labels->singular_name;
	$newline = RADIO_STATION_DEBUG ? "\n" : '';

	// --- Host section ---
	$hosts = get_post_meta( $post_id, 'show_user_list', true );
	if ( $hosts && is_array( $hosts ) ) {

		$hosts_type = get_post_type_object( RADIO_STATION_HOST_SLUG );
		$host_label = $hosts_type->labels->singular_name;
		$hosts_label = $hosts_type->labels->name;
		$sections['hosts']['heading'] = '<a name="' . esc_attr( $type ) . '-hosts">';
		$label = $singular_label;
		$anchor = $hosts_title = ( count( $hosts ) > 1 ) ? $hosts_label : $host_label;
		$label .= ' ' . $anchor;
		$label = apply_filters( 'radio_station_' . $type . '_hosts_label', $label, $post_id );
		$sections['hosts']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-hosts-title">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = apply_filters( 'radio_station_' . $type . '_hosts_anchor', $anchor, $post_id );
		$sections['hosts']['anchor'] = $anchor;

		$radio_station_data[$type . '-hosts'] = $hosts;
		$shortcode = '[show-hosts-archive show="' . $post_id . '"]';
		// TODO: grid view option for show hosts archive
		$hosts_shortcode = apply_filters( 'radio_station_' . $type . '_page_hosts_shortcode', $shortcode, $post_id );
		$sections['hosts']['content'] = do_shortcode( $hosts_shortcode );
	}

	// --- Producer section ---
	$producers = get_post_meta( $post_id, 'show_producer_list', true );
	if ( $producers && is_array( $producers ) ) {

		$producers_type = get_post_type_object( RADIO_STATION_PRODUCER_SLUG );
		$producer_label = $producers_type->labels->singular_name;
		$producers_label = $producers_type->labels->name;
		$sections['producers']['heading'] = '<a name="' . esc_attr( $type ) . '-producers">';
		$label = $singular_label;
		$anchor = $producers_title = ( count( $producers ) > 1 ) ? $producers_label : $producer_label;
		$label .= ' ' . $anchor;
		$label = apply_filters( 'radio_station_' . $type . '_producers_label', $label, $post_id );
		$sections['producers']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-producers-title">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = apply_filters( 'radio_station_' . $type . '_producers_anchor', $anchor, $post_id );
		$sections['producers']['anchor'] = $anchor;

		$radio_station_data[$type . '-producers'] = $producers;
		$shortcode = '[show-producers-archive show="' . $post_id . '"]';
		// TODO: grid view option for show producers archive
		$producers_shortcode = apply_filters( 'radio_station_' . $type . '_page_producers_shortcode', $shortcode, $post_id );
		$sections['producers']['content'] = do_shortcode( $producers_shortcode );
	}

	// 2.4.1.8: allow for combined into team option
	$team_tab = radio_station_get_setting( 'combined_team_tab' );
	if ( $team_tab ) {
	
		$team_types = array();
		$team_content = '';
		
		if ( $hosts && is_array( $hosts ) && ( count( $hosts ) > 0 ) ) {
			$team_content .= '<div id="team-hosts" class="team-section">' . $newline;
			if ( 'yes' == $team_tab ) {
				$team_content .= '<div class="team-hosts-title">' . esc_html( $hosts_title ) . '</div>' . $newline;
			}
			if ( !strstr( $hosts_shortcode, ' view="grid"' ) ) {
				$hosts_shortcode = str_replace( ']', ' view="grid"]', $hosts_shortcode );
			}
			$team_content .= do_shortcode( $hosts_shortcode );
			$team_content .= '<br></div>' . $newline;
			$team_types['hosts'] = $hosts_title;
		}

		if ( $producers && is_array( $producers ) && ( count( $producers ) > 0 ) ) {
			$team_content .= '<div id="team-producers" class="team-section">' . $newline;
			if ( 'yes' == $team_tab ) {
				$team_content .= '<div class="team-producers-title">' . esc_html( $producers_title ) . '</div>' . $newline;
			}
			if ( !strstr( $producers_shortcode, ' view="grid"' ) ) {
				$producers_shortcode = str_replace( ']', ' view="grid"]', $producers_shortcode );
			}
			$team_content .= do_shortcode( $producers_shortcode );
			$team_content .= '<br></div>' . $newline;
			$team_types['producers'] = $producers_title;
		}
		
		// TODO: handle custom team member types

		if ( count( $team_types ) > 0 ) {

			$radio_station_data[$type . '-team'] = $team;
			$sections['team']['heading'] = '<a name="' . esc_attr( $type ) . '-team">';
			$label = $singular_label;
			$anchor = __( 'Team', 'radio-station' );
			$label .= ' ' . $anchor;
			$label = apply_filters( 'radio_station_' . $type . '_team_label', $label, $post_id );
			$sections['team']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-team-title">' . esc_html( $label ) . '</h3>' . $newline;
			$anchor = apply_filters( 'radio_station_' . $type . '_team_anchor', $anchor, $post_id );
			$sections['team']['anchor'] = $anchor;
			
			// --- maybe prepend checkboxes to team content ---
			if ( count( $team_types ) > 1 ) {

				/* $checkboxes = '<div class="team-toggle-checkboxes">' . $newline;
				foreach ( $team_types as $team_type => $label ) {
					$checkboxes .= '<div class="team-toggle-checkbox">' . $newline;
					$checkboxes .= '<input type="checkbox" id="team-checkbox-' . esc_attr( $team_type ). '" checked="checked" onclick="radio_team_check(\'' . esc_js( $team_type ) . '\',false);">' . $newline;
					$checkboxes .= ' <a href="javascript:void(0);" onclick="radio_team_check(\'' . esc_js( $team_type ) . '\',true);">' . esc_html( $label ) . '</a>' . $newline;
					$checkboxes .= '</div>' . $newline;
				}
				$checkboxes .= '</div><br>' . $newline;
				$team_content = $checkboxes . $team_content; */
				
				$grid = ( 'yes' == $team_tab ) ? 'false' : 'true';
				$select = '<div class="team-selection">' . $newline;
				$select .= '<b>' . esc_html( __( 'View', 'radio-station' ) ) . '</b>: ';
				$select .= '<select id="team-select" onchange="radio_team_select(' . esc_js( $grid ) . ');">' . $newline;
				$select .= '<option value="all" selected="selected">' . esc_html( __( 'All', 'radio-station' ) ) . '</option>' . $newline;
				foreach ( $team_types as $team_type => $label ) {
					$select .= '<option value="' . esc_attr( $team_type ) . '">' . esc_html( $label ) . '</option>' . $newline;
				}
				$select .= '</select></div><br>' . $newline;
				$team_content = $select . $team_content; 
				
				// --- add checkbox toggle script ---
				add_action( 'wp_footer', 'radio_station_pro_team_select_script' );
			}

			// --- set team section content ---
			$sections['team']['content'] = '<div id="' . esc_attr( $type ) . '-team" class="show-section-content"><br>' . $newline;
			$sections['team']['content'] .= $team_content . '</div>' . $newline;
		}
	} else {
		
		// --- wrap host/producer content ---
		// 2.4.1.8: moved out from section content
		if ( isset( $sections['hosts']['content'] ) ) {
			$sections['hosts']['content'] = '<div id="' . esc_attr( $type ) . '-hosts" class="show-section-content"><br>' . $sections['hosts']['content'] . '</div>' . $newline;
		}
		if ( isset( $sections['producers']['content'] ) ) {
			$sections['producers']['content'] = '<div id="' . esc_attr( $type ) . '-producers" class="show-section-content"><br>' . $sections['producers']['content'] . '</div>' . $newline;
		}
	}

	return $sections;
}

// ------------------
// Team Toggle Script
// ------------------
function radio_station_pro_team_select_script() {
	
	/* $js = "function radio_team_check(team,check) {
		checkbox = document.getElementById('team-checkbox-'+team);
		if (checkbox.checked == '1') {
			document.getElementById('team-'+team).style.display = 'none';
			if (check) {checkbox.checked = '0';}
		} else {
			document.getElementById('team-'+team).style.display = '';
			if (check) {checkbox.checked = '1';}
		}
	}"; */

	$js = "function radio_team_select(grid) {
		select = document.getElementById('team-select');
		value = select.options[select.selectedIndex].value;
		if (grid) {
			/* TODO: hide individual grid items */
		} else {
			sections = document.getElementsByClassName('team-section');
			if (value == 'all') {
				for (i = 0; i < sections.length; i++) {sections[i].style.display = '';}
			} else {
				for (i = 0; i < sections.length; i++) {sections[i].style.display = 'none';}
				document.getElementById('team-'+value).style.display = '';
			}
		}
	}";
	
	echo "<script>" . $js . "</script>";
}


// ------------------------------
// Filter Show Page Section Order
// ------------------------------
// 2.4.1.8: added for displaying combined team tab
add_filter( 'radio_station_show_page_section_order', 'radio_station_pro_show_page_section_order', 10, 2 );
function radio_station_pro_show_page_section_order( $section_order, $post_id ) {
		
	// 2.4.1.8: allow for combined into team option
	$team_tab = radio_station_get_setting( 'combined_team_tab' );
	if ( $team_tab ) {

		// --- replace host and producer tabs with team tab ---
		foreach ( $section_order as $i => $section ) {
			if ( ( 'hosts' == $section ) || ( 'producers' == $section ) ) {
				unset( $section_order[$i] );
			}
		}
		$section_order = array_unique( $section_order );
		$section_order[] = 'team';
	}
	
	return $section_order;
}


// ------------------
// === Shortcodes ===
// ------------------

// ----------------------------
// Show Hosts Archive Shortcode
// ----------------------------
add_shortcode( 'show-hosts-archive', 'radio_station_show_hosts_archive' );
add_shortcode( 'show-host-archive', 'radio_station_show_hosts_archive' );
function radio_station_show_hosts_archive( $atts ) {
	// 2.4.1.7: enqueue shortcode styles
	radio_station_enqueue_style( 'shortcode' );
	$output = radio_station_show_list_shortcode( RADIO_STATION_HOST_SLUG, $atts );
	return $output;
}

// --------------------------------
// Show Producers Archive Shortcode
// --------------------------------
add_shortcode( 'show-producers-archive', 'radio_station_show_producers_archive' );
add_shortcode( 'show-producer-archive', 'radio_station_show_producers_archive' );
function radio_station_show_producers_archive( $atts ) {
	// 2.4.1.7: enqueue shortcode styles
	radio_station_enqueue_style( 'shortcode' );
	$output = radio_station_show_list_shortcode( RADIO_STATION_PRODUCER_SLUG, $atts );
	return $output;
}


// ---------------------
// Show Host List Filter
// ---------------------
add_filter( 'radio_station_get_show_hosts', 'radio_station_pro_get_show_page_hosts', 10, 3 );
function radio_station_pro_get_show_page_hosts( $posts, $show_id, $args = array() ) {
	return radio_station_get_show_data( 'hosts', $show_id, $args );
}

// -------------------------
// Show Producer List Filter
// -------------------------
add_filter( 'radio_station_get_show_producers', 'radio_station_pro_get_show_page_producers', 10, 3 );
function radio_station_pro_get_show_page_producers( $posts, $show_id, $args = array() ) {
	return radio_station_get_show_data( 'producers', $show_id, $args );
}

// ----------------------
// Host Archive Shortcode
// ----------------------
// 2.4.1.7: added host archive shortcode
add_shortcode( 'dj-archive', 'radio_station_pro_host_archive_list' );
add_shortcode( 'djs-archive', 'radio_station_pro_host_archive_list' );
add_shortcode( 'host-archive', 'radio_station_pro_host_archive_list' );
add_shortcode( 'hosts-archive', 'radio_station_pro_host_archive_list' );
function radio_station_pro_host_archive_list( $atts ) {
	// 2.4.1.7: enqueue shortcode styles
	radio_station_enqueue_style( 'shortcode' );
	return radio_station_pro_archive_list_shortcode( RADIO_STATION_HOST_SLUG, $atts );
}

// --------------------------
// Producer Archive Shortcode
// --------------------------
// 2.4.1.7: added producer archive shortcode
add_shortcode( 'producer-archive', 'radio_station_pro_producer_archive_list' );
add_shortcode( 'producers-archive', 'radio_station_pro_producer_archive_list' );
function radio_station_pro_producer_archive_list( $atts ) {
	// 2.4.1.7: enqueue shortcode styles
	radio_station_enqueue_style( 'shortcode' );
	return radio_station_pro_archive_list_shortcode( RADIO_STATION_PRODUCER_SLUG, $atts );
}

// ----------------------
// Team Archive Shortcode
// ----------------------
// 2.4.1.8: added team archive shortcode
add_shortcode( 'team-archive', 'radio_station_pro_team_archives' );
add_shortcode( 'teams-archive', 'radio_station_pro_team_archives' );
function radio_station_pro_team_archives( $atts ) {

	global $post;
	$post_id = $post->ID;

	// --- extract shortcode attributes ---
	$shortcode_atts = $atts;
	$defaults = array(
		'display' => array( 'all' ),
		'view'    => 'tabs',
	);
	$atts = shortcode_atts( $defaults, $atts, 'team-archive' );
	// echo "Team Atts: " . print_r( $atts, true ) . '<br>' . PHP_EOL;

	// --- check member types to display ---
	if ( is_array( $atts['display'] ) ) {
		$display = $atts['display'];
	} elseif ( strstr( $atts['display'], ',' ) ) {
		$display = explode( ',', $atts['display'] );
		foreach ( $display as $i => $type ) {
			$display[$i] = trim( $type );
		}
	} else {
		$display = array( trim( $atts['display'] ) );
	}

	// --- set section anchors and content ---
	$sections = array();
	if ( in_array( 'all', $display ) || in_array( 'hosts', $display ) ) {		
		$sections['hosts']['anchor'] = __( 'Hosts', 'radio-station' );
		$sections['hosts']['content'] = '<div id="team-hosts" class="team-section-content"><br>' . PHP_EOL;
		$sections['hosts']['content'] .= radio_station_pro_archive_list_shortcode( RADIO_STATION_HOST_SLUG, $shortcode_atts );
		$sections['hosts']['content'] .= '</div>' . PHP_EOL;
	}
	if ( in_array( 'all', $display ) ||  in_array( 'producers', $display ) ) {
		$sections['producers']['anchor'] = __( 'Producers', 'radio-station' );
		$sections['producers']['content'] = '<div id="team-producers" class="team-section-content"><br>' . PHP_EOL;
		$sections['producers']['content'] .= radio_station_pro_archive_list_shortcode( RADIO_STATION_PRODUCER_SLUG, $shortcode_atts );
		$sections['producers']['content'] .= '</div>' . PHP_EOL;
	}
	
	// TODO: and editors other custom team members
	// if ( in_array( 'all', $display ) ||  in_array( 'editors', $display ) ) {
		// $sections['editors']['anchor'] = __( 'Editors', 'radio-station' );
		// $sections['editors']['content'] = '<div id="team-editors" class="team-section-content"><br>' . PHP_EOL;
		// $sections['editors']['content'] = ...
		// $sections['editors']['content'] .= '</div>' . PHP_EOL;
	// }

	$html = '<div id="team-content">' . PHP_EOL;
	// $html .= '<input type="hidden" id="radio-page-type" value="team">' . PHP_EOL;

	// --- filter sections and section order ---
	$sections = apply_filters( 'radio_station_team_shortcode_sections', $sections, $post_id );
	$section_order = array( 'hosts', 'producers', 'editors', 'custom' );
	$section_order = apply_filters( 'radio_station_team_shortcode_section_order', $section_order, $post_id );

	// --- Display Team Sections ---
	if ( ( is_array( $sections ) && ( count( $sections ) > 0 ) )
	     && is_array( $section_order ) && ( count( $section_order ) > 0 ) ) {

		$html .= '<div class="team-tabs">' . PHP_EOL;

		$i = 0;
		$found_section = false;
		foreach ( $section_order as $section ) {
			if ( isset( $sections[$section] ) ) {
				$found_section = true;
				$class = ( 0 == $i ) ? 'tab-active' : 'tab-inactive';
				// 2.4.1.8: added prefix argument to javascript function
				$html .= '<div id="team-' . esc_attr( $section ) . '-tab" class="team-tab ' . esc_attr( $class ) . '" onclick="radio_show_tab(\'team\',\'' . esc_attr( $section ) . '\');">' . PHP_EOL;
					$html .= esc_html( $sections[$section]['anchor'] ) . PHP_EOL;
				$html .= '</div>' . PHP_EOL;
				if ( ( $i + 1 ) < count( $sections ) ) {
					$html .= '<div class="team-tab-spacer">&nbsp;</div>' . PHP_EOL;
				}
				$i++;
			}
		}
		if ( $found_section ) {
			$html .= '<div class="team-tab-spacer">&nbsp;</div>' . PHP_EOL;
		}

		$html .= '</div>' . PHP_EOL;

		// --- team sections ---
		$html .= '<div class="team-sections">' . PHP_EOL;
		$i = 0;
		foreach ( $section_order as $section ) {
			if ( isset( $sections[$section] ) ) {

				// --- add tab classes to section ---
				$classes = array( 'team-section' );
				if ( 0 == $i ) {
					$classes[] = 'tab-active';
				} else {
					$classes[] = 'tab-inactive';
				}
				$class = implode( ' ', $classes );
				$sections[$section]['content'] = str_replace( 'class="team-section-content"', 'class="' . esc_attr( $class ) . '"', $sections[$section]['content'] );

				// --- section content ---
				$html .= $sections[$section]['content'] . PHP_EOL;

				$i ++;
			}
		}
		$html .= '</div>' . PHP_EOL;

	}
	$html .= '</div>' . PHP_EOL;

	$html .= '</div>' . PHP_EOL;

	// --- enqueue styles and scripts ---
	radio_station_enqueue_style( 'shortcode' );
	radio_station_enqueue_style( 'profiles' );
	radio_station_enqueue_script( 'radio-station-page', array( 'radio-station' ), true );

	return $html;
}

// -----------------------------
// Filter Archive Shortcode Meta
// -----------------------------
// 2.4.0.4: added to display profile meta in archive shortcode
add_filter( 'radio_station_archive_shortcode_meta', 'radio_station_pro_archive_meta_profiles', 10, 4 );
function radio_station_pro_archive_meta_profiles( $html, $post_id, $post_type, $atts ) {

	global $radio_station_data;
	$type = str_replace( 'rs-', '', $post_type );
	$radio_station_data['archive_type'] = $type;

	$post_types = array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
	if ( in_array( $post_type, $post_types ) ) {

		$meta = array();

		// --- get contact neta ---
		if ( $atts['website'] ) {
			$website = get_post_meta( $post_id, 'profile_link', true );
		}
		if ( $atts['email'] ) {
			$email = get_post_meta( $post_id, 'profile_email', true );
		}
		if ( $atts['phone'] ) {
			$phone = get_post_meta( $post_id, 'profile_phone', true );
		}
		// if ( $atts['patreon'] ) {
		// 	$patreon = get_post_meta( $post_id, 'profile_patreon', true );
		// }

		// --- TODO: contact icon meta ---
		/* if ( $website || $email || $phone ) {
			$meta['contacts'] = '<div class="' . attr( $type ) . '-contacts">';
			if ( $website ) {

			}
			if ( $email ) {

			}
			if ( $phone ) {

			}
			$meta['contacts'] .= '</div>' . PHP_EOL;
		} */

		// --- social icons meta ---
		if ( $atts['social'] ) {
			$social = radio_station_pro_social_icons_display( '', $post_id );
			if ( '' != $social ) {
				$meta['social'] = '<div class="' . esc_attr( $type ) . '-social-icons">';
				$meta['social'] .= $social;
				$meta['social'] .= '</div>';

				// --- enqueue social icons styles ---
				add_action( 'wp_footer', 'radio_station_pro_social_icon_styles_output' );
			}
		}

		// --- user show meta ---
		if ( $atts['shows'] ) {
			$user_id = get_post_meta( $post_id, $type . '_user_id', true );
			$shows = radio_station_pro_get_user_shows( $user_id, $type );
			if ( $shows ) {
				$meta['shows'] = '<div class="' . esc_attr( $type ) . '-shows">';
				$meta['shows'] .= esc_html( __( 'Shows', 'radio-station' ) ) . ': ';
				foreach ( $shows as $i => $show_id ) {
					if ( ( $i > 0 ) && ( $i != count( $shows ) ) ) {
						$meta['shows'] .= ', ';
					}
					$permalink = get_permalink( $show_id );
					$title = get_the_title( $show_id );
					$meta['shows'] .= '<a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a>';
				}
				$meta['shows'] .= '</div>' . PHP_EOL;
			}
		}

		// -- filter meta display output ---
		$meta = apply_filters( 'radio_station_archive_profile_meta', $meta, $type, $atts );
		$html .= implode( '', $meta );

		// if ( RADIO_STATION_DEBUG ) {
		//	echo "<textarea id='meta-debug'>" . print_r( $meta, 1 ) . "</textarea>";
		// }

	}

	return $html;
}


