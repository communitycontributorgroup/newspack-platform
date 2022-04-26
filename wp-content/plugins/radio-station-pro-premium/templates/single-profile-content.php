<?php

// === Profile Content Template ===
// Package: radio-station
// Author: Tony Hayes
// @since 2.3.4

// -----------------
// Set Template Data
// -----------------

// --- get profile data ---
global $radio_station_data, $post;
$radio_station_data['doing-template'] = true;
$post_id = $radio_station_data['profile-id'] = $post->ID;
$post_type = $post->post_type;
$type = $radio_station_data['profile-type'] = str_replace( 'rs-', '', $post_type );
$author_id = $radio_station_data['author-id'] = get_post_meta( $post_id, $type . '_user_id', true );

// --- set labels ---
$post_type_object = get_post_type_object( $post_type );
$singular_label = $post_type_object->labels->singular_name;
$plural_label = $post_type_object->labels->name;

// --- set new line pseudonym ---
$newline = RADIO_STATION_DEBUG ? "\n" : '';

// --- get schedule time format ---
$time_format = (int) radio_station_get_setting( 'clock_time_format', $post_id );

// --- get meta data ---
$profile_title = get_the_title( $post_id );
$avatar_id = get_post_meta( $post_id, 'profile_avatar', true );
// $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );

// --- get icon / button data ---
$profile_link = get_post_meta( $post_id, 'profile_link', true );
$profile_email = get_post_meta( $post_id, 'profile_email', true );
$profile_phone = get_post_meta( $post_id, 'profile_phone', true );
$profile_patreon = get_post_meta( $post_id, 'profile_patreon', true );
// $profile_rss = get_post_meta( $post_id, 'profile_rss', true );
$profile_rss = false; // TEMP

// $profile_file = get_post_meta( $post_id, 'profile_file', true );
// $profile_download = true;
// $download = get_post_meta( $post_id, 'profile_download', true );
// if ( 'on' == $download ) {
	// note: on = disabled
//	$profile_download = false;
// }

// --- filter all meta data ---
$profile_title = apply_filters( 'radio_station_' . $type . '_title', $profile_title, $post_id );
$avatar_id = apply_filters( 'radio_station_' . $type . '_avatar', $avatar_id, $post_id );
// $thumbnail_id = apply_filters( 'radio_station_' . $type . '_thumbnail', $thumbnail_id, $post_id );
$profile_link = apply_filters( 'radio_station_' . $type . '_link', $profile_link, $post_id );
$profile_email = apply_filters( 'radio_station_' . $type . '_email', $profile_email, $post_id );
$profile_phone = apply_filters( 'radio_station_' . $type . '_phone', $profile_phone, $post_id );
$profile_patreon = apply_filters( 'radio_station_' . $type . '_patreon', $profile_patreon, $post_id );
// $profile_file = apply_filters( 'radio_station_' . $type . '_file', $profile_file, $post_id );
// $profile_download = apply_filters( 'radio_station_' . $type . '_download', $profile_download, $post_id );
// $profile_rss = apply_filters( 'radio_station_' . $type . '_rss', $profile_rss, $post_id );
$social_icons = apply_filters( 'radio_station_' . $type . '_social_icons', false, $post_id );

// --- create icon display early ---
$profile_icons = array();
$icon_colors = radio_station_get_icon_colors( $type . '-page' );

