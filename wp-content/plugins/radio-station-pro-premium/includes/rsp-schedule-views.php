<?php

// =========================
// === Radio Station Pro ===
// =========================
// ---- Schedule Views -----
// =========================

// === Existing Views ===
// - Filter Schedule Loader Controls
// === Extra Views ===
// - Filter Master Schedule Default Attributes
// - Filter Schedule Override for View
// - Filter Master Schedule Loader Script
// - Grid View Javascript
// - Calendar View Javascript
// === View Switching ===
// - Filter Automatic Schedule Views
// - Add Schedule Views to Control Order
// - Add Schedule Views Control
// - Multiview Control Styles
// - Filter Schedule Override Views

// Development TODOs
// -----------------
// ? check for view selection cookie value with javascript ?
// ? save view selection to cookie / user meta ?


// ----------------------
// === Existing Views ===
// ----------------------

// -------------------------------
// Filter Schedule Loader Controls
// -------------------------------
add_filter( 'radio_station_schedule_loader_control', 'radio_station_pro_schedule_loader_control', 10, 3 );
function radio_station_pro_schedule_loader_control( $html, $view, $position ) {

	// --- filter arrows ---
	$arrows = array( 'left' => '&#8249;', 'right' => '&#8250;', 'doubleleft' => '&#171;', 'doubleright' => '&#187;' );
	$arrows = apply_filters( 'radio_station_schedule_arrows', $arrows, $view );

	// --- check loader position ---
	if ( 'left' == $position ) {

		// --- load previous week arrow ---
		$title = __( 'Load Schedule for Previous Week', 'radio-station' );
		$html = '<div class="master-schedule-' . esc_attr( $view ) . '-arrow-left"><div class="master-schedule-' . esc_attr( $view ) . '-arrow" onclick="radio_load_schedule(\'previous\',\'' . esc_attr( $view ) . '\',false);" title="' . esc_attr( $title ) . '">';
		$html .= $arrows['doubleleft'] . '</div></div>' . PHP_EOL;

	} elseif ( 'right' == $position ) {

		// --- load next week arrow ---
		$title = __( 'Load Schedule for Next Week', 'radio-station' );
		$html = '<div class="master-schedule-' . esc_attr( $view ) . '-arrow-right"><div class="master-schedule-' . esc_attr( $view ) . '-arrow" onclick="radio_load_schedule(\'next\',\'' . esc_attr( $view ) . '\',false);" title="' . esc_attr( $title ) . '">';
		$html .= $arrows['doubleright'] . '</div></div>' . PHP_EOL;

	}

	return $html;
}


// -------------------
// === Extra Views ===
// -------------------

// -----------------------------------------
// Filter Master Schedule Default Attributes
// -----------------------------------------
add_filter( 'radio_station_master_schedule_default_atts', 'radio_station_pro_master_schedule_default_atts', 10, 3 );
function radio_station_pro_master_schedule_default_atts( $defaults, $view, $views ) {

	// --- add defaults for grid view ---
	// 2.4.1.6: add defaults for time spaced grid
	if ( 'grid' == $view ) {
		$defaults['show_image'] = 1;
		$defaults['show_hosts'] = 1;
		$defaults['display_day'] = 1;
		$defaults['gridwidth'] = 150;
		$defaults['time_spaced'] = 0;
	} elseif ( in_array( 'grid', $views ) ) {
		$defaults['grid_show_image'] = 1;
		$defaults['grid_show_hosts'] = 1;
		$defaults['grid_display_day'] = 1;
		$defaults['gridwidth'] = 150;
		$defaults['time_spaced'] = 0;
	}
	
	// --- add defaults for calendar view ---
	if ( 'calendar' == $view ) {
		$defaults['show_image'] = 1;
		$defaults['show_hosts'] = 1;
		$defaults['display_day'] = 'full';
		$defaults['display_month'] = 'full';
		$defaults['weeks'] = 4;
		$defaults['previous_weeks'] = 1;
	} elseif ( in_array( 'calendar', $views ) ) {
		$defaults['calendar_show_image'] = 1;
		$defaults['calendar_show_hosts'] = 1;
		$defaults['calendar_display_day'] = 'full';
		$defaults['calendar_display_month'] = 'full';
		$defaults['calendar_weeks'] = 4;
		$defaults['calendar_previous_weeks'] = 1;
	}

	return $defaults;		
}

// --------------------------------------
// Filter Schedule Override to Check View
// --------------------------------------
add_filter( 'radio_station_schedule_override', 'radio_station_pro_extra_views', 11, 2 );
function radio_station_pro_extra_views( $output, $atts ) {

	$extra_views = array( 'grid', 'calendar' );
	if ( !in_array( $atts['view'], $extra_views ) ) {
		return $output;
	}

	$newline = '';
	if ( RADIO_STATION_DEBUG ) {
		$newline = "\n";
	}

	if ( 'grid' == $atts['view'] ) {

		// --- load grid template ---
		ob_start();
		$template = radio_station_get_template( 'file', 'master-schedule-grid.php' );
		require $template;
		$html = ob_get_contents();
		ob_end_clean();
		$html = apply_filters( 'master_schedule_grid_view', $html, $atts );
		$output .= $html;
		$js = radio_station_pro_master_schedule_grid_js();
		wp_add_inline_script( 'radio-station', $js );
		radio_station_enqueue_style( 'views' );

	} elseif ( 'calendar' == $atts['view'] ) {

		// --- load calendar template ---
		ob_start();
		$template = radio_station_get_template( 'file', 'master-schedule-calendar.php' );
		require $template;
		$html = ob_get_contents();
		ob_end_clean();
		$html = apply_filters( 'master_schedule_calendar_view', $html, $atts );
		$output .= $html;
		radio_station_enqueue_style( 'views' );
		$js = radio_station_pro_master_schedule_calendar_js();
		wp_add_inline_script( 'radio-station', $js );

		// --- enqueue show slider script ---
		$js = radio_station_pro_shows_slider_script();
		wp_add_inline_script( 'radio-station', $js );
	}

	// --- add override marker ---
	$output .= '<!-- SCHEDULE OVERRIDE -->';

	return $output;
}

