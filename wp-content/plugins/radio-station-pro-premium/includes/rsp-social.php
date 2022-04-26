<?php

// =========================
// === Radio Station Pro ===
// =========================
// ------ Social Icons -----
// =========================
// - Get Social Icon Services
// - Get Social Icon for Service
// - Social Icons Inputs
// - Social Icon Edit Styles
// - Save Social Icons
// - Filter Social Icon Display
// - Display Social Icons


// --------------------
// === Social Icons ===
// --------------------

/* Social Icon Color Reference
ref: https://www.lockedownseo.com/social-media-colors/
$googleplus: #db4437;
$vimeoblue: #1ab7ea;
$tumblr: #2c4762;
$snapchat: #fffc00;
$tiktokblack: #010101; 
$tiktookblue: #69c9d0;
$tiktokpink: #ee1d52; 
$tiktokwhite: #fff; 
$mastodon: #2b90d9;
$apple: #a6b1b7;
$amazon: #ff9900;
$alexablue: #00a7ce;
$alexadkblue: #232f3e;
$microsoftred: #f35022;
$microsoftgreen: #80bb03;
$microsoftblue: #03a5f0;
$microsoftyellow: #ffb903;
$periscope: #40a4c4;
$foursquarepink: #f94877; 
$foursquarenavy: #073282;
$foursquareblue: #2d5be3; 
$yelp: #d32323;
$swarm: #ffa633;
$medium: #02b875;
$skypeblue: #00aff0;
$skypedkblue: #0078d7;
$android: #a4c639;
$stumbleupon: #e94826;
$flickrpink: #f40083;
$flickrblue: #006add;
$yahoo: #430297;
$dribbble: #ea4c89;
$slackpurple: #4a154b; 
$slackblue: #36c5f0; 
$slackgreen: #2eb67d; 
$slackred: #e01e5a; 
$slackyellow: #ecb22e; 
$deviantart: #05cc47;
$pocket: #ee4056;
$quora: #aa2200;
$quorablue: #2b6dad;
$slideshareorange: #e68523;
$slideshareblue: #00a0dc;
$fivehundredpx: #0099e5;
$vk: #4a76a8;
$listlyorange: #df6d46;
$listlyblue: #52b1b3;
$vine: #00b489;
$steam: #171a21;
$telegram: #0088cc;
$clarity: #61bed9;
$homeadvisor: #f89000;
$houzz: #4dbc15;
$angieslist: #29a036;
$glassdoor: #0caa41;
$reddit: #ff5700
*/


// ------------------------
// Get Social Icon Services
// ------------------------
function radio_station_pro_social_icon_services() {

	$services = array(

		// --- SVG Icons ---
		'soundcloud'     => array(
			'label'    => __( 'Soundcloud', 'radio-station' ),
			'color'    => '#ff5500',	
		),
		'mixcloud'       => array(
			'label'    => __( 'Mixcloud', 'radio-station' ),
			'color'    => '',
		),
		'spotify'	=> array(
			'label'    => __( 'Spotify', 'radio-station' ),
			// 'dashicon' => 'spotify',
			'color'    => '#1ed760',
		),
		'twitch'	=> array(
			'label'    => __( 'Twitch', 'radio-station' ),
			// 'dashicon' => 'twitch',
			'color'    => '#9146ff',
		),
		'discord'       => array(
			'label'    => __( 'Discord', 'radio-station' ),
			'color'    => '#7289da',
		),
		'facebook'	=> array(
			'label'    => __( 'Facebook', 'radio-station' ),
			// 'dashicon' => 'facebook-alt',
			'color'    => '#1877f2',
		),
		'twitter'	=> array(
			'label'    => __( 'Twitter', 'radio-station' ),
			// 'dashicon' => 'twitter',
			'color'    => '#1da1f2',
		),
		'youtube'	=> array(
			'label'    => __( 'YouTube', 'radio-station' ),
			// 'dashicon' => 'youtube',
			'color'    => '#ff0000',
		),
		'linkedin'	=> array(
			'label'    => __( 'LinkedIn', 'radio-station' ),
			// 'dashicon' => 'linkedin',
			'color'    => '#0a66c2',
		),
		'xing'		=> array(
			'label'    => __( 'Xing', 'radio-station' ),
			// 'dashicon' => 'xing',
			'color'    => '',
		),
		'instagram'	=> array(
			'label'    => __( 'Instagram', 'radio-station' ),
			// 'dashicon' => 'instagram',
			'color'    => '#c32aa3',
		),
		'whatsapp'	=> array(
			'label'    => __( 'WhatsApp', 'radio-station' ),
			// 'dashicon' => 'whatsapp',
			'color'    => '#25d366',
		),
		'pinterest'	=> array(
		 	'label'    => __( 'Pinterest', 'radio-station' ),
		 	// 'dashicon' => 'pinterest',
		 	'color'    => '#bd081c',
		),
		
		// 2.4.1.4: added custom icon option
		'custom'	=> array(
			'label'    => __( 'Custom', 'radio-station' ),
			'color'    => '#111111',
		),
	);

	// --- filter and return ---
	$services = apply_filters( 'radio_station_social_icons_services', $services );
	return $services;
}

