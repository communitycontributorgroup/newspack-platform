<?php

// === Episode Content Template ===
// Package: radio-station
// Author: Tony Hayes
// @since 2.3.4

// -----------------
// Set Template Data
// -----------------

// --- get profile data ---
global $radio_station_data, $post;
$radio_station_data['doing-template'] = true;
$post_id = $radio_station_data['episode-id'] = $post->ID;
$post_type = $post->post_type;
$type = 'episode';

// --- set labels ---
$post_type_object = get_post_type_object( $post_type );
$singular_label = $post_type_object->labels->singular_name;
$plural_label = $post_type_object->labels->name;

// --- set new line pseudonym ---
$newline = RADIO_STATION_DEBUG ? "\n" : '';

// --- get schedule time format ---
// $time_format = (int) radio_station_get_setting( 'clock_time_format', $post_id );

// --- get episode meta data ---
$episode_title = get_the_title( $post_id );
$avatar_id = get_post_meta( $post_id, 'episode_avatar', true );
// $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
$show_id = get_post_meta( $post_id, 'episode_show_id', true );
$media_type = get_post_meta( $post_id, 'episode_type', true );
$episode_url = false;
if ( 'url' == $media_type ) {
	$episode_url = get_post_meta( $post_id, 'episode_file_url', true );
} elseif ( 'media' == $media_type ) {
	$media_id = get_post_meta( $post_id, 'episode_media_id', true );
	if ( $media_id ) {
		$episode_url = wp_get_attachment_url( $media_id );
	}
}
$episode_number = get_post_meta( $post_id, 'episode_number', true );
$episode_air_date = get_post_meta( $post_id, 'episode_air_date', true );
$episode_air_time = get_post_meta( $post_id, 'episode_air_time', true );
$episode_playlist = get_post_meta( $post_id, 'episode_playlist', true );

// --- show patreon ---
$show_patreon = false;
if ( $show_id ) {
	$show_patreon = get_post_meta( $show_id, 'show_patreon', true );
}
$show_rss = false;
// $show_rss = get_post_meta( $show_id, 'show_rss', true );

// --- downloadable / disabled ---
$episode_download = true;
$download = get_post_meta( $post_id, 'episode_download', true );
if ( 'on' == $download ) {
	// note: on = disabled
	$episode_download = false;
}

// --- filter all meta data ---
$episode_title = apply_filters( 'radio_station_episode_title', $episode_title, $post_id );
$avatar_id = apply_filters( 'radio_station_episode_avatar', $avatar_id, $post_id );
// $thumbnail_id = apply_filters( 'radio_station_episode_thumbnail', $thumbnail_id, $post_id );
$show_patreon = apply_filters( 'radio_station_show_patreon', $show_patreon, $post_id );
$episode_url = apply_filters( 'radio_station_episode_url', $episode_url, $post_id );
$episode_download = apply_filters( 'radio_station_episode_download', $episode_download, $post_id );
$episode_playlist = apply_filters( 'radio_station_episode_playlist', $episode_playlist, $post_id );
// $show_rss = apply_filters( 'radio_station_episode_rss', $show_rss, $post_id );
$social_icons = apply_filters( 'radio_station_episode_social_icons', false, $post_id );

// --- create icon display early ---
$episode_icons = array();
$icon_colors = radio_station_get_icon_colors( 'episode-page' );

// --- Show RSS feed icon ---
/* if ( $show_rss ) {
	// $feed_url = radio_station_get_show_rss_url( $show_id );
	$title =  __( 'Show RSS Feed', 'radio-station' );
	$title = apply_filters( 'radio_station_show_rss_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['rss'] ) . ';" class="dashicons dashicons-rss" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_show_rss_icon', $icon, $post_id );
	$episode_icons['rss'] = '<div class="episode-icon episode-rss">' . $newline;
	$episode_icons['rss'] .= '<a href="' . esc_url( $feed_url ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
	$episode_icons['rss'] .= $icon . $newline;
	$episode_icons['rss'] .= '</a>' . $newline;
	$episode_icons['rss'] .= '</div>' . $newline;
} */

