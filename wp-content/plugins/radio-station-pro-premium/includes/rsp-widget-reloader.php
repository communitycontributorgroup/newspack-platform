<?php

// =========================
// === Radio Station Pro ===
// =========================
// ---- Widget Reloader ----
// =========================

// === Dynamic Countdown Reloader ===
// - Set Dynamic Countdown URLs
// - Enqueue Dynamic Countdown Script
// === Widget Countdown Filters ===
// - Filter Default Reload Setting
// - Widget Setting Field Filters
// - Widget Setting Update Filters
// - Widget Reload Setting Value Filter
// === AJAX Actions ===
// - Current Show Widget Reloader
// - Upcoming Shows Widget Reloader
// - Current Playlist Widget Reloader
// - Dynamic Reloader Script

// --------------------------
// Set Dynamic Countdown URLs
// --------------------------
add_filter( 'radio_station_countdown_dynamic', 'radio_station_pro_countdown_dynamic', 10, 4 );
function radio_station_pro_countdown_dynamic( $output, $context, $atts, $time ) {

	global $radio_station_data;

	// --- set reload URL for output ---
	$action = str_replace( '-', '_', $context );
	$url = add_query_arg( 'action', 'radio_station_pro_' . $action, admin_url( 'admin-ajax.php' ) );
	foreach ( $atts as $key => $value) {
		$url = add_query_arg( $key, $value, $url );
	}
	// (add an extra second to make work for current show/playlist)
	$url = add_query_arg( 'for_time', ($time + 1), $url );
	$output = '<input type="hidden" class="reload-url" value="' . esc_url( $url ) . '">';

	// --- only load script once ---
	if ( isset( $radio_station_data['dynamic_script'] ) ) {
		return $output;
	} else {
		radio_station_pro_countdown_script();
		$radio_station_data['dynamic_script'] = true;
	}
	
	return $output;
}