// ---------------------------
// Get Social Icon for Service
// ---------------------------
function radio_station_pro_get_social_icon( $service, $icon_url = false ) {

	$html = '';
	$services = radio_station_pro_social_icon_services();

	// --- custom filtered icon URL ---
	$icon_url = apply_filters( 'radio_station_social_icon_url', $icon_url, $service );
	
	if ( !$icon_url || !strstr( $icon_url, '.' ) ) {

		// 2.4.1.4: maybe override service with icon URL slug
		if ( $icon_url && !strstr( $icon_url, '.' ) ) {
			$service = $icon_url;
			$icon_url = false;	
		}

		// 2.4.1.4: filter custom icon paths
		$icon_dir = apply_filters( 'radio_station_social_icon_dir', get_stylesheet_directory() . '/images/' );
		$icon_path = apply_filters( 'radio_station_social_icon_path', get_stylesheet_directory_uri() . '/images/' );
		if ( file_exists( $icon_dir . $service . '.svg' ) ) {
			// --- child theme SVG icon URL ---
			$icon_url = $icon_path . $service . '.svg';
		} elseif ( file_exists( $icon_dir . $service . '.png' ) ) {
			// --- child theme PNG icon URL ---
			$icon_url = $icon_path . $service . '.png';
		} elseif ( file_exists( RADIO_STATION_PRO_DIR . '/images/' . $service . '.svg' ) ) {
			// --- plugin icon URL ---
			$icon_url = plugins_url( 'images/' . $service . '.svg', RADIO_STATION_PRO_FILE );
		} elseif ( array_key_exists( $service, $services ) ) {
			if ( isset( $services[$service]['dashicon'] ) ) {
				// --- fallback to dashicon span ---
				$icon = $services[$service];
				$html = '<span class="dashicons dashicons-' . $icon['dashicon'] . '"  style="color:' . esc_attr( $icon['color'] ) . ';"></span>';
			}
		}
	}

	// --- set SVG icon image ---
	if ( $icon_url ) {
		$html = '<img src="' . esc_url( $icon_url ) . '" class="radio-social-icon-image">';
	}

	// --- filter and return ---	
	$html = apply_filters( 'radio_station_social_icon_output', $html, $service );
	return $html;
}

