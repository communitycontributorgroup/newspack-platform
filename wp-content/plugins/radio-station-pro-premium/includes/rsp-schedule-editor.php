<?php

// =========================
// === Radio Station Pro ===
// =========================
// ---- Schedule Editor-----
// =========================

// === Schedule Editor ===
// - Schedule Editor Menu
// - Load Frontend Editor Resources
// - Enqueue Frontend Relogin Scripts
// - Enqueue Frontend Relogin Styles
// - Shift Thickbox Script
// - Filter Load Script to Display Schedule
// - Show Edit Link Filter
// - Show Shift Edit Link Filter
// === AJAX Actions ===
// - AJAX Edit Show Form
// - AJAX Add Shift Form
// - AJAX Shift Edit Styles
// - AJAX Add Shift
// - AJAX Add Override
// - Single Shift Save Action
// - Relogin AJAX Message


// -----------------------
// === Schedule Editor ===
// -----------------------

// --------------------
// Schedule Editor Menu
// --------------------
add_action( 'all_admin_notices', 'radio_station_pro_schedule_editor_menu', 9999 );
function radio_station_pro_schedule_editor_menu() {
	
	global $pagenow, $typenow;

	// --- check conditions ---	
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG );
	if ( ( 'edit.php' != $pagenow ) || !in_array( $typenow, $post_types ) ) {
		return;
	}
	
	// --- set view labels ---
	$labels = array(
		'editor'   => __( 'Visual Schedule Editor', 'radio-station' ),
		'table'    => __( 'Table', 'radio-station' ),
		'tabs'     => __( 'Tabs', 'radio-station' ),
		'grid'     => __( 'Grid', 'radio-station' ),
		'calendar' => __( 'Calendar', 'radio-station' ),
	);

	// --- set view dashicons ---
	$dashicons = true;
	$icons = array(
		'table'		=> 'dashicons-calendar',
		'tabs'		=> 'dashicons-index-card',
		'grid'		=> 'dashicons-grid-view',
		'calendar'	=> 'dashicons-calendar-alt',
	);

	// --- filter to use images instead of icons ---
	// (by setting URL for each view key)
	$images = apply_filters( 'radio_station_pro_view_images', false );
	if ( $images && is_array( $images ) && ( count( $images ) > 0 ) ) {
		$dashicons = false;
	}

	// --- view selector wrapper ---
	$selector = '<div id="master-schedule-views-wrapper">';

	// --- views menu label ---
	$selector .= '<div id="master-schedule-views-label" class="master-schedule-view-tab">';
	$selector .= '<b>' . esc_html( $labels['editor'] ) . '</b>:';
	$selector .= '</div>';

	// --- loop available views ---
	$views = array( 'table', 'tabs', 'grid', 'calendar' );
	foreach ( $views as $i => $view ) {

		// --- set view tab classes ---
		$view = trim( $view );
		$classes = array( 'master-schedule-view-tab' );
		// $default_view = 'table';
		// $default_view = radio_station_get_setting( 'editor_view' );
		// if ( $view == $default_view ) {
		//	$classes[] = 'current-view';
		// }
		$classlist = implode( ' ', $classes );

		// --- view tab items ---
		$onclick = "radio_load_view('" . esc_attr( $view ) . "')";
		$selector .= '<div id="master-schedule-view-tab-' . esc_attr( $view ) . '" class="' . esc_attr( $classlist ) . '" onclick="' . $onclick . '">' . PHP_EOL;

			// --- view icon (or image) ---
			$selector .= '<div id="master-schedule-view-icon-' .esc_attr( $view ) . '" class="master-schedule-view-icon">' . PHP_EOL;
			if ( $dashicons ) {
				$selector .= '<span class="schedule-view-icon dashicons ' . esc_attr( $icons[$view] ) . '"></span>' . PHP_EOL;
			} else {
				$selector .= '<img class="schedule-view-icon" src="' . esc_url( $images[$view] ) . '" border="0">' . PHP_EOL;
			}
			$selector .= '</div>' . PHP_EOL;

			// --- view label ---
			$selector .= '<div id="master-schedule-view-label-' . esc_attr( $view ) . '" class="master-schedule-view-label">' . PHP_EOL;
				$selector .= esc_html( $labels[$view] );
			$selector .= '</div>' . PHP_EOL;

		$selector .= '</div>' . PHP_EOL;

	}
	$selector .= '</div>' . PHP_EOL;

	// --- schedule loading div ---
	$selector .= '<div id="schedule-editor">' . PHP_EOL;
		$selector .= '<table id="master-program-schedule" class="schedule-editor" cellspacing="0" cellpadding="0"></table>' . PHP_EOL;
		$selector .= '<ul id="master-schedule-tabs" class="schedule-editor"></ul>' . PHP_EOL;
		$selector .= '<div id="master-schedule-tab-panels" class="schedule-editor"></div>' . PHP_EOL;
		$selector .= '<div id="master-schedule-grid" class="schedule-editor"></div>' . PHP_EOL;
		$selector .= '<table id="master-schedule-calendar" class="schedule-editor"></table>' . PHP_EOL;
	$selector .= '</div>' . PHP_EOL;
	
	// --- hidden date inputs ---
	$selector .= '<input type="hidden" id="schedule-start-date" value="">' . PHP_EOL;
	$selector .= '<input type="hidden" id="schedule-active-date" value="">' . PHP_EOL;
	
	// --- schedule loading iframe ---
	$selector .= '<iframe src="javascript:void(0)" id="schedule-loader-frame" name="schedule-loader-frame" style="display:none;"></iframe>' . PHP_EOL;

	// --- output selector ---
	echo $selector;

	// --- set schedule shortcode attributes ---
	$time_format = (int) radio_station_get_setting( 'clock_time_format' );
	$atts = array(

		// --- controls (off) ---
		'selector'          => 0,
		'clock'             => 0,
		'timezone'          => 0,

		// --- schedule display options ---
		'time'              => $time_format,
		'show_times'        => 1,
		'show_link'         => 1,
		'days'              => 0,
		'start_day'         => 0,
		'start_date'        => 0,
		'display_day'       => 'short',
		'display_date'      => 'jS',
		'display_month'	    => 'short',

		// --- show display options ---
		'show_image'        => 0,
		'show_desc'         => 0,
		'show_hosts'        => 1,
		'link_hosts'        => 0,
		'show_genres'       => 0,
		'show_encore'       => 1,
		'show_file'         => 0,

	);
	$atts = apply_filters( 'radio_station_pro_schedule_editor_atts', $atts );
	$atts['schedule_editor'] = 1;

	// --- add localization script ---
	$js = radio_station_localization_script();

	// --- view loader script ---
	$ajax_url = add_query_arg( 'action', 'radio_station_schedule', admin_url( 'admin-ajax.php' ) );
	foreach ( $atts as $key => $value ) {
		$ajax_url .= '&' . $key . '=' . $value;
	}
	$js .= "function radio_load_view(view) {
		views = ['table', 'tabs', 'grid', 'calendar'];
		for (i in views) {jQuery('#master-schedule-view-tab-'+views[i]).removeClass('current-view');}
		jQuery('#master-schedule-view-tab-'+view).addClass('current-view');
		jQuery('.schedule-editor').hide();
		if (view == 'table') {id = '#master-program-schedule';}
		else if (view == 'tabs') {id = '#master-schedule-tabs, #master-schedule-tab-panels';}
		else if (view == 'grid') {id = '#master-schedule-grid';}
		else if (view == 'calendar') {id = '#master-schedule-calendar';}
		jQuery(id).html('').show();
		ajax_url = '" . $ajax_url . "&view='+view;
		timestamp = Math.floor( (new Date()).getTime() / 1000 );
		ajax_url += '&timestamp='+timestamp;
		document.getElementById('schedule-loader-frame').src = ajax_url;
		radio_cookie.set('admin_schedule_view', view, 30);
	}" . PHP_EOL;

	// --- schedule view highlight ---
	$js .= "function radio_select_view(view) {
		if (!jQuery('#master-schedule-view-tab-'+view).hasClass('current-view')) {
			views = ['table', 'tabs', 'list', 'grid', 'calendar'];
			for (i in views) {jQuery('#master-schedule-view-tab-'+views[i]).removeClass('current-view');}
			jQuery('#master-schedule-view-tab-'+view).addClass('current-view');
		}
	}" . PHP_EOL;

	// --- highlight current user view on load ---		
	$js .= "jQuery(document).ready(function() {
		view = radio_cookie.get('admin_schedule_view');
		if (view && (view != '')) {radio_select_view(view);}
	});" . PHP_EOL;

	// --- add shift edit script ---
	$js .= radio_station_shift_edit_script();
	$js .= radio_station_override_edit_script();
	$js .= radio_station_pro_shift_thickbox_script();

	// --- add schedule view scripts to admin ---
	$js .= radio_station_master_schedule_loader_js( $atts );
	$js .= radio_station_master_schedule_table_js();
	$js .= radio_station_master_schedule_tabs_js();
	$js .= radio_station_pro_master_schedule_grid_js();
	$js .= radio_station_pro_master_schedule_calendar_js();
	$js .= radio_station_pro_shows_slider_script();

	// --- enqueue script inline ---
	wp_add_inline_script( 'radio-station-admin', $js );

	// --- add thickbox resources ---
	add_thickbox();
	radio_station_pro_thickbox_loading_image();

	// --- enqueue date picker ---
	radio_station_enqueue_datepicker();

	// --- enqueue schedule styles ---
	radio_station_enqueue_style( 'schedule' );
	radio_station_enqueue_style( 'views' );

	// --- set view switch styles ---
	$css = radio_station_pro_multiview_styles();

	// --- datepicker overlay UI fix ---
	$css .= '#ui-datepicker-div {position: absolute !important; z-index: 999999 !important;}';

	// --- thickbox styles ---
	$css .= radio_station_pro_thickbox_styles();

	// --- enqueue styles inline ---
	wp_add_inline_style( 'rs-schedule', $css );

	// --- maybe enqueue dashicons ---
	if ( $dashicons ) {
		wp_enqueue_style( 'dashicons' );
	}

}

