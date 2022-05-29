<?php

// =========================
// === Radio Station Pro ===
// =========================
// ------- Data Feeds ------
// =========================

// === Data Functions ===
// - Get Show Episodes Data
// * Get Episodes Data
// * Get Hosts Data
// * Get Producers Data
// === Data Endpoints
// - Episodes Data Endpoint
// - Hosts Data Endpoint
// - Producers Data Endpoint
// === REST Routes ===
// - Register REST Routes
// - Add Route Links to Data
// - Show Episodes Route
// - Hosts Route
// - Producer Route
// === Data Feeds ===
// - Add Feeds
// - Add Feed Links to Data
// - Show Episodes Feed
// - Hosts Feed
// - Producer Feed


// ----------------------
// === Data Functions ===
// ----------------------

// ----------------------
// Get Show Episodes Data
// ----------------------
add_filter( 'radio_station_show_data_meta', 'radio_station_pro_get_show_episodes_data', 10, 2 );
function radio_station_pro_get_show_episodes_data( $show_data, $show_id ) {
	$show_data['episodes'] = radio_station_pro_get_show_episodes( $show_id );
	return $show_data;
}

// -----------------
// Get Episodes Data
// -----------------
// (radio_station_get_shows_data)
function radio_station_pro_get_episodes_data( $episode, $show ) {

	// TODO: get episodes data
	if ( $show ) {
		$episodes = radio_station_pro_get_show_episodes( $show );

	}

	return array();
}

// --------------
// Get Hosts Data
// --------------
function radio_station_pro_get_hosts_data( $host ) {
	// TODO: get hosts data


	return array();
}

// ------------------
// Get Producers Data
// ------------------
function radio_station_pro_get_producers_data( $producer ) {
	// TODO: get producers data


	return array();
}

// ----------------------
// === Data Endpoints ===
// ----------------------

// ----------------------
// Episodes Data Endpoint
// ----------------------
function radio_station_pro_episodes_endpoint() {

	// --- get episode and show query parameters ---
	$episode = $singular = $multiple = false;
	$show = $show_singular = $show_multiple = false;
	if ( isset( $_GET['episode'] ) ) {
		$episode = $_GET['episode'];
		if ( !strstr( $episode, ',' ) ) {
			$singular = true;
		} else {
			$multiple = true;
		}
	}
	if ( isset( $_GET['show'] ) ) {
		$show = $_GET['show'];
		if ( !strstr( $show, ',' ) ) {
			$show_singular = true;
		} else {
			$show_multiple = true;
		}
	}

	// --- get episode list data ---
	$episodes = radio_station_pro_get_episodes_data( $episode, $show );

	// --- maybe return error ---
	if ( 0 === count( $episodes ) ) {
		if ( $singular ) {
			if ( $show_singular ) {
				$code = 'episode_not_found';
				$message = 'Requested Episode for this Show was not found.';
			} elseif ( $show_multiple ) {
				$code = 'episodes_not_found';
				$message = 'Requested Episode for these Shows were found.';
			} else {
				$code = 'episode_not_found';
				$message = 'Requested Episode was not found.';
			}
		} elseif ( $multiple ) {
			if ( $show_singular ) {
				$code = 'episodes_not_found';
				$message = 'No Requested Episodes for this Show were found.';
			} elseif ( $show_multiple ) {
				$code = 'episodes_not_found';
				$message = 'No Requested Episodes for these Shows were found.';
			} else {
				$code = 'episodes_not_found';
				$message = 'No Requested Episodes were found.';
			}
		} else {
			$code = 'no_episodes';
			$message = 'No Episodes were found.';
		}
		$episodes = new WP_Error( $code, $message, array( 'status' => 400 ) );
	}

	// --- maybe output debug info ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		header( 'Content-Type: text/plain' );
		echo "Episode: " . $episode . PHP_EOL;
		echo "Show: " . $show . PHP_EOL;
		echo "Episodes: " . print_r( $episodes, true );
	}

	return $episodes;
}