// ------------------------------------
// Filter Master Schedule Loader Script
// ------------------------------------
add_filter( 'radio_station_master_schedule_load_script', 'radio_station_pro_master_schedule_load_script', 10, 2 );
function radio_station_pro_master_schedule_load_script( $js, $atts ) {

	$loadjs = '';	
	if ( strstr( $atts['view'], ',' ) ) {
		$views = explode( ',', $atts['view'] );
	} else {
		$views = array( $atts['view'] );
	}
	foreach ( $views as $view ) {

		$view = trim( $view );

		if ( 'grid' == $view ) {
			$loadjs .= "if (typeof parent.radio_grid_initialize == 'function') {";
				$loadjs .= "parent.radio_grid_init = false;" . PHP_EOL;
				$loadjs .= "parent.radio_grid_initialize();" . PHP_EOL;
				$loadjs .= "clearInterval(schedule_loader);" . PHP_EOL;
			$loadjs .= "}" . PHP_EOL;
		} elseif ( 'calendar' == $view ) {
			$loadjs .= "if (typeof parent.radio_calendar_initialize == 'function') {";
				$loadjs .= "parent.radio_calendar_init = false;" . PHP_EOL;
				$loadjs .= "parent.radio_calendar_initialize();" . PHP_EOL;
				$loadjs .= "parent.radio_shows_slider();" . PHP_EOL;
				$loadjs .= "clearInterval(schedule_loader);" . PHP_EOL;
			$loadjs .= "}" . PHP_EOL;
		}
	}

	// --- insert loader script via placeholder text ---
	if ( '' != $loadjs ) {
		$js = str_replace( '/* LOADER PLACEHOLDER */', '/* LOADER PLACEHOLDER */' . PHP_EOL . $loadjs, $js );
	}
	return $js;
}