// --- home link icon ---
if ( $profile_link ) {
	$title = __( 'Visit %s Website', 'radio-station' );
	$title = sprintf( $title, $singular_label );
	$title = apply_filters( 'radio_station_' . $type . '_website_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['website'] ) . ';" class="dashicons dashicons-admin-links" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_' . $type . '_home_icon', $icon, $post_id );
	$profile_icons['home'] = '<div class="' . esc_attr( $type ) . '-icon ' . esc_attr( $type ) . '-website">' . $newline;
	$profile_icons['home'] .= '<a href="' . esc_url( $profile_link ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '" target="_blank">' . $newline;
	$profile_icons['home'] .= $icon;
	$profile_icons['home'] .= '</a>' . $newline;
	$profile_icons['home'] .= '</div>' . $newline;
}

// --- phone number icon ---
if ( $profile_phone ) {
	$title = __( '%s Phone Number', 'radio-station' );
	$title = sprintf( $title, $singular_label );
	$title = apply_filters( 'radio_station_' . $type . '_phone_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['phone'] ) . ';" class="dashicons dashicons-phone" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_' . $type . '_phone_icon', $icon, $post_id );
	$profile_icons['phone'] = '<div class="' . esc_attr( $type ) . '-icon ' . esc_attr( $type ) . '-phone">' . $newline;
	$profile_icons['phone'] .= '<a href="tel:' . esc_attr( $profile_phone ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
	$profile_icons['phone'] .= $icon . $newline;
	$profile_icons['phone'] .= '</a>' . $newline;
	$profile_icons['phone'] .= '</div>' . $newline;
}

// --- email icon ---
if ( $profile_email ) {
	$title = __( 'Email %s', 'radio-station' );
	$title = sprintf( $title, $singular_label );
	$title = apply_filters( 'radio_station_' . $type . '_email_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['email'] ) . ';" class="dashicons dashicons-email" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_' . $type . '_email_icon', $icon, $post_id );
	$profile_icons['email'] = '<div class="' . esc_attr( $type ) . '-icon ' . esc_attr( $type ) . '-email">' . $newline;
	$profile_icons['email'] .= '<a href="mailto:' . sanitize_email( $profile_email ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
	$profile_icons['email'] .= $icon . $newline;
	$profile_icons['email'] .= '</a>' . $newline;
	$profile_icons['email'] .= '</div>' . $newline;
}

// --- RSS feed icon ---
if ( $profile_rss ) {
	// $feed_url = radio_station_get_profile_rss_url( $author_id );
	$title =  __( '%s RSS Feed', 'radio-station' );
	$title = sprintf( $title, $singular_label );
	$title = apply_filters( 'radio_station_' . $type . '_rss_title', $title, $post_id );
	$icon = '<span style="color:' . esc_attr( $icon_colors['rss'] ) . ';" class="dashicons dashicons-rss" aria-hidden="true"></span>' . $newline;
	$icon = apply_filters( 'radio_station_' . $type . '_rss_icon', $icon, $post_id );
	$profile_icons['rss'] = '<div class="' . esc_attr( $type ) . '-icon ' . esc_attr( $type ) . '-rss">' . $newline;
	$profile_icons['rss'] .= '<a href="' . esc_url( $feed_url ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
	$profile_icons['rss'] .= $icon . $newline;
	$profile_icons['rss'] .= '</a>' . $newline;
	$profile_icons['rss'] .= '</div>' . $newline;
}

// --- filter icons ---
$profile_icons = apply_filters( 'radio_station_' . $type . '_page_icons', $profile_icons, $post_id );

// --- set related defaults ---
$profile_posts = $profile_shows = $profile_episodes = false;

// --- check for profile (authored) blog posts ---
$posts_per_page = radio_station_get_setting( 'show_posts_per_page' );
$posts_per_page = apply_filters( 'radio_station_' . $type . '_posts_per_page', $posts_per_page, $post_id );
if ( absint( $posts_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_' . $type . '_page_posts_limit', false, $post_id );
	$profile_posts = radio_station_pro_get_profile_posts( $author_id, array( 'limit' => $limit ) );
}

// --- check for profile (assigned) shows ---
$shows_per_page = radio_station_get_setting( 'show_posts_per_page' );
$posts_per_page = apply_filters( 'radio_station_' . $type . '_shows_per_page', $shows_per_page, $post_id );
if ( absint( $shows_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_' . $type . '_page_show_limit', false, $post_id );
	if ( 'host' == $type ) {
		$profile_shows = radio_station_pro_get_host_shows( $author_id, array( 'limit' => $limit ) );
	} elseif ( 'producer' == $type ) {
		$profile_shows = radio_station_pro_get_producer_shows( $author_id, array( 'limit' => $limit ) );
	}
}

// --- check for episodes ---
/* $episodes_per_page = radio_station_get_setting( 'profile_episodes_per_page' );
$profile_episodes = apply_filters( 'radio_station_' . $type . '_page_episodes', false, $post_id ); */

// --- get layout display settings ----
$block_position = radio_station_get_setting( 'profile_block_position' );
$block_position = apply_filters( 'radio_station_' . $type . '_block_position', $block_position );
$section_layout = radio_station_get_setting( 'profile_section_layout' );
$section_layout = apply_filters( 'radio_station_' . $type . '_section_layout', $section_layout );
$jump_links = apply_filters( 'radio_station_' . $type . '_jump_links', 'yes', $post_id );


// --------------------------
// === Set Blocks Content ===
// --------------------------

// --- set empty blocks ---
$blocks = array( 'profile_images' => '', 'profile_meta' => '' );

// --------------------
// Profile Images Block
// --------------------
$image_blocks = array();

// --- Profile Avatar ---
if ( $avatar_id ) {
	// --- get profile avatar ---
	$size = apply_filters( 'radio_station_' . $type . '_avatar_size', 'medium', $post_id, $type . '-page' );
	$attr = array( 'class' => esc_attr( $type ) . '-image' );
	if ( $profile_title ) {
		$attr['alt'] = $attr['title'] = $profile_title;
	}
	$profile_avatar = radio_station_pro_get_profile_avatar( $post_id, $size, $attr );
	if ( $profile_avatar ) {
		$image_blocks['avatar'] = '<div class="' . esc_attr( $type ) . '-avatar">';
		$image_blocks['avatar'] .= $profile_avatar;
		$image_blocks['avatar'] .= '</div>';
	}
}

// --- Profile Icons ---
if ( count( $profile_icons ) > 0 ) {
	$image_blocks['icons'] = '<div class="' . esc_attr( $type ) . '-icons">';
	$image_blocks['icons'] .= implode( $newline, $profile_icons );
	$image_blocks['icons'] .= '</div>';
}

// --- Social Icons ---
if ( $social_icons ) {
	$social_icons = apply_filters( 'radio_station_' . $type . '_social_icons_display', '' );
	if ( '' != $social_icons ) {
		$image_blocks['social'] = '<div id="' . esc_attr( $type ) . '-social-icons" class="social-icons">';
		$image_blocks['social'] .= $social_icons;
		$image_blocks['social'] .= '</div>';
	}
}

// --- Patreon Button ---
$patreon_button = '';
if ( $profile_patreon ) {
	$patreon_button .= '<div class="' . esc_attr( $type ) . '-patreon">';
	$title = __( 'Become a Supporter for', 'radio-station' ) . ' ' . $profile_title;
	$title = apply_filters( 'radio_station_' . $type . '_patreon_title', $title, $post_id );
	$patreon_button .= radio_station_patreon_button( $profile_patreon, $title );
	$patreon_button .= '</div>';
}
$patreon_button = apply_filters( 'radio_station_' . $type . '_patreon_button', $patreon_button, $post_id );
if ( '' != $patreon_button ) {
	$image_blocks['patreon'] = $patreon_button;
}

// --- Player ---
/* if ( $profile_file ) {

	$image_blocks['player'] = '<div class="' . esc_attr( $type ) . '-player">' . $newline;
	$label = apply_filters( 'radio_station_show_player_label', '', $post_id );
	if ( $label && ( '' != $label ) ) {
		$image_blocks['player'] .= '<span class="show-player-label">' . esc_html( $label ) . '</span><br>';
	}
	$shortcode = '[audio src="' . $profile_file . '" preload="metadata"]';
	$player_embed = do_shortcode( $shortcode );
	$image_blocks['player'] .= '<div class="show-embed">' . $newline;
	$image_blocks['player'] .= $player_embed . $newline;
	$image_blocks['player'] .= '</div>' . $newline;

	// --- Download Audio Icon ---
	if ( $profile_download ) {
		$title = __( 'Download Latest Broadcast', 'radio-station' );
		$title = apply_filters( 'radio_station_show_download_title', $title, $post_id );
		$image_blocks['player'] .= '<div class="show-download">' . $newline;
		$image_blocks['player'] .= '<a href="' . esc_url( $profile_file ) . '" title="' . esc_attr( $title ) . '" aria-label="' . esc_attr( $title ) . '">' . $newline;
		$image_blocks['player'] .= '<span style="color:#7DBB00;" class="dashicons dashicons-download" aria-hidden="true"></span>' . $newline;
		$image_blocks['player'] .= '</a>' . $newline;
		$image_blocks['player'] .= '</div>' . $newline;
	}

	$image_blocks['player'] .= '</div>' . $newline;
} */

// --- filter image blocks and block order ---
$image_blocks = apply_filters( 'radio_station_' . $type . '_images_blocks', $image_blocks, $post_id );
$image_block_order = array( 'avatar', 'icons', 'social', 'patreon', 'player' );
$image_block_order = apply_filters( 'radio_station_' . $type . '_image_block_order', $image_block_order, $post_id );
if ( RADIO_STATION_DEBUG ) {
	echo '<span style="display:none;">Image Block Order: ' . print_r( $image_block_order, true ) . '</span>';
	echo '<!-- Image Blocks: ' . print_r( $image_blocks, true ) . ' -->';
}

// --- combine image blocks to images block ---
if ( is_array( $image_blocks ) && ( count( $image_blocks ) > 0 )
  && is_array( $image_block_order ) && ( count( $image_block_order ) > 0 ) ) {
	$blocks['profile_images'] .= '<div class="' . esc_attr( $type ) . '-controls">';
	foreach ( $image_block_order as $image_block ) {
		if ( isset( $image_blocks[$image_block] ) ) {
			$blocks['profile_images'] .= $image_blocks[$image_block];
		}
	}
	$blocks['profile_images'] .= '</div>' . $newline;
}


// ------------------
// Profile Meta Block
// ------------------

$meta_blocks = array();

// --- profile meta title ---
if ( $profile_link || $profile_email || $profile_phone ) {
	$label = __( 'Profile Info', 'radio-station' );
	$label = apply_filters( 'radio_station_show_info_label', $label, $post_id );
	$blocks['profile_meta'] = '<h4 class="show-info-label">' . esc_html( $label ) . '</h4>' . $newline;
}

// --- Show Genre(s) ---
/* if ( $genres ) {
	$tax_object = get_taxonomy( RADIO_STATION_GENRES_SLUG );
	if ( count( $genres ) == 1 ) {
		$label = $tax_object->labels->singular_name;
	} else {
		$label = $tax_object->labels->name;
	}
	$label = apply_filters( 'radio_station_show_genres_label', $label, $post_id );
	$meta_blocks['genres'] = '<div class="show-genres">' . $newline;
	$meta_blocks['genres'] .= '<span class="show-genres-label">' . esc_html( $label ) . '</span>: ' . $newline;
	$genre_links = array();
	foreach ( $genres as $genre ) {
		$genre_link = get_term_link( $genre );
		$genre_links[] = '<a href="' . esc_url( $genre_link ) . '">' . esc_html( $genre->name ) . '</a>' . $newline;
	}
	$meta_blocks['genres'] .= implode( ', ', $genre_links ) . $newline;
	$meta_blocks['genres'] .= '</div>' . $newline;
} */

// --- Show Language(s) ---
/* if ( $languages ) {
	$tax_object = get_taxonomy( RADIO_STATION_LANGUAGES_SLUG );
	if ( count( $languages ) == 1 ) {
		$label = $tax_object->labels->singular_name;
	} else {
		$label = $tax_object->labels->name;
	}
	$label = apply_filters( 'radio_station_show_languages_label', $label, $post_id );
	$meta_blocks['languages'] = '<div class="show-languages">' . $newline;
	$meta_blocks['languages'] .= '<span class="show-languages-label">' . esc_html( $label ) . '</span>: ' . $newline;
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

// --- Website ---
if ( $profile_link ) {
	$meta_blocks['website'] = '<div class="' . esc_attr( $type ) . '-phone">';
	$label = __( 'Website', 'radio-station' );
	$label = apply_filters( 'radio_station_profile_website_label', $label, $post_id, $type );
	$meta_blocks['website'] .= '<span class="' . esc_attr( $type ) . '-website-label profile-label">' . esc_html( $label ) . '</span>: ' . $newline;
	$meta_blocks['website'] .= '<span class="' . esc_attr( $type ) . '-website"><a href="' . esc_attr( $profile_link ) . '">' . esc_html( $profile_link ) . '</span></a>';
	$meta_blocks['website'] .= '</div>' . $newline;
}

// --- Email ---
if ( $profile_email ) {
	$meta_blocks['phone'] = '<div class="' . esc_attr( $type ) . '-phone">';
	$label = __( 'Email', 'radio-station' );
	$label = apply_filters( 'radio_station_profile_email_label', $label, $post_id, $type );
	$meta_blocks['phone'] .= '<span class="' . esc_attr( $type ) . '-email-label profile-label">' . esc_html( $label ) . '</span>: ' . $newline;
	$meta_blocks['phone'] .= '<span class="' . esc_attr( $type ) . '-email"><a href="mailto:' . esc_attr( $profile_email ) . '">' . esc_html( $profile_email ) . '</a></span>';
	$meta_blocks['phone'] .= '</div>' . $newline;
}

// --- Show Phone ---
if ( $profile_phone ) {
	$meta_blocks['phone'] = '<div class="profile-phone">';
	$label = __( 'Phone', 'radio-station' );
	$label = apply_filters( 'radio_station_profile_phone_label', $label, $post_id, $type );
	$meta_blocks['phone'] .= '<span class="' . esc_attr( $type ) . '-phone-label profile-label">' . esc_html( $label ) . '</span>: ' . $newline;
	$meta_blocks['phone'] .= '<span class="' . esc_attr( $type ) . '-phone"><a href="tel:' . esc_attr( $profile_phone ) . '">' . esc_html( $profile_phone ) . '</a></span>';
	$meta_blocks['phone'] .= '</div>' . $newline;
}


// --- filter meta blocks and order ---
$meta_blocks = apply_filters( 'radio_station_' . $type . '_meta_blocks', $meta_blocks, $post_id );
$meta_block_order = array( 'genres', 'languages', 'phone' );
$meta_block_order = apply_filters( 'radio_station_' . $type . '_meta_block_order', $meta_block_order, $post_id );
if ( RADIO_STATION_DEBUG ) {
	echo '<span style="display:none;">Meta Block Order: ' . print_r( $meta_block_order, true ) . '</span>';
	echo '<!-- Meta Blocks: ' . print_r( $meta_blocks, true ) . ' -->';
}

// --- combine meta blocks to show meta block ---
if ( is_array( $meta_blocks ) && ( count( $meta_blocks ) > 0 )
  && is_array( $meta_block_order ) && ( count( $meta_block_order ) > 0 ) ) {
	foreach ( $meta_block_order as $meta_block ) {
		if ( isset( $meta_blocks[$meta_block] ) ) {
			$blocks['profile_meta'] .= $meta_blocks[$meta_block];
		}
	}
}


// --- filter all info blocks ---
$blocks = apply_filters( 'radio_station_' . $type . '_page_blocks', $blocks, $post_id );


// ----------------------------
// === Set Profile Sections ===
// ----------------------------

// -----------------------
// Set Profile Description
// -----------------------
$profile_description = false;
if ( strlen( trim( $content ) ) > 0 ) {
	$profile_description = '<div class="' . esc_attr( $type ) . '-desc-content">' . $content . '</div>' . $newline;
	$profile_description .= '<div id="show-more-overlay"></div>' . $newline;
	$show_desc_buttons = '<div id="show-desc-buttons">' . $newline;
	$label = __( 'Show More', 'radio-station' );
	$label = apply_filters( 'radio_station_show_more_label', $label, $post_id );
	$show_desc_buttons .= '	<input type="button" id="show-desc-more" onclick="radio_show_desc(\'more\');" value="' . esc_html( $label ) . '">' . $newline;
	$label = __( 'Show Less', 'radio-station' );
	$label = apply_filters( 'radio_station_show_less_label', $label, $post_id );
	$show_desc_buttons .= '	<input type="button" id="show-desc-less" onclick="radio_show_desc(\'less\');" value="' . esc_html( $label ) . '">' . $newline;
	$show_desc_buttons .= '	<input type="hidden" id="show-desc-state" value="">' . $newline;
	$show_desc_buttons .= '</div>' . $newline;
}

// -------------
// Show Sections
// -------------
$sections = array();

// --- About Profile Tab (Post Content) ---
if ( $profile_description ) {

	$sections['about']['heading'] = '<a name="' . esc_attr( $type ) . '-description"></a>' . $newline;
	$label = __( 'About this %s', 'radio-station' );
	$label = sprintf( $label, $singular_label );
	$label = apply_filters( 'radio_station_' . $type . '_description_label', $label, $post_id );
	$sections['about']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-section-about">' . esc_html( $label ) . '</h3>' . $newline;
	$anchor = __( 'About', 'radio-station' );
	$anchor = apply_filters( 'radio_station_' . $type . '_description_anchor', $anchor, $post_id );
	$sections['about']['anchor'] = $anchor;

	$sections['about']['content'] = '<div id="' . esc_attr( $type ) . '-about" class="' . esc_attr( $type ) . '-tab tab-active"><br>' . $newline;
	$sections['about']['content'] .= '<div id="' . esc_attr( $type ) . '-description" class="' . esc_attr( $type ) . '-description">' . $newline;
	$sections['about']['content'] .= $profile_description;
	$sections['about']['content'] .= '</div>' . $newline;
	$sections['about']['content'] .= $show_desc_buttons;
	$sections['about']['content'] .= '</div>' . $newline;
}

// --- Profile Blog Posts Tab ---
if ( $profile_posts ) {

	$posts_type = get_post_type_object( 'post' );
	$posts_label = $posts_type->labels->name;
	$sections['posts']['heading'] = '<a name="' . esc_attr( $type ) . '-posts"></a>' . $newline;
	$label = $singular_label . ' ' . $posts_label;
	$label = apply_filters( 'radio_station_' . $type . '_posts_label', $label, $post_id );
	$sections['posts']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-section-posts">' . esc_html( $label ) . '</h3>' . $newline;
	$anchor = apply_filters( 'radio_station_' . $type . '_posts_anchor', $posts_label, $author_id );
	$sections['posts']['anchor'] = $anchor;

	$sections['posts']['content'] = '<div id="' . esc_attr( $type ) . '-posts" class="' . esc_attr( $type ) . '-section-content"><br>' . $newline;
	$radio_station_data['profile-posts'] = $profile_posts;
	$posts_per_page = 5; // TEMP
	$shortcode = '[' . $type . '-posts-archive per_page="' . $posts_per_page . '" author="' . $author_id . '"]';
	$shortcode = apply_filters( 'radio_station_' . $type . '_page_posts_shortcode', $shortcode, $author_id );
	$sections['posts']['content'] .= do_shortcode( $shortcode );
	$sections['posts']['content'] .= '</div>' . $newline;
}

// --- Profile Shows Tab ---
if ( $profile_shows ) {

	$shows_type = get_post_type_object( RADIO_STATION_SHOW_SLUG );
	$shows_label = $shows_type->labels->name;
	$sections['shows']['heading'] = '<a name="' . esc_attr( $type ) . '-shows">';
	$label = $singular_label . ' ' . $shows_label;
	$label = apply_filters( 'radio_station_' . $type . '_shows_label', $label, $post_id );
	$sections['shows']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-section-shows">' . esc_html( $label ) . '</h3>' . $newline;
	$anchor = apply_filters( 'radio_station_' . $type . '_shows_anchor', $shows_label, $post_id );
	$sections['shows']['anchor'] = $anchor;

	$sections['shows']['content'] = '<div id="' . esc_attr( $type ) . '-shows" class="' . esc_attr( $type ) . '-section-content"><br>' . $newline;
	$radio_station_data['profile-shows'] = $profile_shows;
	$shows_per_page = 3; // TEMP
	$shortcode = '[' . $type . '-shows-archive per_page="' . $shows_per_page . '" id="' . $author_id . '"]';
	$shortcode = apply_filters( 'radio_station_' . $type . '_page_shows_shortcode', $shortcode, $author_id );
	$sections['shows']['content'] .= do_shortcode( $shortcode );
	$sections['shows']['content'] .= '</div>' . $newline;
}


// --- Profile Episodes Tab ---
/* if ( $profile_episodes ) {

	$episodes_type = get_post_type_object( RADIO_STATION_EPISODE_SLUG );
	$episodes_label = $episodes_type->labels->name;
	$sections['episodes']['heading'] = '<a name="' . esc_attr( $type ) . '-episodes"></a>' . $newline;
	$label = $singular_label . ' ' . $episodes_label;
	$label = apply_filters( 'radio_station_' . $type . '_episodes_label', $label, $post_id );
	$sections['episodes']['heading'] .= '<h3 id="' . esc_attr( $type ) . '-section-episodes">' . esc_html( $label ) . '</h3>' . $newline;
	$anchor = apply_filters( 'radio_station_' . $type . '_episodes_anchor', $episodes_label, $author_id );
	$sections['episodes']['anchor'] = $anchor;

	$sections['episodes']['content'] = '<div id="' . esc_attr( $type ) . '-episodes" class="' . esc_attr( $type ) . '-section-content"><br>' . $newline;
	$radio_station_data['profile-episodes'] = $profile_episodes;
	$shortcode = '[' . $type . '-episodes-archive per_page="' . $episodes_per_page . '" id="' . $author_id . '"]';
	$shortcode = apply_filters( 'radio_station_' . $type . '_page_episodes_shortcode', $shortcode, $author_id );
	$sections['episodes']['content'] .= do_shortcode( $shortcode );
	$sections['episodes']['content'] .= '</div>' . $newline;
} */

$sections = apply_filters( 'radio_station_' . $type . '_page_sections', $sections, $post_id );


// -----------------------
// === Template Output ===
// -----------------------

// --- set content classes ---
$class = 'left-blocks';
if ( in_array( $block_position, array( 'left', 'right', 'top' ) ) ) {
	$class = $block_position . '-blocks';
}

echo '<!-- #' . esc_attr( $type ) . '-content -->' . $newline;
echo '<div id="' . esc_attr( $type ) . '-content" class="' .  esc_attr( $class ) . '">' . $newline;
echo '<input type="hidden" id="radio-page-type" value="' . esc_attr( $type ) . '">' . $newline;

	// --- Info Blocks --- 
	echo '<div id="' . esc_attr( $type ) . '-info" class="' . esc_attr( $type ) . '-info">' . $newline;

		// --- filter block order ---
		$block_order = array( 'profile_images', 'profile_meta' );
		$block_order = apply_filters( 'radio_station_' . $type . '_page_block_order', $block_order, $post_id );

		// --- loop blocks ---
		if ( is_array( $block_order ) && ( count( $block_order ) > 0 ) ) {
			foreach ( $block_order as $i => $block ) {
				if ( isset( $blocks[$block] ) && ( '' != trim( $blocks[$block] ) ) ) {

					// --- set block classes ---
					$classes = array( $type . '-block' );
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
	
	// --- Profile Sections ---
	echo '<div class="' . esc_attr( $type ) . '-sections">' . $newline;

		// --- filter section order ---
		$section_order = array( 'about', 'posts', 'shows', 'episodes' );
		$section_order = apply_filters( 'radio_station_' . $type . '_page_section_order', $section_order, $post_id );

		// --- Display Sections ---
		if ( ( is_array( $sections ) && ( count( $sections ) > 0 ) )
		     && is_array( $section_order ) && ( count( $section_order ) > 0 ) ) {

			// --- tabs for tabbed layout ---
			if ( 'tabbed' == $section_layout ) {

				// --- output first section as non-tabbed ---
				if ( isset( $sections[$section_order[0]] ) ) {
					// phpcs:ignore WordPress.Security.OutputNotEscaped
					echo $sections[$section_order[0]]['heading'];
					echo $sections[$section_order[0]]['content'];
				}
				unset( $section_order[0] );

				echo '<div class="' . esc_attr( $type ) . '-tabs">' . $newline;

					$i = 0;
					$found_section = false;
					foreach ( $section_order as $section ) {
						if ( isset( $sections[$section] ) ) {
							$found_section = true;
							$class = ( 0 == $i ) ? 'tab-active' : 'tab-inactive';
							echo '<div id="' . esc_attr( $type ) . '-' . esc_attr( $section ) . '-tab" class="' . esc_attr( $type ) . '-tab ' . esc_attr( $class ) . '" onclick="radio_show_tab(\'' . esc_attr( $type ) . '\',\'' . esc_attr( $section ) . '\');">' . $newline;
							echo esc_html( $sections[$section]['anchor'] );
							echo '</div>' . $newline;
							if ( ( $i + 1 ) < count( $sections ) ) {
								echo '<div class="' . esc_attr( $type ) . '-tab-spacer">&nbsp;</div>' . $newline;
							}
							$i++;
						}
					}
					if ( $found_section ) {
						echo '<div class="' . esc_attr( $type ) . '-tab-spacer">&nbsp;</div>' . $newline;
					}
				echo '</div>' . $newline;
			}

			echo '<div class="' . esc_attr( $type ) . '-section">' . $newline;
			$i = 0;
			foreach ( $section_order as $section ) {
				if ( isset( $sections[$section] ) ) {

					if ( 'tabbed' != $section_layout ) {

						// --- section heading ---
						// phpcs:ignore WordPress.Security.OutputNotEscaped
						echo $sections[$section]['heading'] . $newline;

						// --- section jump links ---
						if ( 'yes' == $jump_links ) {
							echo '<div class="' . esc_attr( $type ) . '-jump-links">' . $newline;
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

						// --- add tab classes to section ---
						$classes = array( esc_attr( $type ) . '-tab' );
						if ( 0 == $i ) {
							$classes[] = 'tab-active';
						} else {
							$classes[] = 'tab-inactive';
						}
						$class = implode( ' ', $classes );
						$sections[$section]['content'] = str_replace( 'class="' . esc_attr( $type ) . '-section-content"', 'class="' . esc_attr( $class ) . '"', $sections[$section]['content'] );

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
echo '<!-- /#' . esc_attr( $type ) . '-content -->' . $newline;

// --- enqueue show page script ---
radio_station_enqueue_script( 'radio-station-page', array( 'radio-station' ), true );

// --- maybe detect and switch to # tab ---
// 2.4.0.6: add prefix argument to show_tab function
if ( 'tabbed' == $section_layout ) {
	$js = "setTimeout(function() {";
	$js .= " if (window.location.hash) {";
	$js .= "  hash = window.location.hash.substring(1);";
	$js .= "  if (hash.indexOf('" . esc_js( $type ) . "-') > -1) {";
	$js .= "   tab = hash.replace('" . esc_js( $type ) . "-', '');";
	$js .= "   radio_show_tab(" . esc_js( $type ) . ",tab);";
	$js .= "  }";
	$js .= " }";
	$js .= "}, 500);";
	wp_add_inline_script( 'radio-station-page', $js );
}

$radio_station_data['doing-template'] = false;