// -------------------
// Show Hosts Endpoint
// -------------------
function radio_station_pro_hosts_endpoint() {

	// --- get host and show query parameters ---
	$host = $singular = $multiple = false;
	$show = $show_singular = $show_multiple = false;
	if ( isset( $_GET['host'] ) ) {
		$host = $_GET['host'];
		if ( !strstr( $host, ',' ) ) {
			$singular = true;
		} else {
			$multiple = true;
		}
	}
	if ( isset( $_GET['show'] ) ) {
		$show = $_GET['show'];
		if ( !strstr( $show, ',' ) ) {
			$show_singular = true;
		} else {
			$show_multiple = true;
		}
	}

	// --- get hosts data ---
	$hosts = radio_station_pro_get_hosts_data( $host, $show );

	// --- maybe return error ---
	if ( 0 === count( $hosts ) ) {
		if ( $singular ) {
			if ( $show_singular ) {
				$code = 'show_host_not_found';
				$message = 'Requested Host for this Show was not found.';
			} elseif ( $show_multiple ) {
				$code = 'shows_host_not_found';
				$message = 'Requested Host for these Shows were not found.';
			} else {
				$code = 'host_not_found';
				$message = 'Requested Host was not found.';
			}
		} elseif ( $multiple ) {
			if ( $show_singular ) {
				$code = 'show_hosts_not_found';
				$message = 'No Requested Hosts for this Show were found.';
			} elseif ( $show_multiple ) {
				$code = 'shows_hosts_not_found';
				$message = 'No Requested Hosts for these Shows were found.';
			} else {
				$code = 'hosts_not_found';
				$message = 'No Requested Hosts were found.';
			}
		} else {
			$code = 'no_hosts';
			$message = 'No Hosts were found.';
		}
		$hosts = new WP_Error( $code, $message, array( 'status' => 400 ) );
	}

	// --- maybe output debug info ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		header( 'Content-Type: text/plain' );
		echo "Host: " . $host . PHP_EOL;
		echo "Show: " . $show . PHP_EOL;
		echo "Hosts: " . print_r( $hosts, true ) . PHP_EOL;
	}

	return $hosts;
}

// -----------------------
// Show Producers Endpoint
// -----------------------
function radio_station_pro_producers_endpoint() {

	// --- get producer and show query parameters ---
	$producer = $singular = $multiple = false;
	$show = $show_singular = $show_multiple = false;
	if ( isset( $_GET['host'] ) ) {
		$producer = $_GET['host'];
		if ( !strstr( $producer, ',' ) ) {
			$singular = true;
		} else {
			$multiple = true;
		}
	}
	if ( isset( $_GET['show'] ) ) {
		$show = $_GET['show'];
		if ( !strstr( $show, ',' ) ) {
			$show_singular = true;
		} else {
			$show_multiple = true;
		}
	}

	// --- get show producers data ---
	$producers = radio_station_pro_get_producers_data( $producer, $show );

	// --- maybe return error ---
	if ( 0 === count( $producers ) ) {

		if ( $singular ) {
			if ( $show_singular ) {
				$code = 'show_producer_not_found';
				$message = 'Requested Producer for this Show was not found.';
			} elseif ( $show_multiple ) {
				$code = 'show_producer_not_found';
				$message = 'Requested Producer for these Shows were not found.';
			} else {
				$code = 'producer_not_found';
				$message = 'Requested Producer was not found.';
			}
		} elseif ( $multiple ) {
			if ( $show_singular ) {
				$code = 'show_producers_not_found';
				$message = 'No Requested Producers for this Show were found.';
			} elseif ( $show_multiple ) {
				$code = 'shows_producer_not_found';
				$message = 'No Requested Producers for these Shows were found.';
			} else {
				$code = 'producers_not_found';
				$message = 'No Requested Producers were found.';
			}
		} else {
			$code = 'no_producers';
			$message = 'No Producers were found.';
		}
		$producers = new WP_Error( $code, $message, array( 'status' => 400 ) );

	}

	// --- maybe output debug info ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		header( 'Content-Type: text/plain' );
		echo "Producer: " . $producer . PHP_EOL;
		echo "Show: " . $show . PHP_EOL;
		echo "Producers: " . print_r( $producers, true );
	}

	return $producers;
}


// -------------------
// === REST Routes ===
// -------------------

