<?php

// =========================
// === Radio Station Pro ===
// =========================
// ------- Pro Player ------
// =========================

// === Enqueue Pro Player Scripts ===
// - Filter Player Output HTML
// === Widget Options ===
// * Player Widget Fields Filter
// - Player Widget Fields Update
// - Filter Player Theme Options
// - Filter Player Button Options
// - Player Shortcode Attributes Filter
// === Sitewide Player Bar ===
// - Enforce Bar Player as Default
// - Filter Teleporter Enabled Switch
// - Filter Teleporter Page Fade Time
// - Filter Teleporter Page Load Timeout
// - Output Player Bar
// - Player Bar Styles
// - Player Bar Scripts
// === Popup Player ===
// * Add Popup Player Button


// ----------------------------------
// === Enqueue Pro Player Scripts ===
// ----------------------------------
add_action( 'wp_enqueue_scripts', 'radio_station_pro_player_scripts', 12 );
function radio_station_pro_player_scripts() {

	// --- get relevent settings needing pro player script ---
	$autoresume = radio_station_get_setting( 'player_autoresume' );
	$continuous = radio_station_get_setting( 'player_bar_continuous' );
	if ( '1' == $continuous ) {
		$continuous = 'yes';
	}
	if ( defined( 'RADIO_PLAYER_AUTORESUME' ) ) {
		$autoresume = RADIO_PLAYER_AUTORESUME;
	}
	if ( defined( 'RADIO_PLAYER_CONTINUOUS' ) ) {
		$continuous = RADIO_PLAYER_CONTINUOUS;
	}

	// --- maybe enqueue pro player javascript ---
	$inline_handle = 'radio-player';
	if ( ( 'yes' == $autoresume ) || ( 'yes' == $continuous ) ) {
		$inline_handle = 'rsp-player';
		$version = filemtime( RADIO_STATION_PRO_DIR . '/js/rsp-player.js' );
		$pro_player_url = plugins_url( 'js/rsp-player.js', RADIO_STATION_PRO_FILE );
		wp_enqueue_script( $inline_handle, $pro_player_url, array( 'radio-player' ), $version, true );
	}

	// --- enqueue player scripts ---
	radio_station_player_core_scripts();
	$script = radio_station_get_setting( 'player_script' );
	radio_station_player_enqueue_script( $script );

	// --- additional javascript ---
	$js = '';

	// --- set metdata retrieval URL ---
	$routes = radio_station_get_setting( 'enable_data_routes' );
	if ( 'yes' == $routes ) {
		$metadata_url = radio_station_get_route_url( 'broadcast' );
	} else {
		$feeds = radio_station_get_setting( 'enable_data_routes' );
		if ( 'yes' == $feeds ) {
			$metadata_url = radio_station_get_feed_url( 'broadcast' );
		} else {
			$metadata_url = add_query_arg( 'action', 'radio_player_now_playing', admin_url( 'admin-ajax.php' ) );
		}
	}
	// $metadata_url = add_query_arg( 'action', 'radio_player_now_playing', admin_url( 'admin-ajax.php' ) );

	$js .= "radio_player.settings.metadata_url = '" . esc_url( $metadata_url ) . "';" . PHP_EOL;

	// --- autoresume and continous settings
	if ( $autoresume ) {
		$js .= "radio_player.settings.autoresume = true;" . PHP_EOL;
	}
	if ( $continuous ) {
		$js .= "radio_player.settings.continuous = true;" . PHP_EOL;
	}

	// --- check now playing display setting ---
	$currentshow = radio_station_get_setting( 'player_bar_currentshow' );
	if ( 'yes' == $currentshow ) {
		$js .= "radio_player.settings.currentshow = true;" . PHP_EOL;
	} else {
		$js .= "radio_player.settings.currentshow = false;" . PHP_EOL;
	}
	$nowplaying = radio_station_get_setting( 'player_bar_nowplaying' );
	if ( 'yes' == $nowplaying ) {
		$js .= "radio_player.settings.nowplaying = true;" . PHP_EOL;
	} else {
		$js .= "radio_player.settings.nowplaying = false;" . PHP_EOL;
	}

	// --- check player bar position ---
	$position = radio_station_get_setting( 'player_bar' );
	if ( ( '' != $position ) && ( 'off' != $position ) ) {

		// --- maybe fade in player bar ---
		$fade_in = radio_station_get_setting( 'player_bar_fadein' );
		if ( $fade_in ) {
			// 2.4.1.2: tweak to auto-display if not top window (transitioned to window)
			$js .= "if (radio_player.settings.continuous && (typeof radio_player_top_window == 'function') && (radio_player_top_window() != window.self)) {";
				$js .= "if (radio_player.debug) {console.log('Skipping Player Fadein.');} ";
				$js .= "document.getElementById('radio-station-player-bar').style.display = '';";
			$js .= "} else {" . PHP_EOL;
				$js .= "if (typeof jQuery == 'function') {";
					// 2.4.1.4: remove wait for document ready
					$js .= "jQuery('#radio-station-player-bar').fadeIn(" . esc_js( $fade_in ) . ");";
				$js .= "} else {";
					$js .= "setTimeout(function() {document.getElementById('radio-station-player-bar').style.display = '';}, " . esc_js( $fade_in ) . ");";
				$js .= "}" . PHP_EOL;
			$js .= "}" . PHP_EOL;
		}

 		// 2.4.1.5: filter metadata cycle seconds
 		$metadata_cycle = 10;
 		$metadata_cycle = apply_filters( 'radio_station_player_bar_metadata_cycle', $metadata_cycle );

		// --- start metadata cycler via playing event ---
		// TODO: now playing album info and display
		$js .= "var radio_player_metadata_cycler;" . PHP_EOL;
		$js .= "jQuery(document).ready(function() {		
 			radio_player_metadata_cycler = setInterval(function(instance) {
 				metadata_url = radio_player.settings.metadata_url;
				if (radio_player.debug) {console.log('Getting stream metadata via URL: '+metadata_url);}
				jQuery.get(metadata_url, function(data,status) {
					if (status != 'success') {return;}
					/* Update Now Playing Track */
					if (radio_player.debug) {console.log('Now Playing:'); console.log(data.broadcast.now_playing);}
					if (radio_player.settings.nowplaying && data.broadcast.now_playing) {
						jQuery('#radio-station-player-bar').addClass('now_playing');
						if (data.broadcast.now_playing.title && data.broadcast.now_playing.artist) {
							jQuery('#radio-station-player-bar .rp-now-playing-title').html(data.broadcast.now_playing.title).attr('title',data.broadcast.now_playing.title);
							jQuery('#radio-station-player-bar .rp-now-playing-artist').html(data.broadcast.now_playing.artist).attr('title',data.broadcast.now_playing.artist);
						} else {
							jQuery('#radio-station-player-bar .rp-now-playing-title').html(data.broadcast.now_playing.text).attr('title',data.broadcast.now_playing.text);
							jQuery('#radio-station-player-bar .rp-now-playing-artist').html('').attr('title','');
						}
						/* if (data.broadcast.now_playing.album) {
							jQuery('#radio-station-player-bar .rp-now-playing-album').html(data.broadcast.now_playing.album).attr('title',data.broadcast.now_playing.album);
						} else {jQuery('#radio-station-player-bar .rp-now-playing-album').html('').attr('title','');} */
					} else {jQuery('#radio-station-player-bar').removeClass('now_playing');}
					/* Update Current Show Display */
					if (radio_player.debug) {console.log('Current Show:'); console.log(data.broadcast.current_show);}
					if (radio_player.settings.currentshow && data.broadcast.current_show) {
						jQuery('#radio-station-player-bar').addClass('current_show');
						show = data.broadcast.current_show.show;
						if (show.url) {
							if (radio_player.settings.continuous) {click = ' onclick=\"return teleporter_transition_page(this);\"';} else {click = '';}
							link = '<a href=\"'+show.url+'\"'+click+'>'+show.name+'</div>';
							jQuery('#radio-station-player-bar .rp-show-title').html(link).attr('title',show.name);
						} else {jQuery('#radio-station-player-bar .rp-show-title').html(show.name).attr('title',show.name);}
						if (show.avatar_url == '') {
							jQuery('#radio-station-player-bar .rp-show-image').css('background-image','none');
							if (!jQuery('#radio-station-player-bar .rp-show-image').hasClass('no-image')) {jQuery('#radio-station-player-bar .rp-show-image').addClass('no-image');}
						} else {jQuery('#radio-station-player-bar .rp-show-image').removeClass('no-image').css('background-image','url('+data.broadcast.current_show.show.avatar_url+')');}
					} else {jQuery('#radio-station-player-bar').removeClass('current_show');}
				});
			}, " . esc_js( $metadata_cycle * 1000 ) . ");
		});" . PHP_EOL;
		
		// TODO: multiple metadata events in shortcodes/widgets
		/* $js .= "document.addEventListener('rp-playing', function(e) {	
			instance = e.detail.instance;
			if (jQuery('#radio-station-player-bar .radio_player_'+instance').length) {return;}
			radio_player_metadata_cycle[instance] = setInterval(function(instance) {
				href = jQuery('#radio_player_'+instance).attr('data-href');
				console.log('Getting stream metadata for URL: '+href);
				metadata_url = radio_player.settings.metadata_url;
				if (metadata_url.indexOf('?') > -1) {metadata_url += '&';} else {url += '=';}
				metadata_url += encodeURIComponent(href);
				jQuery.get(metadata_url, function(data,status) {
					console.log('Data: '+data); console.log('Status:'+status);
				});
			}, " . esc_js( $metadata_cycle * 1000 ) . ");
		}, false);" . PHP_EOL; */

		/* $js .= "document.addEventListener('rp-pause', function(e) {	
			instance = e.detail.instance;
			if (typeof radio_player_metadata_cycle[instance] != 'undefined') {
				clearInterval(radio_player_metadata_cycle[instance]);
			}
		}, false);" . PHP_EOL; */

	}

	// --- pageload functions ---
	// $js .= "jQuery(document).ready(function() {";
	// $js .= "});" . PHP_EOL;

	// --- filter and enqueue inline ---
	$js = apply_filters( 'radio_station_pro_scripts', $js );
	if ( '' != $js ) {
		wp_add_inline_script( $inline_handle, $js );
	}

}



