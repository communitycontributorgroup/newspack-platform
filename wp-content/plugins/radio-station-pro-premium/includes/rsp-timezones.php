<?php

// =========================
// === Radio Station Pro ===
// =========================
// --- Timezone Switcher ---
// =========================

// - Add Timezone Switcher to Clock
// - Timezone Switcher Selector
// - Set User Timezone from Meta/Cookie
// - Timezone Switcher Javascript
// - AJAX Timezone Switcher Save


// -------------------------
// === Timezone Switcher ===
// -------------------------

// ------------------------------
// Add Timezone Switcher to Clock
// ------------------------------
add_filter( 'radio_station_timezone_shortcode', 'radio_station_pro_add_timezone_switcher' );
add_filter( 'radio_station_clock', 'radio_station_pro_add_timezone_switcher' );
function radio_station_pro_add_timezone_switcher( $html ) {
	$switching = radio_station_get_setting( 'timezone_switching' );
	if ( 'yes' == $switching ) {
		$html .= radio_station_pro_timezone_switcher();
	}
	return $html;
}

// --------------------------
// Timezone Switcher Selector
// --------------------------
function radio_station_pro_timezone_switcher() {

	global $radio_station_data;

	// --- get timezone switcher instance ---
	if ( isset( $radio_station_data['timezone-switcher'] ) ) {
		$radio_station_data['timezone-switcher']++;
		$instance = $radio_station_data['timezone-switcher'];
	} else {
		$instance = 0;
		$radio_station_data['timezone-switcher'] = $instance;
	}

	// --- get current timezone selection ---
	$current_timezone = false;
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		$current_timezone = get_user_meta( $current_user->ID, 'rs_user_timezone', true );
	}
	if ( !$current_timezone && isset( $_COOKIE['rs-user-timezone'] ) ) {
		$current = $_COOKIE['rs-user-timezone'];
		if ( is_user_logged_in() ) {
			// --- maybe resync now logged in user ---
			update_user_meta( $current_user->ID, 'rs_user_timezone', $current_timzone );
		}
	}
	
	$switcher = '';

	// --- change link ---
	$change_link = '<div id="timezone-change-' . esc_attr( $instance ) . '" class="timezone-change">';
	$change_link .= '<a href="javascript:void(0);" class="timezone-change-link" onclick="radio_change_timezone(\'' . esc_attr( $instance ) . '\');">' . esc_html( __( 'Change Timezone', 'radio-station' ) ) . '</a>';
	$change_link .= '</div>';
	$switcher .= $change_link;

	// --- loop timezone options ---
	$timezones = radio_station_get_timezone_options( false );
	$regions = array(); 
	$current_region = false;
	foreach ( $timezones as $value => $label ) {
		if ( strstr( $value, '*OPTGROUP*' ) ) {
			$region = str_replace( '*OPTGROUP*', '', $value );
			if ( !in_array( $region, $regions ) ) {
				$regions[] = $region;
			}
		}
		if ( $current_timezone && ( $current_timezone == $value ) ) {
			$current_region = $region;
		}
	}
	// $switcher .= "Regions: " . print_r( $regions, true );

	// --- timezone switcher inputs ---
	$switcher .= '<div id="timezone-switcher-' . esc_attr( $instance ) . '" class="timezone-switcher">';

		// --- region select input ---
		$switcher .= '<select class="timezone-region-select" id="timezone-region-select-' . esc_attr( $instance ) . '" onchange="radio_timezone_region(this,\'' . esc_attr( $instance ) . '\')">';
		$switcher .= '<option value="">' . __( 'Select Region...', 'radio-station' ) . '</option>';
		foreach ( $timezones as $value => $label ) {
			if ( strstr( $value, '*OPTGROUP*' ) ) {
				$region = str_replace( '*OPTGROUP*', '', $value );
				$switcher .= '<option value="' . esc_attr( $region ) . '"';
				if ( $current_region && ( $current_region == $region ) ) {
					$switcher .= ' selected="selected"';
				}
				$switcher .= '>' . esc_html( $label ) . '</option>' . PHP_EOL;

			}
		}
		$switcher .= '</select>' . PHP_EOL;

		// --- clear timezone link ---
		$switcher .= '<div class="timezone-clear">';
		$switcher .= '<a href="javascript:void(0);" class="timezone-clear-link" onclick="radio_clear_timezone(\'' . esc_attr( $instance ) . '\');">' . esc_html( __( 'Clear', 'radio-station' ) ) . '</a>';
		$switcher .= '</div>';

		// --- cancel change timezone link ---
		$switcher .= '<div class="timezone-cancel">';
		$switcher .= '<a href="javascript:void(0);" class="timezone-cancel-link" onclick="radio_cancel_timezone(\'' . esc_attr( $instance ) . '\');">' . esc_html( __( 'Cancel', 'radio-station' ) ) . '</a>';
		$switcher .= '</div>';

		// --- loop regions for timezone selections ---
		foreach ( $regions as $region ) {
		
			$switcher .= '<select class="timezone-select" id="timezone-select-' . esc_attr( $region ) . '-' . esc_attr( $instance ) . '" onchange="radio_timezone_switch(this,\'' . esc_attr( $instance ) . '\')" style="display:none;">';
			$switcher .= '<option value="">' . __( 'Select Timezone...', 'radio-station' ) . '</option>';
			$timezone_region = false;
			foreach ( $timezones as $value => $label ) {
				if ( strstr( $value, '*OPTGROUP*' ) ) {
					$timezone_region = str_replace( '*OPTGROUP*', '', $value );
				} elseif ( $region == $timezone_region ) {
					$switcher .= '<option value="' . esc_attr( $value ) . '"';
					if ( $current_timezone && ( $current_timezone == $value ) ) {
						$switcher .= ' selected="selected"';
					}
					$switcher .= '>' . esc_html( $label ) . '</option>' . PHP_EOL;
				}
			}
			$switcher .= '</select>';
		}
		
	$switcher .= '</div>';

	// 2.4.1.4: fix to incorrect match on first switcher instance
	// 2.4.1.5: add action instead of relying on instance check
	if ( !has_action( 'wp_footer', 'radio_station_pro_timezone_resources' ) ) {
		add_action( 'wp_footer', 'radio_station_pro_timezone_resources' );
	}

	return $switcher;
}