// --------------------
// Register Rest Routes
// --------------------
add_action( 'rest_api_init', 'radio_station_pro_register_rest_routes' );
function radio_station_pro_register_rest_routes() {

	// --- check rest routes are enabled ---
	$enabled = radio_station_get_setting( 'enable_data_routes' );
	if ( $enabled != 'yes' ) {return;}

	// --- filter route slugs ---
	// (can disable individual routes by returning false from filters)
	$base = apply_filters( 'radio_station_route_slug_base', 'radio' );
	// 2.4.1.6: default episodes to false for modular independence
	$episodes = apply_filters( 'radio_station_route_slug_episodes', false );
	$hosts = apply_filters( 'radio_station_route_slug_hosts', 'hosts' );
	$producers = apply_filters( 'radio_station_route_slug_producers', 'producers' );

	// --- set default REST route args ---
	// 2.4.1.9: fix for missing permission_callback argument (WP 5.5+)
	$args = array(
		'methods' => 'GET',
		'permission_callback' => '__return_true',
	);
		
	// --- Show Episodes Route ---
	// default URL: /wp-json/radio/genres/
	// 2.4.1.9: added filter for rest route args
	if ( $episodes ) {
		$args['callback'] = 'radio_station_route_episodes';
		$args = apply_filters( 'radio_station_route_args_episodes', $args );
		register_rest_route( $base, '/' . $episodes . '/', $args );
	}

	// --- Show Hosts Route ---
	// default URL: /wp-json/radio/hosts/
	// 2.4.1.9: added filter for rest route args
	if ( $hosts ) {
		$args['callback'] = 'radio_station_route_hosts';
		$args = apply_filters( 'radio_station_route_args_hosts', $args );
		register_rest_route( $base, '/' . $hosts . '/', $args );
	}

	// --- Show Producers Route ---
	// default URL: /wp-json/radio/producers/
	// 2.4.1.9: added filter for rest route args
	if ( $producers ) {
		$args['callback'] = 'radio_station_route_producers';
		$args = apply_filters( 'radio_station_route_args_producers', $args );
		register_rest_route( $base, '/' . $producers . '/', $args );
	}

}

// -----------------------
// Add Route Links to Data
// -----------------------
add_filter( 'radio_station_route_urls', 'radio_station_pro_add_route_urls' );
function radio_station_pro_add_route_urls( $routes ) {

	// --- get and add route links ---
	$episodes = radio_station_get_route_url( 'episodes' );
	if ( $episodes ) {
		$routes['episodes'] = $episodes;
	}
	$hosts = radio_station_get_route_url( 'hosts' );
	if ( $hosts ) {
		$routes['hosts'] = $hosts;
	}
	$producers = radio_station_get_route_url( 'producers' );
	if ( $producers ) {
		$routes['producers'] = $producers;
	}

	return $routes;
}

// -------------------
// Show Episodes Route
// -------------------
function radio_station_pro_route_episodes( $request ) {

	// --- get episode list ---
	$episode_list = radio_station_pro_episodes_endpoint();
	if ( !is_wp_error( $episode_list ) ) {
		$episode_list = array( 'episodes' => $episode_list );
		$episode_list = radio_station_add_station_data ( $episode_list );
		$episode_list['endpoints'] = radio_station_get_route_urls();
	}
	$episode_list = apply_filters( 'radio_station_route_episodes', $episode_list );

	// --- output data ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo "Output: " . print_r( $episode_list, true ); exit;
		exit;
	}

	return $episode_list;
}

// ----------------
// Show Hosts Route
// ----------------
function radio_station_pro_route_hosts( $request ) {

	// --- get host list ---
	$host_list = radio_station_pro_hosts_endpoint();
	if ( !is_wp_error( $host_list ) ) {
		$host_list = array( 'hosts' => $host_list );
		$host_list = radio_station_add_station_data ( $host_list );
		$host_list['endpoints'] = radio_station_get_route_urls();
	}
	$host_list = apply_filters( 'radio_station_feed_hosts', $host_list );

	// --- output data ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo "Output: " . print_r( $host_list, true ) . PHP_EOL;
		exit;
	}

	return $host_list;
}

// -------------------
// Show Producer Route
// -------------------
function radio_station_pro_route_producers( $request ) {

	// --- get producer list ---
	$producer_list = radio_station_pro_producers_endpoint();
	if ( !is_wp_error( $producer_list ) ) {
		$producer_list = array( 'producers' => $producer_list );
		$producer_list = radio_station_add_station_data ( $producer_list );
		$producer_list['endpoints'] = radio_station_get_route_urls();
	}
	$producer_list = apply_filters( 'radio_station_route_producers', $producer_list );

	// --- output data ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo "Output: " . print_r( $producer_list, true );
		exit;
	}

	return $producer_list;
}