// ----------------------
// === Widget Options ===
// ----------------------

// ---------------------------
// Player Widget Fields Filter
// ---------------------------
add_filter( 'radio_station_player_widget_fields', 'radio_station_player_widget_fields', 10, 3 );
function radio_station_player_widget_fields( $fields, $widget, $intance ) {

	// TODO: also filter player theme options and/or button shapes ?

	// --- Color Options ---
	// playing_color
	// buttons_color
	// track_color
	// thumb_color

	/* $fields .= '
	<p>
		<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">
		' . esc_html( __( 'Title', 'radio-station' ) ) . ':
			<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" />
		</label>
	</p>'; */

	// --- Advanced Options ---
	// current_show
	// metadata
	// metadata_url
	// popup_player

	return $fields;
}

// ---------------------------
// Filter Player Theme Options
// ---------------------------
// add_filter( 'radio_station_player_theme_options', 'radio_station_pro_theme_options' );
function radio_station_pro_theme_options( $options ) {

	// TODO: add more theme options ?

	return $options;
}

// ----------------------------
// Filter Player Button Options
// ----------------------------
// add_filter( 'radio_station_player_button_options', 'radio_station_pro_button_options' );
function radio_station_pro_button_options( $options ) {

	// TODO: add more button skin options ?
	// eg. range dials, switches etc.
	// ref: https://g200kg.github.io/input-knobs/

	return $options;
}

