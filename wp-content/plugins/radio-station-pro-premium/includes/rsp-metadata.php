<?php

// =========================
// === Radio Station Pro ===
// =========================
// ---- Stream Metadata ----
// =========================

// @since 2.4.1.3

// === Metadata Javascript ===
// - 
// === Stream Metadata ===
// - Get Stream Metadata
// - Filter Broadcast Data
// - AJAX Now Playing Fallback
// === ICY Metadata ===
// - ICY Get Current Song
// - ICY Get Headers via Curl
// - ICY Get Headers Fallback
// - ICY Get Metadata Offset
// - ICY Metadata via CURL
// - ICY Metadata via Socket
// - ICY Get Song Information
// === Shoutcast 1 ===
// - Shoutcast 1 Metadata
// - Shoutcast 1 Current Song
// - Shoutcast 1 Listeners
// - Shoutcast 1 Meta
// === Shoutcast 2 ===
// - Shoutcast 2 Metadata
// - Shoutcast 2 Current Song
// - Shoutcast 2 Listeners
// - Shoutcast 2 Meta
// === Icecast ===
// - Icecast Metadata
// - Icecast Metadata JSON
// - Icecast Metadata XML
// - Icecast Current Song
// - Icecast Listeners
// - Icecast Meta
// === Metadata Helpers ===
// - Download Metadata
// - Get Current Song
// - Get Listeners
// - Get Meta
// - Get SimpleXML


// ---------------------------
// === Metadata Javascript ===
// ---------------------------
// 2.4.1.9: moved here from rsp-player.php
add_action( 'wp_enqueue_scripts', 'radio_station_pro_metadata_script_settings', 13 );
function radio_station_pro_metadata_script_settings() {

	// --- enqueue metadata javascript ---
	$dir = defined( 'STREAM_PLAYER_PRO_DIR' ) ? STREAM_PLAYER_PRO_DIR : RADIO_STATION_PRO_DIR;
	$file = defined( 'STREAM_PLAYER_PRO_FILE' ) ? STREAM_PLAYER_PRO_FILE : RADIO_STATION_PRO_FILE;
	$version = filemtime( $dir . '/js/rsp-metadata.js' );
	$pro_metadata_url = plugins_url( 'js/rsp-metadata.js', $file );
	wp_enqueue_script( 'rsp-metadata', $pro_metadata_url, array( 'radio-player' ), $version, true );

	// --- check player bar position ---
	$position = radio_station_get_setting( 'player_bar' );
	if ( ( '' != $position ) && ( 'off' != $position ) ) {

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
		$js .= "radio_player.settings.metadata_url = '" . esc_url( $metadata_url ) . "';" . PHP_EOL;

 		// 2.4.1.5: filter metadata cycle seconds
		// 2.4.1.9: add metadata cycle time to settings object
 		$metadata_cycle = 10;
 		$metadata_cycle = apply_filters( 'radio_station_player_bar_metadata_cycle', $metadata_cycle );
		$js .= "radio_player.settings.metadata_cycle = " . esc_js( $metadata_cycle * 1000 ) . ";" . PHP_EOL;

		// 2.4.1.9: set empty metadata object
		$js .= "radio_data.metadata = [];" . PHP_EOL;

		// --- start metadata cycler via playing event ---
		// 2.4.1.9: moved to rsp-metadata.js

		// --- filter and enqueue inline ---
		$js = apply_filters( 'radio_station_pro_metadata_scripts', $js );
		if ( '' != $js ) {
			wp_add_inline_script( 'rsp-metadata', $js, 'before' );
		}
	}
}


// -----------------------
// === Stream Metadata ===
// -----------------------