// ------------------
// === Data Feeds ===
// ------------------

// ---------
// Add Feeds
// ---------
add_filter( 'radio_station_feed_slugs', 'radio_station_pro_add_feeds' );
function radio_station_pro_add_feeds( $feeds ) {

	// --- check if feeds are enabled ---
	$enabled = radio_station_get_setting( 'enable_data_feeds' );
	if ( $enabled != 'yes' ) {
		return $feeds;
	}

	// --- filter feed slugs ---
	// 2.4.1.6: default episodes to false for modular independence
	$episodes = apply_filters( 'radio_station_feed_slug_episodes', false );
	$hosts = apply_filters( 'radio_station_feed_slug_hosts', 'hosts' );
	$producers = apply_filters( 'radio_station_feed_slug_producers', 'producers' );

	// --- add Pro feeds ---
	if ( $episodes ) {
		radio_station_add_feed( $episodes, 'radio_station_pro_feed_episodes' );
	}
	if ( $hosts ) {
		radio_station_add_feed( $hosts, 'radio_station_pro_feed_hosts' );
	}
	if ( $producers ) {
		radio_station_add_feed( $producers, 'radio_station_feed_producers' );
	}

	// --- merge in Pro feed slugs ---
	$pro_feeds = array( $episodes, $hosts, $producers );
	$feeds = array_merge( $feeds, $pro_feeds );

	return $feeds;
}

// ----------------------
// Add Feed Links to Data
// ----------------------
add_filter( 'radio_station_feed_urls', 'radio_station_pro_add_feed_urls' );
function radio_station_pro_add_feed_urls( $feeds ) {

	// --- get and add feed links ---
	$episodes = radio_station_get_feed_url( 'episodes' );
	if ( $episodes ) {
		$feeds['episodes'] = $episodes;
	}
	$hosts = radio_station_get_feed_url( 'hosts' );
	if ( $hosts ) {
		$feeds['hosts'] = $hosts;
	}
	$producers = radio_station_get_feed_url( 'producers' );
	if ( $producers ) {
		$feeds['producers'] = $producers;
	}

	return $feeds;
}

// ------------------
// Show Episodes Feed
// ------------------
function radio_station_pro_feed_episodes( $comment_feed, $feed_name ) {

	// --- get episode list ---
	$episode_list = radio_station_pro_episodes_endpoint();
	if ( !is_wp_error( $episode_list ) ) {
		$episode_list = array( 'episodes' => $episode_list );
		$episode_list = radio_station_add_station_data ( $episode_list );
		$episode_list['endpoints'] = radio_station_get_feed_urls();
	}
	$episode_list = apply_filters( 'radio_station_feed_episodes', $episode_list );

	// --- output data ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo "Output: " . print_r( $episode_list, true ) . PHP_EOL;
	} else {
		header( 'Content-Type: application/json' );
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo json_encode( $episode_list );
	}
}

// ---------------
// Show Hosts Feed
// ---------------
function radio_station_pro_feed_hosts( $comment_feed, $feed_name ) {

	// --- get host list ---
	$host_list = radio_station_pro_hosts_endpoint();
	if ( !is_wp_error( $host_list ) ) {
		$host_list = array( 'hosts' => $host_list );
		$host_list = radio_station_add_station_data ( $host_list );
		$host_list['endpoints'] = radio_station_get_feed_urls();
	}
	$host_list = apply_filters( 'radio_station_feed_hosts', $host_list );

	// --- output data ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo "Output: " . print_r( $host_list, true );
	} else {
		header( 'Content-Type: application/json' );
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo json_encode( $host_list );
	}
}

// ------------------
// Show Producer Feed
// ------------------
function radio_station_pro_feed_producers( $comment_feed, $feed_name ) {

	// --- get producer list ---
	$producer_list = radio_station_pro_producers_endpoint();
	if ( !is_wp_error( $producer_list ) ) {


		$producer_list['endpoints'] = radio_station_get_feed_urls();
	}
	$producer_list = apply_filters( 'radio_station_feed_producers', $producer_list );

	// --- output data ---
	if ( RADIO_STATION_DEBUG || RADIO_STATION_PRO_DEBUG ) {
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo "Output: " . print_r( $producer_list, true ) . PHP_EOL;
	} else {
		header( 'Content-Type: application/json' );
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo json_encode( $producer_list );
	}
}