// --- filter icons ---
$episode_icons = apply_filters( 'radio_station_episode_page_icons', $episode_icons, $post_id );

// --- check for show ---
$episode_show = get_post( $show_id );
if ( $episode_show ) {
	$show_title = get_the_title( $show_id );
}

// --- check for playlist ---
if ( $episode_playlist ) {
	$playlist = get_post( $episode_playlist );
}

// --- get layout display settings ----
$block_position = radio_station_get_setting( 'episode_block_position' );
$block_position = apply_filters( 'radio_station_episode_block_position', $block_position );
$section_layout = radio_station_get_setting( 'episode_section_layout' );
$section_layout = apply_filters( 'radio_station_episode_section_layout', $section_layout );
$jump_links = apply_filters( 'radio_station_episode_jump_links', 'yes', $post_id );


// --------------------------
// === Set Blocks Content ===
// --------------------------

// --- set empty blocks ---
$blocks = array( 'episode_images' => '', 'episode_meta' => '' );

// --------------------
// Profile Images Block
// --------------------
$image_blocks = array();

// --- Episode Avatar ---
if ( $avatar_id ) { // $thumbnail_id

	// --- get episode avatar (with thumbnail fallback) ---
	$size = apply_filters( 'radio_station_episode_avatar_size', 'medium', $post_id, 'episode-page' );
	$attr = array( 'class' => 'episode-image' );
	if ( $episode_title ) {
		$attr['alt'] = $attr['title'] = $episode_title;
	}
	$episode_avatar = radio_station_pro_get_episode_avatar( $post_id, $size, $attr );
	if ( $episode_avatar ) {
		$image_blocks['avatar'] = '<div class="episode-avatar">' . $newline;
		$image_blocks['avatar'] .= $episode_avatar;
		$image_blocks['avatar'] .= '</div>' . $newline;
	}
}

// --- Episode Icons ---
if ( count( $episode_icons ) > 0 ) {
	$image_blocks['icons'] = '<div class="episode-icons">';
	$image_blocks['icons'] .= implode( $newline, $episode_icons );
	$image_blocks['icons'] .= '</div>';
}

// --- Social Icons ---
if ( $social_icons ) {
	$social_icons = apply_filters( 'radio_station_episode_social_icons_display', '' );
	if ( '' != $social_icons ) {
		$image_blocks['social'] = '<div id="episode-social-icons" class="social-icons">' . $newline;
		$image_blocks['social'] .= $social_icons;
		$image_blocks['social'] .= '</div>' . $newline;
	}
}

// --- Show Patreon Button ---
$patreon_button = '';
if ( $show_patreon ) {
	$patreon_button .= '<div class="episode-patreon">';
	$patreon_title = __( 'Become a Supporter for', 'radio-station' ) . ' ' . $show_title;
	$patreon_title = apply_filters( 'radio_station_episode_patreon_title', $patreon_title, $post_id );
	$patreon_button .= radio_station_patreon_button( $show_patreon, $patreon_title );
	$patreon_button .= '</div>';
}
$patreon_button = apply_filters( 'radio_station_episode_patreon_button', $patreon_button, $post_id );
if ( '' != $patreon_button ) {
	$image_blocks['patreon'] = $patreon_button;
}

// --- filter image blocks and block order ---
$image_blocks = apply_filters( 'radio_station_episode_images_blocks', $image_blocks, $post_id );
$image_block_order = array( 'avatar', 'icons', 'social', 'patreon' );
$image_block_order = apply_filters( 'radio_station_episode_image_block_order', $image_block_order, $post_id );
if ( RADIO_STATION_DEBUG ) {
	echo '<span style="display:none;">Image Block Order: ' . print_r( $image_block_order, true ) . '</span>';
	echo '<!-- Image Blocks: ' . print_r( $image_blocks, true ) . ' -->';
}

