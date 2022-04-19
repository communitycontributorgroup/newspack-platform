<?php

// =========================
// === Radio Station Pro ===
// =========================
// ------- Shortcodes ------
// =========================

// - Archive List Shortcode
// - Profile List Shortcode Abstract
// - Host Posts Archive Shortcode
// - Producer Posts Archive Shortcode
// - Host Shows Archive Shortcode
// - Producer Shows Archive Shortcode
// - Host Episodes Archive Shortcode
// - Producer Episodes Archive Shortcode
// - Archive Shortcode No Records Message

// ------------------
// === Shortcodes ===
// ------------------

// ----------------------
// Archive List Shortcode
// ----------------------
// (handles Hosts, Producers, Episodes etc.)
// 2.4.1.7: added archive list shortcode abstract
function radio_station_pro_archive_list_shortcode( $post_type, $atts ) {

	// --- set type from post type ---
	$type = str_replace( 'rs-', '', $post_type );

	// --- get clock time format ---
	$time_format = radio_station_get_setting( 'clock_time_format' );

	// --- merge defaults with passed attributes ---
	$defaults = array(
		// --- shortcode display ----
		'description'  => 'excerpt',
		'hide_empty'   => 0,
		'time'         => $time_format,
		'thumbnails'   => 1,
		'view'         => 0,
		// --- query args ---
		'orderby'      => 'title',
		'order'        => 'ASC',
		'status'       => 'publish',
		'perpage'      => -1,
		'offset'       => 0,
		'pagination'   => 1,
		// --- specific posts ---
		'host'         => 0,
		'producer'     => 0,
		'episode'      => 0,
		// --- profile meta display ---
		'shows'        => 1,
		'website'      => 0,
		'email'        => 0,
		'phone'        => 0,
		'social'       => 1,
		// 'patreon'      => 0,
		// --- episode meta display ---
		'number'       => 1,
		'air_date'     => 1,
		// TODO: add more episode meta...
	);

	// --- handle possible pagination offset ---
	if ( isset( $atts['perpage'] ) && !isset( $atts['offset'] ) && get_query_var( 'page' ) ) {
		$page = absint( get_query_var( 'page' ) );
		if ( $page > - 1 ) {
			$atts['offset'] = (int) $atts['perpage'] * $page;
		}
	}
	$atts = shortcode_atts( $defaults, $atts, $post_type . '-archive' );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Shortcode Atts: ' . print_r( $atts, true ) . '</span>';
	}

	// --- get published shows ---
	$args = array(
		'post_type'   => $post_type,
		'post_status' => $atts['status'],
		'numberposts' => - 1,
		'orderby'     => $atts['orderby'],
		'order'       => $atts['order'],
	);

	// --- extra queries for shows ---
	if ( ( RADIO_STATION_HOST_SLUG == $post_type ) && isset( $atts['host'] ) && $atts['host'] ) {
		$args['include'] = explode( ',', $atts['host'] );
	} elseif ( ( RADIO_STATION_PRODUCER_SLUG == $post_type ) && isset( $atts['producer'] ) && $atts['producer'] ) {
		$args['include'] = explode( ',', $atts['producer'] );
	} elseif ( ( RADIO_STATION_EPISODE_SLUG == $post_type ) && isset( $atts['episode'] ) && $atts['episode'] ) {
		$args['include'] = explode( ',', $atts['episode'] );
	}

	// --- get posts via query ---
	$args = apply_filters( 'radio_station_' . $type . '_archive_post_args', $args );
	$archive_posts = get_posts( $args );
	$archive_posts = apply_filters( 'radio_station_' . $type . '_archive_posts', $archive_posts );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Archive Shortcode: ' . PHP_EOL;
		echo 'Args: ' . print_r( $args, true ) . PHP_EOL;
		echo 'Posts: ' . print_r( $archive_posts, true ) . '</span>';
	}

	// --- set time data formats ---
	if ( 24 == (int) $atts['time'] ) {
		$start_data_format = $end_data_format = 'H:i';
	} else {
		$start_data_format = $end_data_format = 'g:i a';
	}
	$start_data_format = 'j, ' . $start_data_format;
	$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, $post_type . '-archive', $atts );
	$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, $post_type . '-archive', $atts );

	// --- check for results ---
	if ( !$archive_posts || !is_array( $archive_posts ) || ( count( $archive_posts ) == 0 ) ) {
		if ( $atts['hide_empty'] ) {
			return '';
		}
		$post_count = 0;
	} else {

		// --- count total archive posts ---
		$post_count = count( $archive_posts );

		// --- manually apply offset and perpage limit ---
		if ( $atts['offset'] && ( $atts['perpage'] > 0 ) ) {
			if ( $post_count > $atts['offset'] ) {
				$offset_posts = array();
				foreach ( $archive_posts as $i => $archive_post ) {
					if ( ( $i > $atts['offset'] ) && ( count( $offset_posts ) < $atts['perpage'] ) ) {
						$offset_posts[] = $archive_post;
					}
				}
				$archive_posts = $offset_posts;
				$post_count = count( $archive_posts );
			} else {
				$archive_posts = array();
				$post_count = 0;
			}
		}
	}

	// --- output list or no results message ---
	$classes = array( $type . '-archives' );
	if ( $atts['view'] ) {
		$classes[] = $atts['view'];
	}
	$class_list = implode( ' ', $classes );
	$list = '<div class="' . esc_attr( $class_list ) . '">' . PHP_EOL;
	if ( !$archive_posts || !is_array( $archive_posts ) || ( count( $archive_posts ) == 0 ) ) {

		// --- no shows messages ----
		if ( RADIO_STATION_HOST_SLUG == $post_type ) {
			$message = __( 'No Hosts were found to display.', 'radio-station' );
		} elseif ( RADIO_STATION_PRODUCER_SLUG == $post_type ) {
			$message = __( 'No Producers were found to display.', 'radio-station' );
		} elseif ( RADIO_STATION_EPISODE_SLUG == $post_type ) {
			$message = __( 'No Episodes were found to display.', 'radio-station' );
		}
		$message = apply_filters( 'radio_station_archive_shortcode_no_records', $message, $post_type, $atts );
		$list .= esc_html( $message );

	} else {

		// --- filter excerpt length and more ---
		$length = apply_filters( 'radio_station_archive_' . $type. '_list_excerpt_length', false );
		$more = apply_filters( 'radio_station_archive_' . $type . '_list_excerpt_more', '[&hellip;]' );

		// --- archive list ---
		$list .= '<ul class="' . esc_attr( $type ) . '-archive-list">' . PHP_EOL;

		// --- set info keys ---
		// (note: meta is shows for hosts/producers, episode meta for episodes)
		$infokeys = array( 'avatar', 'title', 'meta', 'description', 'custom' );
		$infokeys = apply_filters( 'radio_station_archive_shortcode_info_order', $infokeys, $post_type, $atts );

		// --- loop post archive ---
		foreach ( $archive_posts as $archive_post ) {

			$info = array();

			// --- map archive data to variables ---
			$post_id = $image_post_id = $archive_post->ID;
			$title = $archive_post->post_title;
			$permalink = get_permalink( $archive_post->ID );
			$post_content = $archive_post->post_content;
			$post_excerpt = $archive_post->post_excerpt;

			$list .= '<li class="' . esc_attr( $type ) . '-archive-item archive-item">';

			// --- show avatar or thumbnail fallback ---
			$info['avatar'] = '<div class="' . esc_attr( $type ) . '-archive-item-thumbnail">';
			if ( $atts['thumbnails'] ) {
				$info['avatar'] .= '<a href="' . esc_url( $permalink ) . '">';
				$profile_post_types = array( RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
				if ( in_array( $post_type, $profile_post_types ) ) {
					$size = apply_filters( 'radio_station_' . $type . '_archive_avatar_size', 'thumbnail', $post_id, $type . '-archive' );
					$attr = array( 'class' => esc_attr( $type ) . '-thumbnail-image' );
					$attr['alt'] = $attr['title'] = $title;
					$profile_avatar = radio_station_pro_get_profile_avatar( $post_id, $size, $attr );
					if ( $profile_avatar ) {
						$info['avatar'] .= $profile_avatar;
					}
				} elseif ( has_post_thumbnail( $image_post_id ) ) {
					$attr = array( 'class' => esc_attr( $type ) . '-thumbnail-image' );
					$thumbnail = get_the_post_thumbnail( $image_post_id, 'thumbnail', $attr );
					$info['avatar'] .= $thumbnail;
				}
				$info['avatar'] .= '</a>';
			}
			$info['avatar'] .= '</div>';

			// --- title ----
			$info['title'] = '<div class="' . esc_attr( $type ) . '-archive-item-title">';
			$info['title'] .= '<a href="' . esc_url( $permalink ) . '">';
			$info['title'] .= esc_html( $title ) . '</a>';
			$info['title'] .= '</div><span></span>';

			// --- info meta ---
			$info['meta'] = apply_filters( 'radio_station_archive_shortcode_meta', '', $post_id, $post_type, $atts );

			// --- description ---
			// if ( 'none' == $atts['description'] ) {
			if ( ( 'none' == $atts['description'] ) || ( 'grid' == $atts['view'] ) ) {
				$info['description'] = '';
			} elseif ( 'full' == $atts['description'] ) {
				$info['description'] = '<div class="' . esc_attr( $type ) . '-archive-item-content">';
				$content = apply_filters( 'radio_station_' . $type . '_archive_content', $post_content, $post_id );
				$info['description'] .= $content;
				$info['description'] .= '</div>';
			} else {
				$info['description'] = '<div class="' . esc_attr( $type ) . '-archive-item-excerpt">';
				if ( !empty( $post_excerpt ) ) {
					$excerpt = $post_excerpt;
					$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
				} else {
					$excerpt = radio_station_trim_excerpt( $post_content, $length, $more, $permalink );
				}
				$excerpt = apply_filters( 'radio_station_' . $type . '_archive_excerpt', $excerpt, $post_id );
				$info['description'] .= $excerpt;
				$info['description'] .= '</div>';
			}

			// --- filter for custom HTML info ---
			$info['custom'] = apply_filters( 'radio_station_archive_shortcode_info_custom', '', $post_id, $post_type, $atts );

			// --- filter info and loop info keys to add to archive list ---
			$info = apply_filters( 'radio_station_archive_shortcode_info', $info, $post_id, $post_type, $atts );
			foreach ( $infokeys as $infokey ) {
				if ( isset( $info[$infokey] ) ) {
					$list .= $info[$infokey];
				}
			}

			$list .= '</li>' . PHP_EOL;
		}
		$list .= '</ul>' . PHP_EOL;
	}
	$list .= '</div>' . PHP_EOL;

 	// --- add archive_pagination ---
	if ( $atts['pagination'] && ( $atts['perpage'] > 0 ) && ( $post_count > 0 ) ) {
		if ( $post_count > $atts['perpage'] ) {
			$list .= radio_station_archive_pagination( $post_type, $atts, $post_count );
		}
	}

	// --- enqueue pagination javascript ---
	add_action( 'wp_footer', 'radio_station_archive_pagination_javascript' );

	// --- enqueue shortcode styles ---
	radio_station_enqueue_style( 'shortcodes' );

	// --- filter and return  ---
	$list = apply_filters( 'radio_station_' . $type . '_archive_list', $list, $atts );

	return $list;
}