// -------------------
// Get Stream Metadata
// -------------------
function radio_station_pro_get_stream_metadata( $stream, $metadata, $key = false ) {

	$np = false;

	// --- maybe get stored data type ---
	// delete_option( 'radio_player_stream_data' );
	$stream_data = get_option( 'radio_player_stream_data' );
	if ( RADIO_STATION_DEBUG ) {
		echo "Stored Stream Metadata: " . print_r( $stream_data, true ) . PHP_EOL;
	}
	if ( $stream_data && is_array( $stream_data ) && ( count( $stream_data ) > 0 ) ) {
		foreach ( $stream_data as $i => $data ) {
			// 2.4.1.6: added isset check for undefined index warning
			if ( isset( $data['stream'] ) && ( $data['stream'] == $stream ) ) {
				$index = $i;
				$type = $data['type'];
			}
		}
	}

	// --- filter data types ---
	$data_types = array( 'icymeta', 'icecast', 'shoutcast2', 'shoutcast' );
	$data_types = apply_filters( 'radio_station_stream_metadata_types', $data_types );

	// --- maybe detect type by URL ending ---
	if ( !isset( $type ) ) {
		$stream_path = $stream;
		if ( strstr( $stream, '?' ) ) {
			$url_parts = explode( '?', $stream );
			$stream_path = $url_parts[0];
		}
		if ( 'status-json.xsl' == substr( $stream_path, -15, 15 ) ) {
			$type = 'icecast';
		} elseif ( 'stats' == substr( $stream_path, -5, 5 ) ) {
			$type = 'shoutcast2';
		} elseif ( '7.html' == substr( $stream_path, -6, 6 ) ) {
			$type = 'shoutcast1';
		}
	}

	// --- check detected type first ---
	// 2.4.1.4: fix by moving below type detection
	if ( isset( $type ) ) {
		$i = array_search( $type, $data_types );
		unset ( $data_types[$type] );
		$data_types = array_merge( array( $type ), $data_types );
	}

	// --- loop data types ---
	if ( $data_types && is_array( $data_types ) && ( count( $data_types ) > 0 ) ) {
		foreach ( $data_types as $data_type ) {

			if ( 'icymeta' == $data_type ) {
				$result = radio_station_pro_icy_stream_title( $stream, $metadata );
				if ( $result ) {
					$np = array(
					    'stream'      => $stream,
					    // 'url'         => $url,
						'currentsong' => $result,
						'type'        => 'icymeta',
					);
					break;
				}
			} elseif ( 'icecast' == $data_type ) {
				$np = radio_station_pro_icecast_metadata( $stream, $metadata );
				if ( is_array( $np ) && !isset( $np['error'] ) ) {
					break;
				}
			} elseif ( 'shoutcast2' == $data_type ) {
				$np = radio_station_pro_shoutcast2_metadata( $stream, $metadata );
				if ( is_array( $np ) && !isset( $np['error'] ) ) {
					break;
				}
			} elseif ( 'shoutcast1' == $data_type ) {
				$np = radio_station_pro_shoutcast1_metadata( $stream, $metadata );
				if ( is_array( $np ) && !isset( $np['error'] ) ) {
					break;
				}
			}
		}
	}

	// --- fallback to current playlist --
	// 2.4.1.4: added extra conditional fallback checks
	if ( !$np || !is_array( $np ) || isset( $np['error'] ) ) {
		$playlist = radio_station_get_now_playing();
		if ( RADIO_STATION_DEBUG ) {
			echo 'Current Playlist: ' . print_r( $playlist, true ) . PHP_EOL;
		}
		if ( $playlist ) {
			if ( isset( $playlist['latest'] ) ) {
				$latest = $playlist['latest'];
			} elseif (isset( $playlist['queued'] ) && ( count( $playlist['queued'] ) > 0 ) ) {
				$latest = $playlist['queued'][0];
			}
			if ( isset( $latest ) ) {
				if ( RADIO_STATION_DEBUG ) {
					echo 'Latest Playlist Track: ' . print_r( $latest, true ) . PHP_EOL;
				}

				// 2.4.1.8: add check to fix undefined indexes
				$song = isset( $latest['playlist_entry_song'] ) ? $latest['playlist_entry_song'] : '';
				$artist = isset( $latest['playlist_entry_artist'] ) ? $latest['playlist_entry_artist'] : '';
				$currentsong = radio_station_pro_metadata_current_song( '', $song, $artist, ' - ' );
				$np = array(
					'currentsong' => $currentsong,
					'type'        => 'playlist'
				);
			}
		}
	}

	if ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) {
		echo 'Now Playing Metadata: ' . print_r( $np, true ) . PHP_EOL;
	}

	// --- filter stream data ---
	$np = apply_filters( 'radio_station_stream_metadata', $np, $stream );
	// 2.4.1.6: set metadata timestamp
	if ( $np && is_array( $np ) ) {
		$np['timestamp'] = time();

		// --- store stream data ---
		if ( isset( $index ) ) {
			if ( !$np || !is_array( $np ) || isset( $np['error'] ) ) {
				unset( $stream_data[$index] );
			} else {
				$stream_data[$index] = $np;
			}
		} else {
			$stream_data[] = $np;
		}
	}

	// 2.4.1.6: cache detected stream data type
	if ( RADIO_STATION_DEBUG ) {
		echo "Storing Stream Metadata: " . print_r( $stream_data, true ) . PHP_EOL;
	}
	update_option( 'radio_player_stream_data', $stream_data );

	// --- maybe return key data ---
	if ( $key ) {
		if ( isset( $np[$key] ) ) {
			return $np[$key];
		} else {
			return false;
		}
	}

	return $np;
}

// ---------------------
// Filter Broadcast Data
// ---------------------
add_filter( 'radio_station_broadcast_data', 'radio_station_pro_broadcast_data' );
function radio_station_pro_broadcast_data( $broadcast ) {

	// --- get stream URL ---
	$stream = radio_station_get_stream_url();

	// --- get stream metadata URL ---
	// 2.4.1.4: check per show metadata override value
	$playlist = false;
	$show_metadata = $show_metadata_url = '';
	if ( isset( $broadcast['current_show']['show']['id'] ) ) {
		$show_id = $broadcast['current_show']['show']['id'];
		$show_metadata = get_post_meta( $show_id, 'show_metadata', true );
		// $show_metadata_url = get_post_meta( $show_id, 'show_metadata_url', true );
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Metadata Setting for Show ' . $show_id . ': ' . $show_metadata . '</span>';
		}
	}

	if ( !$show_metadata || ( '' == $show_metadata ) || ( 'on' == $show_metadata ) ) {
		// --- use default metadata stream URL ---
		// 2.4.1.6: fix metadata URL setting key
		$metadata = radio_station_get_setting( 'player_bar_metadata' );
		// TODO: check/allow for zero value saving for URL field?
		if ( '0' === $metadata ) {
			// 2.4.1.5: handle plugin metadata off but show metadata ON
			if ( 'on' == $show_metadata ) {
				$metadata = $stream;
			} else {
				$metadata = false;
			}
		} elseif ( !$metadata || ( '' == trim( $metadata ) ) ) {
			$metadata = $stream;
		}
	// } elseif ( 'url' == $show_metadata ) {
		// --- custom override to specific metadata URL ---
		// if ( $show_metadata_url && ( '' != trim( $show_metadata_url ) ) ) {
		//	$stream = $show_metadata_url;
		// }
	} elseif ( 'playlist' == $show_metadata ) {
		// --- for playlist override filter out other types ---
		$metadata = $playlist = true;
		add_filter( 'radio_station_stream_metadata_types', '__return_false' );
	}
	// 2.4.1.4: added stream metadata URL override filter
	$metadata = apply_filters( 'radio_station_stream_metadata_url', $metadata, $broadcast );

	// --- allow for stream shorthand ---
	if ( '1' === $metadata ) {
		$metadata = radio_station_get_stream_url();
	} elseif ( '2' === $metadata ) {
		$metadata = radio_station_get_fallback_url();
	}

	// --- maybe get currently cached value ---
	if ( $metadata && !$playlist ) {
		$interval = apply_filters( 'radio_station_metadata_cache_interval', 5 );
		$stream_data = get_option( 'radio_player_stream_data' );
		if ( $stream_data && is_array( $stream_data ) && ( count( $stream_data ) > 0 ) ) {
			foreach ( $stream_data as $i => $data ) {
				if ( isset( $data['stream'] ) && ( $data['stream'] == $stream ) ) {
					if ( ( $data['timestamp'] + $interval ) > time() ) {
						$currentsong = $data['currentsong'];
					}
				}
			}
		}
	}

	// --- get current song ---
	if ( $metadata ) {
		if ( RADIO_STATION_DEBUG ) {
			echo '<span style="display:none;">Metadata for Stream URL: ' . $stream . ' via ' . $metadata . '</span>';
		}
		if ( !isset( $currentsong ) ) {
			$currentsong = radio_station_pro_get_stream_metadata( $stream, $metadata, 'currentsong' );
		}
	}
	// 2.4.1.4: added current song data filter
	$currentsong = apply_filters( 'radio_station_current_song', $currentsong );
	if ( RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Current song: ' . $currentsong . '</span>';
	}


	// --- add data to broadcast endpoint ---
	// 2.4.1.4: add regardless of value (so false is detectable)
	$broadcast['now_playing'] = $currentsong;

	return $broadcast;
}