// -------------------
// Social Icons Inputs
// -------------------
add_action( 'radio_station_show_fields', 'radio_station_pro_social_icons_inputs', 10, 2 );
add_action( 'radio_station_host_fields', 'radio_station_pro_social_icons_inputs', 10, 2 );
add_action( 'radio_station_producer_fields', 'radio_station_pro_social_icons_inputs', 10, 2 );
// add_action( 'radio_station_episode_fields', 'radio_station_pro_social_icons_inputs', 10, 2 );
function radio_station_pro_social_icons_inputs( $post_id, $type ) {

	// --- start buffer for social icon overrides ---
	if ( 'override' == $type ) {
		return;
	}

	// --- get social icons services list ---
	$services = radio_station_pro_social_icon_services();

	// --- get existing social icons ---
	$icons = get_post_meta( $post_id, 'social_icons', true );

	// --- social icons heading ---
	$item_type = $type;
	if ( in_array( $type, array( 'host', 'producer' ) ) ) {
		$item_type = 'profile';
	}
	echo '<li class="' . esc_attr( $item_type ) . '-item input-list-item">' . PHP_EOL;
	echo '<div class="input-label">' . PHP_EOL;
	echo '<b>' . esc_html( __( 'Social Icons', 'radio-station' ) ) . '</b></div>' . PHP_EOL;

	// --- social icons nonce ---
	$social_icons_nonce = wp_create_nonce( 'social-icons-nonce' );
	echo '<input type="hidden" id="social-icons-nonce" name="social_icons_nonce" value="' . esc_attr( $social_icons_nonce ) . '">' . PHP_EOL;

	// --- set button titles ---
	$add_title = __( 'Add a Social Icon', 'radio-station' );
	$move_up_title = __( 'Move this Icon Up', 'radio-station' );
	$move_down_title = __( 'Move this Icon Down', 'radio-station' );
	$remove_title = __( 'Remove this Social Icon', 'radio-station' );
	$url_label = __( 'URL', 'radio-station' );
	$icon_label = __( 'Icon URL', 'radio-station' );

	// --- add social icon button ---
	echo '<div class="input-field">' . PHP_EOL;
	echo '<a href="javascript:void(0);" class="radio-social-add-button" onclick="radio_add_social_icon();" title="' . esc_attr( $add_title ) . '">';
	echo '<span class="dashicons dashicons-plus"></span></a></div><br>' . PHP_EOL;
	
	// --- existing social icon inputs ---
	echo '<ul id="social-icons-list">' . PHP_EOL;
	if ( $icons && is_array( $icons ) && ( count( $icons ) > 0 ) ) {
		foreach ( $icons as $i => $icon ) {

			$firstlast = '';
			if ( 0 == $i ) {
				$firstlast .= ' first-item';
			}
			if ( $i == ( count( $icons ) - 1 ) ) {
				$firstlast .= ' last-item';
			}

			echo '<li id="social-icon-' . esc_attr( $i ) . '" class="social-icon-item social-icon-current' . esc_attr( $firstlast ) . '">' . PHP_EOL;
			echo '<input id="social-icon-service-' . esc_attr( $i ) . '" type="hidden" name="social_icon[' . esc_attr( $i ) . '][service]" value="' . esc_attr( $icon['service'] ) . '">' . PHP_EOL;
			echo '<div class="social-icon-label">' . PHP_EOL;

				// --- service label ---
				if ( array_key_exists( $icon['service'], $services ) ) {
					$label = $services[$icon['service']]['label'];
				} else {
					$label = __( 'Unknown', 'radio-station' );
				}
				echo esc_html( $label );				

			echo '</div><div class="social-icon-url">' . PHP_EOL;

				// --- service URL ---
				echo esc_html( $url_label ) . ': <input id="social-icon-url-' . esc_attr( $i ) . '" type="text" name="social_icon[' . esc_attr( $i ) . '][url]" value="' . esc_url_raw( $icon['url'] ) . '" placeholder="' . esc_attr( __( 'Social URL Page Link', 'radio-station' ) ) . '"><br>' . PHP_EOL;
				
				// --- custom icon URL ---
				// 2.4.1.4: added for custom icon URLs
				$icon_url = isset( $icon['icon'] ) ? $icon['icon'] : '';
				echo '<div class="social-icon-icon"';
				if ( 'custom' != $icon['service'] ) {
					echo ' style="display: none;"';
				}
				echo '>' . esc_html( $icon_label ) . ': <input id="social-icon-icon-' . esc_attr( $i ) . '" type="text" name="social_icon[' . esc_attr( $i ) . '][icon]" value="' . esc_url_raw( $icon_url ) . '" placeholder="' . esc_attr( __( 'Social Icon Image URL', 'radio-station' ) ) . '">' . PHP_EOL;
				echo '</div>' . PHP_EOL;

			echo '</div><div class="social-icon-move">' . PHP_EOL;

				// --- move up/down ---
				echo '<a href="javascript:void(0);" id="social-icon-move-up-' . esc_attr( $i ) . '" class="social-icon-move-up" type="button" onclick="radio_icon_move(\'up\', ' . esc_attr( $i ) . ');">&#9652</a>' . PHP_EOL;
				echo '<a href="javascript:void(0);" id="social-icon-move-down-' . esc_attr( $i ) . '" class="social-icon-move-down" type="button" onclick="radio_icon_move(\'down\', ' . esc_attr( $i ) . ');">&#9662</a>' . PHP_EOL;

			echo '</div><div class="social-icon-remove">';

				// --- remove ---
				echo '<a href="javascript:void(0);" id="social-icon-remove-' . esc_attr( $i ) . '" type="button" onclick="radio_remove_social_icon(\'' . esc_attr( $i ) . '\');">' . PHP_EOL;
				echo '<span class="dashicons dashicons-no" title="' . esc_attr( $remove_title ) . '"></span></a>' . PHP_EOL;

			echo '</div></li>' . PHP_EOL;
		}
	}
	echo '</ul></li>' . PHP_EOL;

	// --- social icons script ---
	$js = "var social_icon_remove_message = '" . esc_js( __( 'Are you sure you want to remove this Social Icon?', 'radio-station' ) ) . "';
	var social_icon_url_placeholder = '" . esc_js( __( 'Social URL Page Link', 'radio-station' ) ) . "';
	var social_icon_icon_placeholder = '" . esc_js( __( 'Social Icon Image URL', 'radio-station' ) ) . "';
	var social_icon_no_services = '" . esc_js( __( 'No Social Icon Services defined.', 'radio-station' ) ) . "';
	var social_icon_reorder_title = '" . esc_js( __( 'You can reorder this icon after saving it.', 'radio-station' ) ) . "';
	var social_icon_url_label = '" . esc_html( $url_label ) . "';
	var social_icon_icon_label = '" . esc_html( $icon_label ) . "';" . PHP_EOL;
	
	// --- add social icon ---
	$js .= "function radio_add_social_icon() {

		new_icon_count = document.getElementsByClassName('social-icon-new').length + 1;

		item = '<li id=\"social-icon-new-'+new_icon_count+'\" class=\"social-icon-item social-icon-new\">';" . PHP_EOL;
		if ( count( $services ) > 0 ) {
			$js .= "item += '<div class=\"social-icon-label\">';
			item += '<select id=\"social-icon-new-service-'+new_icon_count+'\" name=\"social_icon_new['+new_icon_count+'][service]\" onchange=\"radio_check_social_select(new_icon_count);\">';" . PHP_EOL;
			foreach ( $services as $service => $values ) {
				$js .= "item += '<option value=\"" . esc_attr( $service ) . "\">" . esc_js( $values['label'] ) . "</option>';" . PHP_EOL;
			}
			$js .= "item += '</select></div>';
			item += '<div class=\"social-icon-url\">'+social_icon_url_label+': <input type=\"text\" id=\"social-icon-new-url-'+new_icon_count+'\" name=\"social_icon_new['+new_icon_count+'][url]\" value=\"\" placeholder=\"'+social_icon_url_placeholder+'\"><br>';
			item += '<div class=\"social-icon-icon\" style=\"display:none;\">'+social_icon_icon_label+': <input type=\"text\" id=\"social-icon-icon-url-'+new_icon_count+'\" name=\"social_icon_new['+new_icon_count+'][icon_url]\" value=\"\" placeholder=\"'+social_icon_icon_placeholder+'\"></div></div>';
			item += '<div class=\"social-icon-move\" title=\"'+social_icon_reorder_title+'\"><span class=\"social-icon-move-up\">&#9652</span> <span class=\"social-icon-move-down\">&#9662</span></div>';
			item += '<div class=\"social-icon-remove\"><a href=\"javascript:void(0);\" id=\"social-icon-new-remove-'+new_icon_count+'\" onclick=\"radio_remove_social_add(this);\" title=\"" . esc_js( $remove_title ) . "\">';
			item += '<span class=\"dashicons dashicons-no\"></span></a></div>';";
		} else {
			$js .= "item += '<div>'+social_icon_no_services+'</div>';";
		}
		$js .= "item += '</li>';
		jQuery('#social-icons-list').append(item);
	}" . PHP_EOL;

	// --- check social select ---
	// 2.4.1.4: added for extra custom image field display
	$js .= "function radio_check_social_select(i) {
		value = jQuery('#social-icon-new-service-'+i).val();
		if (value == 'custom') {display = 'block';} else {display = 'none';}
		jQuery('#social-icon-icon-url-'+i).parent().css('display',display);
	}" . PHP_EOL;

	// --- remove social icon ---
	$js .= "function radio_remove_social_icon(c) {
		agree = confirm(social_icon_remove_message);
		if (!agree) {return;}
		var icon_id = c;
		jQuery('#social-icon-'+c).remove();
		jQuery('.social-icon-current').each(function() {
			count = jQuery(this).attr('id').replace('social-icon-', '');
			if (count > icon_id) {
				newid = count - 1;
				jQuery('#social-icon-service-'+count).attr('name', 'social_icon['+newid+'][service]').attr('id', 'social-icon-service-'+newid);
				jQuery('#social-icon-url-'+count).attr('name', 'social_icon['+newid+'][url]').attr('id', 'social-icon-url-'+newid);
				jQuery('#social-icon-move-up-'+count).attr('onclick', 'radio_icon_move(\"up\",\"'+newid+'\");').attr('id', 'social-icon-move-up-'+newid);
				jQuery('#social-icon-move-down-'+count).attr('onclick', 'radio_icon_move(\"down\",\"'+newid+'\");').attr('id', 'social-icon-move-down-'+newid);
				jQuery('#social-icon-remove-'+count).attr('onclick', 'radio_icon_remove(\"'+newid+'\");').attr('id', 'social-icon-remove-'+newid);
				jQuery(this).attr('id', 'social-icon-'+newid);
			}
		});
		radio_social_first_last();
	}" . PHP_EOL;

	// --- remove new social icon ---
	$js .= "function radio_remove_social_add(el) {
		row = jQuery(el).parent().parent().remove();
		newicons = jQuery('.social-icon-new').length;
		var newiconcount = 0;
		jQuery('.social-icon-new').each(function() {
			count = jQuery(this).attr('id').replace('social-icon-new-','');
			jQuery(this).attr('id', 'social-icon-new-'+newiconcount);
			jQuery('#social-icon-new-service-'+count).attr('name', 'social_icon_new['+newiconcount+'][service]').attr('id', 'social-icon-new-service-'+newiconcount);
			jQuery('#social-icon-new-url-'+count).attr('name', 'social_icon_new['+newiconcount+'][url]').attr('id', 'social-icon-new-url-'+newiconcount);
			newiconcount++;
		});
	}" . PHP_EOL;
	
	// --- move social icon ---
	// 2.4.1.4: fix to up/down swapping IDs
	$js .= "function radio_icon_move(updown, count) {
		currenticons = jQuery('#social-icons-list .social-icon-current').length;
		if ((updown == 'up') && (count == 0)) {return;}
		if ((updown == 'down') && (count == currenticons)) {return;}
		if (updown == 'up') {swapwith = parseInt(count) - 1;}
		else if (updown == 'down') {swapwith = parseInt(count) + 1;}
		console.log(swapwith+' - '+count);

		if (updown == 'up') {
			jQuery('#social-icon-'+count).insertBefore('#social-icon-'+swapwith);
		} else if (updown == 'down') {			
			jQuery('#social-icon-'+swapwith).insertBefore('#social-icon-'+count);
		}

		jQuery('#social-icon-'+count).attr('id', 'social-icon-temp-'+count);
		jQuery('#social-icon-service-'+count).attr('id', 'social-icon-service-temp-'+count);
		jQuery('#social-icon-url-'+count).attr('id', 'social-icon-url-temp-'+count);
		jQuery('#social-icon-icon-'+count).attr('id', 'social-icon-icon-temp-'+count);
		jQuery('#social-icon-move-up-'+count).attr('id', 'social-icon-move-up-temp-'+count);
		jQuery('#social-icon-move-down-'+count).attr('id', 'social-icon-move-down-temp-'+count);
		jQuery('#social-icon-remove-'+count).attr('id', 'social-icon-remove-temp-'+count);
		
		jQuery('#social-icon-'+swapwith).attr('id', 'social-icon-'+count);
		jQuery('#social-icon-service-'+swapwith).attr('id', 'social-icon-service-'+count).attr('name', 'social_icon['+count+'][service]');
		jQuery('#social-icon-url-'+swapwith).attr('id', 'social-icon-url-'+count).attr('name', 'social_icon['+count+'][url]');
		jQuery('#social-icon-icon-'+swapwith).attr('id', 'social-icon-icon-'+count).attr('name', 'social_icon['+count+'][icon]');
		jQuery('#social-icon-move-up-'+swapwith).attr('id', 'social-icon-move-up-'+count).attr('onclick', 'radio_icon_move(\"up\",\"'+count+'\");');
		jQuery('#social-icon-move-down-'+swapwith).attr('id', 'social-icon-move-down-'+count).attr('onclick', 'radio_icon_move(\"down\",\"'+count+'\");');
		jQuery('#social-icon-remove-'+swapwith).attr('id', 'social-icon-remove-'+count).attr('onclick', 'radio_icon_remove(\"'+count+'\");');

		jQuery('#social-icon-temp-'+count).attr('id', 'social-icon-'+swapwith);
		jQuery('#social-icon-service-temp-'+count).attr('id', 'social-icon-service-'+swapwith).attr('name', 'social_icon['+swapwith+'][service]');
		jQuery('#social-icon-url-temp-'+count).attr('id', 'social-icon-url-'+swapwith).attr('name', 'social_icon['+swapwith+'][url]');
		jQuery('#social-icon-icon-temp-'+count).attr('id', 'social-icon-icon-'+swapwith).attr('name', 'social_icon['+swapwith+'][icon]');
		jQuery('#social-icon-move-up-temp-'+count).attr('id', 'social-icon-move-up-'+swapwith).attr('onclick', 'radio_icon_move(\"up\",\"'+swapwith+'\");');
		jQuery('#social-icon-move-down-temp-'+count).attr('id', 'social-icon-move-down-'+swapwith).attr('onclick', 'radio_icon_move(\"down\",\"'+swapwith+'\");');
		jQuery('#social-icon-remove-temp-'+count).attr('id', 'social-icon-remove-'+swapwith).attr('onclick', 'radio_icon_remove(\"'+swapwith+'\");');

		radio_social_first_last();
	}"  . PHP_EOL;

	// --- recalculate first/last item ---
	$js .= "function radio_social_first_last() {
		jQuery('.social-icon-current').removeClass('first-item').removeClass('last-item');
		jQuery('.social-icon-current').first().addClass('first-item');
		jQuery('.social-icon-current').last().addClass('last-item');
	}" . PHP_EOL;

	// --- filter and enqueue script ---
	$js = apply_filters( 'radio_station_pro_social_icon_script', $js );
	wp_add_inline_script( 'radio-station-admin', $js );
	
	// --- enqueue edit styles ---
	add_action( 'admin_footer', 'radio_station_pro_social_icon_edit_styles' );

}