// --- combine image blocks to images block ---
if ( is_array( $image_blocks ) && ( count( $image_blocks ) > 0 )
  && is_array( $image_block_order ) && ( count( $image_block_order ) > 0 ) ) {
	$blocks['episode_images'] .= '<div class="episode-controls">';
	foreach ( $image_block_order as $image_block ) {
		if ( isset( $image_blocks[$image_block] ) ) {
			$blocks['episode_images'] .= $image_blocks[$image_block];
		}
	}
	$blocks['episode_images'] .= '</div>' . $newline;
}


// ------------------
// Episode Meta Block
// ------------------
$meta_blocks = array();

// --- episode meta title ---
$label = __( 'Episode Info', 'radio-station' );
$label = apply_filters( 'radio_station_episode_info_label', $label, $post_id );
$blocks['episode_meta'] = '<h4 class="episode-info-label">' . esc_html( $label ) . '</h4>' . $newline;

// --- episode show ---
if ( $episode_show ) {
	$show_permalink = get_permalink( $show_id );
	$meta_blocks['show'] = '<span class="episode-show-label episode-label">' . esc_html( __( 'Show', 'radio-station' ) ) . '</span>: ';
	$meta_blocks['show'] .= '<span class="episode-show"><a href="' . esc_url( $show_permalink ) . '">';
	$meta_blocks['show'] .= esc_html( $show_title ) . '</a></span><br>' . $newline;
}

// --- episode number ---
if ( $episode_number ) {
	$meta_blocks['number'] = '<span class="episode-number-label episode-label">' . esc_html( __( 'Episode Number', 'radio-station' ) ) . '</span>: ';
	$meta_blocks['number'] .= '<span class="episode-number">' . esc_html( $episode_number ) . '</span><br>' . $newline;
}

// --- air date / time ---
if ( $episode_air_date ) {

	$meta_blocks['aired'] = '<span class="episode-broadcasted-label episode-label">' . esc_html( __( 'Broadcasted', 'radio-station' ) ) . '</span>: ';

	if ( $episode_air_time ) {
		$times = explode( ':', $episode_air_time );
		if ( '' != $times[2] ) {
			$meta_blocks['aired'] .= '<span class="episode-broadcast-time">';
			$format = radio_station_get_setting( 'clock_time_format' );
			$format = apply_filters( 'radio_station_episode_display_time_format', $format );
			if ( 24 == $format ) {
				if ( 'pm' == $times[2] ) {
					$times[0] = (int) $times[0] + 12;
				}
				$meta_blocks['aired'] .= esc_html( $times[0] ) . ':' . esc_html( $times[1] );
			}			
			if ( 12 == $format ) {
				$meta_blocks['aired'] .= esc_html( (int) $times[0] ) . ':' . esc_html( $times[1] );
				if ( 'am' == $times[2] ) {
					$meta_blocks['aired'] .= ' ' . radio_station_translate_meridiem( 'am' );
				} elseif ( 'pm' == $times[2] ) {
					$meta_blocks['aired'] .= ' ' . radio_station_translate_meridiem( 'pm' );
				}
			}
			$meta_blocks['aired'] .= '</span><br>' . $newline;
			$meta_blocks['aired'] .= '<span class="episode-label-spacer">&nbsp;</span>';
			$meta_blocks['aired'] .= ' ' . esc_html( __( 'on', 'radio-station' ) ) . ' ';
		}
	}

	$date_format = apply_filters( 'radio_station_episode_date_format', 'jS' );
	$date = radio_station_get_time( $date_format, strtotime( $episode_air_date ) );
	$month = radio_station_get_time( 'F', strtotime( $episode_air_date ) );
	$month = radio_station_translate_month( $month );	
	$year = radio_station_get_time( 'Y', strtotime( $episode_air_date ) );
	$meta_blocks['aired'] .= '<span class="episode-broadcast-date">';
	$meta_blocks['aired'] .= esc_html( $date . ' ' . $month . ' ' . $year ) . '</span>';
	
}

/* TODO: Episode Series */

/* TODO: Topics (Taxonomy) */

/* TODO: Guests (Taxonomy) */