// -------------------------------
// Profile List Shortcode Abstract
// -------------------------------
function radio_station_pro_profile_list_shortcode( $profile_type, $type, $atts ) {

	global $radio_station_data;

	// --- get time and date formats ---
	$timeformat = get_option( 'time_format' );
	$dateformat = get_option( 'date_format' );

	// --- get shortcode attributes ---
	$defaults = array(
		'id'         => false,
		'per_page'   => 15,
		'limit'      => 0,
		'content'    => 'excerpt',
		'thumbnails' => 1,
		'pagination' => 1,
	);
	$atts = shortcode_atts( $defaults, $atts, $profile_type . '-' . $type . '-list' );

	// --- maybe get stored post data ---
	if ( isset( $radio_station_data['profile-' . $type . 's'] ) ) {

		// --- use data stored from template ---
		$posts = $radio_station_data['profile-' . $type . 's'];
		unset( $radio_station_data['profile-' . $type . 's'] );
		$author_id = $radio_station_data['author-id'];

	} else {
		// --- check for show ID (required at minimum) ---
		if ( !$atts['id'] ) {
			return '';
		}
		$author_id = $atts['id'];

		// --- attempt to get show ID via slug ---
		if ( intval( $author_id ) != $author_id ) {
			$author = get_user_by( 'ID', $author_id );
			if ( !$author ) {
				return '';
			}
		}

		// --- get related to show posts ---
		$args = array();
		if ( isset( $atts['limit'] ) ) {
			$args['limit'] = $atts['limit'];
		}
		if ( 'post' == $type ) {
			if ( 'host' == $profile_type ) {
				$posts = radio_station_pro_get_host_posts( $author_id, $args );
			} elseif ( 'producer' == $profile_type ) {
				$posts = radio_station_pro_get_producer_posts( $author_id, $args );
			}
		} elseif ( 'show' == $type ) {
			if ( 'host' == $profile_type ) {
				$posts = radio_station_pro_get_host_shows( $author_id, $args );
			} elseif ( 'producer' == $profile_type ) {
				$posts = radio_station_pro_get_producer_shows( $author_id, $args );
			}
		} elseif ( 'episode' == $type ) {
			if ( 'host' == $profile_type ) {
				$posts = radio_station_pro_get_host_episodes( $author_id, $args );
			} elseif ( 'producer' == $profile_type ) {
				$posts = radio_station_pro_get_producer_episodes( $author_id, $args );
			}
		}
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">' . $profile_type . ' (' . $type . '): ' . print_r( $posts, true ) . '</span>';
		}
	}
	if ( !isset( $posts ) || !$posts || !is_array( $posts ) || ( count( $posts ) == 0 ) )  {
		return '';
	}

	// --- filter excerpt length and more ---
	$length = apply_filters( 'radio_station_' . $profile_type . '_' . $type . '_list_excerpt_length', false );
	$more = apply_filters( 'radio_station_' . $profile_type . '_' . $type . '_list_excerpt_more', '[&hellip;]' );

	// --- show list div ---
	$list = '<div id="' . esc_attr( $profile_type ) . '-' . esc_attr( $author_id ) . '-' . esc_attr( $type ) . 's-list" class="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . 's-list">';

	// --- loop show posts ---
	$post_pages = 1;
	$j = 0;
	foreach ( $posts as $post ) {
		$newpage = $firstpage = false;
		if ( 0 == $j ) {
			$newpage = $firstpage = true;
		} elseif ( $j == $atts['per_page'] ) {
			// --- close page div ---
			$list .= '</div>';
			$newpage = true;
			$post_pages ++;
			$j = 0;
		}
		if ( $newpage ) {
			// --- new page div ---
			if ( !$firstpage ) {
				$hide = ' style="display:none;"';
			} else {
				$hide = '';
			}
			$list .= '<div id="' . esc_attr( $profile_type ) . '-' . esc_attr( $author_id ) . '-' . esc_attr( $type ) . 's-page-' . esc_attr( $post_pages ) . '" class="' . esc_attr( $profile_type ) . '-' . esc_attr( $author_id ) . '-' . esc_attr( $type ) . 's-page"' . $hide . '>';
		}

		// --- new item div ---
		$classes = array( $profile_type . '-' . $type );
		if ( $newpage ) {$classes[] = 'first-item';}
		$class = implode( ' ', $classes );
		$list .= '<div class="' . esc_attr( $class ) . '">';

		// --- post thumbnail ---
		if ( $atts['thumbnails'] ) {
			$has_thumbnail = has_post_thumbnail( $post['ID'] );
			if ( $has_thumbnail ) {
				$attr = array( 'class' => esc_attr( $profile_type ) . '-' . esc_attr( $type ) . '-thumbnail-image' );
				$thumbnail = get_the_post_thumbnail( $post['ID'], 'thumbnail', $attr );
				if ( $thumbnail ) {
					$list .= '<div class="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . '-thumbnail">' . $thumbnail . '</div>';
				}
			}
		}

		$list .= '<div class="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . '-info">';

		// --- link to post ---
		$list .= '<div class="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . '-title">';
		$permalink = get_permalink( $post['ID'] );
		$timestamp = mysql2date( $dateformat . ' ' . $timeformat, $post['post_date'], false );
		$title = __( 'Published on ', 'radio-station' ) . $timestamp;
		$list .= '<a href="' . esc_url( $permalink ) . '" title="' . esc_attr( $title ) . '">';
		$list .= esc_attr( $post['post_title'] );
		$list .= '</a>';
		$list .= '</div>';

		// --- post excerpt ---
		$post_content = $post['post_content'];
		$post_id = $post['ID'];
		if ( 'none' == $atts['content'] ) {
			$list .= '';
		} elseif ( 'full' == $atts['content'] ) {
			$list .= '<div class="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . '-content">';
			$content = apply_filters( 'radio_station_' . $profile_type . '_' . $type . '_content', $post_content, $post_id );
			$list .= $content;
			$list .= '</div>';
		} else {
			$list .= '<div class="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . '-excerpt">';
			$permalink = get_permalink( $post['ID'] );
			if ( !empty( $post['post_excerpt'] ) ) {
				$excerpt = $post['post_excerpt'];
				$excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
			} else {
				$excerpt = radio_station_trim_excerpt( $post_content, $length, $more, $permalink );
			}
			$excerpt = apply_filters( 'radio_station_' . $profile_type . '_' . $type . '_excerpt', $excerpt, $post_id );
			$list .= $excerpt;
			$list .= '</div>';
		}

		$list .= '</div>';

		// --- close item div ---
		$list .= '</div>';
		$j ++;
	}

	// --- close last page div ---
	$list .= '</div>';

	// --- list pagination ---
	if ( $atts['pagination'] && ( $post_pages > 1 ) ) {
		$list .= '<br><br>';
		$list .= '<div id="' . esc_attr( $profile_type ) . '-' . esc_attr( $author_id ) . '-' . esc_attr( $type ) . 's-page-buttons" class="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . 's-page-buttons">';
		$list .= '<div id="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . 's-pagination-button-left" class="' . esc_attr( $profile_type ) . '-pagination-button arrow-button-left arrow-button" onclick="radio_list_page(\'' . esc_js( $profile_type ) . '\', ' . esc_js( $author_id ) . ', \'' . esc_js( $type ) . 's\', \'prev\');" style="display:none;">';
		$list .= '<a href="javascript:void(0);">&larr;</a>';
		$list .= '</div>';
		for ( $pagenum = 1; $pagenum < ( $post_pages + 1 ); $pagenum ++ ) {
			if ( 1 == $pagenum ) {
				$active = ' active';
			} else {
				$active = '';
			}
			$onclick = 'radio_list_page(\'' . esc_js( $profile_type ) . '\', ' . esc_js( $author_id ) . ', \'' . esc_js( $type ) . 's\', ' . esc_js( $pagenum ) . ');';
			$list .= '<div id="' . esc_attr( $profile_type ) . '-' . esc_attr( $author_id ) . '-' . esc_attr( $type ). 's-page-button-' . esc_attr( $pagenum ) . '" class="' . esc_attr( $profile_type ) . '-' . esc_attr( $author_id ) . '-' . esc_attr( $type ) . 's-page-button ' . esc_attr( $profile_type ) . '-pagination-button' . esc_attr( $active ) . '" onclick="' . $onclick . '">';
			$list .= '<a href="javascript:void(0);">';
			$list .= esc_html( $pagenum );
			$list .= '</a>';
			$list .= '</div>';
		}
		$list .= '<div id="' . esc_attr( $profile_type ) . '-' . esc_attr( $type ) . 's-pagination-button-right" class="' . esc_attr( $profile_type ) . '-pagination-button arrow-button-right arrow-button" onclick="radio_list_page(\'' . esc_js( $profile_type ) . '\', ' . esc_js( $author_id ) . ', \'' . esc_js( $type ). 's\', \'next\');">';
		$list .= '<a href="javascript:void(0);">&rarr;</a>';
		$list .= '</div>';
		$list .= '<input type="hidden" id="' . esc_attr( $profile_type ) . '-' . esc_attr( $author_id ) . '-' . esc_attr( $type ) . 's-current-page" value="1">';
		$list .= '<input type="hidden" id="' . esc_attr( $profile_type ) . '-' . esc_attr( $author_id ) . '-' . esc_attr( $type ) . 's-page-count" value="' . esc_attr( $post_pages ) . '">';
		$list .= '</div>';
	}

	// --- close list div ---
	$list .= '</div>';

	// --- enqueue shortcode styles ---
	// TODO: enqueue *pro* shortcode styles
	radio_station_enqueue_style( 'shortcodes' );

	// --- enqueue pagination javascript ---
	add_action( 'wp_footer', 'radio_station_list_pagination_javascript' );

	// --- filter and return ---
	$list = apply_filters( 'radio_station_' . $profile_type . '_' . $type . '_list', $list, $atts );
	return $list;

}