// --------------------
// Grid View Javascript
// --------------------
function radio_station_pro_master_schedule_grid_js() {

	// --- grid day switching function ---
	$js = "function radio_grid_clicks() {
		/* jQuery('.master-schedule-grid-headings').bind('click', function(event) {
			headerID = jQuery(event.target).closest('li').attr('id');
			jQuery('.master-schedule-grid-day').removeClass('active-day');
			jQuery('#'+headerID).addClass('active-day');
		}); */
	}" . PHP_EOL;

	// --- grid view responsiveness ---
	$js .= "/* Initialize Grid */
	var radio_grid_init = false;
	jQuery(document).ready(function() {
		radio_grid_initialize();
		var radio_grid_highlighting = setInterval(radio_grid_show_highlight, 60000);
	});
	jQuery(window).resize(function () {
		radio_resize_debounce(function() {radio_grid_responsive(false);}, 500, 'schedulegrid');
	});

	/* Grid Initialize */
	function radio_grid_initialize() {
		radio_grid_clicks();
		radio_grid_responsive(false);
		radio_grid_show_highlight();
		radio_grid_init = true;
	}

	/* Set Active Day on Load */
	function radio_grid_active_day(day) {
		if (!jQuery('.master-schedule-grid-day').length) {return;}
		jQuery('.master-schedule-grid-day').removeClass('active-day');
		if (!day) {jQuery('.master-schedule-grid-day').first().addClass('active-day');}
		else {
			jQuery('#master-schedule-grid-day-'+day).addClass('active-day');
			/* TODO: set active date input value from day ? */
		}
	}

	/* Current Show Highlighting */
	function radio_grid_show_highlight() {
		if (!jQuery('.master-schedule-grid-day').length) {return;}
		radio.current_time = Math.floor( (new Date()).getTime() / 1000 );
		radio.offset_time = radio.current_time + radio.timezone.offset;
		if (radio.debug) {console.log(radio.current_time+' - '+radio.offset_time);}
		if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
		jQuery('.master-schedule-grid-day').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				jQuery(this).addClass('current-day');
				day = jQuery(this).attr('id').replace('master-schedule-grid-header-', '');
				radio_grid_active_day(day);
			} else {jQuery(this).removeClass('current-day');}
		});
		radio_grid_active_day(false); /* fallback */
		var radio_grid_split = false;
		jQuery('.master-schedule-grid-show').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if (radio.debug) {console.log(radio.offset_time+' : '+start+' : '+end);}
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				radio_grid_current = true;
				if (radio.debug) {console.log('^ Now Playing ^');}
				jQuery(this).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
				/* also highlight split shift via matching shift class */
				if (jQuery(this).hasClass('overnight')) {
					classes = jQuery(this).attr('class').split(/\s+/);
					for (i = 0; i < classes.length; i++) {
						if (classes[i].substr(0,6) == 'split-') {radio_grid_split = classes[i];}
					}
				}
			} else {
				jQuery(this).removeClass('nowplaying');
				if (start > radio.offset_time) {jQuery(this).addClass('after-current');}
				else if (end < radio.offset_time) {jQuery(this).addClass('before-current');}
			}
		});
		if (radio_grid_split) {
			jQuery('.'+radio_grid_split).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
		}
	}

	/* Make Grid Responsive */
	function radio_grid_responsive(leftright) {
		if (!jQuery('.master-schedule-grid-day').length) {return;}

		fallback = -1; selected = -1; foundday = false;
		if (!leftright || (leftright == 'left')) {
			if (jQuery('.master-schedule-grid-day.first-day').length) {
				start = jQuery('.master-schedule-grid-day.first-day');
			} else {start = jQuery('.master-schedule-grid-day').first(); fallback = 0;}
			classes = start.attr('class').split(' ');
		} else if (leftright == 'right') {
			if (jQuery('.master-schedule-grid-day.last-day').length) {
				end = jQuery('.master-schedule-grid-day.last-day');
			} else {end = jQuery('.master-schedule-grid-day').last(); fallback = 6;}
			classes = end.attr('class').split(' ');
		}
		for (i = 0; i < classes.length; i++) {
			if (classes[i].indexOf('day-') === 0) {selected = parseInt(classes[i].replace('day-',''));}
		}
		if (selected < 0) {selected = fallback;}
		if (radio.debug) {console.log('Current Day: '+selected);}

		if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
		if (selected < 0) {selected = 0;} else if (selected > 6) {selected = 6;}
		if (!jQuery('.master-schedule-grid-day.day-'+selected).length) {
			while (!foundday) {
				if (leftright == 'left') {selected--;} else if (leftright == 'right') {selected++;}
				if (jQuery('.master-schedule-grid-day.day-'+selected).length) {foundday = true;}
				if ((selected < 0) || (selected > 6)) {selected = fallback; foundday = true;}
			}
		}
		if (radio.debug) {console.log('Selected Day: '+selected);}

		jQuery('.master-schedule-grid-day, .master-schedule-grid-day.show-info').removeClass('first-day').removeClass('last-day').hide();
		jQuery('#master-schedule-grid').css('width','100%');
		tablewidth = jQuery('#master-schedule-grid').width();
		jQuery('#master-schedule-grid').css('width','auto');
		totalwidth = 0; columns = 0; firstcolumn = -1; lastcolumn = 7; endgrid = false;
		for (i = selected; i < 7; i++) {
			if (!endgrid && (jQuery('.master-schedule-grid-day.day-'+i).length)) {
				if ((i > 0) && (i == selected)) {jQuery('.master-schedule-grid-day.day-'+i).addClass('first-day'); firstcolumn = i;}
				else if (i < 6) {jQuery('.master-schedule-grid-day.day-'+i).addClass('last-day');}
				jQuery('.master-schedule-grid-day.day-'+i+', .master-schedule-grid-day .show-info.day-'+i).css('min-width','').show();
				colwidth = jQuery('.master-schedule-grid-day.day-'+i).width();
				totalwidth = totalwidth + colwidth;
				if (radio.debug) {console.log('('+colwidth+') : '+totalwidth+' / '+tablewidth);}
				jQuery('.master-schedule-grid-day.day-'+i).removeClass('last-day');
				if (totalwidth > tablewidth) {
					if (radio.debug) {console.log('Hiding Day '+i);}
					jQuery('.master-schedule-grid-day.day-'+i+', .master-schedule-grid-day .show-info.day-'+i).hide();
					endgrid = true;
				} else {
					if (radio.debug) {console.log('Showing Day '+i);}
					jQuery('.master-schedule-grid-day.day-'+i).removeClass('last-day');
					totalwidth = totalwidth - colwidth + jQuery('.master-schedule-grid-day.day-'+i).width();
					lastcolumn = i; columns++;
				}
			}

		}
		if (lastcolumn < 6) {jQuery('.master-schedule-grid-day.day-'+lastcolumn).addClass('last-day');}

		if (leftright == 'right') {
			for (i = (selected - 1); i > -1; i--) {
				if (!endgrid && (jQuery('.master-schedule-grid.day-'+i).length)) {
					jQuery('.master-schedule-grid-day.day-'+i+', .master-schedule-grid-day .show-info.day-'+i).show();
					colwidth = jQuery('.master-schedule-grid-day.day-'+i).width();
					totalwidth = totalwidth + colwidth;
					if (radio.debug) {console.log('('+colwidth+') : '+totalwidth+' / '+tablewidth);}
					if (totalwidth > tablewidth) {
						if (radio.debug) {console.log('Hiding Day '+i);}
						jQuery('.master-program-day.day-'+i+', .master-schedule-grid-day .show-info.day-'+i).hide();
						endgrid = true;
					} else {
						if (radio.debug) {console.log('Showing Day '+i);}
						jQuery('.master-schedule-grid-day').removeClass('first-day');
						jQuery('.master-schedule-grid-day.day-'+i).addClass('first-day');
						columns++;
					}
				}
			}
		}
		jQuery('#master-schedule-grid').css('width','100%');
		
		/* loops to match heights on visible grid row items */
		var day; var maxitems = 0;
		var days = new Array(); var itemheights = new Array(); rowheights = new Array();
		maxwidth = jQuery('.master-schedule-grid-day').css('max-width').replace('px','');
		jQuery('.master-schedule-grid-day:visible').each(function() {
			var classes = jQuery(this)[0].className.split(/\s+/);
			for (i = 0; i < classes.length; i++) {
				if (classes[i].substr(0,4) == 'day-') {day = classes[i].replace('day-','');}
			}
			itemheights[day] = new Array(); item = 0;
			jQuery(this).find('.master-schedule-grid-show').each(function() {
				jQuery(this).css('height','');
				itemheights[day][item] = jQuery(this).outerHeight(); item++;
			});
			if (item > maxitems) {maxitems = item;}
			days[days.length] = day;
		});
		for (i = 0; i < maxitems; i++) {
			rowheights[i] = 0;
			for (j in days) {
				day = days[j];
				if (itemheights[day][i] > rowheights[i]) {rowheights[i] = itemheights[day][i];}
			}
		}
		jQuery('.master-schedule-grid-day:visible').each(function() {
			var i = 0;
			jQuery(this).find('.master-schedule-grid-show').each(function() {
				jQuery(this).css('height',rowheights[i]+'px'); i++;
			});
		});
		
		/* auto-spread columns and match grid loader width */
		totalwidth = jQuery('#master-schedule-grid').outerWidth();
		gridwidth = parseInt(totalwidth / days.length);
		if (document.getElementById('master-schedule-grid-column-width')) {
			gridcolwidth = document.getElementById('master-schedule-grid-column-width').value;
			if ((gridcolwidth != '') && (gridwidth > gridcolwidth)) {gridwidth = gridcolwidth;}
		}
		jQuery('.master-schedule-grid-day').css('min-width',gridwidth+'px');
		var listwidth = 0;
		jQuery('.master-schedule-grid-day:visible').each(function() {
			listwidth = listwidth + jQuery(this).width();
		});
		jQuery('.master-schedule-grid-loader').css('width',listwidth);
	}

	/* Shift Day Left / Right */
	function radio_shift_grid(leftright) {
		radio_grid_responsive(leftright); return false;
	}" . PHP_EOL;

	// --- filter and return ---
	$js = apply_filters( 'radio_station_pro_master_schedule_grid_js', $js );
	return $js;
}