// ------------------------------
// Load Frontend Editor Resources
// ------------------------------
add_filter( 'radio_station_schedule_controls_output', 'radio_station_pro_frontend_schedule', 11, 2 );
function radio_station_pro_frontend_schedule( $output, $atts ) {

	if ( is_admin() || !is_user_logged_in() || !current_user_can( 'edit_shows' ) ) {
	 	return $output;
	}

	// --- add thickbox resources ---
	add_thickbox();
	radio_station_pro_thickbox_loading_image();
	
	// --- thickbox styles ---
	$css = radio_station_pro_thickbox_styles();
	echo '<style>' . $css . '</style>';
	
	// --- enqueue date picker ---
	radio_station_enqueue_datepicker();

	// --- include post types admin for shift edit script ---
	include_once( RADIO_STATION_DIR . '/includes/post-types-admin.php' );

	// --- output shift edit script ---
	$js = radio_station_shift_edit_script();
	$js .= radio_station_override_edit_script();
	$js .= radio_station_pro_shift_thickbox_script();
	$output .= "<script>" . $js . "</script>";

	// --- enqueue dashicons ---
	wp_enqueue_style( 'dashicons' );

	// --- enqueue login scripts and styles ---
	radio_station_pro_enqueue_login_scripts();
	radio_station_pro_enqueue_login_styles();

	// --- ensure styles appears in login iframe ---
	add_action( 'login_enqueue_scripts', 'radio_station_pro_enqueue_login_styles' );

	// --- add the login html to the page ---
	add_action( 'wp_print_footer_scripts', 'wp_auth_check_html', 5 );

	// --- add wpbody div for shift saving ---
	$output .= '<div id="wpbody" style="display:none;"></div>';

	return $output;
}

// --------------------------------
// Enqueue Frontend Relogin Scripts
// --------------------------------
function radio_station_pro_enqueue_login_scripts() {

	// --- get scripts suffix ---
	if ( function_exists('wp_scripts_get_suffix') ) {
		$suffix = wp_scripts_get_suffix();
	} else {
		$suffix = '.min';
	}
	if ( RADIO_STATION_DEBUG ) {
		$suffix = '';
	}

	// --- enqueue auth check script ---
	// default interval is 3 minutes
	$interval = 3 * MINUTE_IN_SECONDS;
	wp_register_script( 'wp_auth_check', '/wp-includes/js/wp-auth-check' . $suffix . '.js' , array( 'heartbeat' ), false, 1 );
	wp_localize_script( 'wp_auth_check', 'authcheckL10n', array(
		'beforeunload' => __( 'Your session has expired. You can log in again from this page or go to the login page.' ),
		'interval' => apply_filters( 'wp_auth_check_interval', $interval ), 
	) );
	wp_enqueue_script( 'wp_auth_check' );
}

// -------------------------------
// Enqueue Frontend Relogin Styles
// -------------------------------
function radio_station_pro_enqueue_login_styles() {

	// --- get scripts suffix ---
	if ( function_exists('wp_scripts_get_suffix') ) {
		$suffix = wp_scripts_get_suffix();
	} else {
		$suffix = '.min';
	}
	if ( RADIO_STATION_DEBUG ) {
		$suffix = '';
	}

	// --- enqueue auth check styles ---
	wp_enqueue_style( 'wp_auth_check', '/wp-includes/css/wp-auth-check' . $suffix . '.css', array( 'dashicons' ), null, 'all' );
}

