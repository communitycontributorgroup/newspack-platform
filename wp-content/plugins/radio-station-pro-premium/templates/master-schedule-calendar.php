<?php
/**
 * Template for master schedule shortcode calendar style.
 */

$newline = PHP_EOL;

// --- get all the required info ---
$hours = radio_station_get_hours();
$now = radio_station_get_now();
$date = radio_station_get_time( 'date', $now );
$today =  radio_station_get_time( 'day', $now );

// --- check if start date is set ---
if ( isset( $atts['start_date'] ) && $atts['start_date'] ) {
	$start_date = $atts['start_date'];
	// --- force display of date and month ---
	$atts['display_date'] = ( !$atts['display_date'] ) ? '1' : $atts['display_date'];
	$atts['display_month'] = ( !$atts['display_month'] ) ? 'short' : $atts['display_month'];
} else {
	$start_date = $date;	
}
$start_time = radio_station_to_time( $start_date . ' 00:00:00' );
$start_time = apply_filters( 'radio_station_schedule_start_time', $start_time, 'calendar' );

// --- set shift time formats ---
if ( 24 == (int) $atts['time'] ) {
	$start_data_format = $end_data_format = 'H:i';
} else {
	$start_data_format = $end_data_format = 'g:i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'schedule-calendar', $atts );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'schedule-calendar', $atts );

// --- get schedule start day ---
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$start_day = $atts['start_day'];
} else {
	$start_day = apply_filters( 'radio_station_schedule_start_day', false, 'calendar' );
}
$weekdays = radio_station_get_schedule_weekdays( $start_day );

// --- filter show avatar size ---
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail', 'calendar' );

// --- filter excerpt length and more ---
if ( $atts['show_desc'] ) {
	$length = apply_filters( 'radio_station_schedule_tabs_excerpt_length', false );
	$more = apply_filters( 'radio_station_schedule_tabs_excerpt_more', '[&hellip;]' );
}

// --- filter arrows ---
$arrows = array( 'left' => '&#8249;', 'right' => '&#8250;', 'doubleleft' => '&#171;', 'doubleright' => '&#187;' );
$arrows = apply_filters( 'radio_station_schedule_arrows', $arrows, 'calendar' );

// --- set cell info key order ---
$infokeys = array( 'avatar', 'title', 'hosts', 'times', 'encore', 'file', 'genres', 'custom' );
$infokeys = apply_filters( 'radio_station_schedule_calendar_info_order', $infokeys );

// --- start calendar schedule output ---
$calendar = '<table id="master-schedule-calendar" cellspacing="0" cellpadding="0">' . $newline;
$tcount = 0;

// --- open calendar days row ---
$calendar .= '<tr id="master-schedule-calendar-days-row">' . $newline;

// ---  previous week arrow loader ---
$title = __( 'Load Schedule for Previous Week', 'radio-station' );
$calendar .= '<td id="master-schedule-calendar-arrow-cell-left" class="master-schedule-calendar-arrow-cell" onclick="radio_load_schedule(\'previous\',\'calendar\',false);">' . $newline;
$calendar .= '<div id="master-schedule-calendar-arrow-left" class="master-schedule-calendar-arrow" title="' . esc_attr( $title ) . '">' . $arrows['doubleleft'] . '</div>' . $newline;
$calendar .= '</td>' . $newline;

