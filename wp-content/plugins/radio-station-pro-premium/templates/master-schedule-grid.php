<?php

/**
 * Template for master schedule shortcode grid style.
 */

// --- get all the required info ---
$hours = radio_station_get_hours();
$now = radio_station_get_now();
$date = radio_station_get_time( 'date', $now );
$today = radio_station_get_time( 'day', $now );

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
$start_time = apply_filters( 'radio_station_schedule_start_time', $start_time, 'grid' );

// --- set shift time formats ---
// 2.4.0.6: add filter for shift times separator
$shifts_separator = __( 'to', 'radio-station' );
$shifts_separator = apply_filters( 'radio_station_schedule_show_time_separator', $shifts_separator, 'schedule-grid' );
$time_separator = ':';
$time_separator = apply_filters( 'radio_station_time_separator', $time_separator, 'schedule-grid' );
if ( 24 == (int) $atts['time'] ) {
	$start_data_format = $end_data_format = 'H' . $time_separator . 'i';
} else {
	$start_data_format = $end_data_format = 'g' . $time_separator . 'i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'schedule-grid', $atts );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'schedule-grid', $atts );

// --- get schedule days and dates ---
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$start_day = $atts['start_day'];
} else {
	$start_day = apply_filters( 'radio_station_schedule_start_day', false, 'grid' );
}
if ( $start_day ) {
	$schedule = radio_station_get_current_schedule( $start_time, $start_day );
} elseif ( $start_time != $now ) {
	$schedule = radio_station_get_current_schedule( $start_time );
} else {
	$schedule = radio_station_get_current_schedule();
}
$weekdays = radio_station_get_schedule_weekdays( $start_day );
$weekdates = radio_station_get_schedule_weekdates( $weekdays, $start_time );

// --- filter show avatar size ---
$avatar_size = ( $atts['gridwidth'] > 150 ) ? 'medium' : 'thumbnail';	
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', $avatar_size, 'grid' );

// --- filter arrows ---
$arrows = array( 'left' => '&#8249;', 'right' => '&#8250;', 'doubleleft' => '&#171;', 'doubleright' => '&#187;' );
$arrows = apply_filters( 'radio_station_schedule_arrows', $arrows, 'grid' );

// --- set cell info key order ---
$infokeys = array( 'avatar', 'title', 'hosts', 'times', 'encore', 'file', 'genres', 'custom' );
$infokeys = apply_filters( 'radio_station_schedule_grid_info_order', $infokeys );