// -------------------------
// AJAX Now Playing Fallback
// -------------------------
// note: for edge case where REST and Feeds endpoints are disabled in plugin settings
add_action( 'wp_ajax_radio_player_now_playing', 'radio_station_pro_now_playing_data' );
add_action( 'wp_ajax_nopriv_radio_player_now_playing', 'radio_station_pro_now_playing_data' );
function radio_station_pro_now_playing_data() {
	// 2.4.1.6: allow for alternative stream/metadata URLs
	if ( isset( $_REQUEST['stream'] ) ) {
		$valid = filter_var( $_REQUEST['stream'], FILTER_VALIDATE_URL );
		if ( $valid ) {
			$metadata = false;
			if ( isset( $_REQUEST['metadata'] ) ) {
				$valid = filter_var( $_REQUEST['metadata'], FILTER_VALIDATE_URL );
				if ( $valid ) {
					$metadata = $_REQUEST['metadata'];
				}
			}
			$currentsong = radio_station_pro_get_stream_metadata( $stream, $metadata, 'currentsong' );
			$now_playing = array( 'now_playing' => $currentsong );
			echo json_encode( $now_playing );
			exit;
		}
	}

	$broadcast = array( 'broadcast' => radio_station_get_broadcast_data() );
	header( 'Content-Type: application/json' );
	echo json_encode( $broadcast );
	exit;
}


// --------------------
// === ICY Metadata ===
// --------------------
// Credit: adapted from MP3StreamTitle class by Oleg Kovalenko

// --------------------
// ICY Get Current Song
// --------------------
function radio_station_pro_icy_stream_title( $stream, $metadata = false, $method = 'curl' ) {

	if ( RADIO_STATION_DEBUG ) {
		echo 'Stream URL: ' . $stream . '<br>';
		// echo 'Metadata URL: ' . $stream . '<br>';
	}

	// --- use metadata URL only if overridden ---
	if ( $metadata ) {
		$stream = $metadata;
		$metadata = false;
	}

	// --- get the stream headers ---
	$method = apply_filters( 'radio_station_icy_metadata_method', $method );
	if ( ( 'curl' == $method ) && extension_loaded( 'curl' ) && function_exists( 'curl_init' ) ) {
		$headers = radio_station_pro_icy_curl_headers( $stream );
		if ( !$headers ) {
			$headers = radio_station_pro_icy_get_headers( $stream );
		}
	} else {
		$headers = radio_station_pro_icy_get_headers( $stream );
		if ( !$headers && extension_loaded( 'curl' ) && function_exists( 'curl_init' ) ) {
			$headers = radio_station_pro_icy_curl_headers( $stream );
		}
	}

	// --- make sure we have headers ---
	if ( $headers && is_array( $headers ) && ( count( $headers ) > 0 ) ) {

		// --- get icy-metaint offset ---
		$offset = radio_station_pro_icy_get_offset( $headers );

		// --- check if we have an offset ---
		if ( $offset ) {

			// --- get metadata from stream via offset ---
			if ( ( 'curl' == $method ) && extension_loaded( 'curl' ) && function_exists( 'curl_init' ) ) {
			 	$metadata = radio_station_pro_icy_curl_metadata( $stream, $offset );
			 	if ( !$metadata ) {
				 	$metadata = radio_station_pro_icy_get_metadata( $stream, $offset );
				 }
			} else {
				$metadata = radio_station_pro_icy_get_metadata( $stream, $offset );
				if ( !$metadata && extension_loaded( 'curl' ) && function_exists( 'curl_init' ) ) {
					$metadata = radio_station_pro_icy_curl_metadata( $stream, $offset );
				}
			}

			// --- check if we have metadata ---
			if ( $metadata ) {
				// --- get song info from metadata ---
				$result = radio_station_pro_icy_song_info( $metadata );
				$currentsong = radio_station_pro_metadata_current_song( $result );
			} else {
				$error = 'Failed to get metadata from stream.';
			}
		} else {
			$error = 'Failed to get "icy-metaint" header value.';
		}
	} else {
		$error = 'Failed to get headers from server response to HTTP-request.';
	}

	// --- maybe output / return error ---
	if ( isset( $error ) ) {
		if ( RADIO_STATION_DEBUG ) {
			echo 'Error: ' . $error . '<br>';
		}
		return false;
	}

	return $currentsong;
}

// ------------------------
// ICY Get Headers via Curl
// ------------------------
function radio_station_pro_icy_curl_headers( $stream ) {

	if ( RADIO_STATION_DEBUG ) {
		echo 'Getting stream headers via Curl.<br>';
	}

	$headers = false;

	// Initialize the cURL session.
	$ch = curl_init();

	// Parse URL.
	// $url_part = parse_url( $stream );
	// if ( !empty( $url_part['port'] ) ) {
	//	curl_setopt( $ch, CURLOPT_PORT, $url_part['port'] );
	//	$stream = str_replace( ':' . $url_part['port'], '', $stream );
	// }

	// Set the parameters for the session.
	curl_setopt( $ch, CURLOPT_URL, $stream );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_HEADER, 1 );
	curl_setopt( $ch, CURLOPT_NOBODY, 1 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'icy-metadata: 1' ) );

	// Execute the request.
	$tmp = @curl_exec( $ch );

	// If there are errors we save them into variables.
	$errno = curl_errno( $ch );
	$error = curl_error( $ch );

	// End the session.
	curl_close( $ch );

	$headers = explode( "\n", $tmp );

	return $headers;
}