// ----------------------
// Add Timezone Resources
// ----------------------
function radio_station_pro_timezone_resources() {

	// --- timezone selection iframe save ---
	echo '<iframe name="timezone-save-frame" id="timezone-save-frame" style="display:none;" frameborder="0"></iframe>';

	// --- output timezone switcher styling ---
	$css = ".timezone-switcher {display: none;} .timezone-switcher.active {display: block;}
	.timezone-region-select, .timezone-select, .timezone-clear, .timezone-cancel {
		display: inline-block; vertical-align: middle; font-size: 0.9em;}
	.timezone-region-select, .timezone-clear, .timezone-cancel {margin-right: 20px;}";

	// --- filter and output timezone switcher styling ---
	$css = apply_filters( 'radio_station_timezone_switcher_styles', $css );
	echo '<style>' . $css . '</style>';

	// --- enqueue timezone switcher javascript ---
	// 2.4.1.5: fix possible double script output
	radio_station_pro_timezone_javascript();
}
		
// ----------------------------------
// Set User Timezone from Meta/Cookie
// ----------------------------------
add_filter( 'radio_station_localization_script', 'radio_station_pro_set_user_timezone' );
function radio_station_pro_set_user_timezone( $js ) {

	// --- set override flags ---
	$js .= "radio.timezone.meta_override = false; radio.timezone.user_override = false;";
	$js .= "radio.timezone.offset_override = false; radio.timezone.zonename_override = false;" . PHP_EOL;

	// --- get timezone override ---
	$js .= "function radio_timezone_override() {
		if (radio.timezone.user_override) {return radio.timezone.user_override;}
		timezone = radio_cookie.get('rs-user-timezone');
		if (radio.debug) {console.log('Cookie Timezone: '+timezone);}
		if (timezone != null) {	
			radio.timezone.user_override = timezone;
		} else if (radio.timezone.meta_override) {
			radio.timezone.user_override = radio.timezone.meta_override;
		}
		return radio.timezone.user_override;
	}" . PHP_EOL;

	//  --- get timezone offset ---
	$js .= "function radio_offset_override(init) {
		if (!init && radio.timezone.user_offset) {return radio.timezone.user_offset;}
		datetime = new Date();
		zonetimedate = moment(datetime.toISOString()).tz(radio.timezone.user_override);
		radio.timezone.zonename_override = zonetimedate.format('z');
		offset = zonetimedate.format('Z');
		if (offset.substr(0,1) == '-') {multiplier = -1;} else {multiplier = 1;}
		offset = offset.replace('-','').replace('+', ''); parts = offset.split(':');
		radio.timezone.user_offset = multiplier * (parseInt(parts[0] * 60) + parseInt(parts[1]));
		if (radio.debug) {console.log('Moment Zone: '+zonetimedate.format()+' - Offset: '+offset+' + User Offset: '+radio.timezone.user_offset);}
		return radio.timezone.user_offset;
	}" . PHP_EOL;

	// --- get formatted time with timezone override ---
	$js .= "function radio_user_override(time, format) {
		override = radio_timezone_override();
		if (!override) {return false;}
		datetime = new Date(time * 1000);
		zonetime = moment(datetime.toISOString()).tz(override);	
		formatted = radio_convert_time(zonetime, format);
		if (radio.debug) {console.log('Time: '+time+' - Zone: '+override+' - Format: '+format+' - Formatted: '+formatted);}
		return formatted;
	}" . PHP_EOL;

	// --- set saved user timezone ---
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		$timezone = get_user_meta( $current_user->ID, 'rs-user-timezone', true );
		if ( $timezone ) {
			$js .= "radio.timezone.meta_override = '" . esc_js( $timezone ) . "';" . PHP_EOL;
			$js .= "if (radio.debug) {console.log('Meta Timezone: '+radio.timezone.meta_override);}" . PHP_EOL;
		}
	}

	return $js;
}