// --- Show DJs / Hosts (check for override ) ---
/* if ( $hosts ) {
	$meta_blocks['hosts'] = '<div class="episode-djs episode-hosts">' . $newline;
	$label = __( 'Hosted by', 'radio-station' );
	$label = apply_filters( 'radio_station_episode_hosts_label', $label, $post_id );
	$meta_blocks['hosts'] .= '<span class="episode-hosts-label episode-label">' . esc_html( $label ) . '</span>: ' . $newline;
	$count = 0;
	$host_count = count( $hosts );
	foreach ( $hosts as $host ) {
		$count ++;
		$user_info = get_userdata( $host );

		// --- DJ / Host URL and/or display ---
		$host_url = radio_station_get_host_url( $host );
		if ( $host_url ) {
			$meta_blocks['hosts'] .= '<a href="' . esc_url( $host_url ) . '">';
		}
		$meta_blocks['hosts'] .= esc_html( $user_info->display_name );
		if ( $host_url ) {
			$meta_blocks['hosts'] .= '</a>' . $newline;
		}

		if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
		     || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
			$meta_blocks['hosts'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
		} elseif ( ( count( $hosts ) > 2 ) && ( $count < count( $hosts ) ) ) {
			$meta_blocks['hosts'] .= ', ';
		}
	}
	$meta_blocks['hosts'] .= '</div>' . $newline;
} */

// --- Show Producers (check for override) ---
/* if ( $producers ) {
	$meta_blocks['producers'] = '<div class="episode-producers">' . $newline;
	$label = __( 'Produced by', 'radio-station' );
	$label = apply_filters( 'radio_station_episode_producers_label', $label, $post_id );
	$meta_blocks['producers'] .= '<span class="episode-producers-label episode-label">' . esc_html( $label ) . '</span>: ' . $newline;
	$count = 0;
	$producer_count = count( $producers );
	foreach ( $producers as $producer ) {
		$count ++;
		$user_info = get_userdata( $producer );

		// --- Producer URL / display ---
		$producer_url = radio_station_get_producer_url( $producer );
		if ( $producer_url ) {
			$meta_blocks['producers'] .= '<a href="' . esc_url( $producer_url ) . '">';
		}
		$meta_blocks['producers'] .= esc_html( $user_info->display_name );
		if ( $producer_url ) {
			$meta_blocks['producers'] .= '</a>' . $newline;
		}

		if ( ( ( 1 === $count ) && ( 2 === $producer_count ) )
		     || ( ( $producer_count > 2 ) && ( ( $producer_count - 1 ) === $count ) ) ) {
			$meta_blocks['producers'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
		} elseif ( ( count( $producers ) > 2 ) && ( $count < count( $producers ) ) ) {
			$meta_blocks['producers'] .= ', ';
		}
	}
	$meta_blocks['producers'] .= '</div>' . $newline;
} */

// --- Show Genre(s) (check for override) ---
/* if ( $genres ) {
	$tax_object = get_taxonomy( RADIO_STATION_GENRES_SLUG );
	if ( count( $genres ) == 1 ) {
		$label = $tax_object->labels->singular_name;
	} else {
		$label = $tax_object->labels->name;
	}
	$label = apply_filters( 'radio_station_episode_genres_label', $label, $post_id );
	$meta_blocks['genres'] = '<div class="episode-genres">' . $newline;
	$meta_blocks['genres'] .= '<span class="episode-genres-label episode-label">' . esc_html( $label ) . '</span>: ' . $newline;
	$genre_links = array();
	foreach ( $genres as $genre ) {
		$genre_link = get_term_link( $genre );
		$genre_links[] = '<a href="' . esc_url( $genre_link ) . '">' . esc_html( $genre->name ) . '</a>' . $newline;
	}
	$meta_blocks['genres'] .= implode( ', ', $genre_links ) . $newline;
	$meta_blocks['genres'] .= '</div>' . $newline;
} */