// ----------------------------
// Grid Time Spacing Javascript
// ----------------------------
// 2.4.1.6: added grid view time spacing javascript
function radio_station_pro_schedule_grid_spacing_js( $js ) {

		// --- maybe enqueue time spaced grid ---
		// 2.4.1.6: check for time spacing view option
		$js .= "jQuery(document).ready(function() {setTimeout(function() {
		 var grid_column_width = document.getElementById('master-schedule-grid-column-width').value;
		 jQuery('.master-schedule-grid-show').each(function() {
		  if (!jQuery(this).hasClass('master-schedule-grid-no-shows')) {
		   if (jQuery(this).hasClass('master-schedule-grid-spacer')) {
		    jQuery(this).css({'height':'1px','width':'100%'});
		    length = (jQuery(this).find('.end-time').attr('data') - jQuery(this).find('.start-time').attr('data')) / 60;
		    height = grid_column_width * 2 * (length / 100);
		    jQuery(this).css({'height':height});
		   } else {
		    length = (jQuery(this).find('.show-time .rs-end-time').attr('data') - jQuery(this).find('.show-time .rs-start-time').attr('data')) / 60;
		    maxheight = grid_column_width * 2 * (length / 100);
		    icontainer = jQuery(this).find('.show-image'); image = icontainer.find('img');
		    iheight = parseInt(image.attr('height')) / (parseInt(image.attr('width')) / grid_column_width);
		    isrc = image.attr('src'); icontainer.hide();
		    maxtiles = Math.floor(maxheight / grid_column_width); 
		    if (maxtiles > 0) {height = maxtiles * iheight; margin = maxheight - height;} else {margin = 0; height = maxheight;}
		    jQuery(this).addClass('background-image').css({'max-height':maxheight+'px','height':''});
		    jQuery(this).find('.show-info').css({'background-image':'url('+isrc+')','background-size':iheight+'px '+grid_column_width+'px','background-repeat':'repeat-y','height':height+'px','margin-bottom':margin+'px'});
		   }
		  }
		  jQuery('.master-schedule-grid-spacer').show();
		 });
		}, 2000); });" . PHP_EOL;
		
		return $js;
}

