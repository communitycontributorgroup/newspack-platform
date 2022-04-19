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
// === Shortcodes ===
// - Show Hosts Archive Shortcode
// - Show Producers Archive Shortcode
// - Show Host List Filter
// - Show Producer List Filter
// - Host Archive Shortcode
// - Producer Archive Shortcode
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
	if ( $hosts ) {

		$hosts_type = get_post_type_object( RADIO_STATION_HOST_SLUG );
		$host_label = $hosts_type->labels->singular_name;
		$hosts_label = $hosts_type->labels->name;
		$sections['hosts']['heading'] = '<a name="' . esc_attr( $type ) . '-hosts">';
		$label = $singular_label;
		$anchor = ( is_array( $hosts ) && ( count( $hosts ) > 1 ) ) ? $hosts_label : $host_label;
		$label .= ' ' . $anchor;
		$label = apply_filters( 'radio_station_' . $type . '_hosts_label', $label, $post_id );
		$sections['hosts']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-section-hosts">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = apply_filters( 'radio_station_' . $type . '_hosts_anchor', $anchor, $post_id );
		$sections['hosts']['anchor'] = $anchor;

		$radio_station_data[$type . '-hosts'] = $hosts;
		$sections['hosts']['content'] = '<div id="' . esc_attr( $type ) . '-hosts" class="show-section-content"><br>' . $newline;
		$shortcode = '[show-hosts-archive show="' . $post_id . '"]';
		$shortcode = apply_filters( 'radio_station_' . $type . '_page_hosts_shortcode', $shortcode, $post_id );
		$sections['hosts']['content'] .= do_shortcode( $shortcode );
		$sections['hosts']['content'] .= '</div>' . $newline;
	}

	// --- Producer section ---
	$producers = get_post_meta( $post_id, 'show_producer_list', true );
	if ( $producers ) {

		$producers_type = get_post_type_object( RADIO_STATION_PRODUCER_SLUG );
		$producer_label = $producers_type->labels->singular_name;
		$producers_label = $producers_type->labels->name;
		$sections['producers']['heading'] = '<a name="' . esc_attr( $type ) . '-producers">';
		$label = $singular_label;
		$anchor = ( is_array( $producers ) && ( count( $producers ) > 1 ) ) ? $producers_label : $producer_label;
		$label .= ' ' . $anchor;
		$label = apply_filters( 'radio_station_' . $type . '_producers_label', $label, $post_id );
		$sections['producers']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-section-producers">' . esc_html( $label ) . '</h3>' . $newline;
		$anchor = apply_filters( 'radio_station_' . $type . '_producers_anchor', $anchor, $post_id );
		$sections['producers']['anchor'] = $anchor;

		$radio_station_data[$type . '-producers'] = $producers;
		$sections['producers']['content'] = '<div id="' . esc_attr( $type ) . '-producers" class="show-section-content"><br>' . $newline;
		$shortcode = '[show-producers-archive show="' . $post_id . '"]';
		$shortcode = apply_filters( 'radio_station_' . $type . '_page_producers_shortcode', $shortcode, $post_id );
		$sections['producers']['content'] .= do_shortcode( $shortcode );
		$sections['producers']['content'] .= '</div>' . $newline;
	}

	return $sections;
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