// --- Show Language(s) (check for override) ---
/* if ( $languages ) {
	$tax_object = get_taxonomy( RADIO_STATION_LANGUAGES_SLUG );
	if ( count( $languages ) == 1 ) {
		$label = $tax_object->labels->singular_name;
	} else {
		$label = $tax_object->labels->name;
	}
	$label = apply_filters( 'radio_station_show_languages_label', $label, $post_id );
	$meta_blocks['languages'] = '<div class="episode-languages">' . $newline;
	$meta_blocks['languages'] .= '<span class="episode-languages-label episode-label">' . esc_html( $label ) . '</span>: ' . $newline;
	$language_links = array();
	foreach ( $languages as $language ) {
		$lang_label = $language->name;
		if ( !empty( $language->description ) ) {
			$lang_label .= ' (' . $language->description . ')';
		}
		$language_link = get_term_link( $language );
		$language_links[] = '<a href="' . esc_url( $language_link ) . '">' . esc_html( $lang_label ) . '</a>' . $newline;
	}
	$meta_blocks['languages'] .= implode( ', ', $language_links ) . $newline;
	$meta_blocks['languages'] .= '</div>' . $newline;
} */

// --- filter meta blocks and order ---
$meta_blocks = apply_filters( 'radio_station_episode_meta_blocks', $meta_blocks, $post_id );
$meta_block_order = array( 'show', 'number', 'aired', 'topics', 'hosts', 'guests', 'producers', 'genres', 'languages' );
$meta_block_order = apply_filters( 'radio_station_episode_meta_block_order', $meta_block_order, $post_id );
if ( RADIO_STATION_DEBUG ) {
	echo '<span style="display:none;">Meta Block Order: ' . print_r( $meta_block_order, true ) . '</span>';
	echo '<!-- Meta Blocks: ' . print_r( $meta_blocks, true ) . ' -->';
}

// --- combine meta blocks to meta block ---
if ( is_array( $meta_blocks ) && ( count( $meta_blocks ) > 0 )
  && is_array( $meta_block_order ) && ( count( $meta_block_order ) > 0 ) ) {
	foreach ( $meta_block_order as $meta_block ) {
		if ( isset( $meta_blocks[$meta_block] ) ) {
			$blocks['episode_meta'] .= $meta_blocks[$meta_block];
		}
	}
}

// --- filter all info blocks ---
$blocks = apply_filters( 'radio_station_episode_page_blocks', $blocks, $post_id );


// ----------------------------
// === Set Episode Sections ===
// ----------------------------

// -----------------------
// Set Episode Description
// -----------------------
$episode_description = false;
if ( strlen( trim( $content ) ) > 0 ) {
	$episode_description = '<div class="episode-desc-content">' . $content . '</div>' . $newline;
	$episode_description .= '<div id="show-more-overlay"></div>' . $newline;
	$show_desc_buttons = '<div id="show-desc-buttons">' . $newline;
	$label = __( 'Show More', 'radio-station' );
	$label = apply_filters( 'radio_station_show_more_label', $label, $post_id, 'episode' );
	$show_desc_buttons .= '	<input type="button" id="show-desc-more" onclick="radio_show_desc(\'more\');" value="' . esc_html( $label ) . '">' . $newline;
	$label = __( 'Show Less', 'radio-station' );
	$label = apply_filters( 'radio_station_show_less_label', $label, $post_id, 'episode' );
	$show_desc_buttons .= '	<input type="button" id="show-desc-less" onclick="radio_show_desc(\'less\');" value="' . esc_html( $label ) . '">' . $newline;
	$show_desc_buttons .= '	<input type="hidden" id="show-desc-state" value="">' . $newline;
	$show_desc_buttons .= '</div>' . $newline;
}

// -----------------
// Epsisode Sections
// -----------------
$sections = array();