// ------------------------
// ICY Metadata Get Headers
// ------------------------
function radio_station_pro_icy_get_headers( $stream ) {

	if ( RADIO_STATION_DEBUG ) {
		echo 'Getting stream headers via Socket.<br>';
	}

	// HTTP-request headers.
	$options_header = "";
	// $options_header .= "User-Agent: " . $user_agent . "\r\n";
	$options_header .= "icy-metadata: 1\r\n\r\n";

	$options = array(
		'http' => array (
			'method'  => "GET",
			'header'  => $options_header,
			'timeout' => 10
		)
	);

	// Create a thread context.
	// $context = stream_context_create( $options );
	stream_context_set_default( $options );

	// Get the headers from the server response to the HTTP-request.
	// $headers = @get_headers( $stream, 0, $context );
	$headers = @get_headers( $stream, 0 );

	if ( RADIO_STATION_DEBUG ) {
		print_r( $headers );
	}

	return $headers;

}

// -----------------------
// ICY Get Metadata Offset
// -----------------------
function radio_station_pro_icy_get_offset( $headers ) {

	$offset = false;

	// Looking for the header "icy-metaint".
	foreach ( $headers as $header ) {

		/* Find out how many bytes of data from the stream you need to read before
		the metadata begins (which contains the name of the artist and the name of the song). */
		$h = trim( $header );
		if ( strpos( $header, 'icy-metaint' ) !== false ) {
			if ( RADIO_STATION_DEBUG ) {
				echo '<b>' . $header . '</b><br>';
			}
			$exploded = explode( ':', $header );
			if ( $exploded[1] ) {
				$offset = (int)$exploded[1];
				break;
			}
		}
	}

	return $offset;
}

// ---------------------
// ICY Metadata via CURL
// ---------------------
function radio_station_pro_icy_curl_metadata( $stream, $offset ) {

	if ( RADIO_STATION_DEBUG ) {
		echo 'Getting stream metadata via Curl.<br>';
	}

	// Initialize variables.
	$metadata = false;
	$meta_max_length = 5228;

	// Find out how many bytes of data you need to get.
	$data_byte = $offset + $meta_max_length;

	/* The callback-function returns the number of data bytes received or metadata.
	The function is used as the value of the parameter "CURLOPT_WRITEFUNCTION". */
	$write_function = function( $ch, $chunk ) use ( $data_byte, $offset, &$metadata ) {

		// Initialize variables.
		static $data = '';

		// echo 'Chunk:';
		// echo '<textarea rows="3" cols="80">' . $chunk . '</textarea><br>';

		// Find out the length of the data.
		$data_length = strlen( $data ) + strlen( $chunk );

		// If the length of the received data is greater than or equal to the desired length.
		if ( $data_length >= $data_byte ) {

			// Save the data part into a variable.
			$data .= substr( $chunk, 0, $data_byte - strlen( $data ) );

			// Find out the length of the metadata.
			$meta_length = ord( substr( $data, $offset, 1 ) ) * 16;

			// Get metadata in the following format "StreamTitle='artist name and song name';".
			$metadata = substr( $data, $offset, $meta_length );

			// echo 'Metadata:';
			// echo '<textarea rows="3" cols="80">' . $metadata . '</textarea><br>';

			// Interrupt receiving data (with an error "curl_errno: 23").
			return -1;
		}

		// Save the data part into a variable.
		$data .= $chunk;

		// Return the number of received data bytes.
		return strlen( $chunk );
	};

	// Initialize the cURL session.
	$ch = curl_init();

	// Parse URL.
	// $url_part = parse_url( $stream );
	// if ( !empty( $url_part['port'] ) ) {
	//	curl_setopt( $ch, CURLOPT_PORT, $url_part['port'] );
	//	$stream = str_replace( ':' . $url_part['port'], '', $stream );
	// }

	// Set the parameters for the session.
	curl_setopt( $ch, CURLOPT_URL, $stream );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'icy-metadata: 1' ) );
	// curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent );
	curl_setopt( $ch, CURLOPT_WRITEFUNCTION, $write_function );

	// Execute the request.
	@curl_exec( $ch );

	// If there are errors we save them into variables.
	$errno = curl_errno( $ch );
	$err = curl_error( $ch );

	// End the session.
	curl_close( $ch );

	// echo 'Metadata: <textarea rows="3" cols="80">' . $metadata . '</textarea>';

	// Return the result of the request.
	if ( !$metadata ) {
		if ( RADIO_STATION_DEBUG ) {
			$error = $err . ' (' . $errno . ').';
			echo 'Error: ' . $error . '<br>';
		}
		return false;
	}

	return $metadata;
}