// --- start grid schedule output ---
$grid = '';
$tcount = 0;
$first_day = $start_grid = false;
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

		$nextday = radio_station_get_next_day( $weekday );
		$prevday = radio_station_get_previous_day( $weekday );

		$day_start_time = radio_station_to_time( $weekdates[$weekday] . ' 00:00' );
		$day_end_time = $day_start_time + ( 24 * 60 * 60 );

		if ( !in_array( $atts['display_day'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_day'] = 'long';
		}
		if ( 'short' == $atts['display_day'] ) {
			$display_day = radio_station_translate_weekday( $weekday, true );
		} elseif ( ( 'full' == $atts['display_day'] ) || ( 'long' == $atts['display_day'] ) ) {
			$display_day = radio_station_translate_weekday( $weekday, false );
		}

		if ( $atts['display_date'] ) {
			if ( '1' == $atts['display_date'] ) {
				$date_subheading = radio_station_get_time( 'jS', $day_start_time );
			} else {
				$date_subheading = radio_station_get_time( $atts['display_date'], $day_start_time );
			}
		} else {
			$date_subheading = radio_station_get_time( 'j', $day_start_time );
		}

		$month = radio_station_get_time( 'F', $day_start_time );
		if ( $atts['display_month'] && !in_array( $atts['display_month'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_month'] = 'short';
		}
		if ( ( 'long' == $atts['display_month'] ) || ( 'full' == $atts['display_month'] ) ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, false );
		} elseif ( 'short' == $atts['display_month'] ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, true );
		}

		// --- set day grid classes ---
		$weekdate = $weekdates[$weekday];
		$classes = array( 'master-schedule-grid-day', 'day-' . $i );
		if ( !$start_grid ) {
			$classes[] = 'start-day';
			$start_grid = true;
		}
		if ( $weekdate == $date ) {
			$classes[] = 'current-day';
			$classes[] = 'active-day';
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
		$classlist  = implode( ' ', $classes );

		// --- start day grid list ---
		$grid .= '<ul id="master-schedule-grid-day-' . esc_attr( strtolower( $weekday ) ) . '" class="' . esc_attr( $classlist ) . '">' . $newline;

		// --- day grid date header ---
		$grid .= '<li id="master-schedule-grid-header-' . strtolower( $weekday ) . '" class="master-schedule-grid-header">' . $newline;
		$grid .= '<div class="grid-shift-arrow grid-shift-arrow-left">' . $newline;
		$grid .= '<a href="javacript:void(0);" onclick="return radio_shift_grid(\'left\');" title="' . esc_attr( __( 'Previous Day', 'radio-station' ) ) . '">' . $arrows['left'] . '</a>' . $newline;
		$grid .= '</div>' . $newline;

		$grid .= '<div class="master-schedule-grid-headings">' . $newline;
		$grid .= '<div class="master-schedule-grid-day-name"';
		if ( !$atts['display_date'] ) {
			$tabs .= ' title="' . esc_attr( $date_subheading ) . '"';
		}
		$grid .= '>' . esc_html( $display_day ) . '</div>' . $newline;
		if ( $atts['display_date'] ) {
			$grid .= '<div class="master-schedule-grid-date">' . esc_html( $date_subheading ) . '</div>' . $newline;
		}
		$grid .= '</div>' . $newline;

		$grid .= '<div class="grid-shift-arrow grid-shift-arrow-right">' . $newline;
		$grid .= '<a href="javacript:void(0);" onclick="return radio_shift_grid(\'right\');" title="' . esc_attr( __( 'Next Day', 'radio-station' ) ) . '">' . $arrows['right'] . '</a>' . $newline;
		$grid .= '</div>' . $newline;

		$grid .= '<span class="rs-time rs-start-time" data="' . esc_attr( $day_start_time ) . '"></span>' . $newline;
		$grid .= '<span class="rs-time rs-end-time" data="' . esc_attr( $day_end_time ) . '"></span>' . $newline;
		$grid .= '</li>';

		$display_day = radio_station_translate_weekday( $weekday, false );
		// $grid .= '<li class="master-schedule-grid-selected" id="master-schedule-grid-selected-' . esc_attr( $i ) . '">' . $newline;
		// $grid .= __( 'Viewing', 'radio-station' ) . ': ' . esc_html( $display_day ) . '</li>';

		// --- get shifts for this day ---
		if ( isset( $schedule[$weekday] ) ) {
			$shifts = $schedule[$weekday];
		} else {
			$shifts = array();
		}

		$foundshows = false;

		// 2.3.0: loop schedule day shifts instead of hours and minutes
		if ( count( $shifts ) > 0 ) {

			$foundshows = true;
			$j = 0;
			unset( $prev_shift );
			
			foreach ( $shifts as $index => $shift ) {

				$j++;
				$show = $shift['show'];
				$info = array();
				$split_id = false;

				$show_link = false;
				$show_id = $show['id'];
				if ( $atts['show_link'] ) {
					$show_link = $show['url'];
				}
				$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show_id, 'grid' );

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

				// 2.4.1.6: add spacer times to grid
				if ( isset( $prev_shift ) ) {
					$prev_shift_start = radio_station_convert_shift_time( $prev_shift['start'] );
					$prev_shift_start_time = radio_station_to_time( $weekdate . ' ' . $prev_shift_start );
					if ( ( '11:59:59 pm' == $prev_shift['end'] ) || ( '12:00 am' == $prev_shift['end'] ) ) {
						$prev_shift_end_time = radio_station_to_time( $weekdate . ' 23:59:59' ) + 1;
					} else {
						$prev_shift_end = radio_station_convert_shift_time( $prev_shift['end'] );
						$prev_shift_end_time = radio_station_to_time( $weekdate . ' ' . $prev_shift_end );
					}
					if ( isset( $_GET['shift-debug'] ) && ( '1' == $_GET['shift-debug'] ) ) {
						echo "Prev Shift: " . print_r( $prev_shift, true );
						echo "Prev Shift End Time: " . $prev_shift_end_time;
						echo "Shift Start Time: " . $shift_start_time;
					}
					if ( $prev_shift_end_time != $shift_start_time ) {
						$difference = $shift_start_time - $prev_shift_end_time;
						$grid .= '<li class="master-schedule-grid-show master-schedule-grid-spacer">';
						$grid .= '<span class="start-time" data="' . esc_attr( $prev_shift_end_time ) . '"></span>';
						$grid .= '<span class="end-time" data="' . esc_attr( $shift_start_time ) . '"></span>';
						$grid .= '</li>' . $newline;
					}		
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

				$classes = array( 'master-schedule-grid-show' );
				$terms = wp_get_post_terms( $show_id, RADIO_STATION_GENRES_SLUG, array() );
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
				if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$classes[] = 'nowplaying';
				}
				if ( $split_id ) {
					$classes[] = 'overnight';
					$classes[] = 'split-' . $split_id;
				}

				// --- open list item ---
				$classlist = implode( ' ' , $classes );
				$grid .= '<li class="' . esc_attr( $classlist ) . '">' . $newline;

				// --- Show Image ---
				// (defaults to display on)
				$avatar = '';
				if ( $atts['show_image'] ) {

					// --- get show avatar image ---
					$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size );
					$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show_id, 'grid' );

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
					$avatar = apply_filters( 'radio_station_schedule_show_avatar_display', $avatar, $show_id, 'grid' );
					$info['avatar'] = $avatar;
				}

				// --- show title ---
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>';
				} else {
					$show_title = esc_html( $show['name'] );
				}
				$title = '<div class="show-title">' . $newline;
				$title .= $show_title . $newline;
				$title .= '</div>' . $newline;
				$title = apply_filters( 'radio_station_schedule_show_title_display', $title, $show_id, 'grid' );
				if ( ( '' != $title ) && is_string( $title ) ) {
					$info['title'] = $title;
				}
				// 2.3.3.9: allow for admin edit link
				$edit_link = apply_filters( 'radio_station_show_edit_link', '', $show_id, $shift['id'], 'grid' );
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
							// 2.3.0: added link_hosts attribute check
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

					$show_hosts = apply_filters( 'radio_station_schedule_show_hosts', $show_hosts, $show_id, 'grid' );
					if ( $show_hosts ) {
						$hosts = '<div class="show-dj-names show-host-names">' . $newline;
						$hosts .= $show_hosts;
						$hosts .= '</div>' . $newline;
						$hosts = apply_filters( 'radio_station_schedule_show_hosts_display', $hosts, $show_id, 'grid' );
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
					$show_time .= '<span class="rs-sep"> ' . esc_html( $shifts_separator ) . ' </span>' . $newline;
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . $newline;

				} else {
					$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '"></span>' . $newline;
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '"></span>' . $newline;

				}

				// 2.3.3.9: added tcount argument to filter
				$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show_id, 'grid', $shift, $tcount );
				$times = '<div class="show-time" id="show-time-' . esc_attr( $tcount ) . '"';
				// note: unlike other display filters this hides/shows times rather than string filtering
				$display = apply_filters( 'radio_station_schedule_show_times_display', true, $show_id, 'grid', $shift );
				if ( !$display ) {
					$times .= ' style="display:none;"';
				}
				$times .= '>' . $show_time . '</div>' . $newline;

				// --- show user times ---
				// 2.4.1.8: use filtered shift times separator
				$times .= '<div class="show-user-time" id="show-user-time-' . esc_attr( $tcount ) . '">' . $newline;
				$times .= '[<span class="rs-time rs-start-time"></span>' . $newline;
				$times .= '<span class="rs-sep"> ' . esc_html( $shifts_separator ) . ' </span>' . $newline;
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
					$show_encore = apply_filters( 'radio_station_schedule_show_encore', $show_encore, $show_id, 'grid' );
					if ( 'on' == $show_encore ) {
						$encore = '<div class="show-encore">';
						$encore .= esc_html( __( 'encore airing', 'radio-station' ) );
						$encore .= '</div>' . $newline;
						$encore = apply_filters( 'radio_station_schedule_show_encore_display', $encore, $show_id, 'grid' );
						if ( ( '' != $encore ) && is_string( $encore ) ) {
							$info['encore'] = $encore;
						}
					}
				}

				// --- show audio file ---
				if ( $atts['show_file'] ) {
					$show_file = get_post_meta( $show_id, 'show_file', true );
					$show_file = apply_filters( 'radio_station_schedule_show_file', $show_file, $show_id, 'grid' );
					$disable_download = get_post_meta( $show_id, 'show_download', true );
					if ( $show_file && !empty( $show_file ) && !$disable_download ) {
						$anchor = __( 'Audio File', 'radio-station' );
						$anchor = apply_filters( 'radio_station_schedule_show_file_anchor', $anchor, $show_id, 'grid' );
						$file = '<div class="show-file">' . $newline;
						$file .= '<a href="' . esc_url( $show_file ) . '">';
						$file .= esc_html( $anchor );
						$file .= '</a>' . $newline;
						$file .= '</div>' . $newline;
						$file = apply_filters( 'radio_station_schedule_show_file_display', $file, $show_file, $show_id, 'grid' );
						if ( ( '' != $file ) && is_string( $file ) ) {
							$info['file'] = $file;
						}
					}
				}

				// --- show genres ---
				// (defaults to display on)
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
					$genres = apply_filters( 'radio_station_schedule_show_genres', $genres, $show_id, 'grid' );
					if ( ( '' != $genres ) && is_string( $genres ) ) {
						$info['genres'] = $genres;
					}
				}

				// --- custom info section ---
				$custom = apply_filters( 'radio_station_schedule_show_custom_display', '', $show_id, 'grid' );
				if ( ( '' != $custom ) && is_string( $custom ) ) {
					$info['custom'] = $custom;
				}

				// --- Show Information ---
				$classes = array( 'show-info', 'day-' . $i );
				$classlist = implode( ' ', $classes );
				$grid .= '<div class="' . esc_attr( $classlist ) . '">' . $newline;

				// --- add item info according to key order ---
				foreach ( $infokeys as $infokey ) {
					if ( isset( $info[$infokey] ) ) {
						$grid .= $info[$infokey];
					}
				}

				// --- close show info section ---
				$grid .= '</div>' . $newline;

				$grid .= '</li>' . $newline;

				// --- set previous shift ---
				// 2.4.1.6: track current shift as previous shift
				$prev_shift = $shift;

			}
		}

		if ( !$foundshows ) {
			$grid .= '<li class="master-schedule-grid-show master-schedule-grid-no-shows">' . $newline;
			$grid .= esc_html( __( 'No Shows scheduled for this day.', 'radio-station' ) );
			$grid .= '</li>' . $newline;
		}

		$grid .= '</ul>' . $newline;
	}

}