// ---------------------------
// Player Widget Fields Update
// ---------------------------
add_filter( 'radio_station_player_widget_update', 'radio_station_pro_player_widget_update', 10, 3 );
function radio_station_pro_player_widget_update( $instance, $new_instance, $old_instance ) {

	// --- Player Colors ---
	$instance['playing_color'] = isset( $new_instance['playing_color'] ) ? $new_instance['playing_color'] : 0;
	$instance['buttons_color'] = isset( $new_instance['buttons_color'] ) ? $new_instance['buttons_color'] : 0;
	$instance['track_color'] = isset( $new_instance['track_color'] ) ? $new_instance['track_color'] : 0;
	$instance['thumb_color'] = isset( $new_instance['thumb_color'] ) ? $new_instance['thumb_color'] : 0;

	// --- Advanced Options ---
	// $instance['current_show'] = isset( $new_instance['current_show'] ? $new_instance['current_show'] : 1;
	// $instance['metadata'] = isset( $new_instance['metadata'] ? $new_instance['metadata'] : 1;
	// $instance['metadata_url'] = isset( $new_instance['metadata_url'] ? $new_instance['metadata_url'] : '';
	// $instance['popup_player'] = isset( $new_instance['popup_player'] ) ? $new_instance['popup_player'] : 0;

	return $instance;
}