// -----------------------
// ICY Metadata via Socket
// -----------------------
function radio_station_pro_icy_get_metadata( $stream, $offset ) {

	if ( RADIO_STATION_DEBUG ) {
		echo 'Getting stream metadata via Socket.<br>';
	}

	// Initialize variables.
	$metadata = false;
	$meta_max_length = 5228;
	$prefix = '';
	$port   = 80;
	$path   = '/';

	// Parse URL.
	$url_part = parse_url( $stream );

	// Find out protocol.
	if ( $url_part['scheme'] == 'https' ) {
		$prefix = 'ssl://'; // If HTTPS, use the SSL protocol.
		$port   = 443; // If HTTPS, the port can only be 443.
	}

	// Find out port and protocol.
	if ( !empty( $url_part['port'] ) && ( $url_part['scheme'] == 'http' ) ) {
		// If the HTTP protocol, then the port is non-standard.
		$port = $url_part['port'];
	}

	// Find out path.
	if ( !empty($url_part['path'] ) ) {
		$path = $url_part['path'];
	}

	// Open connection.
	if ( $fp = @fsockopen( $prefix . $url_part['host'], $port, $errno, $errstr, 30 ) ) {

		// HTTP-request headers.
		$headers = "GET " . $path . " HTTP/1.0\r\n";
		// $headers .= "User-Agent: ".$this->user_agent."\r\n";
		$headers .= "icy-metadata: 1\r\n\r\n";

		// Send a request to the stream-server.
		if ( fwrite( $fp, $headers ) ) {

			// Find out how many bytes of data need to be received.
			$data_byte = $offset + $meta_max_length;

			// Save the data part into the variable.
			$buffer = stream_get_contents( $fp, $data_byte );

			// Close the connection.
			fclose( $fp );

			// Separate the headers from the "body".
			list( $tmp, $body ) = explode( "\r\n\r\n", $buffer, 2 );

			// Find out length of metadata.
			$meta_length = ord( substr( $body, $offset, 1 ) ) * 16;

			// Get metadata in the following format "StreamTitle='artist name and song name';".
			$metadata = substr( $body, $offset, $meta_length );

			// echo '<textarea rows="3" cols="80">' . $metadata . '</textarea>';

			// Return the result of the request.
			// $result = radio_station_pro_icy_song_info( $metadata );

		} else {

			// Close the connection.
			fclose( $fp );
			$error = 'Failed to get server response for stream.';
		}

	} else {
		$error = 'An error occurred while using sockets. ' . $errstr . ' (' . $errno . ').';
	}


	if ( isset( $error ) ) {
		if ( RADIO_STATION_DEBUG ) {
			echo 'Error: ' . $error . '<br>';
		}
		return false;
	}

	return $metadata;
}

// ------------------------
// ICY Get Song Information
// ------------------------
function radio_station_pro_icy_song_info( $metadata ) {
	/* Find the position of the string "='" indicating the beginning of information about the
	   song and find position of the string "';" which indicates the end of the song information. */
	if ( ( $info_start = strpos( $metadata, "='" ) ) && ( $info_end = strpos( $metadata, "';" ) ) ) {
		// Get information about the song in the following format "artist name and song name".
		$result = substr( $metadata, $info_start + 2, $info_end - ( $info_start + 2 ) );
	} else {
		$result = 0;
	}
	return $result;
}


// -------------------
// === Shoutcast 1 ===
// -------------------

// --------------------
// Shoutcast 1 Metadata
// --------------------
function radio_station_pro_shoutcast1_metadata( $stream, $metadata, $mount = false, $key = false ) {

	// --- maybe use metadata override ---
	if ( !$metadata ) {
		// 2.4.1.8: fix to missing $ on variable causing constant error 
		$metadata = $stream;
	}

	// --- get data ---
	$payload = false;
	if ( '7.html' == substr( $metadata, -6, 6 ) ) {
		$url = $stream;
	} elseif ( '/' == substr( $metadata, -1, 1 ) ) {
		$url = $stream . '7.html';
	} else {
		// 2.4.1.6: parse the stream URL to remove path
		$pos = strrpos( trim( $metadata ), '/' ) + 1;
		$parts = str_split( $metadata, $pos );
		$url = $parts[0];
		$mountpath = $parts[1];
		$url = trailingslashit( $url ) . '7.html';

		// 2,4.1.6: try URL variation
		$payload = radio_station_pro_download_metadata( $url );
		if ( !$payload ) {
			$url = trailingslashit( $metadata ) . '7.html';
		}
	}
	if ( !$payload ) {
		$payload = radio_station_pro_download_metadata( $url );
		if ( !$payload ) {
			return false;
		}
	}

	// --- extract data ---
	preg_match( "/<body.*>(.*)<\/body>/smU", $payload, $return );
	// [$current_listeners, , , , $unique_listeners, $bitrate, $title] = explode( ',', $return[1], 7 );
	$data = explode( ',', $return[1], 7 );
	$current_listeners = $data[0];
	// ??? = $data[1];
	// ??? = $data[2];
	// ??? = $data[3];
	$unique_listeners = $data[4];
	$bitrate = $data[5];
	$title = $data[6];

	// --- set metadata ---
	$currentsong = radio_station_pro_metadata_current_song( $title );
	if ( $key && ( 'currentsong' == $key ) ) {
		return $currentsong;
	}
	// $listeners = new Listeners($current_listeners, $unique_listeners);
	$listeners = radio_station_pro_metadata_listeners( $current_listeners, $unique_listeners );
	if ( $key && ( 'listeners' == $key ) ) {
		return $listeners;
	}
	// $meta = new Meta( !empty($np->currentSong->text), $bitrate );
	$meta = radio_station_pro_metadata_meta( !empty( $currentsong['text'] ), $bitrate );
	if ( $key && ( 'meta' == $key ) ) {
		return $meta;
	}

	// --- set metadata array ---
	// 2.4.1.6: added missing type
	$metadata = array(
		'stream'      => $stream,
		'url'         => $url,
		'currentsong' => $currentsong,
		'listeners'   => $listeners,
		'meta'        => $meta,
		'type'        => 'shoutcast1',
	);

	// --- filter and return ---
	$metadata = apply_filters( 'radio_station_pro_metadata', $metadata, $stream, 'shoutcast1' );
	return $metadata;
}

// ------------------------
// Shoutcast 1 Current Song
// ------------------------
function radio_station_pro_shoutcast1_current_song( $stream, $metadata = false ) {
	$currentsong = radio_station_pro_shoutcast1_metadata( $stream, $metadata, 'currentsong' );
	return $currentsong;
}

// ---------------------
// Shoutcast 1 Listeners
// ---------------------
function radio_station_pro_shoutcast1_listeners( $stream, $metadata = false ) {
	$listeners = radio_station_pro_shoutcast1_metadata( $stream, $metadata, 'listeners' );
	return $listeners;
}

// ----------------
// Shoutcast 1 Meta
// ----------------
function radio_station_pro_shoutcast1_meta( $stream, $metadata = false ) {
	$meta = radio_station_pro_shoutcast1_metadata( $stream, $metadata, 'meta' );
	return $meta;
}