// -----------------------
// Social Icon Edit Styles
// -----------------------
function radio_station_pro_social_icon_edit_styles() {

	// --- social icon edit styles ---
	// 2.4.1.4: added explicit list item style for consistency
	$css = '.input-list-item {list-style: none; margin-top: 20px;}
	.radio-social-add-button {text-decoration: none;}
	.radio-social-add-button span {opacity: 0.8;}
	.radio-social-add-button span:hover {opacity: 1;}
	.social-icon-label {width: 120px;}
	.social-icon-label, .social-icon-url, .social-icon-move, .social-icon-remove {display: inline-block; vertical-align: top;}
	.social-icon-url, .social-icon-move, .social-icon-remove {margin-left: 20px;}
	.social-icon-url input {width: 240px;}
	.social-icon-icon input {width: 210px;}
	.social-icon-new .social-icon-move {color: #CCC;}
	.social-icon-move, .social-icon-move a {text-decoration: none; font-size: 28px;}
	.first-item .social-icon-move-up, .last-item .social-icon-move-down {display: none;}
	.first-item .social-icon-move-down {margin-left: 24px;}
	.last-item .social-icon-move-up {margin-right: 24px;}
	.social-icon-remove a {text-decoration: none; opacity: 0.8;}
	.social-icon-remove a:hover {opacity: 1;}' . PHP_EOL;

	$css = apply_filters( 'radio_station_social_icon_edit_styles', $css );
	echo '<style>' . $css . '</style>';

}

// -----------------
// Save Social Icons
// -----------------
add_action( 'save_post', 'radio_station_pro_social_icons_save' );
add_action( 'publish_post', 'radio_station_pro_social_icons_save' );
function radio_station_pro_social_icons_save( $post_id ) {

	// --- check post type ---
	$post = get_post( $post_id );
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG ); // RADIO_STATION_EPISODE_SLUG
	if ( !in_array( $post->post_type, $post_types ) ) {
		return;
	}

	// --- verify nonce value ---
	if ( !isset( $_REQUEST['social_icons_nonce'] ) || !wp_verify_nonce( $_REQUEST['social_icons_nonce'], 'social-icons-nonce' ) ) {
		return;
	}

	// --- get social icon services ---
	$services = radio_station_pro_social_icon_services();

	// --- get posted social icon data ---
	$social_icons = array();
	if ( isset( $_POST['social_icon'] ) ) {
		$icons = $_POST['social_icon'];
		foreach ( $icons as $i => $icon ) {
			if ( array_key_exists( $icon['service'], $services ) ) {
				// --- filter sanitize URL ---
				$icon['url'] = filter_var( trim( $icon['url'] ), FILTER_SANITIZE_URL );
				$social_icons[] = $icon;
			}
		}
	}

	// --- append newly added social icons ---
	if ( isset( $_POST['social_icon_new'] ) ) {
		$new_icons = $_POST['social_icon_new'];
		foreach ( $new_icons as $i => $icon ) {
			if ( array_key_exists( $icon['service'], $services ) ) {
				// --- filter sanitize URL ---
				$icon['url'] = filter_var( trim( $icon['url'] ), FILTER_SANITIZE_URL );
				// 2.4.1.4: save custom icon URL
				if ( 'custom' == $service ) {
					$icon['icon'] = filter_var( trim( $icon['icon'] ), FILTER_SANITIZE_URL );
				}
				$social_icons[] = $icon;
			}
		}
	}

	// --- update social icons post meta ---
	update_post_meta( $post_id, 'social_icons', $social_icons );

}