// ----------------------------------
// Filter Player Shortcode Attributes
// ----------------------------------
add_filter( 'radio_station_player_shortcode_attributes', 'radio_station_pro_player_settings' );
function radio_station_pro_player_settings( $atts ) {

	global $radio_player;

	// --- check for shortcode ID ---
	if ( isset( $atts['id'] ) ) {

		// --- set advanced styles for instance ---
		$custom = false;
		$instance = $atts['id'];
		if ( isset( $atts['playing_color'] ) ) {
			$radio_player['instance-colors'][$instance] = $atts['playing_color'];
			$custom = true;
		}
		if ( isset( $atts['buttons_color'] ) ) {
			$radio_player['instance-colors'][$instance] = $atts['buttons_color'];
			$custom = true;
		}
		if ( isset( $atts['track_color'] ) ) {
			$radio_player['instance-colors'][$instance] = $atts['track_color'];
			$custom = true;
		}
		if ( isset( $atts['thumb_color'] ) ) {
			$radio_player['instance-colors'][$instance] = $atts['thumb_color'];
			$custom = true;
		}

		// --- maybe enqueue custom styles ---
		if ( $custom ) {
			if ( !isset( $radio_player['pro_instances'] ) ) {
				$radio_player['pro_instances'] = array();
			}
			$radio_player['pro_instances'][] = $instance;
			add_action( 'wp_footer', 'radio_station_pro_custom_player_styles' );
		}
	}
	
	return $atts;
}

// ---------------------------
// Custom Player Styles Output
// ---------------------------
function radio_station_pro_custom_player_styles() {

	global $radio_player;
	
	if ( isset( $radio_player['pro_instances'] ) ) {
		$control_styles = '';
		foreach ( $radio_player['pro_instances'] as $i => $instance ) {	
			$control_styles .= radio_station_player_control_styles( $instance );
		}
		echo "<style>" . $control_styles . "</style>";
	}
}


// ----------------------------
// === Sitewide Player Bar  ===
// ----------------------------

// -------------------------
// Filter Player Output HTML
// -------------------------
add_filter( 'radio_station_player_html', 'radio_station_pro_player_html', 11, 3 );
function radio_station_pro_player_html( $html, $args, $instance ) {

	// --- for player bar ---
	if ( isset( $args['sitewide'] ) && $args['sitewide'] ) {

		// --- check now playing display setting ---
		$nowplaying = radio_station_get_setting( 'player_bar_nowplaying' );
		if ( 'yes' == $nowplaying ) {
			$metadata_url = trim( radio_station_get_setting( 'player_bar_metadata' ) );
			if ( '' == $metadata_url ) {
				$metadata_url = radio_station_get_stream_url();
			}
		}

	} elseif ( isset( $args['metadata'] ) ) {

		// --- shortcode or function supplied argument ---
		$metadata_url = $args['metadata'];
		if ( '1' == $metadata_url ) {
			$metadata_url = radio_station_get_stream_url();
		} elseif ( '2' == $metadata_url ) {
			$metadata_url = radio_station_get_fallback_url();
		}
	}

	// --- add the metedata referance to the player element ---
	if ( isset( $metadata_url ) && ( '' != $metadata_url ) ) {
		
		// echo 'Find: ' . $find . ' - Replace: ' . $replace;
		$find = 'id="radio_player_' . $instance . '"';
		$replace = $find . ' data-href="' . $metadata_url . '"';
		$html = str_replace( $find, $replace, $html );
	}	

	return $html;
}

// -----------------------------
// Enforce Bar Player as Default
// -----------------------------
add_filter( 'radio_station_player_shortcode_attributes', 'radio_station_pro_player_default' );
function radio_station_pro_player_default( $atts ) {

	// --- check for sitewide display ---
	$position = radio_station_get_setting( 'player_bar' );
	if ( ( '' == $position ) || ( 'off' == $position ) ) {
		return $atts;
	}

	// --- enforce sitewide as default ---
	if ( isset( $atts['sitewide'] ) ) {
		$atts['default'] = true;
	} else {
		$atts['default'] = false;
	}

	return $atts;
}