// -------------------
// === ShoutCast 2 ===
// -------------------

// --------------------
// ShoutCast 2 Metadata
// --------------------
function radio_station_pro_shoutcast2_metadata( $stream, $metadata, $mount = false, $key = false ) {

	// --- maybe use metadata override ---
	if ( !$metadata ) {
		// 2.4.1.8: fix to missing $ on variable causing constant error
		$metadata = $stream;
	}

	// --- get data ---
	$payload = false;
	if ( 'stats' == substr( $metadata, -5, 5 ) ) {
		$url = $metadata;
	} elseif ( '/' == substr( $metadata, -1, 1 ) ) {
		$url = $metadata . 'stats';
	} else {
		// 2.4.1.6: parse the stream URL to remove path
		$pos = strrpos( trim( $metadata ), '/' ) + 1;
		$parts = str_split( $metadata, $pos );
		$url = $parts[0];
		$mountpath = $parts[1];
		$url = trailingslashit( $url ) . 'stats';

		// 2,4.1.6: try URL variation
		if ( !empty( $mount ) ) {
			$url = add_query_arg( 'sid', $mount, $url );
		}
		$payload = radio_station_pro_download_metadata( $url );
		if ( !$payload ) {
			$url = trailingslashit( $metadata ) . 'stats';
		}
	}

	// if ( isset( $admin_password ) ) {
		// $url = trailingslashit( $stream ) . '/admin.cgi';
		// $url = add_query_arg( 'mode', 'viewxml', $url );
		// $url = add_query_arg( 'page', '7', $url );
	// }
	if ( !empty( $mount ) ) {
		$url = add_query_arg( 'sid', $mount, $url );
	}
	if ( !$payload ) {
		$payload = radio_station_pro_download_metadata( $url );
		if ( !$payload ) {
			return false;
		}
	}

	// --- extract data ---
	$xml = radio_station_pro_get_simple_xml( $payload );

	// --- set metadata ---
	$title = (string) $xml->SONGTITLE;
	$currentsong = radio_station_pro_metadata_current_song( $title );
	if ( $key && ( 'currentsong' == $key ) ) {
		return $currentsong;
	}
	// $listeners = new Listeners( (int)$xml->CURRENTLISTENERS, (int)$xml->UNIQUELISTENERS );
	$listeners = radio_station_pro_metadata_listeners( (int)$xml->CURRENTLISTENERS, (int)$xml->UNIQUELISTENERS );
	// if ( $includeClients && !empty( $this->adminPassword ) ) {
	//	$np->clients = $this->getClients($mount, true);
	//	$np->listeners = new Listeners(
	//		$np->listeners->current,
	//		count($np->clients)
	// 	);
	// }
	if ( $key && ( 'listeners' == $key ) ) {
		return $listeners;
	}
	// $meta = new Meta( !empty($np->currentSong->text), (int)$xml->BITRATE, (string)$xml->CONTENT );
	$meta = radio_station_pro_metadata_meta( !empty( $currentsong['text'] ), (int)$xml->BITRATE, (string)$xml->CONTENT );
	if ( $key && ( 'meta' == $key ) ) {
		return $meta;
	}

	// --- set metadata ---
	$metadata = array(
		'stream'      => $stream,
		'url'         => $url,
		'currentsong' => $currentsong,
		'listeners'   => $listeners,
		'meta'        => $meta,
		'type'        => 'shoutcast2',
	);

	// --- filter and return ---
	$metadata = apply_filters( 'radio_station_pro_metadata', $metadata, $stream, 'shoutcast2' );
	return $metadata;
}

// ------------------------
// ShoutCast 2 Current Song
// ------------------------
function radio_station_pro_shoutcast2_current_song( $stream, $metadata = false ) {
	$currentsong = radio_station_pro_shoutcast2_metadata( $stream, $metadata, 'currentsong' );
	return $currentsong;
}

// ---------------------
// ShoutCast 2 Listeners
// ---------------------
function radio_station_pro_shoutcast2_listeners( $stream, $metadata = false ) {
	$listeners = radio_station_pro_shoutcast1_metadata( $stream, $metadata, 'listeners' );
	return $listeners;
}

// ----------------
// ShoutCast 2 Meta
// ----------------
function radio_station_pro_shoutcast2_meta( $stream, $metadata = false ) {
	$meta = radio_station_pro_shoutcast1_metadata( $stream, $metadata, 'meta' );
	return $meta;
}


// ---------------
// === IceCast ===
// ---------------

// ----------------
// IceCast Metadata
// ----------------
// 2.4.1.6: set default mount number to zero instead of 1
function radio_station_pro_icecast_metadata( $stream, $metadata, $mount = 0, $key = false ) {

	// if ( !empty( $admin_password ) ) {
	// If the XML doesn't parse for any reason, fail back to the JSON below.
	//	try {$np = $this->getXmlNowPlaying( $mount );}
	//	catch (Exception $e) {}
	// }

	// if ( null === $np ) {
		// 2.3.4.6: added filter for alterniative mount index
		$mount = apply_filters( 'radio_station_stream_mount_index', $mount, $stream );
		$np = radio_station_pro_icecast_metadata_json( $stream, $metadata, $mount, $key );
	// }

	/* if ($includeClients && !empty($this->adminPassword)) {
		$np->clients = $this->getClients($mount, true);
		$np->listeners = new Listeners( $np->listeners->current, count($np->clients) );
	} */

	return $np;
}