// --- Episode Player ---
if ( $episode_url ) {

	$sections['player']['heading'] = '<a name="episode-player"></a>' . $newline;
	$label = __( 'Listen to this %s', 'radio-station' );
	$label = sprintf( $label, $singular_label );
	$label = apply_filters( 'radio_station_episode_player_label', $label, $post_id );
	$sections['player']['heading'] .= '<h3 id="episode-section-player">' . esc_html( $label ) . '</h3>' . $newline;
	$anchor = __( 'Player', 'radio-station' );
	$anchor = apply_filters( 'radio_station_episode_player_anchor', $anchor, $post_id );
	$sections['player']['anchor'] = $anchor;

	// --- Player Embed ---
	// $player = '<div class="' . esc_attr( $type ) . '-player">' . $newline;
	$label = apply_filters( 'radio_station_episode_player_label', '', $post_id );
	$player = '';
	if ( $label && ( '' != $label ) ) {
		$player .= '<span class="episode-player-label episode-label">' . esc_html( $label ) . '</span><br>';
	}
	$shortcode = '[audio src="' . $episode_url . '" preload="metadata"]';
	$player_embed = do_shortcode( $shortcode );
	$player .= '<div class="episode-embed">' . $newline;
	$player .= $player_embed . $newline;
	$player .= '</div>' . $newline;

	// --- Download Audio Icon ---
	if ( $episode_download ) {
		$title = __( 'Download this Episode', 'radio-station' );
		$title = apply_filters( 'radio_station_episode_download_title', $title, $post_id );
		$player .= '<div class="episode-download">' . $newline;
		$player .= '<a href="' . esc_url( $episode_url ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
		$player .= '<span style="color:' . esc_attr( $icon_colors['download'] ) . ';" class="dashicons dashicons-download" aria-hidden="true"></span>' . $newline;
		$player .= '</a>' . $newline;
		$player .= '</div>' . $newline;
	}
	// $player .= '</div><span>3</span>' . $newline;

	// --- Player Content ---
	$sections['player']['content'] = '<div id="episode-player"><br>' . $newline;
	$sections['player']['content'] .= $player;
	$sections['player']['content'] .= '</div>' . $newline;
}

// --- About Episode (Post Content) ---
if ( $episode_description ) {

	$sections['about']['heading'] = '<a name="episode-description"></a>' . $newline;
	$label = __( 'About this %s', 'radio-station' );
	$label = sprintf( $label, $singular_label );
	$label = apply_filters( 'radio_station_episode_description_label', $label, $post_id );
	$sections['about']['heading'] .= '<h3 id="episode-section-about">' . esc_html( $label ) . '</h3>' . $newline;
	$anchor = __( 'About', 'radio-station' );
	$anchor = apply_filters( 'radio_station_episode_description_anchor', $anchor, $post_id );
	$sections['about']['anchor'] = $anchor;

	$sections['about']['content'] = '<div id="episode-about"><br>' . $newline;
	$sections['about']['content'] .= '<div id="episode-description" class="episode-description">' . $newline;
	$sections['about']['content'] .= $episode_description;
	$sections['about']['content'] .= '</div>' . $newline;
	$sections['about']['content'] .= $show_desc_buttons;
	$sections['about']['content'] .= '</div>' . $newline;
}

// --- Episode Show Tab ---
if ( $episode_show ) {

	$sections['show']['heading'] = '<a name="episode-shows">';
	$label = __( '%s Show', 'radio-station' );
	$label = sprintf( $label, $singular_label );
	$label = apply_filters( 'radio_station_episode_show_label', $label, $post_id );
	$sections['show']['heading'] .= '<h3 id="episode-section-show">' . esc_html( $label ) . '</h3>' . $newline;
	$show_post_type = get_post_type_object( RADIO_STATION_SHOW_SLUG );	
	$show_label = $show_post_type->labels->singular_name;
	$anchor = apply_filters( 'radio_station_episode_show_anchor', $show_label, $post_id );
	$sections['show']['anchor'] = $anchor;

	$sections['show']['content'] = '<div id="episode-show" class="' . esc_attr( $type ) . '-section-content"><br>' . $newline;
	// $radio_station_data['episode-show'] = $episode_show;
	$shortcode = '[shows-archive show="' . $show_id . '"]';
	$shortcode = apply_filters( 'radio_station_episode_page_show_shortcode', $shortcode, $show_id );
	$sections['show']['content'] .= do_shortcode( $shortcode );
	$sections['show']['content'] .= '</div>' . $newline;
}

// --- Episode Playlist Tab ---
if ( $episode_playlist ) {

	$sections['playlist']['heading'] = '<a name="' . esc_attr( $type ) . '-playlist">';
	$label = __( '%s Playlist', 'radio-station' );
	$label = sprintf( $label, $singular_label );
	$label = apply_filters( 'radio_station_episode_playlist_label', $label, $post_id );
	$sections['playlist']['heading'] .= '<h3 id="episode-section-playlist">' . esc_html( $label ) . '</h3>' . $newline;
	$playlist_post_type = get_post_type_object( RADIO_STATION_PLAYLIST_SLUG );
	$playlist_label = $playlist_post_type->labels->singular_name;
	// TODO: maybe check for multiple playlists and use plural label ?
	$anchor = apply_filters( 'radio_station_episode_playlist_anchor', $playlist_label, $post_id );
	$sections['playlist']['anchor'] = $anchor;

	$sections['playlist']['content'] = '<div id="episode-playlist" class="episode-section-content"><br>' . $newline;
	// $radio_station_data['episode-playlist'] = $episode_playlist;
	$shortcode = '[playlist-archive playlist="' . $episode_playlist . '"]';
	$shortcode = apply_filters( 'radio_station_episode_page_playlist_shortcode', $shortcode, $episode_playlist );
	$sections['playlist']['content'] .= do_shortcode( $shortcode );
	$sections['playlist']['content'] .= '</div>' . $newline;
}

$sections = apply_filters( 'radio_station_' . $type . '_page_sections', $sections, $post_id );


// -----------------------
// === Template Output ===
// -----------------------

// --- set content classes ---
$class = 'left-blocks';
if ( in_array( $block_position, array( 'left', 'right', 'top' ) ) ) {
	$class = $block_position . '-blocks';
}

echo '<!-- #episode-content -->' . $newline;
echo '<div id="episode-content" class="' .  esc_attr( $class ) . '">' . $newline;
echo '<input type="hidden" id="radio-page-type" value="episode">' . $newline;

	// --- Info Blocks --- 
	echo '<div id="episode-info" class="episode-info">' . $newline;

		// --- filter block order ---
		$block_order = array( 'episode_images', 'episode_meta' );
		$block_order = apply_filters( 'radio_station_episode_page_block_order', $block_order, $post_id );

		// --- loop blocks ---
		if ( is_array( $block_order ) && ( count( $block_order ) > 0 ) ) {
			foreach ( $block_order as $i => $block ) {
				if ( isset( $blocks[$block] ) && ( '' != trim( $blocks[$block] ) ) ) {

					// --- set block classes ---
					$classes = array( 'episode-block' );
					$classes[] = str_replace( '_', '-', $block );
					if ( 0 == $i ) {
						$classes[] = 'first-block';
					} elseif ( count( $block_order ) == ( $i + 1 ) ) {
						$classes[] = 'last-block';
					}
					$class = implode( ' ', $classes );

					// --- output blocks ---
					echo '<div class="' . esc_attr( $class ) . '">' . $newline;
					// phpcs:ignore WordPress.Security.OutputNotEscaped
					echo $blocks[$block] . $newline;
					echo '</div>' . $newline;

					$first = '';
				}
			}
		}
	echo '</div>' . $newline;
	
	// --- Episode Sections ---
	echo '<div class="episode-sections">' . $newline;

		// --- filter section order ---
		$section_order = array( 'player', 'about', 'show', 'playlist' );
		$section_order = apply_filters( 'radio_station_episode_page_section_order', $section_order, $post_id );

		// --- Display Sections ---
		if ( ( is_array( $sections ) && ( count( $sections ) > 0 ) )
		     && is_array( $section_order ) && ( count( $section_order ) > 0 ) ) {

			// --- tabs for tabbed layout ---
			if ( 'tabbed' == $section_layout ) {

				// --- output first section as non-tabbed ---
				// (default is player at top) 
				if ( isset( $sections[$section_order[0]] ) ) {
					// phpcs:ignore WordPress.Security.OutputNotEscaped
					echo $sections[$section_order[0]]['heading'];
					echo $sections[$section_order[0]]['content'];
				}
				unset( $section_order[0] );
				echo '<p>&nbsp;</p>';

				// --- also output second section as non-tabbed ---
				// (default is description content)
				if ( isset( $sections[$section_order[1]] ) ) {
					// phpcs:ignore WordPress.Security.OutputNotEscaped
					echo $sections[$section_order[1]]['heading'];
					echo $sections[$section_order[1]]['content'];
				}
				unset( $section_order[1] );

				echo '<div class="episode-tabs">' . $newline;

					$i = 0;
					$found_section = false;
					foreach ( $section_order as $section ) {
						if ( isset( $sections[$section] ) ) {
							$found_section = true;
							$class = ( 0 == $i ) ? 'tab-active' : 'tab-inactive';
							// 2.4.1.8: added prefix argument to javascript function
							echo '<div id="episode-' . esc_attr( $section ) . '-tab" class="episode-tab ' . esc_attr( $class ) . '" onclick="radio_show_tab(\'episode\',\'' . esc_attr( $section ) . '\');">' . $newline;
							echo esc_html( $sections[$section]['anchor'] );
							echo '</div>' . $newline;
							if ( ( $i + 1 ) < count( $sections ) ) {
								echo '<div class="episode-tab-spacer">&nbsp;</div>' . $newline;
							}
							$i++;
						}
					}
					if ( $found_section ) {
						echo '<div class="episode-tab-spacer">&nbsp;</div>' . $newline;
					}
				echo '</div>' . $newline;
			}

			echo '<div class="episode-section">' . $newline;
			$i = 0;
			foreach ( $section_order as $section ) {
				if ( isset( $sections[$section] ) ) {

					if ( 'tabbed' != $section_layout ) {

						// --- section heading ---
						// phpcs:ignore WordPress.Security.OutputNotEscaped
						echo $sections[$section]['heading'] . $newline;

						// --- section jump links ---
						if ( 'yes' == $jump_links ) {
							echo '<div class="episode-jump-links">' . $newline;
							echo '<b>' . esc_html( __( 'Jump to', 'radio-station' ) ) . '</b>: ' . $newline;
							$found_link = false;
							foreach ( $section_order as $link ) {
								if ( isset( $sections[$link] ) && ( $link != $section ) ) {
									if ( $found_link ) {
										echo ' | ';
									}
									echo '<a href="javascript:void(0);" onclick="radio_scroll_link(\'' . esc_attr( $link ) . '\');">';
									echo esc_html( $sections[$link]['anchor'] );
									echo '</a>' . $newline;
									$found_link = true;
								}
							}
							echo '</div>' . $newline;
						}

					} else {

						// --- add tab classes to sections ---
						$classes = array( 'episode-tab' );
						if ( 0 == $i ) {
							$classes[] = 'tab-active';
						} else {
							$classes[] = 'tab-inactive';
						}
						$class = implode( ' ', $classes );
						$sections[$section]['content'] = str_replace( 'class="episode-section-content"', 'class="' . esc_attr( $class ) . '"', $sections[$section]['content'] );

					}

					// --- section content ---
					// phpcs:ignore WordPress.Security.OutputNotEscaped
					echo $sections[$section]['content'] . $newline;

					$i ++;
				}
			}

			echo '</div>' . $newline;

		}

	echo '</div>' . $newline;

echo '</div>' . $newline;
echo '<!-- /#episode-content -->' . $newline;

// --- enqueue show page script ---
radio_station_enqueue_script( 'radio-station-page', array( 'radio-station' ), true );

// --- maybe detect and switch to # tab ---
if ( 'tabbed' == $section_layout ) {
	$js = "setTimeout(function() {";
	$js .= " if (window.location.hash) {";
	$js .= "  hash = window.location.hash.substring(1);";
	$js .= "  if (hash.indexOf('episode-') > -1) {";
	$js .= "   tab = hash.replace('episode-', '');";
	$js .= "   radio_show_tab('episode',tab);";
	$js .= "  }";
	$js .= " }";
	$js .= "}, 500);";
	wp_add_inline_script( 'radio-station-page', $js );
}

$radio_station_data['doing-template'] = false;