// ---------------------
// Shift Thickbox Script
// ---------------------
function radio_station_pro_shift_thickbox_script() {
	
	// --- set admin AJAX url ---
	$admin_ajax = admin_url( 'admin-ajax.php' );
	
	$js = '';

	// --- load thickbox content ---
	$js .= "var radio_edit_loader; var radio_edit_shift;
	function radio_thickbox_show(title, url, shiftid) {
		tb_show(title, url); 
		radio_edit_shift = false;
		if (shiftid) {radio_edit_shift = shiftid;}
		radio_edit_loader = setInterval(function() {
			ready = false;
			if (jQuery('#TB_ajaxContent #new-times').length) {
				if (radio.debug) {console.log('Add Shift Time Thickbox Loaded');}
				jQuery('.override-date').datepicker({dateFormat : 'yy-mm-dd'});
				ready = true;
			} else if (jQuery('#TB_ajaxContent #shifts-list').length) {
				if (radio.debug) {console.log('Show Edit Thickbox Loaded');}
				if (radio_edit_shift) {radio_single_shift(radio_edit_shift);}
				ready = true;
			} else if (jQuery('#TB_ajaxContent #overrides-list').length) {
				if (radio.debug) {console.log('Override Edit Thickbox Loaded');}
				if (radio_edit_shift) {radio_single_override(radio_edit_shift);}
				jQuery('.override-date').datepicker({dateFormat : 'yy-mm-dd'});
				ready = true;
			}
			if (ready) {
				clearInterval(radio_edit_loader);
				radio_resize_thickbox(false);
				jQuery(window).resize(function () {
					radio_resize_debounce(function() {radio_resize_thickbox(false)}, 500, 'radio-thickbox');
				});
			}
		}, 250);
	}" . PHP_EOL;

	// --- remove thickbox event ---
	// TODO: check if override date changed ?
	$confirm_close = __( 'You have unsaved changes. Are you sure you want to close this window?', 'radio-station' );
	$js .= "var radio_remove_loader; var radio_thickbox_remove;" . PHP_EOL;
	$js .= "radio_remove_loader = setInterval(function() {
		if (typeof tb_remove != 'function') {return;}
		radio_thickbox_remove = tb_remove;
		tb_remove = function() {
			if (jQuery('.show-shift.changed').length) {
				agree = confirm('" . esc_js( $confirm_close ) . "');
				if (!agree) {return false;}
			}	
 			if (onbeforeunloadset) {
				window.onbeforeunload = storedonbeforeunload;
				onbeforeunloadset = false;
			}
			clearInterval(radio_edit_loader);
			radio_thickbox_remove();
			/* console.log('Thickbox Removed'); */
		}
		clearInterval(radio_remove_loader);
	}, 1000);" . PHP_EOL;
	
	// --- resize thickbox ---
	$js .= "function radio_resize_thickbox(percent) {
		if ((!jQuery('#TB_window').length) || (!jQuery('#TB_ajaxContent').length)) {return;}
		percent = 80; /* temp */
		if (percent === false) {
			percent = radio_cookie.get('admin_thickbox_size');
			if ((percent == null) || (parseInt(percent) < 0)) {
				percent = 80;
				radio_cookie.set('admin_thickbox_size', percent, 30);
			}
		} else {radio_cookie.set('admin_thickbox_size', percent, 30);}
		winwidth = jQuery(window).width(); winheight = jQuery(window).height();
		jQuery('#TB_window').css({'width':percent+'%','height':percent+'%'});
		jQuery('#TB_ajaxContent').css({'width':'auto','height':'auto'});
		width = jQuery('#TB_ajaxContent').width();
		height = jQuery('#TB_ajaxContent').height();
		tbwidth = parseInt((winwidth * percent / 100), 10);
		tbheight = parseInt((winheight * percent / 100), 10);
		if (tbwidth > (width + 75)) {tbwidth = width + 75;}
		if (tbheight > (height + 100)) {tbheight = height + 100;}
		if (winwidth < 400) {tbwidth = winwidth; tbheight = winheight;}
		console.log('Thickbox Size: '+tbwidth+' x '+tbheight);
		jQuery('#TB_window').css({
			width: tbwidth+'px',
			height: tbheight+'px',
			marginLeft: '-'+parseInt((tbwidth / 2), 10)+'px',
			marginTop: '-'+parseInt((tbheight / 2), 10)+'px',
			overflow: 'auto'			
		});
		jQuery('#TB_title').css({'position':'relative','width':'auto'});
		jQuery('#TB_ajaxContent').css({'height':'auto','width':width+'px','overflow':'hidden','paddingTop':'25px','paddingBottom':'25px'});
		cwidth = jQuery('#TB_window')[0].clientWidth; owidth = jQuery('#TB_window')[0].offsetWidth;
		if (cwidth != owidth) {
			paddingtop = jQuery('#TB_title').height() + 25;
			jQuery('#TB_ajaxContent').css({'paddingTop':paddingtop+'px'});
			jQuery('#TB_title').css({'position':'fixed','width':(tbwidth-(owidth-cwidth))+'px'});
		}
	}" . PHP_EOL;

	// --- show only single shift ---
	$js .= "function radio_single_shift(id) {" . PHP_EOL;

		// --- to display all shifts ---
		$save_shift = __( 'Save Shift', 'radio-station' );
		$save_shifts = __( 'Save Shifts', 'radio-station' );
		$js .= "if (id === false) {
			jQuery('#all-shifts').prop('checked', true);
			jQuery('#single-shift').prop('checked', false);
			jQuery('.shift-wrapper, .shifts-clear, .shift-add, .shift-conflicts-message').show();
			jQuery('.shifts-save').attr('value', '" . esc_js( $save_shifts ) . "');
			radio_resize_thickbox(false);
			return;
		}" . PHP_EOL;

		// --- bug out if shift no longer exists ---
		$js .= "if (!jQuery('#shift-'+id).length) {jQuery('#shift-display').hide(); return;}" . PHP_EOL;

		// --- select radio checkbox ---
		$js .= "jQuery('#single-shift').prop('checked', true);" . PHP_EOL;
		$js .= "jQuery('#all-shifts').prop('checked', false);" . PHP_EOL;

		// --- to hide other shifts ---
		$js .= "jQuery('.shift-wrapper').each(function() {
			if (jQuery(this).attr('id').replace('shift-wrapper-','') != id) {jQuery(this).hide();}
		});" . PHP_EOL;

		// --- to hide clear and add buttons ---
		$js .= "jQuery('.shifts-clear, .shift-add').hide();" . PHP_EOL;

 		// --- maybe remove conflict message (if not for this shift) ---
 		$js .= "if (!jQuery('#shift-'+id).hasClass('conflicts')) {
 			jQuery('.shift-conflicts-message').hide();
 		}" . PHP_EOL;

		// --- make save shifts button singular ---
		$js .= "jQuery('.shifts-save').attr('value', '" . esc_js( $save_shift ) . "');" . PHP_EOL;

		// --- resize thickbox ---
		$js .= "radio_resize_thickbox(false);" . PHP_EOL;

	$js .= "}" . PHP_EOL;

	// --- switch new shift type ---
	$js .= "function radio_shift_type(type) {
		if (type === false) {
			if (jQuery('#add-show-shift').prop('checked')) {type = 'show';}
			else if (jQuery('#add-shift-override').prop('checked')) {type = 'override';}
		} else if (type == 'show') {
			jQuery('#add-show-shift').prop('checked', true);
			jQuery('#add-shift-override').prop('checked', false);
		} else if (type == 'override') {
			jQuery('#add-shift-override').prop('checked', true);
			jQuery('#add-show-shift').prop('checked', false);
		}
		if (type == 'show') {
			jQuery('#override-select, #override-existing, #override-new, #new-override-date-item, #overrides-list, .override-buttons').hide();
			jQuery('#show-select,/*#shifts-list, */ #new-encore-item').show();
			jQuery('#new-shift-day-item').css('display', 'inline-block');
			radio_show_type(false);
		} else if (type == 'override') {
			jQuery('#show-select, #show-existing, #show-new, #new-shift-day-item, #shifts-list, .shift-buttons, #new-encore-item').hide();
			jQuery('#override-select/*, #overrides-list*/').show();
			jQuery('#new-override-date-item').css('display', 'inline-block');
			radio_override_type(false);
		}
	}" . PHP_EOL;

	// --- switch new show type ---
	$js .= "function radio_show_type(type) {
		if (type === false) {
			if (jQuery('#add-new-shift').prop('checked')) {type = 'existing';}
			else if (jQuery('#add-new-show').prop('checked')) {type = 'new';}
		} else if (type == 'existing') {
			jQuery('#add-new-shift').prop('checked', true);
			jQuery('#add-new-show').prop('checked', false);
		} else if (type == 'new') {
			jQuery('#add-new-show').prop('checked', true);
			jQuery('#add-new-shift').prop('checked', false);
		}
		if (type == 'existing') {
			jQuery('#show-new').hide();
			jQuery('#show-existing').show();
		} else if (type == 'new') {
			jQuery('#show-existing').hide();
			jQuery('#show-new').show();
		}
		radio_resize_thickbox(false);
	}" . PHP_EOL;

	// --- check on change of show ---
	$js .= "function radio_change_show() {
		showid = jQuery('#show-id').val();
		if (showid == '') {jQuery('#shift-button-add').hide();}
		else {jQuery('#shift-button-add').show();}
		jQuery('#shifts-list, .shift-buttons').hide(); return; /* TEMP */		
		loadedid = jQuery('#shifts-list-id').val();
		if (showid == loadedid) {jQuery('#shifts-list').show();}
		else {jQuery('#shifts-list').hide();}
	}" . PHP_EOL;

	// --- add shift to selected show ---
	$js .= "function radio_add_show_shift() {
		showid = jQuery('#show-id').val();
		jQuery('#post_ID').val(showid);
		if (radio.debug) {console.log('Selected Show ID: '+showid);}
		if (showid == '') {jQuery('#shifts-list').hide();}
		else {
			url = '" . esc_url( $admin_ajax ) . "?action=radio_station_add_show_shift&show_id='+showid;
			day = document.getElementById('new-shift-day').value;
			start_hour = document.getElementById('new-start-hour').value;
			start_min = document.getElementById('new-start-min').value;
			start_meridian = document.getElementById('new-start-meridian').value;
			end_hour = document.getElementById('new-end-hour').value;
			end_min = document.getElementById('new-end-min').value;
			end_meridian = document.getElementById('new-end-meridian').value;
			nonce = document.getElementById('show_shifts_nonce').value;
			if (document.getElementById('new-encore').checked) {encore = 'on';} else {encore = '';}
			if (document.getElementById('new-disabled').checked) {disabled = 'yes';} else {disabled = '';}
			url += '&day='+day+'&start_hour='+start_hour+'&start_min='+start_min+'&start_meridian='+start_meridian;
			url += '&end_hour='+end_hour+'&end_min='+end_min+'&end_meridian='+end_meridian+'&disabled='+disabled+'&encore='+encore+'&nonce='+nonce;
			if (jQuery('#shift-load-frame').attr('src') != url) {jQuery('#shift-load-frame').attr('src', url);}
		}
	}" . PHP_EOL;

	// --- show only single shift ---
	$js .= "function radio_single_override(id) {" . PHP_EOL;

		// --- to display all overrides ---
		$save_override = __( 'Save Override', 'radio-station' );
		$save_overrides = __( 'Save Overrides', 'radio-station' );
		$js .= "if (id === false) {
			jQuery('#all-overrides').prop('checked', true);
			jQuery('#single-override').prop('checked', false);
			jQuery('.override-wrapper, .overrides-clear, .override-add, .override-conflicts-message').show();
			jQuery('.overrides-save').attr('value', '" . esc_js( $save_overrides ) . "');
			radio_resize_thickbox(false);
			return;
		}" . PHP_EOL;

		// --- bug out if shift no longer exists ---
		$js .= "if (!jQuery('#override-wrapper-'+id).length) {jQuery('#override-display').hide(); return;}" . PHP_EOL;

		// --- select radio checkbox ---
		$js .= "jQuery('#single-override').prop('checked', true);" . PHP_EOL;
		$js .= "jQuery('#all-overrides').prop('checked', false);" . PHP_EOL;

		// --- to hide other shifts ---
		$js .= "jQuery('.override-wrapper').each(function() {
			if (jQuery(this).attr('id').replace('override-wrapper-','') != id) {jQuery(this).hide();}
		});" . PHP_EOL;

		// --- to hide clear and add buttons ---
		$js .= "jQuery('.overrides-clear, .override-add').hide();" . PHP_EOL;

 		// --- maybe remove conflict message (if not for this override) ---
 		// $js .= "if (!jQuery('#override-'+id).hasClass('conflicts')) {
 		//	jQuery('.override-conflicts-message').hide();
 		// }" . PHP_EOL;

		// --- make save shifts button singular ---
		$js .= "jQuery('.overrides-save').attr('value', '" . esc_js( $save_override ) . "');" . PHP_EOL;

		// --- resize thickbox ---
		$js .= "radio_resize_thickbox(false);" . PHP_EOL;

	$js .= "}" . PHP_EOL;


	// --- switch new override type ---
	$js .= "function radio_override_type(type) {
		if (type === false) {
			if (jQuery('#add-override-shift').prop('checked')) {type = 'existing';}
			else if (jQuery('#add-new-override').prop('checked')) {type = 'new';}
		} else if (type == 'existing') {
			jQuery('#add-override-shift').prop('checked', true);
			jQuery('#add-new-override').prop('checked', false);
		} else if (type == 'new') {
			jQuery('#add-new-override').prop('checked', true);
			jQuery('#add-override-shift').prop('checked', false);
		}
		if (type == 'existing') {
			jQuery('#override-new').hide();
			jQuery('#override-existing').show();
		} else if (type == 'new') {
			jQuery('#override-existing').hide();
			jQuery('#override-new').show();
		}
		radio_resize_thickbox(false);
	}" . PHP_EOL;
	
	// --- check on change of override ---
	$js .= "function radio_change_override() {
		overrideid = jQuery('#override-id').val();
		if (overrideid == '') {jQuery('#override-button-add').hide();}
		else {jQuery('#override-button-add').show();}
		jQuery('#overrides-list').hide(); return; /* TEMP */
		loadedid = jQuery('#override-list-id').val();
		if (overrideid == loadedid) {jQuery('#overrides-list').show();}
		else {jQuery('#overrides-list').hide();}
	}" . PHP_EOL;

	// --- add time to selected override ---
	$js .= "function radio_add_override_time() {
		overrideid = jQuery('#override-id').val();
		jQuery('#post_ID').val(overrideid);
		if (radio.debug) {console.log('Selected Override ID: '+overrideid);}
		if (overrideid == '') {jQuery('#overrides-list').hide();}
		else {
			url = '" . esc_url( $admin_ajax ) . "?action=radio_station_add_override_time&override_id='+overrideid;
			date = document.getElementById('new-override-date').value;
			start_hour = document.getElementById('new-start-hour').value;
			start_min = document.getElementById('new-start-min').value;
			start_meridian = document.getElementById('new-start-meridian').value;
			end_hour = document.getElementById('new-end-hour').value;
			end_min = document.getElementById('new-end-min').value;
			end_meridian = document.getElementById('new-end-meridian').value;
			nonce = document.getElementById('show_override_nonce').value;
			if (document.getElementById('new-disabled').checked) {disabled = 'yes';} else {disabled = '';}
			url += '&date='+date+'&start_hour='+start_hour+'&start_min='+start_min+'&start_meridian='+start_meridian;
			url += '&end_hour='+end_hour+'&end_min='+end_min+'&end_meridian='+end_meridian+'&disabled='+disabled+'&nonce='+nonce;
			if (jQuery('#override-load-frame').attr('src') != url) {jQuery('#override-load-frame').attr('src', url);}
		}
	}" . PHP_EOL;

	// --- set new record action on submit ---
	$post_new = admin_url( 'post-new.php' );
	$js .= "function radio_new_type(type) {
		form = document.getElementById('new-shift-form')
		actionurl = '" . esc_url( $post_new ) . "';
		if (type == 'show') {
			actionurl += '?post_type=" . RADIO_STATION_SHOW_SLUG . "';
			if (document.getElementById('new-show-window').checked) {
				form.setAttribute('target', '_blank');
			} else {form.setAttribute('target', '_self');}
		} else if (type == 'override') {
			actionurl += '?post_type=" . RADIO_STATION_OVERRIDE_SLUG . "';
			if (document.getElementById('new-show-window').checked) {
				form.setAttribute('target', '_blank');
			} else {form.setAttribute('target', '_self');}
		}
		form.setAttribute('action', actionurl);
		form.submit();		
	}" . PHP_EOL;

	// [unused] thickbox iframe loaded event
	/* $js .= "jQuery(document).on('thickbox:iframe:loaded', function (event) {
		console.log( 'Thickbox Iframe Loaded' );
	});"; */
	
	return $js;
}	