// ----------------------------
// Timezone Switcher Javascript
// ----------------------------
function radio_station_pro_timezone_javascript() {

	$admin_ajax = admin_url( 'admin-ajax.php' );

	$js = '';
	
	// --- change timezone link click function ---
	$js .= "function radio_change_timezone(id) {
		if (!jQuery('#timezone-switcher-'+id).hasClass('active')) {
			jQuery('#timezone-switcher-'+id).addClass('active');
			jQuery('#timezone-change-'+id).hide();
		}
	}" . PHP_EOL;

	// --- clear timezone selection ---
	$js .= "function radio_clear_timezone(id) {
		radio.timezone.user_override = false;
		radio.timezone.meta_override = false;
		radio.timezone.offset_override = false;
		radio.timezone.zonename_override = false;
		if (radio.debug) {console.log('Timezone Selection Cleared');}
		radio_clock_date_time(true);
		radio_convert_times();
		radio_cookie.delete('rs_user_timezone');
		radio_cancel_timezone(id);" . PHP_EOL;

		// --- maybe clear user meta ---
		if ( is_user_logged_in() ) {
			$js .= "		src = '" . esc_url( $admin_ajax ) . "?action=radio_station_pro_timezone_save&timezone=CLEAR';".PHP_EOL;
			$js .= "		document.getElementById('timezone-save-frame').src = src;" . PHP_EOL;
		} 

	$js .= "}" . PHP_EOL;

	// --- cancel timezone change function ---
	$js .= "function radio_cancel_timezone(id) {
		jQuery('#timezone-switcher-'+id).removeClass('active');
		jQuery('#timezone-change-'+id).show();
	}" . PHP_EOL;

	// --- on region change function ---
	$js .= "function radio_timezone_region(el,id) {
		region = el.options[el.selectedIndex].value;
		/* region = jQuery(el).val(); */
		if (radio.debug) {console.log('Selected Region: '+region);}
		jQuery('.timezone-select').hide();
		if (region != '') {jQuery('#timezone-select-'+region+'-'+id).show();}
	}" . PHP_EOL;

	// --- on timezone change function ---
	$js .= "function radio_timezone_switch(el,id) {
		timezone = el.options[el.selectedIndex].value;
		if (radio.debug) {console.log('Selected Timezone: '+timezone);}
		radio.timezone.user_override = timezone;
		offset = radio_offset_override(true);
		radio_cookie.set('rs_user_timezone', timezone, 30); 
		if (typeof radio_clock_date_time == 'function') {radio_clock_date_time(true);}
		radio_convert_times();
		radio_cancel_timezone(id);" . PHP_EOL;

		// --- maybe save to user meta ---
		if ( is_user_logged_in() ) {
			$js .= "		src = '" . esc_url( $admin_ajax ) . "?action=radio_station_pro_timezone_save&timezone='+encodeURIComponent(timezone);".PHP_EOL;
			$js .= "		document.getElementById('timezone-save-frame').src = src;" . PHP_EOL;
		} 

	$js .= "}" . PHP_EOL;

	// --- Display User Timezone Override ---
	$js .= "function radio_display_override() {
		if (jQuery('.radio-user-timezone').length) {
			if (typeof radio.timezone.user_override == 'undefined') {return;}
			tz = radio_timezone_override();
			if (!tz) {return false;}
			offset = radio_offset_override(false);
			if (radio.debug) {console.log('Custom Timezone Display: '+tz+' - '+offset);}
			if (tz.indexOf('/') > -1) {
				tz = tz.replace('/', ', '); tz = tz.replace('_',' ');
				houroffset = parseInt(offset);
				if (houroffset == 0) {userzone = ' [UTC]';}
				else {
					houroffset = houroffset / 60;
					if (houroffset > 0) {tz += ' [UTC+'+houroffset+']';}
					else {tz += ' [UTC'+houroffset+']';}
				}
				jQuery('.radio-user-timezone').html(tz).css('display','inline-block');
				jQuery('.radio-user-timezone-title').css('display','inline-block');
			}
			return true;
		}
	}" . PHP_EOL;

	// --- enqueue inline script ---
	wp_add_inline_script( 'radio-station', $js );
}

// ---------------------------
// AJAX Timezone Switcher Save
// ---------------------------
add_action( 'wp_ajax_radio_station_pro_timezone_save', 'radio_station_pro_timezone_save' );
function radio_station_pro_timezone_save() {

	// --- get and validate timezone ---
	if ( !isset( $_REQUEST['timezone'] ) ) {exit;}
	$timezone = trim( $_REQUEST['timezone'] );
	$timezones = radio_station_get_timezone_options( false );
	if ( ( 'CLEAR' != $timezone ) && !array_key_exists( $timezone, $timezones ) ) {
		exit;
	}

	// --- save selected timezone to user meta ---
	$current_user = wp_get_current_user();
	if ( 'CLEAR' == $timezone ) {
		delete_user_meta( $current_user->ID, 'rs-user-timezone' );
	} else {
		update_user_meta( $current_user->ID, 'rs-user-timezone', $timezone );
	}

	exit;
}