// ------------------------
// Calendar View Javascript
// ------------------------
function radio_station_pro_master_schedule_calendar_js() {

	// --- calendar date switching function ---
	$js = "function radio_calendar_clicks() {
		jQuery('.master-schedule-calendar-header').bind('click', function(event) {
			week = jQuery(this).closest('tr').attr('id').replace('master-schedule-calendar-week-row-','');
			jQuery('.master-schedule-calendar-shows').removeClass('active-week');
			jQuery('#master-schedule-calendar-shows-'+week).addClass('active-week');
			date = jQuery(this).attr('id').replace('master-schedule-calendar-header-','');
			jQuery('.master-schedule-calendar-header, .master-schedule-calendar-shows-list, .master-schedule-calendar-shows-slider').removeClass('active-date');
			jQuery('#master-schedule-calendar-header-'+date+', #master-schedule-calendar-shows-list-'+date+', #master-schedule-calendar-shows-slider-'+date).addClass('active-date');
			radio_slide_check('shows', date);
			radio_slider_responsive('shows');
		});
	}" . PHP_EOL;
	
	// --- scroll slider to current show ---
	$js .= "function radio_calendar_current_show() {
		if (!jQuery('.current-date .shows-slider').length) {return;}
		date = jQuery('.current-date .shows-slider').attr('id').replace('shows-slider-','');
		radio_slide_check('shows', date);
		showlist = jQuery('.current-date .shows-slider-item.nowplaying').parent();
		slides = showlist.find('.shows-slider-item'); i = 0;
		slides.each(function() {if (jQuery(this).hasClass('nowplaying')) {current = i;} i++;});
		prevshows = showlist.find('.before-current');
		if (radio.debug) {console.log('Slides: '+radio_slider.count+' - Current: '+current+' - Max: '+radio_slider.max+' Prev: '+prevshows.length);}
		if (prevshows.length) {
			shift = prevshows.length;
			for (i = 0; i < shift; i++) {radio_slide_right('shows', date);}
		}
		radio_slider_responsive('shows');
	}" . PHP_EOL;

	// --- calendar view functions ---
	$js .= "/* Initialize Calendar */
	var radio_calendar_init = false;
	jQuery(document).ready(function() {
		radio_calendar_initialize();
		var radio_calendar_highlighting = setInterval(radio_calendar_show_highlight, 60000);
	});
	jQuery(window).resize(function () {
		radio_resize_debounce(function() {radio_calendar_responsive();}, 500, 'schedulecalendar');
	});
	
	/* Calendar Initialize */
	function radio_calendar_initialize() {
		radio_calendar_clicks();
		radio_calendar_responsive();
		radio_calendar_show_highlight();
		radio_calendar_current_show();
		radio_calendar_init = true;
	}

	/* Set Active Date on Load */
	function radio_calendar_active_date(date) {
		if (!jQuery('.master-schedule-calendar-date').length) {return;}
		jQuery('.master-schedule-calendar-date').removeClass('active-date');
		if (!date) {active_date = jQuery('#schedule-active-date').val();}
		else {jQuery('#schedule-active-date').val(date);}
		jQuery('#master-schedule-calendar-date-'+date).addClass('active-date');
	}

	/* Current Show Highlighting */	
	function radio_calendar_show_highlight() {
		if (!jQuery('.master-schedule-calendar-date').length) {return;}
		radio.current_time = Math.floor( (new Date()).getTime() / 1000 );
		radio.offset_time = radio.current_time + radio.timezone.offset;
		if (radio.debug) {console.log(radio.current_time+' - '+radio.offset_time);}
		if (radio.timezone.adjusted) {radio.offset_time = radio.current_time;}
		jQuery('.master-schedule-calendar-date').each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				jQuery(this).addClass('current-day');
				date = jQuery(this).attr('id').replace('master-schedule-calendar-date-', '');
				radio_calendar_active_date(date);
			} else {jQuery(this).removeClass('current-date');}
		});
		radio_calendar_active_date(false); /* fallback */
		var radio_calendar_split = false;
		jQuery.merge(jQuery('.master-schedule-calendar-show'),jQuery('.shows-slider-item')).each(function() {
			start = parseInt(jQuery(this).find('.rs-start-time').attr('data'));
			end = parseInt(jQuery(this).find('.rs-end-time').attr('data'));
			if (radio.debug) {console.log(radio.offset_time+' - '+start+' - '+end);}
			if ( (start < radio.offset_time) && (end > radio.offset_time) ) {
				radio_calendar_current = true;
				if (radio.debug) {console.log('^ Now Playing ^');}
				jQuery(this).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
				/* also highlight split shift via matching shift class */
				if (jQuery(this).hasClass('overnight')) {
					classes = jQuery(this).attr('class').split(/\s+/);
					for (i = 0; i < classes.length; i++) {
						if (classes[i].substr(0,6) == 'split-') {radio_calendar_split = classes[i];}
					}
				}
			} else {
				jQuery(this).removeClass('nowplaying');
				if (start > radio.offset_time) {jQuery(this).addClass('after-current');}
				else if (end < radio.offset_time) {jQuery(this).addClass('before-current');}
			}
		});
		if (radio_calendar_split) {
			jQuery('.'+radio_calendar_split).removeClass('before-current').removeClass('after-current').addClass('nowplaying');
		}
	}

	/* Make Calendar Responsive */
	function radio_calendar_responsive() {
		if (!jQuery('.master-schedule-calendar-date').length) {return;}
		jQuery('.master-schedule-calendar-day-cell, .master-schedule-calendar-header').css({'min-width':'1px','max-width':'1px'});
		width = jQuery('#master-schedule-calendar').width();
		jQuery('.master-schedule-calendar-day-cell, .master-schedule-calendar-header').css({'min-width':'','max-width':''});
		larrow = jQuery('#master-schedule-calendar-arrow-cell-left').width();
		rarrow = jQuery('#master-schedule-calendar-arrow-cell-right').width();
		cellwidth = parseInt((width - larrow - rarrow ) / 7);
		/* console.log(width+' - '+(larrow+rarrow)+' = '+(width-larrow-rarrow)+' / 7 = '+cellwidth); */
		jQuery('.master-schedule-calendar-day-cell, .master-schedule-calendar-header').css({'min-width':cellwidth+'px','max-width':cellwidth+'px'});
		if (width < 450) {jQuery('#master-schedule-calendar').addClass('narrow');}
		else {jQuery('#master-schedule-calendar').removeClass('narrow');}
		radio_slider_responsive('shows');
	}" . PHP_EOL;

	// --- filter and return ---
	$js = apply_filters( 'radio_station_pro_master_schedule_calendar_js', $js );
	return $js;
}