// --------------------------------------
// Filter Load Script to Display Schedule
// --------------------------------------
add_filter( 'radio_station_master_schedule_load_script', 'radio_station_pro_editor_load_script', 20, 2 );
function radio_station_pro_editor_load_script( $js, $atts ) {

	if ( !isset( $_REQUEST['schedule_editor'] ) || ( '1' != $_REQUEST['schedule_editor'] ) ) {
		return $js;
	}

	$view = $atts['view'];
	if ( 'table' == $view ) {
		$schedule_id = 'master-program-schedule';
	} elseif ( 'tabs' == $view ) {
		$schedule_id = 'master-schedule-tabs';
	} elseif ( 'grid' == $view ) {
		$schedule_id = 'master-schedule-grid';
	} elseif ( 'calendar' == $view ) {
		$schedule_id = 'master-schedule-calendar';
	}
	
	$js .= "parent.document.getElementById('" . esc_js( $schedule_id ) . "').style.display = 'block';";
	// $js .= "height = document.getElementById('" . esc_js( $schedule_id ) . "').scrollHeight;";
	// $js .= "parent.document.getElementById('" . esc_js( $schedule_id ) . "').style.height = height+'px';";

	return $js;
}

// ---------------------
// Show Edit Link Filter
// ---------------------
add_filter( 'radio_station_show_edit_link', 'radio_station_pro_show_edit_link', 10, 4 );
function radio_station_pro_show_edit_link( $link, $show_id, $shift_id, $view ) {

	if ( !is_user_logged_in() || !current_user_can( 'edit_shows' ) ) {
		return $link;
	}

	// --- prepare edit link ---
	$url = admin_url( 'admin-ajax.php' );
	$url = add_query_arg( 'action', 'radio_station_edit_show', $url );
	$url = add_query_arg( 'show', $show_id, $url );
	$url = add_query_arg( 'shift', $shift_id, $url );
	$url = add_query_arg( 'view', $view, $url );
	// $url = add_query_arg( 'TB_iframe', '1', $url );

	// --- set title based on post type ---
	global $wpdb;
	$query = "SELECT post_type FROM " . $wpdb->prefix . "posts WHERE ID = %d";
	$query = $wpdb->prepare( $query, $show_id );
	$post_type = $wpdb->get_var( $query );
	if ( RADIO_STATION_SHOW_SLUG == $post_type ) {
		$title = __( 'Edit Show Shifts', 'radio-station' );
	} elseif ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
		$title = __( 'Edit Override Times', 'radio-station' );
	}

	$onclick = "radio_thickbox_show('" . $title . "', '" . $url . "', false);";
	$link = '<div class="show-edit-link" onclick="' . $onclick . '" title="' . esc_attr( $title ) . '">';
	$link .= '<span class="dashicons dashicons-edit"></span></div>';

	return $link;
}

// ---------------------------
// Show Shift Edit Link Filter
// ---------------------------
add_filter( 'radio_station_schedule_show_time', 'radio_station_pro_shift_edit_link', 10, 5 );
function radio_station_pro_shift_edit_link( $show_time, $show_id, $view, $shift, $tcount ) {

	// 2.4.1.6: bug out for legacy views
	$legacy_views = array( 'legacy', 'div' );
	if ( in_array( $view, $legacy_views ) ) {
		return $show_time;
	}

	if ( !is_user_logged_in() || !current_user_can( 'edit_shows' ) ) {
		return $show_time;
	}

	if ( '' != $show_time ) { 

		// --- prepare edit link ---
		$url = admin_url( 'admin-ajax.php' );
		$url = add_query_arg( 'action', 'radio_station_edit_shift', $url );
		$url = add_query_arg( 'show', $show_id, $url );
		$url = add_query_arg( 'shift', $shift['id'], $url );
		$url = add_query_arg( 'view', $view, $url );
		$url = add_query_arg( 'tcount', $tcount, $url );
		// $url = add_query_arg( 'TB_iframe', '1', $url );

		// --- set title based on post type ---
		global $wpdb;
		$query = "SELECT post_type FROM " . $wpdb->prefix . "posts WHERE ID = %d";
		$query = $wpdb->prepare( $query, $show_id );
		$post_type = $wpdb->get_var( $query );
		if ( RADIO_STATION_SHOW_SLUG == $post_type ) {
			$title = __( 'Edit Show Shift', 'radio-station' );
		} elseif ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
			$title = __( 'Edit Override Time', 'radio-station' );
		}
		
		$onclick = "radio_thickbox_show('" . esc_js( $title ) . "', '" . esc_url( $url ) . "', '" . esc_attr( $shift['id'] ) . "');";
		$link_open = '<a href="javascript:void(0);" onclick="' . $onclick . '" class="shift-edit-link" title="' . esc_attr( $title ) . '">';
		$link_close = '</a>';
		$show_time = $link_open . $show_time . $link_close;

	}

	return $show_time;
}