// ---------------------
// IceCast Metadata JSON
// ---------------------
function radio_station_pro_icecast_metadata_json( $stream, $metadata, $mount = null, $key = false ) {

	// --- maybe use metadata override ---
	if ( !$metadata ) {
		// 2.4.1.8: fix to missing $ on variable causing constant error 
		$metadata = $stream;
	}

	// --- get metadata URL ---
	$payload = false;
	if ( 'status-json.xsl' == substr( $stream, -15, 15 ) ) {
		$url = $metadata;
	} elseif ( '/' == substr( $stream, -1, 1 ) ) {
		$url = $metadata . 'status-json.xml';
	} else {
		// 2.4.1.6: parse the stream URL to get correct path
		$pos = strrpos( trim( $metadata ), '/' ) + 1;
		$parts = str_split( $metadata, $pos );
		$url = $parts[0];
		$mountpath = $parts[1];
		if ( RADIO_STATION_DEBUG ) {
			echo "Mount Path: " . $mountpath . PHP_EOL;
		}
		$url = trailingslashit( $url ) . 'status-json.xsl';

		// 2,4.1.6: try URL variation
		$payload = radio_station_pro_download_metadata( $url );
		if ( !$payload ) {
			$url = trailingslashit( $metadata ) . 'status-json.xsl';
		}
	}

	// --- retrieve metadata ---
	if ( !$payload ) {
		$payload = radio_station_pro_download_metadata( $url );
		if ( !$payload ) {
			return false;
		}
	}

	// --- extract data ---
	// 2.4.1.6: remove 3rd and 4th argument as causing decode failure
	$return = json_decode( $payload, true ); // 512, JSON_THROW_ON_ERROR
	// if ( RADIO_STATION_DEBUG ) {
	//	echo PHP_EOL . "JSON DATA: " . PHP_EOL;
	//	print_r( $return );
	// }

	// --- check for data ---
	if ( !$return || !isset( $return['icestats']['source'] ) ) {
		// throw new Exception( sprintf( 'Invalid response: %s', $payload ) );
		// echo PHP_EOL . "No Sources found." . PHP_EOL;
		return false;
	}
	$sources = $return['icestats']['source'];
	$mounts = ( key( $sources ) === 0 ) ? $sources : array( $sources );
	if ( 0 === count( $mounts ) ) {
		// throw new Exception('Remote server has no mount points.');
		// echo PHP_EOL . "No Mounts found." . PHP_EOL;
		return false;
	}

	if ( RADIO_STATION_DEBUG ) {
		echo PHP_EOL . "Source Rows: " . PHP_EOL;
		print_r( $mounts );
	}

	// --- loop mount data ---
	$nps = array();
	foreach ( $mounts as $i => $row ) {

		$parse_url = parse_url( $row['listenurl'] );
		$mountname = $parse_url['path'];
		if ( '/' == substr( $mountname, 0, 1 ) ) {
			$mountname = substr( $mountname, 1 );
		}
		if ( RADIO_STATION_DEBUG ) {
			echo "Listen URL: " . $row['listenurl'] . PHP_EOL;
			echo "Mount name: " . $mountname . PHP_EOL;
		}

		$currently_playing = isset( $row['yp_currently_playing'] ) ? $row['yp_currently_playing'] : '';
		$title = isset( $row['title'] ) ? $row['title'] : '';
		$artist = isset( $row['artist'] ) ? $row['artist'] : '';
		$currentsong = radio_station_pro_metadata_current_song( $currently_playing, $title, $artist, ' - '	);
		// 2.4.1.6: fix to incorrect function names
		$listeners = radio_station_pro_metadata_listeners( $row['listeners'] );
		$meta = radio_station_pro_metadata_meta( !empty($currentsong['text']), $row['bitrate'], $row['server_type'] );

		$np = array(
			'stream'      => $row['listenurl'],
			'url'         => $url,
			'currentsong' => $currentsong,
			'listeners'   => $listeners,
			'meta'        => $meta,
			'type'        => 'icecast',
		);

		$nps[$mountname] = $np;
	}

	if ( RADIO_STATION_DEBUG ) {
		echo PHP_EOL . "Now Playing Mountpoints: " . PHP_EOL;
		print_r( $nps );
	}

	// 2.4.1.6: maybe return matching mountpath
	if ( isset( $mountpath ) && isset( $nps[$mountpath] ) ) {
		if ( RADIO_STATION_DEBUG ) {
			echo "Found Matching Mount Path: " . $mountpath . PHP_EOL;
			print_r( $nps[$mountpath] );
		}
		if ( $key && ( 'currentsong' == $key ) ) {
			return $nps[$mountpath]['currentsong'];
		}
		if ( $key && ( 'listeners' == $key ) ) {
			return $nps[$mountpath]['listeners'];
		}
		if ( $key && ( 'meta' == $key ) ) {
			return $nps[$mountpath]['meta'];
		}
		return $nps[$mountpath];
	}

	$i = 0;
	if ( RADIO_STATION_DEBUG ) {
		echo "Searching for Mount Index: " . $mount . PHP_EOL;
	}
	foreach ( $nps as $mountname => $np ) {
		// 2.4.1.6: maybe return matching mount index
		if ( ( !is_null( $mount ) ) && ( $i == $mount ) ) {
			if ( RADIO_STATION_DEBUG ) {
				echo "Found Mount Index: " . $mount . PHP_EOL;
			}
			if ( $key && ( 'currentsong' == $key ) ) {
				return $np['currentsong'];
			}
			if ( $key && ( 'listeners' == $key ) ) {
				return $np['listeners'];
			}
			if ( $key && ( 'meta' == $key ) ) {
				return $np['meta'];
			}
			return $np;
		}
		$i++;
	}

	return $nps;
}

// --------------------
// IceCast Metadata XML
// --------------------
/* note: unused as requires admin authentication
function radio_station_pro_icecast_now_playing_xml( $stream ) {

	// --- get data ---
	$url = trailingslashit( $stream ) . 'admin/stats';
	$payload = radio_station_pro_download_metadata( $url );
	if ( !$payload ) {
		return false;
	}

	// --- extract data ---
	$xml = radio_station_pro_get_simple_xml( $payload );

	$mountSelector = ( null !== $mount )
	? '(/icestats/source[@mount=\'' . $mount . '\'])[1]'
	: '(/icestats/source)[1]';

	$mount = $xml->xpath( $mountSelector );

	if ( empty( $mount ) ) {
		// return Result::blank();
		return false;
	}

	$row = $mount[0];

	$np = array();
	$artist = (string) $row->artist;
	$title = (string) $row->title;
	$currentsong = radio_station_pro_metadata_current_song( '', $artist, $title, ' - ' );
	if ( $key && ( 'currentsong' == $key ) ) {
		return $currentsong;
	}
	// $listeners = new Listeners( (int)$row->listeners );
	$listeners = radio_station_pro_metadata_listeners( (int)$row->listeners );
	if ( $key && ( 'listeners' == $key ) ) {
		return $listeners;
	}
	// $meta = new Meta( !empty($np->currentSong->text), (int)$row->bitrate, (string)$row->server_type );
	$meta = radio_station_pro_metadata_meta( !empty( $currentsong['text'] ), (int)$row->bitrate, (string)$row->server_type );
	if ( $key && ( 'meta' == $key ) ) {
		return $meta;
	}

	return $np;
}
*/