// --------------------------------
// Enqueue Dynamic Countdown Script
// --------------------------------
function radio_station_pro_countdown_script() {

	// --- set javascript ---
	// TODO: maybe move to a separate javascript file ?
	$js = "var radio_widget_reloader;
	jQuery(document).ready(function() {
		radio_widget_reloader = setInterval(radio_countdown_dynamic, 5000);
	});
	
	function radio_countdown_dynamic() {

		radio.current_time = Math.floor((new Date()).getTime() / 1000);
		radio.offset_time = radio.current_time + radio.timezone.offset;
		if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
		if (radio.debug) {console.log(radio.current_time+' - '+radio.offset_time);}
		
		/* Current Show */
		jQuery('.current-show-wrap').each(function() {
			if (jQuery(this).find('.current-show-end').length && !jQuery(this).hasClass('preloaded')) {
				end = jQuery(this).find('.current-show-end').val();
				if (end) {
					if (jQuery(this).find('.current-time-override').length) {
						val = jQuery(this).find('.current-time-override').val();
						if ('' != val) {radio.offset_time = val * 1000; jQuery(this).find('.current-time-override').val('');}
					}
					diff = end - radio.offset_time;
					if (radio.debug) {console.log('Current Show End: '+end+' - Now: '+radio.offset_time+' - Diff: '+diff);}
					if ((diff > -1) && (diff < 30)) {
						jQuery(this).addClass('preloaded');
						url = jQuery(this).find('.reload-url').val();
						id = jQuery(this).attr('id');
						radio_countdown_iframe(url, id);
					}
				}
			}
		});
		/* Upcoming Shows */
		jQuery('.upcoming-shows-wrap').each(function() {
			if (jQuery(this).find('.upcoming-show-times').length && !jQuery(this).hasClass('preloaded')) {
				value = jQuery(this).find('.upcoming-show-times').val();
				if (value) {
					if (jQuery(this).find('.current-time-override').length) {
						val = jQuery(this).find('.current-time-override').val();
						if ('' != val) {radio.offset_time = val * 1000; jQuery(this).find('.current-time-override').val('');}
					}
					times = value.split('-');
					diff = times[0] - radio.offset_time;
					if (radio.debug) {console.log('Upcoming Show Start: '+times[0]+' - Now: '+radio.offset_time+' - Diff: '+diff);}
					if ((diff > -1) && (diff < 20)) {
						jQuery(this).addClass('preloaded');
						url = jQuery(this).find('.reload-url').val();
						id = jQuery(this).attr('id');
						radio_countdown_iframe(url, id);
					}
				}
			}
		});
		/* Current Playlist */
		jQuery('.current-playlist-wrap').each(function() {
			if (jQuery(this).find('.playlist-show-end') && !jQuery(this).hasClass('preloaded')) {
				end = jQuery(this).find('.current-playlist-end').val();
				if (end) {
					if (jQuery(this).find('.current-time-override').length) {
						val = jQuery(this).find('.current-time-override').val();
						if ('' != val) {radio.offset_time = val * 1000; jQuery(this).find('.current-time-override').val('');}
					}
					diff = end - radio.offset_time;
					if (radio.debug) {console.log('Current Playlist End: '+end+' - Now: '+radio.offset_time+' - Diff: '+diff);}
					if ((diff > -1) && (diff < 10)) {
						jQuery(this).addClass('preloaded');
						url = jQuery(this).find('.reload-url').val();
						id = jQuery(this).attr('id');
						radio_countdown_iframe(url, id);
					}
				}
			}
		});
	}

	function radio_countdown_iframe(url,id) {
		url += '&timezone_offset='+radio.timezone.offset+'&timestamp='+radio.current_time+'&widgetid='+id;
		if (radio.debug) {url += '&rs-debug=1';}
		iframe = document.createElement('iframe');
		iframe.setAttribute('id', 'iframe-'+id);
		iframe.setAttribute('src', url);
		iframe.setAttribute('style', 'display: none;');
		console.log(id);
		/* document.getElementById(id).appendChild(iframe); */
		document.getElementsByTagName('body')[0].appendChild(iframe);
	}" . PHP_EOL;

	// --- add script inline ---
	wp_add_inline_script( 'radio-station', $js );

}


// --------------------------------
// === Widget Countdown Filters ===
// --------------------------------

// -----------------------------
// Filter Default Reload Setting
// -----------------------------
add_filter( 'radio_station_current_show_dynamic', 'radio_station_pro_dynamic_default' );
add_filter( 'radio_station_upcoming_shows_dynamic', 'radio_station_pro_dynamic_default' );
add_filter( 'radio_station_current_playlist_dynamic', 'radio_station_pro_dynamic_default' );
function radio_station_pro_dynamic_default( $value ) {
	$dynamic = radio_station_get_setting( 'dynamic_reload' );
	$value = ( 'yes' == $dynamic ) ? true : false;
	return $value;
}

// ----------------------------
// Widget Setting Field Filters
// ----------------------------
add_filter( 'radio_station_current_show_widget_fields', 'radio_station_pro_widget_dynamic_field', 10, 3 );
add_filter( 'radio_station_upcoming_shows_widget_fields', 'radio_station_pro_widget_dynamic_field', 10, 3 );
add_filter( 'radio_station_playlist_widget_fields', 'radio_station_pro_widget_dynamic_field', 10, 3 );
function radio_station_pro_widget_dynamic_field( $fields, $widget, $instance ) {

	// --- add dynamic reloading widget field ---
	$dynamic = isset( $instance['dynamic'] ) ? $instance['dynamic'] : '';
	$fields .= '
	<p>
		<label for="' . esc_attr( $widget->get_field_id( 'dynamic' ) ) . '">
			<select id="' .esc_attr( $widget->get_field_id( 'dynamic' ) ) . '" name="' . esc_attr( $widget->get_field_name( 'dynamic' ) ) . '">
				<option value="" ' . selected( $dynamic, '', false ) . '>' . esc_html( __( 'Default', 'radio-station' ) ) . '</option>
				<option value="on" ' . selected( $dynamic, 'on', false ) . '>' . esc_html( __( 'On', 'radio-station' ) ) . '</option>
				<option value="off" ' . selected( $dynamic, 'off', false ) . '>' . esc_html( __( 'Off', 'radio-station' ) ) . '</option>
			</select>
			' . esc_html( __( 'Automatic Reloading', 'radio-station' ) ) . '
		</label>
	</p>';
		
	return $fields;
}