// ---------------------------
// Show Shift Edit Link Filter
// ---------------------------
add_filter( 'radio_station_schedule_add_link', 'radio_station_pro_shift_add_link', 10, 3 );
function radio_station_pro_shift_add_link( $link, $times, $view ) {

	if ( !is_user_logged_in() || !current_user_can( 'edit_shows' ) ) {
		return $link;
	}

	$url = admin_url( 'admin-ajax.php' );
	$url = add_query_arg( 'action', 'radio_station_add_shift', $url );
	$keys = array( 'date', 'day', 'hour', 'mins' );
	foreach ( $keys as $key ) {
		if ( isset( $times[$key] ) ) {
			$url = add_query_arg( $key, (string)$times[$key], $url );
		}
	}
	// $url = add_query_arg( 'TB_iframe', '1', $url );

	$window_title = __( 'Add to Schedule', 'radio-station' );
	$title = __( 'Add Show or Override to Schedule', 'radio-station' );
	$onclick = "radio_thickbox_show('" . esc_js( $window_title ) . "', '" . esc_url( $url ) . "', false);";
	$link = '<div class="show-add-link" onclick="' . $onclick . '" title="' . esc_attr( $title ) . '">';
	$link .= '<span class="dashicons dashicons-plus-alt"></span></div>';

	return $link;
}


// --------------------
// === AJAX Actions ===
// --------------------

// -------------------
// AJAX Edit Show Form
// -------------------
add_action( 'wp_ajax_nopriv_radio_station_edit_show', 'radio_station_pro_relogin_message' );
add_action( 'wp_ajax_nopriv_radio_station_edit_shift', 'radio_station_pro_relogin_message' );
add_action( 'wp_ajax_radio_station_edit_show', 'radio_station_pro_edit_show' );
add_action( 'wp_ajax_radio_station_edit_shift', 'radio_station_pro_edit_show' );
function radio_station_pro_edit_show() {
	
	global $post;
	$show_id = absint( $_REQUEST['show'] );
	$post = get_post( $show_id );
	if ( !$post ) { 
		echo __( 'Error. Provided Show ID does not exist.', 'radio-station' );
		exit;
	}

	// --- single or multiple selection ---
	if ( 'radio_station_edit_show' == $_REQUEST['action'] ) {
		$selection = 'multiple';
	} elseif ( 'radio_station_edit_shift' == $_REQUEST['action'] ) {
		$selection = 'single';
	}

	// --- output show ID input value for shift saving form ---
	echo '<input type="hidden" name="post_ID" id="post_ID" value="' . esc_attr( $show_id ) . '">';

	// --- check provided post type ---	
	if ( RADIO_STATION_SHOW_SLUG == $post->post_type ) {

		if ( !current_user_can( 'edit_shows' ) ) {
			wp_die( __( 'You do not have permission to do that.', 'radio_station' ) );
		}

		if ( preg_match( '/^[a-zA-Z0-9_]+$/', $_REQUEST['shift'] ) ) {
			$shift_id = $_REQUEST['shift'];
		} else {
			echo __( 'Warning. Provided Shift ID is not valid.', 'radio-station' );
			$shift_id = false;
		}
	
		if ( $shift_id ) {
			$shifts = radio_station_get_show_schedule( $show_id );
			if ( !array_key_exists( $shift_id, $shifts ) ) {
				echo __( 'Warning. Provided Shift ID no longer exists.', 'radio-station' );
				$shift_id = false;
			} else {
				echo '<input type="hidden" name="shift_ID" id="shift_ID" value="' . esc_attr( $shift_id ) . '">';
			}
		}

		// --- display show title ---
		$edit_link = get_edit_post_link( $show_id );
		echo '<div id="show-title">';
			echo esc_html( __( 'Show', 'radio-station' ) ) . ': ';
			echo '<a href="' . esc_url( $edit_link ) . '" target="_blank" class="show-edit-link">';
				echo esc_html( $post->post_title );
			echo '</a>';
		echo '</div>';

		// --- single / all shifts selection buttons --- 
		if ( $shift_id ) {
			echo '<center><table id="shift-display" class="shift-display"><tr><td>';
				echo '<b>' . esc_html( __( 'Display Shifts', 'radio-station' ) ) . ': </b>';
			echo '</td><td width="10"></td><td>';
				echo '<input type="radio" id="single-shift" name="edit-type" value="single" onchange="radio_single_shift(\'' . esc_js( $shift_id ) . '\');"';
				if ( 'single' == $selection ) {
					echo ' checked="checked"';
				}
				echo '> <a href="javascript:void(0);" id="single-shift-link" onclick="radio_single_shift(\'' . esc_js( $shift_id ) . '\');" style="text-decoration: none;">';
				echo esc_html( 'Selected Shift', 'radio-station' ) . '</a>';
			echo '</td><td width="10"></td><td>';
				echo '<input type="radio" id="all-shifts" name="edit-type" value="all" onchange="radio_single_shift(false);"';
				if ( 'multiple' == $selection ) {
					echo ' checked="checked"';
				}
				echo '> <a href="javascript:void(0);" id="all-shifts-link" onclick="radio_single_shift(false);" style="text-decoration: none;">';
				echo esc_html( __( 'All Show Shifts', 'radio-station' ) ) . '</a>';
			echo '</td></tr></table></center>';
		}

		// --- output show shifts metabox ---
		radio_station_show_shifts_metabox();

	} elseif ( RADIO_STATION_OVERRIDE_SLUG == $post->post_type ) {

		if ( !current_user_can( 'edit_overrides' ) ) {
			wp_die( __( 'You do not have permission to do that.', 'radio_station' ) );
		}

		// --- sanitize override time ID ---
		if ( preg_match( '/^[a-zA-Z0-9_]+$/', $_REQUEST['shift'] ) ) {
			$shift_id = $_REQUEST['shift'];
		} else {
			echo __( 'Warning. Provided Shift ID is not valid.', 'radio-station' );
			$shift_id = false;
		}
	
		// --- recheck if passed override time ID is valid ---
		if ( $shift_id ) {
			$override_times = get_post_meta( $show_id, 'show_override_sched', true );
			if ( $override_times && is_array( $override_times ) && count( $override_times ) > 0 ) {
				$found_time = false;
				foreach ( $override_times as $i => $override_time ) {
					if ( $override_time['id'] == $shift_id ) {
						$found_time = true;
					}
				}
				if ( !$found_time ) {
					echo __( 'Warning. Provided Override Shift ID no longer exists.', 'radio-station' );
					$shift_id = false;
				} else {
					echo '<input type="hidden" name="shift_ID" id="shift_ID" value="' . esc_attr( $shift_id ) . '">';
				}
			}
		}

		// --- display override title ---
		$edit_link = get_edit_post_link( $show_id );
		echo '<div id="override-title">';
			echo esc_html( __( 'Override', 'radio-station' ) ) . ': ';
			echo '<a href="' . esc_url( $edit_link ) . '" target="_blank" class="override-edit-link">';
				echo esc_html( $post->post_title );
			echo '</a>';
		echo '</div>';

		// --- single / all times selection buttons --- 
		if ( $shift_id ) {
			echo '<center><table class="override-display"><tr><td>';
				echo '<b>' . esc_html( __( 'Display Override', 'radio-station' ) ) . ': </b>';
			echo '</td><td width="10"></td><td>';
				echo '<input type="radio" id="single-override" name="edit-type" value="single" onchange="radio_single_override(\'' . esc_js( $shift_id ) . '\');"';
				if ( 'single' == $selection ) {
					echo ' checked="checked"';
				}
				echo '> <a href="javascript:void(0);" id="single-override-link" onclick="radio_single_override(\'' . esc_js( $shift_id ) . '\');" style="text-decoration: none;">';
				echo esc_html( 'Selected Override Time', 'radio-station' ) . '</a>';
			echo '</td><td width="10"></td><td>';
				echo '<input type="radio" id="all-overrides" name="edit-type" value="all" onchange="radio_single_override(false);"';
				if ( 'multiple' == $selection ) {
					echo ' checked="checked"';
				}
				echo '> <a href="javascript:void(0);" id="all-overrides-link" onclick="radio_single_override(false);" style="text-decoration: none;">';
				echo esc_html( __( 'All Override Times', 'radio-station' ) ) . '</a>';
			echo '</td></tr></table></center>';
		}

		// --- output override metabox ---
		radio_station_schedule_override_metabox();

	} else {
		echo __( 'Error. Provided ID is not a Show or Override.', 'radio-station' );
	}

	// --- edit show/override styles ---
	$css = radio_station_pro_shift_edit_styles();
	echo '<style>' . $css . '</style>';

	exit;
}