// --- loop week days for headings ---
foreach ( $weekdays as $i => $weekday ) {

	// --- maybe skip all days but those specified ---
	$skip_day = false;
	if ( $atts['days'] ) {
		$days = explode( ',', $atts['days'] );
		$found_day = false;
		foreach ( $days as $day ) {
			$day = trim( $day );
			if ( is_numeric( $day ) && ( $day > -1 ) && ( $day < 7 ) ) {
				$day = radio_station_get_weekday( $day );
			}
			if ( trim( strtolower( $day ) ) == strtolower( $weekday ) ) {
				$found_day = true;
			}
		}
		if ( !$found_day ) {
			$skip_day = true;
		}
	}

	if ( !$skip_day ) {

		// --- day display heading cell ---
		if ( !in_array( $atts['display_day'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_day'] = 'long';
		}
		if ( 'short' == $atts['display_day'] ) {
			$display_day = radio_station_translate_weekday( $weekday, true );
		} elseif ( ( 'full' == $atts['display_day'] ) || ( 'long' == $atts['display_day'] ) ) {
			$display_day = radio_station_translate_weekday( $weekday, false );
		}

		$calendar .= '<td class="master-schedule-calendar-day-cell">' . $newline;

			$calendar .= '<div class="master-schedule-calendar-day-name">';
			$calendar .= esc_html( $display_day );
			$calendar .= '</div>' . $newline;

		$calendar .= '</td>' . $newline;

	}
}
// --- next week arrow loader ---
$title = __( 'Load Schedule for Next Week', 'radio-station' );
$calendar .= '<td id="master-schedule-calendar-arrow-cell-right" class="master-schedule-calendar-arrow-cell" onclick="radio_load_schedule(\'next\',\'calendar\',false);">';
$calendar .= '<div id="master-schedule-calendar-arrow-right" class="master-schedule-calendar-arrow" title="' . esc_attr( $title ) . '">' . $arrows['doubleright'] . '</div>';
$calendar .= '</td>' . $newline;

// --- close days row ---
$calendar .= '</tr>' . $newline;

// --- loop week months ---
$weeks = 4;
if ( isset( $atts['weeks'] ) ) {
	if ( absint( $atts['weeks'] ) > -1 ) {
		$weeks = absint( $atts['weeks'] );
	}
}
if ( !isset( $atts['previous_weeks'] ) || ( absint( $atts['previous_weeks'] ) > ( $weeks - 1 ) ) ) {
	$atts['previous_weeks'] = 1;
}
for ( $week = 0; $week < $weeks; $week++ ) {

	// --- get schedule for week ---
	$offset = $week - absint( $atts['previous_weeks'] );
	$week_start_time = $start_time + ( $offset * ( 7 * 24 * 60 * 60 ) );
	$weekdates = radio_station_get_schedule_weekdates( $weekdays, $week_start_time );
	if ( $start_day ) {
		$schedule = radio_station_get_current_schedule( $week_start_time, $start_day );
	} elseif ( $week_start_time != $now ) {
		$schedule = radio_station_get_current_schedule( $week_start_time );
	} else {
		$schedule = radio_station_get_current_schedule();
	}
	
	// --- debug week times ---
	// echo 'Start Time: ' . $start_time . ': ' . date( 'Y-m-d', $start_time ) . ' - Week Offset: ' .  $offset . '<br>';
	// echo 'Week Start Time: ' . $week_start_time . ': ' . date( 'Y-m-d', $week_start_time ) . '<br>';
	// echo 'Week ' . $week . ' Dates: ' . print_r( $weekdates, true ) . '<br>';

	$calendar .= '<tr id="master-schedule-calendar-week-row-' . esc_attr( $week ) . '" class="master-schedule-calendar-week-row">' . $newline;

	// --- month display cell (left) ---
	$date_time = radio_station_to_time( $weekdates[$weekdays[0]] );
	$month = radio_station_get_time( 'F', $date_time );
	if ( $atts['display_month'] && !in_array( $atts['display_month'], array( 'short', 'full', 'long' ) ) ) {
		$atts['display_month'] = 'short';
	}
	if ( ( 'long' == $atts['display_month'] ) || ( 'full' == $atts['display_month'] ) ) {
		$display_month = radio_station_translate_month( $month, false );
	} elseif ( 'short' == $atts['display_month'] ) {
	 	$display_month = radio_station_translate_month( $month, true );
	}
	$calendar .= '<td class="master-schedule-calendar-month">';
	$calendar .= '<div class="master-schedule-calendar-month-name left-side">';
	$calendar .= esc_html( $display_month );
	$calendar .= '</div></td>';

	// --- reset week loop defaults ---
	$shows = $slider = '';
	$i = 0;
	$oddeven = 'even';
	$first_day = $current_week = false;

	// --- loop the dates in this week ---
	foreach ( $weekdates as $weekday => $weekdate ) {

		// --- maybe skip all days but those specified ---
		$skip_day = false;
		if ( $atts['days'] ) {
			$days = explode( ',', $atts['days'] );
			$found_day = false;
			foreach ( $days as $day ) {
				$day = trim( $day );
				if ( is_numeric( $day ) && ( $day > -1 ) && ( $day < 7 ) ) {
					$day = radio_station_get_weekday( $day );
				}
				if ( trim( strtolower( $day ) ) == strtolower( $weekday ) ) {
					$found_day = true;
				}
			}
			if ( !$found_day ) {
				$skip_day = true;
			}
		}

		if ( !$skip_day ) {

			// --- get shifts for this day ---
			if ( isset( $schedule[$weekday] ) ) {
				$shifts = $schedule[$weekday];
			} else {
				$shifts = array();
			}

			// --- get next and previous days ---
			$nextday = radio_station_get_next_day( $weekday );
			$prevday = radio_station_get_previous_day( $weekday );

			$day_start_time = radio_station_to_time( $weekdate . ' 00:00' );
			$day_end_time = $day_start_time + ( 24 * 60 * 60 );

			if ( $atts['display_date'] ) {
				if ( '1' == $atts['display_date'] ) {
					$display_date = radio_station_get_time( 'jS', $day_start_time );
				} else {
					$display_date = radio_station_get_time( $atts['display_date'], $day_start_time );
				}
			} else {
				$display_date = radio_station_get_time( 'j', $day_start_time );
			}

			// --- get genre terms for shows ---
			$date_terms = array();
			if ( count( $shifts ) > 0 ) {
				foreach ( $shifts as $shift ) {
					$show_id = $shift['show']['id'];
					if ( !isset( $genre_terms[$show_id] ) ) {
						$terms = wp_get_post_terms( $show_id, RADIO_STATION_GENRES_SLUG, array() );
						$genre_terms[$show_id] = $terms;
					} else {
						$terms = $genre_terms[$show_id];
					}
					foreach ( $terms as $term ) {
						$date_terms[] = strtolower( $term->slug );
					}
				}
			}

			// --- set date cell classes ---
			$weekdate = $weekdates[$weekday];
			$classes = array( 'master-schedule-calendar-header' );
			$classes[] = 'weekday-' . $i;
			$classes[] = strtolower( $weekday );
			if ( $weekdate == $date ) {
				$classes[] = 'current-date';
				$classes[] = 'active-date';
				$current_week = true;
			}
			if ( !$first_day ) {
				$first_day = $weekday;
				$classes[] = 'first-day';
			} else {
				$next_day = radio_station_get_next_day( $weekday );
				if ( $next_day == $first_day ) {
					$classes[] = 'last-day';
				}
			}
			if ( '01' == substr( $weekdate, -2, 2) ) {
				$classes[] = 'first-date';
			}
			$next_date = radio_station_get_next_date( $weekdate );
			if ( substr( $weekdate, 6, 2 ) != substr( $next_date, 6, 2 ) ) {
				$classes[] = 'last-date';
			}
			$oddeven = ( 'even' == $oddeven ) ? 'odd' : 'even';
			$classes[] = $oddeven . '-day';
			$classlist  = implode( ' ', $classes );

			// --- calendar date cell ---
			$calendar .= '<td id="master-schedule-calendar-header-' . esc_attr( $weekdate ) . '" class="' . esc_attr( $classlist ) . '">' . $newline;
			$calendar .= '<div class="master-schedule-calendar-headings">' . $newline;

			$month = radio_station_get_time( 'F', $day_start_time );
			$display_month = radio_station_translate_month( $month, false );
			$date_title = $display_date . ' ' . $display_month;
			$classes = array( 'master-schedule-calendar-date' );
			$classes = array_merge( $classes, $date_terms );
			$classlist = implode( ' ', $classes );
			$calendar .= '<div class="' . esc_attr( $classlist ) . '" title="' . esc_attr( $date_title ) . '">' . esc_html( $display_date ) . '</div>' . $newline;

			// --- add to schedule link ---
			$times = array( 'date' => $weekdate, 'day' => $weekday, 'hour' => 12, 'mins' => 0 );
			$add_link = apply_filters( 'radio_station_schedule_add_link', '', $times, 'calendar' );
			if ( '' != $add_link ) {
				$calendar .= $add_link;
			}
			
			$calendar .= '<span class="rs-time rs-start-time" data="' . esc_attr( $day_start_time ) . '"></span>' . $newline;
			$calendar .= '<span class="rs-time rs-end-time" data="' . esc_attr( $day_end_time ) . '"></span>' . $newline;

			if ( count( $shifts ) > 0 ) {

				$calendar .= '<div class="master-schedule-calendar-date-count">';
					$calendar .= '<div class="master-schedule-calendar-show-count">';
					$display_count = number_format_i18n( count( $shifts ) );
					$calendar .= $display_count;
					$calendar .= '</div>' . $newline;
					$calendar .= '<div class="master-schedule-calendar-show-label">';
					if ( 1 == count( $shifts ) ) {
						$calendar .= esc_html( __( 'Show', 'radio-station' ) );
					} else {
						$calendar .= esc_html( __( 'Shows', 'radio-station' ) );
					}
					$calendar .= '</div>';
				$calendar .= '</div>';

				$classes = array( 'master-schedule-calendar-shows-list' );
				if ( $weekdate == $date ) {
					$classes[] = 'current-date';
					$classes[] = 'active-date';
				}
				$classlist = implode( ' ', $classes );
				$shows .= '<ul id="master-schedule-calendar-shows-list-' . esc_attr( $weekdate ) . '" class="' . esc_attr( $classlist ) . '">' . $newline;

				$slides = array();
				$j = 0;
				foreach ( $shifts as $shift ) {

					$j++;
					$show = $shift['show'];
					$show_id = $show['id'];
					$info = array();
					$split_id = false;
					$slide = '';

					// --- show page link ---
					$show_link = false;			
					if ( $atts['show_link'] ) {
						$show_link = $show['url'];
					}
					$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show_id, 'calendar' );

					// --- convert shift time data ---
					if ( '00:00 am' == $shift['start'] ) {
						$shift_start_time = radio_station_to_time( $weekdate . ' 00:00' );
					} else {
						$shift_start = radio_station_convert_shift_time( $shift['start'] );
						$shift_start_time = radio_station_to_time( $weekdate . ' ' . $shift_start );
					}
					if ( ( '11:59:59 pm' == $shift['end'] ) || ( '12:00 am' == $shift['end'] ) ) {
						$shift_end_time = radio_station_to_time( $weekdate . ' 23:59:59' ) + 1;
					} else {
						$shift_end = radio_station_convert_shift_time( $shift['end'] );
						$shift_end_time = radio_station_to_time( $weekdate . ' ' . $shift_end );
					}

					// --- get split shift real start and end times ---
					$real_shift_start = $real_shift_end = false;
					if ( isset( $shift['split'] ) && $shift['split'] ) {
						if ( isset( $shift['real_start'] ) ) {
							$real_shift_start = radio_station_convert_shift_time( $shift['real_start'] );
							$real_shift_start = radio_station_to_time( $weekdate . ' ' . $real_shift_start ) - ( 24 * 60 * 60 );
							$split_id = strtolower( $prevday . '-' . $weekday );
						} elseif ( isset( $shift['real_end'] ) ) {
							$real_shift_end = radio_station_convert_shift_time( $shift['real_end'] );
							$real_shift_end = radio_station_to_time( $weekdate . ' ' . $real_shift_end ) + ( 24 * 60 * 60 );
							$split_id = strtolower( $weekday . '-' . $nextday );
						}
					}

					// --- shift debug ---
					if ( isset( $_GET['rs-shift-debug'] ) && ( '1' == $_GET['rs-shift-debug'] ) ) {
						if ( !isset( $shiftdebug ) ) {$shiftdebug = '';}
						$shiftdebug .= 'Now: ' . $now . ' (' . radio_station_get_time( 'datetime', $now ) . ') -- Today: ' . $today . '<br>';
						$shiftdebug .= 'Shift Start: ' . $shift_start . ' (' . date( 'Y-m-d l H: i', $shift_start ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $shift_start ) . ')' . '<br>';
						$shiftdebug .= 'Shift End: ' . $shift_end . ' (' . date( 'Y-m-d l H: i', $shift_end ) . ' - ' . radio_station_get_time( 'Y-m-d l H:i', $shift_end ) . ')' . '<br>';
					}

					$classes = array( 'master-schedule-calendar-show' );
					$terms = $genre_terms[$show_id];
					if ( $terms && ( count( $terms ) > 0 ) ) {
						foreach ( $terms as $term ) {
							$classes[] = strtolower( $term->slug );
						}
					}
					if ( 1 == $j ) {
						$classes[] = 'first-show';
					}
					if ( $j == count( $shifts ) ) {
						$classes[] = 'last-show';
					}
					// --- check for now playing shift ---
					if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
						$classes[] = 'nowplaying';
						$classes[] = 'active-show';
					}
					// --- add overnight split ID for highlighting ---
					if ( $split_id ) {
						$classes[] = 'overnight';
						$classes[] = 'split-' . $split_id;
					}

					// --- open list item ---
					$showclasslist = implode( ' ' , $classes );
					$shows .= '<li class="' . esc_attr( $showclasslist ) . '">' . $newline;

					// --- Show Image ---
					// (defaults to display on)
					$avatar = '';
					if ( $atts['show_image'] ) {

						// --- get show avatar image ---
						$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size );
						$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show_id, 'calendar' );

						// --- set show image classes ---
						if ( $show_avatar ) {
							$avatar = '<div class="show-image">' . $newline;
							if ( $show_link ) {
								$avatar .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>' . $newline;
							} else {
								$avatar .= $show_avatar;
							}
							$avatar .= '</div>' . $newline;
						} else {
							$avatar = '<div class="show-image no-show-image"></div>' . $newline;
						}
						$avatar = apply_filters( 'radio_station_schedule_show_avatar_display', $avatar, $show_id, 'calendar' );
					}
					$info['avatar'] = $avatar;

					// --- show title ---
					if ( $show_link ) {
						$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
					} else {
						$show_title = esc_html( $show['name'] );
					}
					$title = '<div class="show-title">' . $newline;
					$title .= $show_title . $newline;
					$title .= '</div>' . $newline;
					$title = apply_filters( 'radio_station_schedule_show_title_display', $title, $show_id, 'calendar' );
					if ( ( '' != $title ) && is_string( $title ) ) {
						$info['title'] = $title;
					}
					$edit_link = apply_filters( 'radio_station_show_edit_link', '', $show_id, $shift['id'], 'calendar' );
					if ( '' != $edit_link ) {
						if ( isset( $info['title'] ) ) {
							$info['title'] .= $edit_link;
						} else {
							$info['title'] = $edit_link;
						}
					}				

					// --- show hosts ---
					if ( $atts['show_hosts'] ) {

						$show_hosts = '';
						if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

							$count = 0;
							$host_count = count( $show['hosts'] );
							$show_hosts .= '<span class="show-dj-names-leader"> ';
							$show_hosts .= esc_html( __( 'with', 'radio-station' ) );
							$show_hosts .= ' </span>' . $newline;

							foreach ( $show['hosts'] as $host ) {
								$count ++;
								if ( $atts['link_hosts'] && !empty( $host['url'] ) ) {
									$show_hosts .= '<a href="' . esc_url( $host['url'] ) . '">' . esc_html( $host['name'] ) . '</a>' . $newline;
								} else {
									$show_hosts .= esc_html( $host['name'] );
								}

								if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
									 || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
									$show_hosts .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
								} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
									$show_hosts .= ', ';
								}
							}
						}

						$show_hosts = apply_filters( 'radio_station_schedule_show_hosts', $show_hosts, $show_id, 'calendar' );
						if ( $show_hosts ) {
							$hosts = '<div class="show-dj-names show-host-names">' . $newline;
							$hosts .= $show_hosts;
							$hosts .= '</div>' . $newline;
							$hosts = apply_filters( 'radio_station_schedule_show_hosts_display', $hosts, $show_id, 'calendar' );
							if ( ( '' != $hosts ) && is_string( $hosts ) ) {
								$info['hosts'] = $hosts;
							}
						}
					}

					// --- show times ---
					if ( $atts['show_times'] ) {

						// --- get start and end times ---
						if ( $real_shift_start ) {
							$start = radio_station_get_time( $start_data_format, $real_shift_start );
						} else {
							$start = radio_station_get_time( $start_data_format, $shift_start_time );
						}
						if ( $real_shift_end ) {
							$end = radio_station_get_time( $end_data_format, $real_shift_end );
						} else {
							$end = radio_station_get_time( $end_data_format, $shift_end_time );
						}
						$start = radio_station_translate_time( $start );
						$end = radio_station_translate_time( $end );

						$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . $newline;
						$show_time .= '<span class="rs-sep"> ' . esc_html( __( 'to', 'radio-station' ) ) . ' </span>' . $newline;
						$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . $newline;

					} else {

						$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '"></span>' . $newline;
						$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '"></span>' . $newline;

					}

					$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show_id, 'calendar', $shift, $tcount );
					$times = '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '"';
					// note: unlike other display filters this hides/shows times rather than string filtering
					$display = apply_filters( 'radio_station_schedule_show_times_display', true, $show_id, 'calendar', $shift );
					if ( !$display ) {
						$times .= ' style="display:none;"';
					}
					$times .= '>' . $show_time . '</div>' . $newline;
					$times .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '">' . $newline;
					$times .= '[<span class="rs-time rs-start-time"></span>' . $newline;
					$times .= '<span class="rs-sep"> ' . esc_html( __( 'to', 'radio-station' ) ) . ' </span>' . $newline;
					$times .= '<span class="rs-time rs-end-time"></span>]' . $newline;
					$times .= '</div>' . $newline;
					$info['times'] = $times;
					$tcount ++;

					// --- encore ---
					if ( $atts['show_encore'] ) {
						if ( isset( $shift['encore'] ) ) {
							$show_encore = $shift['encore'];
						} else {
							$show_encore = false;
						}
						$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show_id, 'calendar' );
						if ( 'on' == $show_encore ) {
							$encore = '<div class="show-encore">';
							$encore .= esc_html( __( 'encore airing', 'radio-station' ) );
							$encore .= '</div>' . $newline;
							$encore = apply_filters( 'radio_station_schedule_show_encore_display', $encore, $show_id, 'calendar' );
							if ( ( '' != $encore ) && is_string( $encore ) ) {
								$info['encore'] = $encore;
							}
						}
					}

					// --- show audio file ---
					if ( $atts['show_file'] ) {
						$show_file = get_post_meta( $show_id, 'show_file', true );
						$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show_id, 'calendar' );
						$disable_download = get_post_meta( $show_id, 'show_download', true );
						if ( $show_file && !empty( $show_file ) && !$disable_download ) {
							$anchor = __( 'Audio File', 'radio-station' );
							$anchor = apply_filters( 'radio_station_schedule_show_file_anchor', $anchor, $show_id, 'calendar' );
							$file = '<div class="show-file">' . $newline;
							$file .= '<a href="' . esc_url( $show_file ) . '">';
							$file .= esc_html( $anchor );
							$file .= '</a>' . $newline;
							$file .= '</div>' . $newline;
							$file = apply_filters( 'radio_station_schedule_show_file_display', $file, $show_file, $show_id, 'calendar' );
							if ( ( '' != $file ) && is_string( $file ) ) {
								$info['file'] = $file;
							}
						}
					}

					// --- show genres ---
					if ( $atts['show_genres'] ) {
						$genres = '<div class="show-genres">' . $newline;
						$show_genres = array();
						if ( count( $terms ) > 0 ) {
							$genres .= esc_html( __( 'Genres', 'radio-station' ) ) . ': ';
							foreach ( $terms as $term ) {
								$show_genres[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>' . $newline;
							}
							$genres .= implode( ', ', $show_genres );
						}
						$genres .= '</div>' . $newline;
						$genres = apply_filters( 'radio_station_schedule_show_genres', $genres, $show_id, 'calendar' );
						if ( ( '' != $genres ) && is_string( $genres ) ) {
							$info['genres'] = $genres;
						}
					}

					// --- custom info section ---
					$custom = apply_filters( 'radio_station_schedule_show_custom_display', '', $show_id, 'calendar' );
					if ( ( '' != $custom ) && is_string( $custom ) ) {
						$info['custom'] = $custom;
					}

					// --- Show Information ---
					$classes = array( 'show-info' );
					if ( $atts['show_desc'] ) {
						$classes[] = 'has-show-desc';
					}
					$classlist = implode( ' ', $classes );
					$shows .= '<div class="' . esc_attr( $classlist ) . '">' . $newline;

					// --- add item info according to key order ---
					foreach ( $infokeys as $infokey ) {
						if ( isset( $info[$infokey] ) ) {
							$shows .= $info[$infokey];
						}
					}

					// --- close show info section ---
					$shows .= '</div>' . $newline;

					// --- show description ---
					// TODO: alternative position for show description
					/* if ( $atts['show_desc'] ) {

						$show_post = get_post( $show_id );
						$permalink = get_permalink( $show_id );

						// --- get show excerpt ---
						if ( !empty( $show_post->post_excerpt ) ) {
							$show_excerpt = $show_post->post_excerpt;
							$show_excerpt .= ' <a href="' . esc_url( $permalink ) . '">' . $more . '</a>';
						} else {
							$show_excerpt = radio_station_trim_excerpt( $show_post->post_content, $length, $more, $permalink );
						}

						// --- filter excerpt by context ---
						$show_excerpt = apply_filters( 'radio_station_schedule_show_excerpt', $show_excerpt, $show_id, 'calendar' );

						// --- output excerpt ---
						$excerpt = '<div class="show-desc">' . $newline;
						$excerpt .= $show_excerpt . $newline;
						$excerpt .= '</div>' . $newline;
						$excerpt = apply_filters( 'radio_station_schedule_show_excerpt_display', $excerpt, $show_id, 'calendar' );
						if ( ( '' != $excerpt ) && is_string( $excerpt ) ) {
							$shows .= $excerpt;
						}

					} */

					// --- add item info to slides ---
					foreach ( $infokeys as $infokey ) {
						if ( isset( $info[$infokey] ) ) {
							$slide .= $info[$infokey];
						}
					}
					$slide = '<div class="shows-slider-item" class="' . esc_attr( $classlist ) . '">' . $slide . '</div>';
					$slides[] = $slide;

					$shows .= '</li>';

				}

			} else {

				// --- empty div for no shows ---
				$calendar .= '<div class="master-schedule-calendar-show-count">';
				// $calendar .= count( $shifts ) . ' ' . esc_html( __( 'No Shows', 'radio-station' ) );
				$calendar .= '</div>' . $newline;

				// TODO: show or not show this message ?
				// $shows .= '<li class="master-schedule-calendar-show master-schedule-calendar-no-shows">' . $newline;
				// $shows .= esc_html( __( 'No Shows scheduled for this date.', 'radio-station' ) );
				// $shows .= '</li>' . $newline;
			}

			$calendar .= '<div class="master-schedule-calendar-date-bottom"></div>' . $newline;
			$calendar .= '</div></td>' . $newline;
			$shows .= '</ul>' . $newline;

			// --- set slider wrapper classes ---			
			$classes = array( 'master-schedule-calendar-shows-slider' );
			if ( $weekdate == $date ) {
				$classes[] = 'current-date';
				$classes[] = 'active-date';
			}
			$classlist = implode( ' ', $classes );
		
			// --- set slider output for day ---
			$slider .= '<div id="master-schedule-calendar-shows-slider-' . esc_attr( $weekdate ) . '" class="' . esc_attr( $classlist ) . '">';
				$slider .= '<div id="shows-slider-' . esc_attr( $weekdate ) . '" class="shows-slider">';
					$slider .= '<input type="hidden" class="shows-last-slid" value="-1">';
					$slider .= '<div class="shows-slider-wrapper">';
						$onclick = "radio_slide_left('shows', '" . esc_attr( $weekdate ) . "', true); radio_slider_responsive('shows');";
						$slider .= '<div class="shows-slider-arrow-wrapper shows-slider-arrow-left inactive" onclick="' . $onclick  . '">';
							$slider .= '<div class="shows-slider-arrow">' . $arrows['left'] . '</div>';
						$slider .= '</div>';
						$slider .= '<div class="shows-slider-mask">';
							$slider .= '<div class="shows-slider-items">';
							foreach ( $slides as $slide ) {								
								$slider .= $slide;
							}
							$slider .= '</div>';
						$slider .= '</div>';
						$onclick = "radio_slide_right('shows', '" . esc_attr( $weekdate ) . "', true); radio_slider_responsive('shows');";
						$slider .= '<div class="shows-slider-arrow-wrapper shows-slider-arrow-right" onclick="' . $onclick . '">';
							$slider .= '<div class="shows-slider-arrow">' . $arrows['right'] . '</div>';
						$slider .= '</div>';
					$slider .= '</div>';
				$slider .= '</div>';
			$slider .= '</div>';
		}

		// --- increment day count ---
		$i++;		
	}

	// --- month display cell (right) ---
	$date_time = radio_station_to_time( $weekdates[$weekdays[6]] );
	$month = radio_station_get_time( 'F', $date_time );
	if ( $atts['display_month'] && !in_array( $atts['display_month'], array( 'short', 'full', 'long' ) ) ) {
		$atts['display_month'] = 'short';
	}
	if ( ( 'long' == $atts['display_month'] ) || ( 'full' == $atts['display_month'] ) ) {
		$display_month = radio_station_translate_month( $month, false );
	} elseif ( 'short' == $atts['display_month'] ) {
	 	$display_month = radio_station_translate_month( $month, true );
	}
	$calendar .= '<td class="master-schedule-calendar-month">';
	$calendar .= '<div class="master-schedule-calendar-month-name right-side">';
	$calendar .= esc_html( $display_month );
	$calendar .= '</div></td>';
	
	// --- close calendar row ---
	$calendar .= '</tr>' . $newline;

	// --- set classes for show list row ---
	$classes = array( 'master-schedule-calendar-shows' );
	if ( $current_week ) {
		$classes[] = 'current-week';
		$classes[] = 'active-week';
	}
	$classlist = implode( ' ', $classes );

	// --- add shows list row for week ---
	$calendar .= '<tr id="master-schedule-calendar-shows-' . esc_attr( $week ) . '" class="' . esc_attr( $classlist ) . '">' . $newline;
	$calendar .= '<td class="master-schedule-calendar-shows-cell" colspan="9">' . $newline;
	$calendar .= $shows;
	$calendar .= $slider;	
	$calendar .= '</td>' . $newline;
	$calendar .= '</tr>' . $newline;

}

// --- extra row to complete bottom border ----
$calendar .= '<tr><td style="padding:0;"></td>';
$calendar .= '<td colspan="7" class="master-schedule-calendar-header">' . $newline;
$calendar .= '<div class="master-schedule-calendar-headings" style="padding:0;"></div>' . $newline;
$calendar .= '</td><td style="padding:0;"></td></tr>' . $newline;

$calendar .= '</table>' . $newline;

// --- hidden iframe for schedule reloading ---
$calendar .= '<iframe src="javascript:void(0);" id="schedule-calendar-loader" name="schedule-calendar-loader" style="display:none;"></iframe>' . $newline;

if ( isset( $_GET['rs-shift-debug'] ) && ( '1' == $_GET['rs-shift-debug'] ) ) {
	$calendar .= '<br><b>Shift Debug Info:</b><br>' . $shiftdebug . '<br>';
}

echo $calendar;