// -----------------------------
// Widget Setting Update Filters
// -----------------------------
add_filter( 'radio_station_current_show_widget_update', 'radio_station_pro_widget_dynamic_update', 10, 3 );
add_filter( 'radio_station_upcoming_shows_widget_update', 'radio_station_pro_widget_dynamic_update', 10, 3 );
add_filter( 'radio_station_playlist_widget_update', 'radio_station_pro_widget_dynamic_update', 10, 3 );
function radio_station_pro_widget_dynamic_update( $instance, $new_instance, $old_instance ) {
	if ( isset( $new_instance['dynamic'] ) ) {
		$instance['dynamic'] = $new_instance['dynamic'];
	} elseif ( isset( $old_instance['dynamic'] ) ) {
		$instance['dynamic'] = $old_instance['dynamic'];
	} else {
		$instance['dynamic'] = '';
	}
	return $instance;
}

// ----------------------------------
// Widget Reload Setting Value Filter
// ----------------------------------
add_filter( 'radio_station_current_show_widget_atts', 'radio_station_pro_widget_reload_atts', 10, 2 );
add_filter( 'radio_station_upcoming_shows_widget_atts', 'radio_station_pro_widget_reload_atts', 10, 2 );
add_filter( 'radio_station_current_playlist_widget_atts', 'radio_station_pro_widget_reload_atts', 10, 2 );
function radio_station_pro_widget_reload_atts( $atts, $instance ) {
	// --- only set dynamic attribute if not default ---
	if ( isset( $instance['dynamic'] ) ) {
		if ( 'on' == $instance['dynamic'] ) {
			$atts['dynamic'] = 1;
		} elseif ( 'off' == $instance['dynamic'] ) {
			$atts['dynamic'] = 0;
		}
	}
	return $atts;
}


// --------------------
// === AJAX Actions ===
// --------------------

// ----------------------------
// Current Show Widget Reloader
// ----------------------------
add_action( 'wp_ajax_radio_station_pro_current_show', 'radio_station_pro_current_show_reloader' );
add_action( 'wp_ajax_nopriv_radio_station_pro_current_show', 'radio_station_pro_current_show_reloader' );
function radio_station_pro_current_show_reloader() {

	// --- sanitize shortcode attribute values ---
	$extras = array( 'for_time' => 'integer', 'timezone_offset' => 'integer' );
	$atts = radio_station_sanitize_shortcode_values( 'current-show', $extras );
	
	// --- output new widget contents ---
	echo '<div id="widget-reload-contents">';
	echo radio_station_current_show_shortcode( $atts );
	echo '</div>';

	// --- dynamic reload new shortcode content ---
	radio_station_pro_dynamic_reloader_script( $atts, 'current-show' );

	exit;
}

// -----------------------
// Upcoming Shows Reloader
// -----------------------
add_action( 'wp_ajax_radio_station_pro_upcoming_shows', 'radio_station_pro_upcoming_shows_reloader' );
add_action( 'wp_ajax_nopriv_radio_station_pro_upcoming_shows', 'radio_station_pro_upcoming_shows_reloader' );
function radio_station_pro_upcoming_shows_reloader() {

	// --- sanitize shortcode attribute values ---
	$extras = array( 'for_time' => 'integer', 'timezone_offset' => 'integer' );
	$atts = radio_station_sanitize_shortcode_values( 'upcoming-shows', $extras );
	
	// --- output new widget contents ---
	echo '<div id="widget-reload-contents">';
	echo radio_station_upcoming_shows_shortcode( $atts );
	echo '</div>';

	// --- dynamic reload new shortcode content ---
	radio_station_pro_dynamic_reloader_script( $atts , 'upcoming-shows' );

	exit;
}