// -------------------
// AJAX Add Shift Form
// -------------------
add_action( 'wp_ajax_nopriv_radio_station_add_shift', 'radio_station_pro_relogin_message' );
add_action( 'wp_ajax_radio_station_add_shift', 'radio_station_pro_add_shift' );
function radio_station_pro_add_shift() {

	if ( !current_user_can( 'edit_shows' ) ) {
	 	wp_die( __( 'You do not have permission to do that.', 'radio_station' ) );
	}

	// --- set time arrays ---
	$days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	$hours = $mins = array();
	for ( $i = 1; $i <= 12; $i ++ ) {
		$hours[$i] = $i;
	}
	for ( $i = 0; $i < 60; $i ++ ) {
		if ( $i < 10 ) {
			$min = '0' . $i;
		} else {
			$min = $i;
		}
		$mins[$i] = $min;
	}
	$am = radio_station_translate_meridiem( 'am' );
	$pm = radio_station_translate_meridiem( 'pm' );

	/// --- check posted values ---
	$date = $_REQUEST['date'];
	$time = strtotime( $date );
	if ( date( 'Y-m-d', $time ) != $date ) {
		wp_die( __( 'Error! Invalid date value passed.', 'radio-station' ) );
	}
	$day = $_REQUEST['day'];
	if ( !in_array( $day, $days ) ) {
		wp_die( __( 'Error! Invalid day value passed.', 'radio-station' ) );
	}
	$hour = absint( $_REQUEST['hour'] );
	if ( ( $hour < 0 ) || ( $hour > 23 ) ) {
		$hour = '00';
	}
	$minute = absint( $_REQUEST['mins'] );
	if ( ( $minute < 0 ) || ( $minute > 59 ) ) {
		$minute = '0';
	}
	if ( $minute < 10 ) {
		$minute = '0' . $minute;
	}

	// --- set new time via schedule values ---
	$time = strtotime( $date . ' ' . $hour . ':' . $minute );
	$start_hour = date( 'g', $time );
	$start_meridian = date( 'a', $time );
	$end_hour = date( 'g', $time + 3600 );
	$end_meridian = date( 'a', $time + 3600 );
	$start_min = $end_min = $minute;
	$times = array(
		'date'           => $date,
		'day'            => $day,
		'start_hour'     => $start_hour,
		'start_min'      => $start_min,
		'start_meridian' => $start_meridian,
		'end_hour'       => $end_hour,
		'end_min'        => $end_min,
		'end_meridian'   => $end_meridian,
	);
	// print_r( $_REQUEST ); echo '<br>'; print_r( $times );

	// --- start shift add form ---
	$admin_ajax = admin_url( 'admin-ajax.php' );
	echo '<form method="post" id="new-shift-form" action="' . esc_url( $admin_ajax ) . '">';

	// --- add show and override save nonces ---
	wp_nonce_field( 'radio-station', 'show_shifts_nonce' );
	wp_nonce_field( 'radio-station', 'show_override_nonce' );

	// --- open shift add table ---
	echo '<center><table id="shift-add-table">' . PHP_EOL;

	// --- top spacer row ---
	echo '<tr height="10"><td></td></tr>' . PHP_EOL;

	// --- shift type radios ---
	echo '<tr><td>' . PHP_EOL;
		echo '<b>' . esc_html( __( 'Timeslot Type', 'radio-station' ) ) . ': </b>' . PHP_EOL;
	echo '</td><td width="10"></td><td>' . PHP_EOL;
		echo '<input type="radio" name="shift-type" id="add-show-shift" value="show" onclick="radio_shift_type(\'show\');" checked="checked">' . PHP_EOL;
		echo ' <a href="javascript:void(0);" onclick="radio_shift_type(\'show\');" style="text-decoration:none;">' . PHP_EOL;
		echo esc_html( 'Show Shift', 'radio-station' ) . '</a>' . PHP_EOL;
	echo '</td><td width="10"></td><td>' . PHP_EOL;
		echo '<input type="radio" name="shift-type" id="add-shift-override" value="override" onclick="radio_shift_type(\'override\');">' . PHP_EOL;
		echo ' <a href="javascript:void(0);" onclick="radio_shift_type(\'override\');" style="text-decoration:none;">' . PHP_EOL;
		echo esc_html( 'Schedule Override', 'radio-station' ) . '</a>' . PHP_EOL;
	echo '</td></tr>' . PHP_EOL;
	
	// --- spacer row ---
	echo '<tr height="15"><td></td></tr>' . PHP_EOL;

	// --- new times row ---
	echo '<tr id="new-times"><td colspan="5" style="text-align:center;">';
		echo '<ul style="list-style:none;">';
			echo '<li id="new-override-date-item" style="display:none;">';

				// --- override date ---
				echo '<b>' . esc_html( __( 'Override Date', 'radio-station' ) ) . '</b>: ';
				echo '<input type="text" class="override-date" id="new-override-date" name="date" value="' . esc_attr( $times['date'] ) . '">';

			echo '</li><li id="new-shift-day-item" style="display:inline-block;">';

				// --- shift day ---
				echo '<b>'. esc_html( __( 'Shift Day', 'radio-station' ) ) . '</b>: ';
				echo '<select id="new-shift-day" name="day">';
					foreach ( $days as $weekday ) {
						echo  '<option value="' . esc_attr( $weekday ) . '" ' . selected( $weekday, $day, false ) . '>';
						echo esc_html( radio_station_translate_weekday( $weekday ) ) . '</option>';
					}
				echo '</select>';
			echo '</li><li id="new-encore-item" style="display:inline-block; margin-left:30px;">';

				// --- encore airing ---
				echo '<b>' . esc_html( __( 'Encore', 'radio-station' ) ) . '</b>: ';
				echo '<input id="new-encore" type="checkbox" value="on">';

			echo '</li><li style="display:inline-block; margin-left:30px;">';

				// --- disabled switch ---
				echo '<b>' . esc_html( __( 'Disabled', 'radio-station' ) ) . '</b>: ';
				echo '<input id="new-disabled" type="checkbox" value="yes">';

			echo '</li>';

		echo '</ul><ul style="list-style:none;">';

			echo '<li style="display:inline-block;">';

				// --- start time label ---
				echo '<b>' . esc_html( __( 'Start Time', 'radio-station' ) ) . '</b>: ';

				// --- start hour ---
				echo '<select id="new-start-hour" name="start_hour" style="min-width:35px;">';
				foreach ( $hours as $hour ) {
					echo '<option value="' . esc_attr( $hour ) . '" ' . selected( $hour, $start_hour, false ) . '>' . esc_html( $hour ) . '</option>';
				}
				echo '</select>';

				// --- start minute ---
				echo '<select id="new-start-min" name="start_min" style="min-width:35px;">';
					echo '<option value=""></option>';
					echo '<option value="00">00</option>';
					echo '<option value="15">15</option>';
					echo '<option value="30">30</option>';
					echo '<option value="45">45</option>';
					foreach ( $mins as $min ) {
						echo '<option value="' . esc_attr( $min ) . '" ' . selected( $min, $start_min, false ) . '>' . esc_html( $min ) . '</option>';
					}
				echo '</select>';

				// --- start meridian ---
				echo '<select id="new-start-meridian" name="start_meridian" style="min-width:35px;">';
					echo '<option value="am" ' . selected( $start_meridian, 'am', false ) . '>' . esc_html( $am ) . '</option>';
					echo '<option value="pm" ' . selected( $start_meridian, 'pm', false ) . '>' . esc_html( $pm ) . '</option>';
				echo '</select>';

			echo '</li><li style="display:inline-block; margin-left:30px;">';

				// --- end time label ---
				echo '<b>' . esc_html( __( 'End Time', 'radio-station' ) ) . '</b>: ';

				// --- end hour ---
				echo '<select id="new-end-hour" name="end_hour" style="min-width:35px;">';
				foreach ( $hours as $hour ) {
					echo '<option value="' . esc_attr( $hour ) . '" ' . selected( $hour, $end_hour, false ) . '>' . esc_html( $hour ) . '</option>';
				}
				echo '</select>';

				// --- end minute ---
				echo '<select id="new-end-min" name="end_min" style="min-width:35px;">';
					echo '<option value=""></option>';
					echo '<option value="00">00</option>';
					echo '<option value="15">15</option>';
					echo '<option value="30">30</option>';
					echo '<option value="45">45</option>';
					foreach ( $mins as $min ) {
						echo '<option value="' . esc_attr( $min ) . '" ' . selected( $min, $end_min, false ) . '>' . esc_html( $min ) . '</option>';
					}
				echo '</select>';

				// --- end meridian ---
				echo '<select id="new-end-meridian" name="end_meridian" style="min-width:35px;">';
					echo '<option value="am" ' . selected( $end_meridian, 'am', false ) . '>' . esc_html( $am ) . '</option>';
					echo '<option value="pm" ' . selected( $end_meridian, 'pm', false ) . '>' . esc_html( $pm ) . '</option>';
				echo '</select>';

			echo '</li>';
			
		echo '</ul>';
	echo '</td></tr>';

	// --- get all shows ---
	$args = array(
		'post_type'   => RADIO_STATION_SHOW_SLUG,
		'post_status' => array( 'publish', 'draft' ),
		'orderby'     => 'modified',
		'numberposts' => -1,
	);
	$shows = get_posts( $args );

	// --- get all overides ---
	$args = array(
		'post_type'   => RADIO_STATION_OVERRIDE_SLUG,
		'post_status' => array( 'publish', 'draft' ),
		'orderby'     => 'modified',
		'numberposts' => -1,
	);
	$overrides = get_posts( $args );

	// --- show type selection ---
	echo '<tr id="show-select"><td>' . PHP_EOL;
		echo '<b>' . esc_html( __( 'Show Type', 'radio-station' ) ) . ': </b>' . PHP_EOL;
	echo '</td><td width="10"></td><td>' . PHP_EOL;
		echo '<input type="radio" name="show-type" id="add-new-shift" value="existing" onclick="radio_show_type(false);" checked="checked">' . PHP_EOL;
		echo ' <a href="javascript:void(0);" onclick="radio_show_type(\'existing\');" style="text-decoration:none;">' . PHP_EOL;
		echo esc_html( 'Existing Show', 'radio-station' ) . '</a>' . PHP_EOL;
	echo '</td><td width="10"></td><td>' . PHP_EOL;
		echo '<input type="radio" name="show-type" id="add-new-show" value="new" onclick="radio_show_type(false);">' . PHP_EOL;
		echo ' <a href="javascript:void(0);" onclick="radio_show_type(\'new\');" style="text-decoration:none;">' . PHP_EOL;
		echo esc_html( 'New Show', 'radio-station' ) . '</a>' . PHP_EOL;
	echo '</td></tr>' . PHP_EOL;

	// --- override type selector ---
	echo '<tr id="override-select" style="display:none;"><td>' . PHP_EOL;
		echo '<b>' . esc_html( __( 'Override Type', 'radio-station' ) ) . ': </b>' . PHP_EOL;
	echo '</td><td width="10"></td><td>' . PHP_EOL;
		echo '<input type="radio" name="override-type" id="add-override-shift" value="existing" onclick="radio_override_type(false);" checked="checked">' . PHP_EOL;
		echo ' <a href="javascript:void(0);" onclick="radio_override_type(\'existing\');" style="text-decoration:none;">' . PHP_EOL;
		echo esc_html( 'Existing Override', 'radio-station' ) . '</a>' . PHP_EOL;
	echo '</td><td width="10"></td><td>' . PHP_EOL;
		echo '<input type="radio" name="override-type" id="add-new-override" value="new" onclick="radio_override_type(false);">' . PHP_EOL;
		echo ' <a href="javascript:void(0);" onclick="radio_override_type(\'new\');" style="text-decoration:none;">' . PHP_EOL;
		echo esc_html( 'New Override', 'radio-station' ) . '</a>' . PHP_EOL;
	echo '</td></tr>' . PHP_EOL;

	// --- spacer row ---
	echo '<tr height="15"><td></td></tr>' . PHP_EOL;
	
	// --- existing show selector ---
	echo '<tr id="show-existing"><td>' . PHP_EOL;
		echo '<b>' . esc_html( __( 'Show', 'radio-station' ) ) . ': </b>' . PHP_EOL;
	echo '</td><td></td><td>' . PHP_EOL;
		echo '<select id="show-id" name="showid" onchange="radio_change_show();">' . PHP_EOL;
		echo '<option value="" selected="selected">' . esc_html( __( 'Select Show...', 'radio-station' ) ) . '</option>' . PHP_EOL;
		foreach ( $shows as $i => $show ) {
			$show_id = $show->ID;
			$title = $show->post_title;
			$status = $show->post_status;
			$active = get_post_meta( $show_id, 'show_active', true );
			echo '<option value="' . $show_id . '">';
			echo esc_html( $title );
			if ( 'draft' == $status ) {
				echo ' (Draft)';
			}
			if ( 'on' != $active ) {
				echo ' (Inactive)';
			}
			echo '</option>' . PHP_EOL;
		}
		echo '</select>' . PHP_EOL;
	echo '</td><td></td><td align="center">' . PHP_EOL;
		echo '<input type="button" id="shift-button-add" class="button-primary" onclick="radio_add_show_shift();" value="' . esc_attr( __( 'Add Shift', 'radio-station' ) ) . '" style="margin-top:0; display:none;">' . PHP_EOL;
	echo '</td></tr>' . PHP_EOL;

	// --- existing override selector ---
	echo '<tr id="override-existing" style="display:none;"><td>';
		echo '<b>' . esc_html( __( 'Override', 'radio-station' ) ) . ': </b>' . PHP_EOL;
	echo '</td><td></td><td>' . PHP_EOL;
		echo '<select id="override-id" name="overrideid" onchange="radio_change_override();">' . PHP_EOL;
		echo '<option value="" selected="selected">' . esc_html( __( 'Select Override...', 'radio-station' ) ) . '</option>' . PHP_EOL;
		foreach ( $overrides as $i => $override ) {
			$override_id = $override->ID;
			$title = $override->post_title;
			echo '<option value="' . esc_attr( $override_id ) . '">';
			echo esc_html( $title );
			if ( 'draft' == $override->post_status ) {
				echo ' (Draft)';
			}
			echo '</option>' . PHP_EOL;
		}
		echo '</select>' . PHP_EOL;
	echo '</td><td></td><td align="center">' . PHP_EOL;
		echo '<input type="button" id="override-button-add" class="button-primary" onclick="radio_add_override_time();" value="' . esc_attr( __( 'Add Override', 'radio-station' ) ) . '" style="margin-top:0; display:none;">' . PHP_EOL;
	echo '</td></tr>' . PHP_EOL;

	// --- new show button ---
	echo '<tr id="show-new" style="display:none;"><td></td><td></td><td>';
		echo '<input type="checkbox" checked="checked" id="new-show-window" name="new-show-window">' . PHP_EOL;
		echo ' ' . esc_html( __( 'Open in a New Window', 'radio-station' ) ) . PHP_EOL;
	echo '</td><td></td><td>' . PHP_EOL;
		$onclick = "radio_new_type('show');";
		echo '<input type="button" id="show-button-new" class="button-primary" onclick="' . $onclick . '" value="' . esc_attr( __( 'Create New Show', 'radio-station' ) ) . '" style="margin-top:0;">' . PHP_EOL;
	echo '</td></tr>' . PHP_EOL;

	// --- new override button ---
	echo '<tr id="override-new" style="display:none;"><td></td><td></td><td>' . PHP_EOL;
		echo '<input type="checkbox" checked="checked" id="new-override-window" name="new-override-window">' . PHP_EOL;
		echo ' ' . esc_html( __( 'Open in a New Window', 'radio-station' ) ) . PHP_EOL;
	echo '</td><td></td><td>' . PHP_EOL;
		$onclick = "radio_new_type('override');";
		echo '<input type="button" id="override-button-new" class="button-primary" onclick="' . $onclick . '" value="' . esc_attr( __( 'Create New Override', 'radio-station' ) ) . '" style="margin-top:0;">' . PHP_EOL;
	echo '</td></tr>' . PHP_EOL;

	// --- close add shift table form ---
	echo '</table></center><br></form>' . PHP_EOL;

	// --- how ID input for shift saving form ---
	echo '<input type="hidden" id="post_ID" name="post_ID" value="">';

	// --- shift saving messages ---
	echo '<center><div id="shifts-saving-message" style="display:none;">' . esc_html( __( 'Adding Show Shift...', 'radio-station' ) ) . '</div>';
	echo '<div id="shifts-saved-message" style="display:none;">' . esc_html( __( 'Show Shift Added.', 'radio-station' ) ) . '</div>';
	echo '<div id="shifts-error-message" style="display:none;"></div></center>' . PHP_EOL;

	// --- show title display ---
	echo '<div id="show-title" style="display:none;"></div>';

	// --- shift saving buttons ---
	$buttons = '<center><table class="shift-buttons" width="100%" style="display:none;"><tr><td width="33%" align="center">';
	$buttons .= '<input type="button" class="shifts-clear button-secondary" value="' . esc_attr( __( 'Clear Shifts', 'radio-station' ) ) . '" onclick="radio_shifts_clear();">';
	$buttons .= '</td><td width="33%" align="center">';
	$buttons .= '<input type="button" class="shifts-save button-primary" value="' . esc_attr( __( 'Save Shifts', 'radio-station' ) ) . '" onclick="radio_shifts_save();">';
	$buttons .= '</td><td width="33%" align="center">';
	$buttons .= '<input type="button" class="shift-add button-secondary" value="' . esc_attr( __( 'Add Shift', 'radio-station' ) ) . '" onclick="radio_shift_new();">';
	$buttons .= '</td></tr></table></center>';

	// --- show shifts loading divs ---
	echo $buttons . PHP_EOL;
	echo '<div id="shifts-list" style="display:none;"></div>' . PHP_EOL;
	echo $buttons . PHP_EOL;
	echo '<iframe src="javascript:void(0);" id="shift-load-frame" style="display:none;"></iframe>' . PHP_EOL;

	// -- override saving messages ---
	echo '<center><div id="overrides-saving-message" style="display:none;">' . esc_html( __( 'Adding Override Time...', 'radio-station' ) ) . '</div>';
	echo '<div id="overrides-saved-message" style="display:none;">' . esc_html( __( 'Override Time Saved.', 'radio-station' ) ) . '</div>';
	echo '<div id="overrides-error-message" style="display:none;"></div></center>' . PHP_EOL;

	// --- override title display ---
	echo '<div id="override-title" style="display:none;"></div>';

	// --- override saving buttons ---
	$buttons = '<center><table class="override-buttons" width="100%" style="display:none;"><tr><td width="33%" align="center">';
	$buttons .= '<input type="button" class="overrides-clear button-secondary" value="' . esc_attr( __( 'Clear Overrides', 'radio-station' ) ) . '" onclick="radio_overrides_clear();">';
	$buttons .= '</td><td width="33%" align="center">';
	$buttons .= '<input type="button" class="overrides-save button-primary" value="' . esc_attr( __( 'Save Overrides', 'radio-station' ) ) . '" onclick="radio_overrides_save();">';
	$buttons .= '</td><td width="33%" align="center">';
	$buttons .= '<input type="button" class="override-add button-secondary" value="' . esc_attr( __( 'Add Override', 'radio-station' ) ) . '" onclick="radio_override_new();">';
	$buttons .= '</td></tr></table></center>';
		
	// --- override times loading divs ---
	echo $buttons . PHP_EOL;
	echo '<div id="overrides-list" style="display:none;"></div>' . PHP_EOL;
	echo $buttons . PHP_EOL;
	echo '<iframe src="javascript:void(0);" id="override-load-frame" style="display:none;"></iframe>' . PHP_EOL;

	// --- output add shift interface styles ---
	$css = radio_station_pro_shift_edit_styles();
	$css .= radio_station_shifts_list_styles();
	$css .= radio_station_overrides_list_styles();

	$css = apply_filters( 'radio_station_schedule_add_styles', $css );
	echo '<style>' . $css . '</style>';

	exit;
}