// --- set week loader ---
$loader = '<div class="master-schedule-grid-loader">' . $newline;
$loader .= apply_filters( 'radio_station_schedule_loader_control', '', 'grid', 'left' );
$loader .= apply_filters( 'radio_station_schedule_loader_control', '', 'grid', 'right' );
$loader .= '</div>' . $newline;

// --- add day grid to output ---
$html = '<div id="master-schedule-grid">' . $newline;
$html .= $loader;
$html .= $grid;
$html .= '</div>' . $newline;

// --- hidden iframe for schedule reloading ---
$html .= '<iframe src="javascript:void(0);" id="schedule-grid-loader" name="schedule-grid-loader" style="display:none;"></iframe>' . $newline;

// --- add grid width style ---
$html .= '<input type="hidden" id="master-schedule-grid-column-width" value="' . esc_attr( $atts['gridwidth'] ) . '">' . $newline;
$css = '#master-schedule-grid .master-schedule-grid-day {width: ' . esc_attr( $atts['gridwidth'] ) . 'px;}';
$css = apply_filters( 'radio_station_master_schedule_styles_grid', $css );
if ( '' != $css ) {
	$html .= '<style>' . $css . '</style>' . $newline;
}

// --- shift debug info ---
if ( isset( $_GET['rs-shift-debug'] ) && ( '1' == $_GET['rs-shift-debug'] ) ) {
	$html .= '<span style="display:none;"><b>Shift Debug Info:</b><br>' . $shiftdebug . '<br></span>';
}

// --- maybe enqueue time spaced grid ---
// 2.4.1.6: check for time spacing view option
if ( isset( $atts['time_spaced'] ) && $atts['time_spaced'] ) {
	add_filter( 'radio_station_pro_master_schedule_grid_js', 'radio_station_pro_schedule_grid_spacing_js' );
}

echo $html;