// --------------------
// Now Playing Reloader
// --------------------
add_action( 'wp_ajax_radio_station_pro_current_playlist', 'radio_station_pro_current_playlist_reloader' );
add_action( 'wp_ajax_nopriv_radio_station_pro_current_playlist', 'radio_station_pro_current_playlist_reloader' );
function radio_station_pro_current_playlist_reloader() {

	// --- sanitize shortcode attribute values ---
	$extras = array( 'for_time' => 'integer', 'timezone_offset' => 'integer' );
	$atts = radio_station_sanitize_shortcode_values( 'current-playlist', $extras );

	// --- output new widget contents ---
	echo '<div id="widget-reload-contents">';
	echo radio_station_current_playlist_shortcode( $atts );
	echo '</div>';

	// --- dynamic reload new shortcode content ---
	radio_station_pro_dynamic_reloader_script( $atts, 'current-playlist' );

	exit;
}

// -----------------------
// Dynamic Reloader Script
// -----------------------
function radio_station_pro_dynamic_reloader_script( $atts, $type ) {

	// --- get widget element ID to reload ---
	$id = $_REQUEST['widgetid'];
	$sanitized = sanitize_title( $id );
	if ( $id != $sanitized ) {
		$id = $type . '-widget-contents-' . $atts['id'];
	}
		
	// --- reload javascript ---
	$js = '';

	// --- set debug switch ---
	if ( isset( $_REQUEST['rs-debug'] ) && ( '1' == $_REQUEST['rs-debug'] ) ) {
		$js .= 'var radiodebug = true;' . PHP_EOL;
	} else {
		$js .= 'var radiodebug = false;' . PHP_EOL;
	}
	
	// --- set reload variables ---
	$js .= "var reloadtime = " . esc_js( $atts['for_time'] ) . ";
	var timezoneoffset = " . esc_js( $atts['timezone_offset'] ) . ";
	var reloadid = '" . esc_js( $id ) . "';
	var reloadcycler = setInterval(radio_reload_check, 1000); " . PHP_EOL;

	// --- reloader cycle function ---
	$js .= "function radio_reload_check() {
		currenttime = Math.floor((new Date()).getTime() / 1000);
		offsettime = currenttime + timezoneoffset;
		if (radiodebug) {
			console.log('Now: '+currenttime+' - Reload at: '+reloadtime);
			console.log('Offset: '+timezoneoffset+' - Offset Reload time: '+offsettime);
		}
		diffa = reloadtime - currenttime;
		diffb = reloadtime - offsettime;
		if (radiodebug) {console.log('Diff to Current Time: '+diffa+' - Diff to Offset Time: '+diffb);}
		if (diffa < 0) {
			if (!parent.jQuery('#'+reloadid).length) {clearInterval(reloadcycler); return;}
			parent.jQuery('#'+reloadid).fadeOut();
			widget = document.getElementById('widget-reload-contents').innerHTML;
			container = parent.document.getElementById(reloadid).innerHTML = widget;
			parent.jQuery('#'+reloadid).fadeIn().removeClass('preloaded');
			console.log('" . esc_js( $type ) . " widget reloaded!');
			clearInterval(reloadcycler);
			parent.window.jQuery('#iframe-'+reloadid).remove();
		} else if (radiodebug) {console.log(reloadid+' reloading in '+diffa);}
	}" . PHP_EOL;

	// phpcs:ignore WordPress.Security.OutputNotEscaped
	echo '<script>' . $js . '</script>' . PHP_EOL;
}