// --------------------------------
// Filter Teleporter Enabled Switch
// --------------------------------
// 2.4.1.4: added for new teleporter setting
add_filter( 'teleporter_page_fade_switch', 'radio_station_pro_page_fade_switch' );
function radio_station_pro_page_fade_switch( $enabled ) {

	$continuous = radio_station_get_setting( 'player_bar_continuous' );
	if ( ( '1' == $continuous ) || ( 'yes' == $continuous ) ) {
		$enabled = 'yes';
	}
	return $enabled;
}

// ------------------------------------
// Filter Teleporter Page Fade Time
// ------------------------------------
// 2.4.1.4: added filter for new teleporter setting
add_filter( 'teleporter_fade_time', 'radio_station_pro_page_fade_time' );
add_filter( 'teleporter_page_fade_time', 'radio_station_pro_page_fade_time' );
function radio_station_pro_page_fade_time( $time ) {
	$time = radio_station_get_setting( 'player_bar_pagefade' );
	return $time;
}

// -----------------------------------
// Filter Teleporter Page Load Timeout
// -----------------------------------
// 2.4.1.4: added filter for new teleporter setting
add_filter( 'teleporter_load_timeout', 'radio_station_pro_page_load_timeout' );
add_filter( 'teleporter_page_load_timeout', 'radio_station_pro_page_load_timeout' );
function radio_station_pro_page_load_timeout( $timeout ) {
	$timeout = radio_station_get_setting( 'player_bar_timeout' );
	return $timeout;
}

// -----------------
// Output Player Bar
// -----------------
// 2.4.1.4: change priority to earlier than standard
add_action( 'wp_footer', 'radio_station_pro_player_bar', 9 );
function radio_station_pro_player_bar() {

	// --- get player bar position ---
	$layout = 'horizontal';
	$position = radio_station_get_setting( 'player_bar' );
	if ( ( '' == $position ) || ( 'off' == $position ) ) {
		return;
	} elseif ( ( 'left' == $position ) || ( 'right' == $position ) ) {
		// note: not yet implemented
		$layout = 'vertical';
	}

	// --- output player bar ---
	$classes = array( 'player-bar', $position, $layout );
	$class_list = implode( ' ', $classes );
	echo '<div id="radio-station-player-bar" class="' . esc_attr( $class_list ) . '"';
	$fade_in = radio_station_get_setting( 'player_bar_fadein' );
	if ( ( '' != $fade_in ) && ( $fade_in > 0 ) ) {
		echo ' style="display: none;"';
	}
	echo '>';

		// --- get player bar settings ---
		// note: layout is already set according to bar position
		$title = radio_station_get_setting( 'player_title' );
		if ( 'yes' == $title ) {
			$title = radio_station_get_setting( 'station_title' );
		} else {
			// 2.4.1.3: fix by setting off value as string
			$title = '0';
		}
		$image = radio_station_get_setting( 'player_image' );
		if ( 'yes' == $image ) {
			$image_id = radio_station_get_setting( 'station_image' );
			$attachment = wp_get_attachment_image_src( $image_id );
			if ( $attachment && is_array( $attachment ) ) {
				$image = $attachment[0];
			} else {
				$image = 0;
			}
		} else {
			$image = 0;
		}
		$script = radio_station_get_setting( 'player_script' );
		$theme = radio_station_get_setting( 'player_theme' );
		$buttons = radio_station_get_setting( 'player_buttons' );
		$volume = radio_station_get_setting( 'player_volume' );

		// --- merge settings to shortcode attributes ---
		// note: layout set according to bar position
		$atts = array(
			'title'		=> $title,
			'image'		=> $image,
			'script'	=> $script,
			'layout'	=> $layout,
			'theme'		=> $theme,
			'buttons'	=> $buttons,
			'volume'	=> $volume,
			'default'	=> true,
			'sitewide'  => true,
		);
		$atts = apply_filters( 'radio_station_player_bar_atts', $atts );

		if ( isset( $_REQUEST['player-debug'] ) && ( '1' == $_REQUEST['player-debug'] ) ) {
			echo '<span style="display:none;">Player Bar Atts: ' . print_r( $atts, true ) . '</span>';
		}

		// --- echo player bar shortcode output ---
		echo radio_station_player_shortcode( $atts );

	echo '</div>';

}