// ------------------
// Show Slider Script
// ------------------
function radio_station_pro_shows_slider_script() {

	// --- output slider script ---
	$js = "radio_slider = {}; radio_slider.offset = 200;
	jQuery.fn.reverse = Array.prototype.reverse;

	/* Load Slider */
	jQuery(document).ready(function() {setTimeout(radio_shows_slider, 2000);});
	function radio_shows_slider() {
		radio_slide_check('shows', false);
		jQuery(window).resize(function() {radio_slide_check('shows', false);});
	}

	// --- show display sizer function ---
	function radio_slider_responsive(type) {
		jQuery('.'+type+'-slider-mask').css('max-width','');
		width = jQuery('.'+type+'-slider:visible').outerWidth();
		arrowswidth = jQuery('.'+type+'-slider-arrow-wrapper:visible').width() * 2;
		numslides = Math.floor((width - arrowswidth) / radio_slider.offset);
		if (numslides < 1) {numslides = 1;}
		jQuery('.'+type+'-slider-mask').css('max-width',(numslides * radio_slider.offset)+'px');
		if (radio.debug) {console.log('Slides: '+numslides);}

		jQuery('.'+type+'-slider:visible').each(function() {
			jQuery(this).find('.'+type+'-slider-item').css('height','auto');
			jQuery(this).find('.'+type+'-slider-arrow-wrapper').css('height','auto');
			jQuery(this).find('.'+type+'-slider-arrow').css('margin-top',0);
			height = 0;
			jQuery(this).find('.'+type+'-slider-item').each(function() {
				thisheight = jQuery(this).height();
				if (thisheight > height) {height = thisheight;}
			});
			if (height > 0) {
				jQuery(this).find('.'+type+'-slider-item').css('height',height);
				jQuery(this).find('.'+type+'-slider-arrow-wrapper').css('height',height);
				arrowtop = (height - jQuery(this).find('.'+type+'-slider-arrow').height()) / 2;
				jQuery(this).find('.'+type+'-slider-arrow').css('margin-top', arrowtop);
			}
		});
	}
	
	/* Slide Left */
	function radio_slide_left(type, id, animate) {
		radio_slider.type = type; radio_slider.id = id;
		if (jQuery('#'+type+'-slider-'+id+' .'+type+'-slider-arrow-left').hasClass('inactive')) {return;}
		radio_slider.slides = jQuery('#'+type+'-slider-'+id+' .'+type+'-slider-item');
		radio_slider.found = false; radio_slider.count = 0;
		reversed = radio_slider.slides.reverse();
		reversed.each(function() {
			if (!radio_slider.found) {
				marginleft = parseInt(jQuery(this).css('margin-left'));
				if (marginleft === -(radio_slider.offset)) {
					radio_slider.found = true;
					if (animate) {jQuery(this).animate({marginLeft: '0px'}, 500);}
					else {jQuery(this).css('marginLeft', '0px');}
					jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-arrow-right').removeClass('inactive');
					if (radio_slider.count == (radio_slider.slides.length-1)) {
						jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-arrow-left').addClass('inactive');
					}

				}
			}
			radio_slider.count++;
		});
	}

	/* Slide Right */
	function radio_slide_right(type, id, animate) {
		radio_slider.type = type; radio_slider.id = id;
		if (jQuery('#'+type+'-slider-'+id+' .'+type+'-slider-arrow-right').hasClass('inactive')) {return;}
		radio_slider.slides = jQuery('#'+type+'-slider-'+id+' .'+type+'-slider-item');
		radio_slider.found = false; radio_slider.count = 0;
		radio_slider.slides.each(function(id) {
			if (!radio_slider.found) {
				marginleft = parseInt(jQuery(this).css('margin-left'));
				if (marginleft === 0) {
					radio_slider.found = true;
					if (animate) {jQuery(this).animate({marginLeft: '-'+radio_slider.offset+'px'}, 500);}
					else {jQuery(this).css('margin-left', '-'+radio_slider.offset+'px');}
					jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-arrow-left').removeClass('inactive');
					maskwidth = jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-mask').width();
					radio_slider.max = Math.floor(maskwidth / radio_slider.offset);
					/* if (radio_slider.max < 1) {radio_slider.max = 1;} */
					/* console.log(maskwidth+' : '+radio_slider.count+' : '+radio_slider.slides.length+' : '+radio_slider.max); */
					if (radio_slider.count == (radio_slider.slides.length - radio_slider.max - 1)) {
						jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-arrow-right').addClass('inactive');
						jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-last-slid').val(radio_slider.count);
					}
				}
			}
			radio_slider.count++;
		});
	}

	/* Slide Check */
	function radio_slide_check(type, id) {
		if (!jQuery('.active-date .'+type+'-slider').length) {return;}
		if (!id) {id = jQuery('.active-date .'+type+'-slider').attr('id').replace(type+'-slider-','');}
		if (!document.getElementById(type+'-slider-'+id)) {return;}
		radio_slider.type = type; radio_slider.id = id;
		jQuery('#'+type+'-slider-'+id+' .'+type+'-slider-arrow-right').removeClass('inactive');
		radio_slider.slides = jQuery('#'+type+'-slider-'+id+' .'+type+'-slider-item');
		radio_slider.found = false; radio_slider.count = 0;
		radio_slider.slides.each(function(id) {
			if (!radio_slider.found) {
				marginleft = parseInt(jQuery(this).css('margin-left'));
				if (marginleft === 0) {
					radio_slider.found = true;
					maskwidth = jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-mask').width();
					radio_slider.max = Math.floor(maskwidth / radio_slider.offset);
					/* if (radio_slider.max < 1) {radio_slider.max = 1;} */
					/* console.log(maskwidth+' : '+radio_slider.count+' : '+radio_slider.slides.length+' : '+radio_slider.max); */
					if (radio_slider.count == (radio_slider.slides.length - radio_slider.max)) {
						jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-arrow-right').addClass('inactive');
						jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-last-slid').val(radio_slider.count);
					}
				}
			}
			radio_slider.count++;
		});
		if (radio_slider.slides.length < radio_slider.max) {
			jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-arrow-left').addClass('inactive');
			jQuery('#'+radio_slider.type+'-slider-'+radio_slider.id+' .'+radio_slider.type+'-slider-arrow-right').addClass('inactive');
		}
	}" . PHP_EOL;

	// --- filter script and return ---
	$js = apply_filters( 'radio_station_show_slider_script', $js);
	return $js;
}


// -------------------------
// === Schedule Switcher ===
// -------------------------

// -------------------------------
// Filter Automatic Schedule Views
// -------------------------------
add_filter( 'radio_station_automatic_schedule_atts', 'radio_station_pro_automatic_schedule_atts' );
function radio_station_pro_automatic_schedule_atts( $atts ) {

	// 2.4.1.8: fix to check schedule switching setting is enabled
	$switching = radio_station_get_setting( 'schedule_switcher' );
	if ( 'yes' != $switching ) {
		return $atts;
	}

	// --- get available views setting ---
	$views = radio_station_get_setting( 'schedule_views' );

	// --- loop to add views ---
	if ( $views && is_array( $views ) && ( count( $views ) > 0 ) ) {
		if ( !in_array( $atts['view'], $views ) ) {
			$views = array_merge( array( $atts['view'] ), $views );
		}
		$atts['view'] = implode( ',', $views );
	}

	// if ( RADIO_STATION_DEBUG ) {
	// 	echo '<span style="display:none;">Schedule Views: ' . $atts['view'] . '</span>';
	// }

	// --- set default view ---
	$atts['default_view'] = radio_station_get_setting( 'schedule_view' );

	return $atts;
}