// ------------------------
// === Metadata Helpers ===
// ------------------------

// -----------------
// Download Metadata
// -----------------
function radio_station_pro_download_metadata( $url ) {

	// TODO: check for forwarded / redirected URLs ?
	// eg. follow location

	$response = wp_remote_get( $url );
	if ( RADIO_STATION_DEBUG ) {
		echo 'Retrieving Metadata URL: ' . $url . '<br>';
	}

	if ( is_wp_error( $response ) ) {

		// --- on wp error ---
		if ( RADIO_STATION_DEBUG ) {
			echo 'WP Response Error: ' . print_r( $response, true ) . '<br>';
		}
		return false;

	} elseif ( !isset( $response['body'] ) || empty( $response['body'] ) ) {

		// --- if there is no response body ---
		if ( RADIO_STATION_DEBUG ) {
			echo 'Error: Empty HTTP Response Body<br>';
		}
		return false;

	} else {

		// --- check response code ---
		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 != $code ) {
			if ( RADIO_STATION_DEBUG ) {
				echo 'Error: HTTP Response not 200 - Received ' . $code . '<br>';
			}
			return false;
		}

		if ( RADIO_STATION_DEBUG ) {
			echo 'HTTP Response Body: <br>';
			echo '<textarea cols="80" rows="5">';
			print_r( $response['body'] );
			echo '</textarea><br>';
		}

		return $response['body'];
	}
}

// ----------------
// Get Current Song
// ----------------
function radio_station_pro_metadata_current_song( $text = '', $title = '', $artist = '', $delimiter = ' - ' ) {

	if ( empty( $text ) ) {
		if ( !empty( $title ) && !empty( $artist ) ) {
			$text = $artist . $delimiter . $title;
		} elseif ( !empty( $title ) ) {
			if ( stristr( $title, ' by ' ) ) {
				// 2.4.1.6: add handling of alternative delimiter in title
				$text = str_replace( ' BY ', ' by ', $title );
				$text = str_replace( ' By ', ' by ', $title );
				$parts = explode( ' by ', $title );
				$title = $parts[0];
				unset( $parts[0] );
				$artist = implode( ' by ', $parts );
				$text = $title . $delimiter . $artist;
			} else {
				$text = $title;
			}
		} elseif ( !empty( $artist ) ) {
			$text = $artist;
		}
	}

	if ( !empty( $text ) && empty( $title ) && empty( $artist ) ) {

		// Fix ShoutCast 2 bug where 3 spaces = " - "
		$text = str_replace( '   ', $delimiter, $text );

		// Remove dashes or spaces on both sides of the name.
		// $text = trim( $text, " \t\n\r\0\x0B-" );

		// 2.4.1.6: fix logic check for delimiter
		if ( strstr( $text, $delimiter ) ) {
			$parts = explode( $delimiter, $text );
			$title = $parts[0];
			unset( $parts[0] );
			$artist = implode( $delimiter, $parts );
		} elseif ( stristr( $text, ' by ' ) ) {
			// 2.4.1.6: add handling of alternative delimiter
			$text = str_replace( ' BY ', ' by ', $text );
			$text = str_replace( ' By ', ' by ', $text );
			$parts = explode( ' by ', $text );
			$title = $parts[0];
			unset( $parts[0] );
			$artist = implode( ' by ', $parts );
		} else {
			// --- if no delimiter present ---
			$text = trim( $text, " \t\n\r\0\x0B-" );
			$title = $text;
		}
	}

	$text = trim( htmlspecialchars_decode( $text ) );
	$title = trim( htmlspecialchars_decode( $title ) );
	$artist = trim( htmlspecialchars_decode( $artist ) );

	$currentsong = array(
		'text'		=> $text,
		'title'		=> $title,
		'artist'	=> $artist,
	);
	return $currentsong;
}

// -------------
// Get Listeners
// -------------
function radio_station_pro_metadata_listeners( $current = 0, $unique = 0, $total = null ) {

	if ( null === $total ) {
		if ( $unique === 0 || $current === 0 ) {
			$total =  max( $unique, $current );
		} else {
			$total = min( $unique, $current );
		}
	}

	$listeners = array(
		'current' => $current,
		'unique'  => $unique,
		'total'   => $total,
	);
	return $listeners;
}

// ---------------
// Get Stream Meta
// ---------------
function radio_station_pro_metadata_meta( $online, $bitrate = '', $server = '' ) {

	$mets = array(
		'online'  => $online,
		'bitrate' => $bitrate,
		'server'  => $server,
	);

	return $meta;
}


// ---------------------
// Get Simple XML Helper
// ---------------------
function radio_station_pro_get_simple_xml( $xml_string ) {

	$xml_string = html_entity_decode( $xml_string );
	$xml_string = preg_replace( '/&(?!#?[a-z0-9]+;)/', '&amp;', $xml_string );

	libxml_use_internal_errors( true );
	$xml = simplexml_load_string( $xml_string );

	/* if ( false === $xml ) {
		$xml_errors = [];
		foreach ( libxml_get_errors() as $error ) {
			$xml_errors[] = $error->message;
		}
		libxml_clear_errors();

		throw new Exception('XML parsing errors: ' . implode(', ', $xml_errors));
	} */

	return $xml;
}