// -----------------
// Bar Player Styles
// -----------------
add_action( 'wp_enqueue_scripts', 'radio_station_pro_player_bar_styles', 11 );
function radio_station_pro_player_bar_styles() {

	// --- get player bar position ---
	$position = radio_station_get_setting( 'player_bar' );
	if ( ( '' == $position ) || ( 'off' == $position ) ) {
		return;
	}

	// --- get player bar height ---
	$bar_height = radio_station_get_setting( 'player_bar_height' );
	if ( !$bar_height ) {
		$bar_height = 80;
	}

	// --- enqueue radio player styles ---
	radio_station_player_enqueue_styles();

	// --- player bar styles ---
	$css = '';

	// --- main player bar styles ---
	$text_color = radio_station_get_setting( 'player_bar_text' );
	if ( '' == $text_color ) {
		$text_color = 'inherit';
	}
	$background_color = radio_station_get_setting( 'player_bar_background' );
	if ( '' == $background_color ) {
		$background_color = 'transparent';
	}
	$css = "#radio-station-player-bar {";
	$css .= "position: fixed; left: 0; right: 0; text-align: center; z-index: 9999;";
	$css .= "color: " . $text_color . "; background-color: " . $background_color . ";";
	$css .= "}" . PHP_EOL;
	
	// --- adjust body padding to give bar space ---
	// 2.4.1.6: add top or bottom padding to body automatically
	if ( 'top' == $position ) {
		$css .= "body {padding-top: " . esc_js( $bar_height ) . "px;}" . PHP_EOL;
	} elseif ( 'bottom' == $position ) {
		$css .= "body {padding-bottom: " . esc_js( $bar_height ) . "px;}" . PHP_EOL;
	}
	
	// --- positional adjustments ---
	$css .= "#radio-station-player-bar.top {top: 0; width: 100%; height: " . $bar_height . "px;}" . PHP_EOL;
	$css .= "body.admin-bar #radio-station-player-bar.top {top: 32px;}" . PHP_EOL;
	$css .= "body.admin-bar.teleporter-loading #radio-station-player-bar.top {top: 39px;}" . PHP_EOL;
	$css .= "@media screen and (max-width: 782px) {body.admin-bar #radio-station-player-bar.top {top: 46px;} }" . PHP_EOL;
	$css .= "@media screen and (max-width: 782px) {body.admin-bar.teleporter-loading.top #radio-station-player-bar {top: 53px;} }" . PHP_EOL;
	$css .= "#radio-station-player-bar.bottom {bottom: 0; width: 100%; height: " . $bar_height . "px;}" . PHP_EOL;
	// $css .= "#radio-station-player-bar.left {left: 0; width: 120px; height: 100%;}" . PHP_EOL;
	// $css .= "#radio-station-player-bar.right {right: 0; width: 120px; height: 100%;}" . PHP_EOL;

	// --- page loader style adjustments ---
	if ( 'top' == $position ) {
		// --- admin bar adjustments for top bar player ---
		$css .= "body.teleporter-loading #radio-station-player-bar.top {top: 7px;}" . PHP_EOL;
	} elseif ( 'bottom' == $position ) {
		// --- move loading div to above bottom bar player ---
		$css .= "body.teleporter-loading #teleporter-loading {bottom: " . $bar_height . "px;}" . PHP_EOL;
	}

	// --- set station logo image ---
	// 2.4.1.3: fix to CSS selector ID target
	$station_image_url = radio_station_get_station_image_url();
	if ( '' != $station_image_url ) {
		$css .= "#radio-station-player-bar .rp-station-image {background-image: url(" . esc_url( $station_image_url ) . ");}" . PHP_EOL;
	} else {
		$css .= "#radio-station-player-bar .rp-station-image {display: none;}" . PHP_EOL;
	}

	// --- player bar section widths ---
	$css .= "#radio-station-player-bar .rp-station-info, ";
	$css .= "#radio-station-player-bar .rp-interface, ";
	$css .= "#radio-station-player-bar .rp-show-info {width: auto;}" . PHP_EOL;
	// 2.4.1.6: moved show-info min-width from media queries
	$css .= "#radio-station-player-bar .rp-show-info {min-width: 330px;}" . PHP_EOL;
	// 2.4.1.6: increased max-width rule as causing Firefox issues
	$css .= "#radio-station-player-bar .rp-interface {min-width: 330px; max-width: 430px;}" . PHP_EOL;

	// --- override text color on show title link ---
	// 2.4.1.3: fix color for clickable show links
	$css .= "#radio-station-player-bar .rp-show-title a, #radio-station-player-bar .rp-show-title a:focus,
	#radio-station-player-bar .rp-show-title a:visited {color: " . $text_color . ";}" . PHP_EOL;

	// --- now playing section ---
	// 2.4.1.3: added now playing styles for animation
	$css .= "#radio-station-player-bar .rp-interface {overflow: hidden;}" . PHP_EOL;

	// --- now playing animations ---
	// 2.4.1.3: added now playing animations
	// ref: https://code-boxx.com/html-css-news-ticker-horizontal-vertical/
	// $animation = radio_station_get_setting( 'player_bar_nowplaying_animation' );
	$animation = 'backandforth';
	if ( 'horizontal' == $animation ) {
		$css .= "#radio-station-player-bar .rp-now-playing:hover {animation-play-state: paused;}" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-now-playing {display: flex;}" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-now-playing-item, #radio-station-player-bar .rp-now-playing div {
			flex-shrink: 0; box-sizing: border-box; padding: 0 10px; text-align: center;
		}" . PHP_EOL;
		$css .= "@keyframes hticker { 0% {transform: translate3d(100%, 0, 0);} 100% {transform: translate3d(-100%, 0, 0);} }" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-now-playing {animation: hticker linear 15s infinite;}" . PHP_EOL;
	} elseif ( 'backandforth' == $animation ) {
		$css .= "#radio-station-player-bar .rp-now-playing:hover {animation-play-state: paused;}" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-now-playing {text-align: center; white-space: nowrap;}" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-now-playing {animation: backandforth linear 15s infinite;}" . PHP_EOL;
		$css .= "@keyframes backandforth {0% {transform: translateX(0);} 25% {transform: translateX(calc(-50%));}";
	  	$css .= " 50% {transform: translateX(0);} 75% {transform: translateX(calc(50%));} 100% {transform: translateX(0);} }" . PHP_EOL;
	} else {
		$css .= "#radio-station-player-bar .rp-now-playing {overflow: hidden; text-align: left;}" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-now-playing:hover {text-align:right;}" . PHP_EOL;
	}

	// --- width media queries for player bar ---
	$css .= radio_station_pro_player_bar_media_queries();

	// --- filter and add player bar styles inline ---
	$css = apply_filters( 'radio_station_player_bar_styles', $css );
	wp_add_inline_style( 'radio-player', $css );

}