// ----------------------
// AJAX Shift Edit Styles
// ----------------------
function radio_station_pro_shift_edit_styles() {

	$css = '.show-edit-link, .override-edit-link {text-decoration: none; font-weight: bold;}
	#ui-datepicker-div {position: absolute !important; z-index: 999999 !important;}
	.override-date {width: 100px; text-align: center;}
	#show-title, #shift-display, #override-title, #override-display {margin-bottom: 10px;}
	#shifts-list, #new-shifts, #overrides-list, #new-overrides {margin: 0 auto;}
	#shift-add-table {max-width: 700px;}
	#shift-add-table, #shift-add-table tr, #shift-add-table td,
	#shift-display, .shift-display tr, .shift-display td,
	#override-display, .override-display tr, .override-display td,
	.shift-buttons, .shift-buttons tr, .shift-buttons td, 
	.override-buttons, .override-buttons tr, .override-buttons td {padding: 0; margin: 0; border: 0;}
	.shift-item label, .override-item label {font-size: 14px;}
	.shift-item.shift-encore label, .shift-item.shift-disable label, .override-item.override-disable label {font-size: 12px;}
	#show-select, #show-existing, #show-new {line-height: 32px; vertical-align: middle;}
	#override-select, #override-existing, #override-new {line-height: 32px; vertical-align: middle;}' . PHP_EOL;

	$css = apply_filters( 'radio_station_schedule_edit_styles', $css );
	return $css;
}
	
	
// --------------
// AJAX Add Shift
// --------------
add_action( 'wp_ajax_nopriv_radio_station_add_show_shift', 'radio_station_pro_relogin_message' );
add_action( 'wp_ajax_radio_station_add_show_shift', 'radio_station_pro_add_show_shift' );
function radio_station_pro_add_show_shift() {

	// --- save the shift data ---
	$_POST['show_id'] = $_REQUEST['show_id'];
	$_POST['show_shifts_nonce'] = $_REQUEST['nonce'];
	$_POST['new_shift'] = array(
		'day'            => $_REQUEST['day'],
		'start_hour'     => $_REQUEST['start_hour'],
		'start_min'      => $_REQUEST['start_min'],
		'start_meridian' => $_REQUEST['start_meridian'],
		'end_hour'       => $_REQUEST['end_hour'],
		'end_min'        => $_REQUEST['end_min'],
		'end_meridian'   => $_REQUEST['end_meridian'],
		'encore'         => $_REQUEST['encore'],
		'disabled'       => $_REQUEST['disabled'],
	);
	$show_id = absint( $_REQUEST['show_id'] );
	if ( !defined( 'RADIO_STATION_DEBUG' ) ) {
		define( 'RADIO_STATION_DEBUG', true );
	}
	radio_station_show_save_data( $show_id );

	// --- remove the new shift form ---
	$show_label = __( 'Show', 'radio-station' );
	$show_title = get_the_title( $show_id );
	$show_edit_link = get_edit_post_link( $show_id );
	$js = "parent.jQuery('.shift-buttons').show();" . PHP_EOL;
	$js .= "parent.jQuery('#new-shift-form').hide();" . PHP_EOL;
	$js .= "parent.jQuery('#TB_ajaxWindowTitle').html('" . esc_js( __( 'Edit Show Shifts', 'radio-station') ) . "');" . PHP_EOL;
	$js .= "showhtml = '<b>" . esc_js( $show_label ) . "</b>: ';";
	$js .= "showhtml += '<a href=\"" . esc_url_raw( $show_edit_link ) . "\" target=\"_blank\" class=\"show-edit-link\">" . esc_js( $show_title ) . "</a>';" . PHP_EOL;
	$js .= "parent.jQuery('#show-title').html(showhtml).show();" . PHP_EOL;
	$js .= "parent.radio_load_schedule(false,false,true);" . PHP_EOL;

	// --- filter and output script ---
	$js = apply_filters( 'radio_station_add_show_shift_script', $js, $show_id );
	echo '<script>' . $js . '</script>';
	
	exit;
}