// --------------------------
// Filter Social Icon Display
// --------------------------
add_filter( 'radio_station_show_social_icons', 'radio_station_pro_social_icons', 10, 2 );
add_filter( 'radio_station_host_social_icons', 'radio_station_pro_social_icons', 10, 2 );
add_filter( 'radio_station_producer_social_icons', 'radio_station_pro_social_icons', 10, 2 );
// add_filter( 'radio_station_episode_social_icons', 'radio_station_pro_social_icons', 10, 2 );
function radio_station_pro_social_icons( $social_icons, $post_id ) {
	// --- check for icon meta data ---
	$icons = get_post_meta( $post_id, 'social_icons', true );
	if ( $icons && is_array( $icons ) && ( count( $icons ) > 0 ) ) {
		return true;
	}
	return $social_icons;
}

// --------------------
// Display Social Icons
// --------------------
// 2.4.1.7: fix add actions to filters
add_filter( 'radio_station_show_social_icons_display', 'radio_station_pro_social_icons_display' );
add_filter( 'radio_station_host_social_icons_display', 'radio_station_pro_social_icons_display' );
add_filter( 'radio_station_producer_social_icons_display', 'radio_station_pro_social_icons_display' );
// add_filter( 'radio_station_episode_social_icons_display', 'radio_station_pro_social_icons_display' );
function radio_station_pro_social_icons_display( $icons, $post_id = null ) {

	// -- check post type ---
	// 2.4.1.7: allow for post to be passed for archives
	if ( is_null( $post_id ) ) {
		global $post;
		$post_id = $post->ID;
	} else {
		$post = get_post( $post_id );
	}
	$type = str_replace( 'rs-', '', $post->post_type );

	// --- get icon meta data ---
	$icons = get_post_meta( $post_id, 'social_icons', true );
	if ( !$icons || !is_array( $icons ) || ( count( $icons ) < 1 ) ) {
		return;
	}
	if ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) {
		echo '<span style="display:none;">Social Icons: ' . print_r( $icons, true ) . '</span>';
	}

	// --- generate social icons output ---
	$html = '<ul id="radio-' . esc_attr( $type ) . '-social-icons" class="radio-social-icons">';
	foreach ( $icons as $i => $icon ) {

		$html .= '<li class="radio-social-item';
		if ( 0 == $i ) {
			$html .= ' first-item';
		} elseif ( $i == ( count( $icons ) - 1 ) ) {
			$html .= ' last-item';
		}
		$html .= '">';
		$html .= '<a href="' . esc_url_raw( $icon['url'] ) . '" target="_blank" rel="nofollow">';
		$html .= '<div class="radio-social-icon radio-social-icon-' . esc_attr( $icon['service'] ) . '">';
		// 2.4.1.4: allow for custom icon URL
		if ( isset( $icon['icon'] ) ) {
			$html .= radio_station_pro_get_social_icon( $icon['service'], $icon['icon'] );
		} else {
			$html .= radio_station_pro_get_social_icon( $icon['service'] );
		}
		$html .= '</div></a></li>' . PHP_EOL;

	}
	$html .= '</ul>' . PHP_EOL;

	// --- maybe enqueue dashicons styles ---
	if ( strstr( $html, 'dashicon' ) ) {
		wp_enqueue_style( 'dashicons' );
	}

	// --- add social icon styles ---
	$css = radio_station_pro_social_icon_styles( $post_id, $type );
	$html .= '<style>' . $css . '</style>';
	
	// --- filter and return ---
	$html = apply_filters( 'radio_station_social_icons_output', $html, $post_id, $type );
	return $html;
}