// ------------------------
// Player Bar Media Queries
// ------------------------
function radio_station_pro_player_bar_media_queries() {

	// 2.4.1.6: moved show info min-width to non-media queries

	// --- reduced station info
	$css = "@media (max-width: 990px) {" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-station-title {width: 150px;}" . PHP_EOL;
	$css .= "}" . PHP_EOL;

	// --- reduced show info ---
	$css .= "@media (max-width: 890px) {" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-show-info {width: 150px;}" . PHP_EOL;
	$css .= "}" . PHP_EOL;

	// --- alternative station text ---
	$css .= "@media (max-width: 790px) {" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-station-text {display: none;}" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-station-text-alt {display: block;}" . PHP_EOL;
		// 2.4.1.3: do not display alt station text with now playing metadata
		$css .= "#radio-station-player-bar.now-playing .rp-station-text-alt {display: none;}" . PHP_EOL;
		// 2.4.1.6: remove title width when alt station text is displayed
		$css .= "#radio-station-player-bar .rp-station-title {width: auto;}" . PHP_EOL;
	$css .= "}" . PHP_EOL;

	// alternative show text ---
	$css .= "@media (max-width: 640px) {" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-show-text {display: none;}" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-show-text-alt {display: block;}" . PHP_EOL;
	$css .= "}" . PHP_EOL;

	// --- hide show image ---
	$css .= "@media (max-width: 490px) {" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-show-image {display:none;}" . PHP_EOL;
	$css .= "}" . PHP_EOL;

	// --- hide station image ---
	$css .= "@media (max-width: 410px) {" . PHP_EOL;
		$css .= "#radio-station-player-bar .rp-station-image {display:none;}" . PHP_EOL;
		// 2.4.1.6: hide alternative station text on narrow screens
		$css .= "#radio-station-player-bar .rp-station-text-alt {display:none;}" . PHP_EOL;
	$css .= "}" . PHP_EOL;

	// --- filter and return ---
	$css = apply_filters( 'radio_station_player_bar_media_queries', $css );
	return $css;
}

// --------------------
// === Popup Player ===
// --------------------

// TODO: ...