// -----------------
// AJAX Add Override
// -----------------
add_action( 'wp_ajax_nopriv_radio_station_add_override_time', 'radio_station_pro_relogin_message' );
add_action( 'wp_ajax_radio_station_add_override_time', 'radio_station_pro_add_override_time' );
function radio_station_pro_add_override_time() {

	// --- save the new override data ---
	$_POST['override_id'] = $_REQUEST['override_id'];
	$_POST['show_override_nonce'] = $_REQUEST['nonce'];
	$_POST['new_override'] = array(
		'date'           => $_REQUEST['date'],
		'start_hour'     => $_REQUEST['start_hour'],
		'start_min'      => $_REQUEST['start_min'],
		'start_meridian' => $_REQUEST['start_meridian'],
		'end_hour'       => $_REQUEST['end_hour'],
		'end_min'        => $_REQUEST['end_min'],
		'end_meridian'   => $_REQUEST['end_meridian'],
		/* 'multiday'       => $_REQUEST['multiday'], */
		/* 'end_date'       => $_REQUEST['end_date'], */
		'disabled'       => $_REQUEST['disabled'],
	);
	$override_id = absint( $_REQUEST['override_id'] );
	if ( !defined( 'RADIO_STATION_DEBUG' ) ) {
		define( 'RADIO_STATION_DEBUG', true );
	}
	radio_station_override_save_data( $override_id );

	// --- remove the new shift form ---
	$override_label = __( 'Override', 'radio-station' );
	$override_title = get_the_title( $override_id );
	$override_edit_link = get_edit_post_link( $override_id );
	$js = "parent.jQuery('.override-buttons').show();" . PHP_EOL;
	$js .= "parent.jQuery('#new-shift-form').hide();" . PHP_EOL;
	$js .= "parent.jQuery('#TB_ajaxWindowTitle').html('" . esc_js( __( 'Edit Override Times', 'radio-station') ) . "');" . PHP_EOL;
	$js .= "overridehtml = '<b>" . esc_js( $override_label ) . "</b>:';";
	$js .= "overridehtml += '<a href=\"" . esc_url_raw( $override_edit_link ) . "\" target=\"_blank\" class=\"override-edit-link\">" . esc_js( $override_title ) . "</a>';" . PHP_EOL;
	$js .= "parent.jQuery('#override-title').html(override_html).show();" . PHP_EOL;
	$js .= "parent.radio_load_schedule(false,false,true);" . PHP_EOL;

	// --- filter and output script ---
	$js = apply_filters( 'radio_station_add_override_time_script', $js, $override_id );
	echo '<script>' . $js . '</script>';
	
	exit;
}

// ------------------------
// Single Shift Save Action
// ------------------------
add_action( 'radio_station_show_save_shift', 'radio_station_pro_show_save_shift', 10, 1 );
function radio_station_pro_show_save_shift( $shift_id ) {
	// --- hide other shifts if single shift saving ---
	// (uncheck and check single radio to trigger onchange function)
	echo "<script>shiftid = '" . esc_js( $shift_id ) . "';
	parent.radio_single_shift(false); 
	parent.radio_single_shift(shiftid);</script>" . PHP_EOL;
}