// ----------------------------
// Host Posts Archive Shortcode
// ----------------------------
// requires: author shortcode attribute, eg. [host-posts-archive id="1"]
add_shortcode( 'host-posts-archive', 'radio_station_pro_host_posts_archive' );
add_shortcode( 'host-post-archive', 'radio_station_pro_host_posts_archive' );
function radio_station_pro_host_posts_archive( $atts ) {
	$output = radio_station_pro_profile_list_shortcode( 'host', 'post', $atts );
	return $output;
}

// --------------------------------
// Producer Posts Archive Shortcode
// --------------------------------
add_shortcode( 'producer-posts-archive', 'radio_station_pro_producer_posts_archive' );
add_shortcode( 'producer-post-archive', 'radio_station_pro_producer_posts_archive' );
function radio_station_pro_producer_posts_archive( $atts ) {
	$output = radio_station_pro_profile_list_shortcode( 'producer', 'post', $atts );
	return $output;
}

// ----------------------------
// Host Shows Archive Shortcode
// ----------------------------
add_shortcode( 'host-shows-archive', 'radio_station_pro_profile_shows_archive' );
add_shortcode( 'host-show-archive', 'radio_station_pro_profile_shows_archive' );
function radio_station_pro_profile_shows_archive( $atts ) {
	$output = radio_station_pro_profile_list_shortcode( 'host', 'show', $atts );
	return $output;
}