// -----------------------------------
// Add Schedule Views to Control Order
// -----------------------------------
add_filter( 'radio_station_schedule_control_order', 'radio_station_pro_schedule_control_order', 10, 2 );
function radio_station_pro_schedule_control_order( $control_order, $atts ) {
	if ( strstr( $atts['view'], ',' ) ) {
		// --- append view switching control key ---
		$control_order = array_merge( $control_order, array( 'views' ) );
	}
	return $control_order;
}

// --------------------------
// Add Schedule Views Control
// --------------------------
add_filter( 'radio_station_schedule_controls', 'radio_station_pro_schedule_controls', 11, 2 );
function radio_station_pro_schedule_controls( $controls, $atts ) {

	if ( strstr( $atts['view'], ',' ) ) {

		// --- get default view ---
		$default_view = $atts['default_view'];

		// --- get view options ---
		$views = explode( ',', $atts['view'] );
		foreach ( $views as $i => $view ) {
			$views[$i] = trim( $view );
		}

		// --- recheck default view ---
		if ( !in_array( $default_view, $views ) ) {
			$default_view = $views[0];
		}

		// TODO: check view selection cookie
		// if ( isset( $_COOKIE['schedule-view'] ) && in_array( $_COOKIE['schedule-view'], $views ) ) {
		// 	$default_view = $_COOKIE['schedule-view'];
		// }

		// --- filter view order ---
		$view_order = array( 'table', 'tabs', 'grid', 'calendar', 'list' );
		$view_order = apply_filters( 'radio_station_pro_view_order', $view_order, $atts );
		$ordered_views = array();
		foreach ( $view_order as $i => $view ) {
			if ( in_array( $view, $views ) ) {
				$ordered_views[] = $view;
			}
		}
		if ( count( $ordered_views ) > 0 ) {
			$views = $ordered_views;
		}

		// --- set view labels ---
		$labels = array(
			'view'		=> __( 'View', 'radio-station' ),
			'table'		=> __( 'Table', 'radio-station' ),
			'tabs'		=> __( 'Tabs', 'radio-station' ),
			'list'		=> __( 'List', 'radio-station' ),
			'grid'          => __( 'Grid', 'radio-station' ),
			'calendar'      => __( 'Calendar', 'radio-station' ),
		);

		// --- set view dashicons ---
		$dashicons = true;
		$icons = array(
			'table'		=> 'dashicons-calendar',
			'tabs'		=> 'dashicons-index-card',
			'list'		=> 'dashicons-editor-ul',
			'grid'		=> 'dashicons-grid-view', // dashicons-screenoptions ?
			'calendar'	=> 'dashicons-calendar-alt',
		);

		// --- filter to use images instead of icons ---
		// (by setting URL for each view key)
		$images = apply_filters( 'radio_station_pro_view_images', false, $atts );
		if ( $images && is_array( $images ) && ( count( $images ) > 0 ) ) {
			$dashicons = false;
		}

		// --- view selector wrapper ---
		$selector = '<div id="master-schedule-views-wrapper">';

		// --- views menu label ---
		$selector .= '<div id="master-schedule-views-label" class="master-schedule-view-tab">';
		$selector .= '<b>' . esc_html( $labels['view'] ) . '</b>:';
		$selector .= '</div>';

		// --- loop available views ---
		foreach ( $views as $i => $view ) {

			// --- view tab items ---
			$view = trim( $view );
			$onclick = "radio_switch_view('" . esc_attr( $view ) . "')";
			$classes = array( 'master-schedule-view-tab' );
			if ( $view == $default_view ) {
				$classes[] = 'current-view';
			}
			$classlist = implode( ' ', $classes );
			$selector .= '<div id="master-schedule-view-tab-' . esc_attr( $view ) . '" class="' . esc_attr( $classlist ) . '" onclick="' . $onclick . '">';

				// --- view icon (or image) ---
				$selector .= '<div id="master-schedule-view-icon-' .esc_attr( $view ) . '" class="master-schedule-view-icon">';
				if ( $dashicons ) {
					$selector .= '<span class="schedule-view-icon dashicons ' . esc_attr( $icons[$view] ) . '"></span>';
				} else {
					$selector .= '<img class="schedule-view-icon" src="' . esc_url( $images[$view] ) . '" border="0">';
				}
				$selector .= '</div>';

				// --- view label ---
				$selector .= '<div id="master-schedule-view-label-' . esc_attr( $view ) . '" class="master-schedule-view-label">';
				$selector .= esc_html( $labels[$view] );
				$selector .= '</div>';

			$selector .= '</div>';

		}
		$selector .= '</div>';

		// --- add view selector to controls ---
		$controls['views'] = $selector;

		// --- view switcher script ---
		$js = "function radio_switch_view(view) {
			if (!jQuery('#master-schedule-view-tab-'+view).hasClass('current-view')) {
				views = ['table', 'tabs', 'list', 'grid', 'calendar'];
				for (i in views) {jQuery('#master-schedule-view-tab-'+views[i]).removeClass('current-view');}
				jQuery('#master-schedule-view-tab-'+view).addClass('current-view');
				for (i in views) {jQuery('#master-schedule-view-'+views[i]).hide().removeClass('current-view');}
				jQuery('#master-schedule-view-'+view).addClass('current-view').fadeIn(2000);
				if (view == 'table') {
					jQuery('.master-program-day, .master-program-hour-row .show-info').show();
					radio_table_responsive();
				} else if (view == 'tabs') {radio_tabs_responsive();}
				else if (view == 'grid') {radio_grid_responsive();}
				else if (view == 'calender') {radio_calendar_responsive();}
				radio_cookie.set('schedule_view', view, 14);
			}
		}" . PHP_EOL;

		// --- switch to user view on load ---		
		$js .= "jQuery(document).ready(function() {
			view = radio_cookie.get('schedule_view');
			if (view && (view != '')) {radio_switch_view(view);}
		});" . PHP_EOL;

		// --- enqueue script inline ---
		wp_add_inline_script( 'radio-station', $js );

		// --- set view switch styles ---
		$css = radio_station_pro_multiview_styles();

		// --- enqueue styles inline ---
		wp_add_inline_style( 'rs-schedule', $css );

		// --- maybe enqueue dashicons ---
		if ( $dashicons ) {
			wp_enqueue_style( 'dashicons' );
		}
	}

	return $controls;
}