// ------------------
// Social Icon Styles
// ------------------
function radio_station_pro_social_icon_styles( $post_id, $type ) {

	// --- social icon styles ---
	// 2.4.1.8: add style for archive display
	$css = '.radio-social-icons, .radio-social-item {list-style: none; padding: 0; margin: 0;}
	.radio-social-item {display: inline-block; margin-right: 12px; margin-bottom: 12px;}
	.archive-item .radio-social-item {margin-bottom: 0;}
	.radio-social-item.last-item {margin-right: 0;}
	.radio-social-icon a {text-decoration: none;}
	.radio-social-icon {opacity: 0.8;}
	.radio-social-icon:hover {opacity: 1;}
	.radio-social-icon-image {border: 0; width: 32px; height: 32px;}
	.radio-social-icon span {width: 32px; height: 32px;}' . PHP_EOL;

	// --- filter and return ---
	$css = apply_filters( 'radio_station_social_icon_styles', $css, $post_id, $type );
	return $css;
}

// -------------------------
// Social Icon Styles Output
// -------------------------
// 2.4.1.7: added for shortcode archive displays
function radio_station_pro_social_icon_styles_output() {
	global $post, $radio_station_data;
	$post_id = isset( $post ) ? $post->ID : null;
	$type = $radio_station_data['archive_type'];
	$css = radio_station_pro_social_icon_styles( $post_id, $type );
	if ( '' != $css ) {
		echo '<style>' . $css . '</style>' . PHP_EOL;
	}
	if (RADIO_STATION_DEBUG) {echo '<span>MARKER</span>';}
}