// --------------------------------
// Producer Shows Archive Shortcode
// --------------------------------
add_shortcode( 'producer-shows-archive', 'radio_station_pro_producer_shows_archive' );
add_shortcode( 'producer-show-archive', 'radio_station_pro_producer_shows_archive' );
function radio_station_pro_producer_shows_archive( $atts ) {
	$output = radio_station_pro_profile_list_shortcode( 'producer', 'show', $atts );
	return $output;
}

// -------------------------------
// Host Episodes Archive Shortcode
// -------------------------------
add_shortcode( 'host-episodes-archive', 'radio_station_pro_host_episodes_archive' );
add_shortcode( 'host-episode-archive', 'radio_station_pro_host_episodes_archive' );
function radio_station_pro_host_episodes_archive( $atts ) {
	$output = radio_station_pro_profile_list_shortcode( 'host', 'episode', $atts );
	return $output;
}

// -----------------------------------
// Producer Episodes Archive Shortcode
// -----------------------------------
add_shortcode( 'producer-episodes-archive', 'radio_station_pro_producer_episodes_archive' );
add_shortcode( 'producer-episode-archive', 'radio_station_pro_producer_episodes_archive' );
function radio_station_pro_producer_episodes_archive( $atts ) {
	$output = radio_station_pro_profile_list_shortcode( 'producer', 'episode', $atts );
	return $output;
}

// ------------------------------------
// Archive Shortcode No Records Message
// ------------------------------------
add_filter( 'radio_station_archive_shortcode_no_records', 'radio_station_pro_archive_shortcode_no_records', 10, 2 );
function radio_station_pro_archive_shortcode_no_records( $message, $post_type ) {

	$post_types = array( RADIO_STATION_EPISODE_SLUG, RADIO_STATION_EPISODE_SLUG, RADIO_STATION_EPISODE_SLUG );
	if ( in_array( $post_type, $post_types ) ) {
		$post_type_object = get_post_type_object( $post_type );
		$plural_label = $post_type_object->labels->name;
		$message = __( 'No %s were found to display.', 'radio-station' );
		$message = sprintf( $message, $plural_label );
	}

	return $message;
}