// ------------------------
// Multiview Control Styles
// ------------------------
function radio_station_pro_multiview_styles() {

	$css = "#master-schedule-views-wrapper {float: right;}
	.master-schedule-view-tab {display: inline-block; margin-left: 30px; vertical-align: top; text-align: center;}
	#master-schedule-views-label {margin-left: 0; font-size: 1.2em;}
	.master-schedule-view-tab .master-schedule-view-label {font-size: 0.6em; padding: 0px 5px; border-radius: 5px;}
	.master-schedule-view-icon, .master-schedule-view-label {cursor: pointer;}
	.master-schedule-view-tab.current-view .master-schedule-view-label {background-color: #DDDDDD;}
	.master-schedule-view-icon span {opacity: 0.9;}
	.master-schedule-view-icon:hover span {opacity: 1;}
	.master-schedule-view {}
	.master-schedule-view.current-view {}
	.schedule-view-icon {}" . PHP_EOL;

	if ( is_admin() ) {
		$css .= "#wpbody-content #master-schedule-views-wrapper {margin-top: 25px; float: none;}" . PHP_EOL;
		$css .= "#wpbody-content #schedule-editor {margin-top: 20px; width: 100%;}" . PHP_EOL;
	}
	
	// --- filter and return ---
	$css = apply_filters( 'radio_station_multiview_styles', $css);
	return $css;
}

// ------------------------------
// Filter Schedule Override Views
// ------------------------------
add_filter( 'radio_station_schedule_override', 'radio_station_pro_schedule_multiviews', 11, 2 );
function radio_station_pro_schedule_multiviews( $output, $atts ) {

	// echo '<span style="display:none;">Filter Output: ' . print_r( $output, true ) . '</span>';

	// --- check for multiple views ---
	if ( strstr( $atts['view'], ',' ) ) {

		// --- filter multiview shortcode attributes ---
		$shortcode_atts = apply_filters( 'radio_station_pro_multiview_atts', $atts );

		// --- get default view ---
		if ( isset( $atts['default_view'] ) ) {
			$default_view = $atts['default_view'];
		} else {
			$default_view = radio_station_get_setting( 'schedule_view' );
		}

		// --- get view options ---
		$views = explode( ',', $atts['view'] );
		foreach ( $views as $i => $view ) {
			$views[$i] = trim( $view );
		}

		// --- recheck default view ---
		if ( !in_array( $default_view, $views ) ) {
			$default_view = $views[0];
		}

		// --- check view selection cookie ---
		// if ( isset( $_COOKIE['schedule_view'] ) && in_array( $_COOKIE['schedule_view'], $views ) ) {
		// 	$default_view = $_COOKIE['schedule_view'];
		// }

		// --- loop view types and add to output ---
		$view_types = array( 'table', 'tabs', 'list', 'grid', 'calendar' );
		foreach ( $view_types as $view_type ) {

			if ( in_array( $view_type, $views ) ) {

				// --- allow for view-specific overrides ---
				// eg. tabs_display_day etc.
				$atts = $shortcode_atts;
				foreach ( $atts as $key => $value ) {
					if ( $view_type . '_' == substr( $key, 0, ( strlen( $view_type ) + 1 ) ) ) {
						$override_key = substr( $key, ( strlen( $view_type ) + 1 ), strlen( $key ) );
						$atts[$override_key] = $value;
						unset( $atts[$key] );
					}
				}

				// --- open schedule view wrapper ---
				$output .= '<div id="master-schedule-view-' . esc_attr( $view_type ) . '"';
				if ( $view_type != $default_view ) {
					$output .= ' style="display: none;"';
				}
				$output .= '>';

					// --- get schedule view output ---
					ob_start();
					$template = radio_station_get_template( 'file', 'master-schedule-' . $view_type . '.php' );
					require $template;
					$html = ob_get_contents();
					$html = apply_filters( 'master_schedule_' . $view_type . '_view', $html, $atts );
					ob_end_clean();
					$output .= $html;

				// --- close schedule view wrapper ---
				$output .= '</div>';
			}			
		}

		// --- enqueue view styles and scripts ---
		if ( in_array( 'table', $views ) ) {
			$js = radio_station_master_schedule_table_js();
			wp_add_inline_script( 'radio-station', $js );
		}
		if ( in_array( 'tabs', $views ) ) {
			$js = radio_station_master_schedule_tabs_js();
			wp_add_inline_script( 'radio-station', $js );
		}
		if ( in_array( 'grid', $views ) ) {
			radio_station_enqueue_style( 'views' );
			$js = radio_station_pro_master_schedule_grid_js();
			wp_add_inline_script( 'radio-station', $js );
		}
		if ( in_array( 'calendar', $views ) ) {
			radio_station_enqueue_style( 'views' );
			$js = radio_station_pro_master_schedule_calendar_js();
			wp_add_inline_script( 'radio-station', $js );
			
			// --- enqueue show slider script ---
			$js = radio_station_pro_shows_slider_script();
			wp_add_inline_script( 'radio-station', $js );			
		}

		// --- add output override marker ---
		$output .= '<!-- SCHEDULE OVERRIDE -->';
	}

	// echo '<span style="display:none;">Filtered Output: ' . print_r( $output, true ) . '</span>';

	return $output;
}

