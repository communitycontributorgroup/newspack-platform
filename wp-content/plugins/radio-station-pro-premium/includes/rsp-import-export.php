<?php
/*
 * Import/Export Show data from/to YAML file
 * Author: Andrew DePaula
 * (c) Copyright 2020
 * Licence: GPL3
 */

// === Helper Functions ===
// - Delete Folder
// - Get Image Path
// - Get Show Users Email List
// - Get Show Producer Email List
// - Get Users IDs by Email
// - Validate Image URL or ID
// - Is Associative Array
// - Get Maximum Upload Size
// - Parse File Size
// - Get Readable File Size


require_once RADIO_STATION_DIR . '/vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;


//FIXME debug code
// error_log("t ". print_r($post_id,true)."\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)

//collection of useful template code.
//call __success if import is successful
	// $yaml_import_message = __( 'this is a success message', 'radio-station' );
	// add_action('admin_notices', 'radio_station_yaml_import__success');

//call __failure if we have a problem
	// $yaml_import_message = __( 'this is a failure message', 'radio-station' );
	// add_action('admin_notices', 'radio_station_yaml_import__failure');

// wp_safe_redirect( admin_url( 'admin.php?page=import-export-shows' ) ); exit;
// error_log("YAML file uploaded but not parsed.\n", 3, "/tmp/my-errors.log"); //FIXME debugging code


// -----------------------------------------
// Import (and replace) all show data (YAML)
// -----------------------------------------
// this function handles the response to the post requests from the page's form submissions
add_action( 'admin_init', 'radio_station_process_show_data_import' );
function radio_station_process_show_data_import() {

	// using a global variable since that seems to be the only easy way to
	// get a parameter to an add_action() callback function
	global $yaml_import_message;
	global $yaml_parse_errors;
	$yaml_parse_errors = '';
	$yaml_import_message = '';

	// error_log("POST DATA\n". print_r($_POST, true) . "\n", 3, "/tmp/my-errors.log"); //FIXME debugging code
	// return;
	
	// --- but out if action is not set ---
	if ( !isset( $_POST['action'] ) ) {
		return;
	}

	switch ( $_POST['action'] ) {
		case 'radio_station_yaml_import_action':
		radio_station_import_helper();
	break;
		case 'radio_station_yaml_export_action':
		radio_station_export_helper();
	break;
		default:
		//do nothing.
		return;
	}
}

 // ----------------------
 // Import Helper Function
 // ----------------------
 function radio_station_import_helper() {
 
	global $yaml_import_message;
	global $yaml_parse_errors;
	if( ! wp_verify_nonce( $_POST['yaml_import_nonce'], 'yaml_import_nonce' ) ) {
		return;
	}
	if( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$extension = strtolower( end( explode( '.', $_FILES['import_file']['name'] ) ) );
	// error_log("POST DATA\n>>". print_r($extension, true) . "<<\n", 3, "/tmp/my-errors.log"); // FIXME debugging code

  	if ( $extension != 'yaml' && $extension != 'yml' ) {
		// call __failure if we have a problem
		$yaml_import_message = __( 'Please upload a valid YAML file.', 'radio-station' );
		add_action( 'admin_notices', 'radio_station_yaml_import__failure' );
		// wp_die( __( 'Please upload a valid YAML file' ) );
		return;
  	}

	$import_file = $_FILES['import_file']['tmp_name'];
	if( empty( $import_file ) ) {
		// call __failure if we have a problem
		$yaml_import_message = __( 'Please upload a file to import.', 'radio-station' );
		add_action( 'admin_notices', 'radio_station_yaml_import__failure' );
		return;
		// wp_die( __( 'Please upload a file to import' ) );
	}

	// fetch the fate of the existing show data (delete existing show data checkbox state)
	$existing_data_fate = filter_var( $_POST['delete_show_data'], FILTER_VALIDATE_BOOLEAN );

  	// parse and save the yaml file if possible, returning success or failure messages to the user as appropriate
  	if ( radio_station_yaml_import_ok( $import_file, $existing_data_fate ) ) {
		// $globals $yaml_import_message, and $yaml_parse_errors are empty
		if ( $existing_data_fate ) {
			$yaml_import_message = __( 'Successfully parsed and imported YAML file, deleting pre-existing show data.', 'radio-station' );
		} else {
			$yaml_import_message = __( 'Successfully parsed and imported YAML file. Pre-existing show data remains unchanged.', 'radio-station' );
		}
  		add_action( 'admin_notices', 'radio_station_yaml_import__success' );
  	} else {
  		//global $yaml_import_message contins message to display to the user
  		//global $yaml_parse_errors contains the detail for display by import-export-shows.php
  		add_action( 'admin_notices', 'radio_station_yaml_import__failure' );
  	}
 }

// ----------------------
// Export Helper Function
// ----------------------
function radio_station_export_helper() {
 
	global $yaml_import_message;
	global $yaml_parse_errors;
	
	// error_log("POST DATA\n". print_r($_POST, true) . "\n", 3, "/tmp/my-errors.log"); //FIXME debugging code
	// return;
	if( ! wp_verify_nonce( $_POST['yaml_export_nonce'], 'yaml_export_nonce' ) ) {
		return;
	}
	if( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$image_url = filter_var( $_POST['image_prefix_url'], FILTER_VALIDATE_URL );
	$upload_dir = wp_upload_dir();
	$base_url = $upload_dir['baseurl'];
	$base_dir = $upload_dir['basedir'];

	if ( $_POST['export_file_name'] ) {
		$export_filename = sanitize_file_name( $_POST['export_file_name'] );
	} else {
		// create file name based on date/time
		$file_date = new DateTime();
		// $result = $date->format('Y-m-d H:i:s');
		$export_filename = $file_date->format( 'Y-m-d-' ) . time() . '_show_data.yaml';
	}

	// create the zip files that the user can download
	$image_index = radio_station_create_show_image_archive();

	// create show_data.yaml
	$yaml_data = radio_station_get_published_shows( $image_url, $image_index );
	$yaml = Yaml::dump( $yaml_data );
	$yaml = "---\n#Show data file (YAML format)\n\n" . $yaml . "...\n";
	file_put_contents( $base_dir . '/show_data.yaml', $yaml );

	$zip_size = radio_station_convert_filesize( filesize( $base_dir . "/show_images.zip" ), 1);
	$tgz_size = radio_station_convert_filesize( filesize( $base_dir . "/show_images.tgz" ), 1);

	// set up links for user to download the files
	$yaml_import_message = __( 'Export successful. Use the following link(s) to download your data.', 'radio-station' );
	if ( $image_index ){
		// provide image archive links if there were images to export
		$yaml_import_message .= "
		  <ul style=\"padding-left: 10px;\">
			<li><a href=\"" . $base_url . "/show_data.yaml\" download=\"" . $export_filename . "\">" . __( 'Data file', 'radio-station' ) . "</a></li>
			<li><a href=\"" . $base_url . "/show_images.zip\" download>" . __( 'Image zip file', 'radio-statoin' ) . "</a> <small>(" . $zip_size . ")</small></li>
			<li><a href=\"" . $base_url . "/show_images.tgz\" download>" . __( 'Image tgz file', 'radio-station' ) . "</a> <small>(" . $tgz_size . ")</small></li>
		  </ul>
	 	";
		$yaml_import_message .= __( 'Download one of the image files and the data file. See help for details on how to stage an import including images.', 'radio-station' );
	} else {
		$yaml_import_message .= "
		  <ul style=\"padding-left: 10px;\">
			<li><a href=\"" . $base_url . "/show_data.yaml\" download=\"" . $export_filename . "\">" . __( 'Show data file (YAML)', 'radio-station' ) . "</a></li>
		  </ul>
		";
		$yaml_import_message .= __( 'Images <strong>not</strong> exported. See help for details on how to include images.', 'radio-station' );
	}

	// queue up messaging and return
	add_action( 'admin_notices', 'radio_station_yaml_import__success' );
 }
 
 // --------------------------
 // Get Published Shows Helper
 // --------------------------
 // returns an array of all published shows
 function radio_station_get_published_shows( $image_location = false, $image_index ){
	$parameters = array(
		'posts_per_page' => -1,
		'post_type'      => RADIO_STATION_SHOW_SLUG,
		'post_status'    => 'publish',
	);

	$shows = get_posts( $parameters );
	$yaml_shows = array();
	foreach ( $shows as $show ) {

		// fetch all the metadata for this show
		$metadata = get_post_meta( $show->ID );
		
		// error_log("POST METADATA---------\n". print_r($metadata,true)."\n", 3, "/tmp/my-errors.log"); //code to write a line to wp-content/debug.log (works)
		//build an array matching the YAML structure we wish to have
		//these fields are core post fields
		$yaml_show = array();
		$yaml_show['show-title'] = $show->post_title;
		$yaml_show['show-description'] = $show->post_content;
		$yaml_show['show-excerpt'] = $show->post_excerpt;
		$yaml_show['show-schedule'] = radio_station_show_schedule( $show->ID );
		$yaml_show['show-user-list'] = radio_station_get_show_users( $show->ID );
		$yaml_show['show-producer-list'] = radio_station_get_show_producers( $show->ID );

		//these fields are metadata
		$yaml_show['show-tagline'] = $metadata['show_tagline'][0];
		$yaml_show['show-url'] = $metadata['show_link'][0];
		if ( $metadata['show_podcast'][0] == '' ){
			$yaml_show['show-podcast'] = null;
		} else {
			$yaml_show['show-podcast'] = $metadata['show_podcast'][0];
		}
		$yaml_show['show-email'] = $metadata['show_email'][0];
		$yaml_show['show-active'] = $metadata['show_active'][0];
		$yaml_show['show-patreon'] = $metadata['show_patreon'][0];

		// these fields are image related. Populate according to 'upload-images' setting
		if ( $image_location ) {
			$yaml_show['show-image'] = $image_location . "/" . $image_index[$metadata['show_image_id'][0]]['file'];
			$yaml_show['show-avatar'] = $image_location . "/" . $image_index[$metadata['show_avatar'][0]]['file'];
			$yaml_show['show-header'] = $image_location . "/" . $image_index[$metadata['show_header'][0]]['file'];
			$yaml_show['upload-images'] = 'yes';
		} else {
			$yaml_show['show-image'] = null;
			$yaml_show['show-avatar'] = null;
			$yaml_show['show-header'] = null;
			$yaml_show['upload-images'] = 'no';
		}

		array_push( $yaml_shows, $yaml_show );
	}

	return $yaml_shows;
}

// ----------------------
// Import Success Message
// ----------------------
// Display success admin message
function radio_station_yaml_import__success() {
	global $yaml_import_message;
 	echo '<div class="notice notice-success is-dismissible">' . PHP_EOL;
	echo '<p>' . $yaml_import_message . '</p>'. PHP_EOL;
	echo '</div>'. PHP_EOL;
}

// ----------------------
// Import Failure Message
// ----------------------
// Display failure admin message
function radio_station_yaml_import__failure( $msg ) {
	global $yaml_import_message;
	echo '<div class="notice notice-error is-dismissible">';
		echo '<p>' . $yaml_import_message . '</p>';
	echo '</div>';
}

// -----------
// Import Data
// -----------
// this function handles processing data from a YAML file and writing it to the DB
function radio_station_yaml_import_ok( $file_name = '', $delete_existing = false ){

	global $yaml_import_message;
	global $yaml_parse_errors;

	// try importing the YAML file
	try {
		$shows = Yaml::parseFile( $file_name );
	} catch ( ParseException $exception ) {
		$yaml_parse_errors = $exception->getMessage();
		$yaml_import_message = __( 'YAML import error. See below for details.', 'radio-station' );
		return false;
	}

	// Base import worked, proceed with import if show data validates
	$return_value = true;
	foreach ( $shows as $show ) {

		$sanitized_show = array();
		if ( radio_station_show_is_valid( $show, $sanitized_show ) ) {

			//FIXME re-factor the code to insert an "are you sure" dialogue step prior to actually doing the import

			if ( $delete_existing ) {
				// remove all existing show data prior to import if requested
				radio_station_delete_show_data();
			}

			//convert the show schedule metadata to the legacy format
			$converted_show_schedule = radio_station_convert_show_schedule( $sanitized_show['show-schedule'] );

			// retrieve the show's producer and user IDs from the DB
			$show_users = radio_station_convert_user_list( $sanitized_show['show-user-list'] );
			$show_producers = radio_station_convert_user_list( $sanitized_show['show-producer-list'] );

			// format show image url's for inclusion if supplied
			$image_urls = array();
			if ( !is_null($sanitized_show['show-image'] ) ) {
				$image_urls['show_image_url'] = $sanitized_show['show-image']; 
			}
			if ( !is_null( $sanitized_show['show-avatar'] ) ) {
				$image_urls['avatar_image_url'] = $sanitized_show['show-avatar'];
				if ( $sanitized_show['upload-images'] ) {
					// upload avatar image here so we have its ID for the metadata below
					$image_urls['show_avatar'] = radio_station_upload_image( $sanitized_show['show-avatar'], null );
				}
			}
			if ( !is_null($sanitized_show['show-header'] ) ) {
				$image_urls['header_image_url'] = $sanitized_show['show-header'];
				if ( $sanitized_show['upload-images'] ) {
					$image_urls['show_header'] = radio_station_upload_image( $sanitized_show['show-header'], null );
				}
			}
			$post_data = array(
				'post_title'   => $sanitized_show['show-title'], //show-title
				'post_content' => $sanitized_show['show-description'], //show-desciption
				'post_excerpt' => $sanitized_show['show-excerpt'], //show-excerpt
				'post_status'  => 'publish',
				'post_type'    => 'show',
				'meta_input' => array(
					// all the post meta data that doesn't require special handling
					'imported_on'        => date("D M d, Y G:i ") . "UTC",
					'show_tagline'       => $sanitized_show['show-tagline'],
					'show_link'          => $sanitized_show['show-url'],
					'show_podcast'       => $sanitized_show['show-podcast'],
					'show_email'         => $sanitized_show['show-email'],
					'show_active'        => $sanitized_show['show-active'],
					'show_patreon'       => $sanitized_show['show-patreon'],
					'show_sched'         => $converted_show_schedule,
					'show_user_list'     => $show_users,
					'show_producer_list' => $show_producers,
					'show_image_id'      => ''
				),
			);
			$post_data['meta_input'] = array_merge( $post_data['meta_input'], $image_urls );
			
			// insert the new show into the database
			$new_show = wp_insert_post( $post_data );

			// upload show-image & show-header if show inserted correctly and upload of images is called for
			if ( $sanitized_show['upload-images'] && is_int( $new_show ) && $new_show > 0 ) {
				$image_id = radio_station_upload_image( $sanitized_show['show-image'], $new_show );
				// store the image ID so we can use it later
				update_post_meta( $new_show, 'show_image_id', $image_id ); 
			}
		} else {
			$return_value = false;
			// errors are accumulated in the global $yaml_import_message, for display to the user
		}
	}

	return $return_value;
}

// ------------
// Import Image
// ------------
// this function uploads an image to the media library and adds it as the featured image of a post if $post_id is supplied
function radio_station_upload_image( $image_url, $post_id = null ) {

	$image_name       = basename( parse_url( $image_url, PHP_URL_PATH ) );
	$upload_dir       = wp_upload_dir(); // Set upload folder
	$image_data       = file_get_contents($image_url); // Get image data
	$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
	$filename         = basename( $unique_file_name ); // Create image file name

	// Check folder permission and define file location
	if( wp_mkdir_p( $upload_dir['path'] ) ) {
		$file = $upload_dir['path'] . '/' . $filename;
	} else {
		$file = $upload_dir['basedir'] . '/' . $filename;
	}

	// Create the image  file on the server
	file_put_contents( $file, $image_data );

	// Check image file type
	$wp_filetype = wp_check_filetype( $filename, null );

	// Set attachment data
	$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => sanitize_file_name( $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	// Create the attachment
	$attach_id = wp_insert_attachment( $attachment, $file);

	// Include image.php
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	// Define attachment metadata
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

	// Assign metadata to attachment
	wp_update_attachment_metadata( $attach_id, $attach_data );

	// And finally assign featured image to the post if id has been supplied
	if ( $post_id ) {
		set_post_thumbnail( $post_id, $attach_id );
	}

	return $attach_id;
}

// -------------------------
// Create Show Image Archive
// -------------------------
//this function creates an archive of all the images associated with all published shows defined in the system.
function radio_station_create_show_image_archive(){

	//Three files are created in the WP uploads directory: show_images.tgz, show_images.zip, and show_images.yaml
	//The images .tgz and .zip files contain the actual image files. The .yaml file contains the file names, and size
	//information needed to render the data to the user. If previous files are found, they are replaced.
	global $yaml_parse_errors;
	global $yaml_import_message;

	$parameters = array(
		'posts_per_page'     => -1,
		'post_type'          => 'show',
		'post_status'        => 'publish'
	);
	$shows = get_posts( $parameters );
	$upload_dir = wp_upload_dir();
	$base_dir = $upload_dir['basedir'];
	$image_index = array();

	// remove the previous files if they exist
	radio_station_delete_folder( $base_dir . '/export' );
	unlink( $base_dir . '/show_images.yaml' );
	unlink( $base_dir . '/show_images.tgz' );
	unlink( $base_dir . '/show_images.zip' );

	// create the export directory that will hold our image files raising suitable errors on failure
	if ( !mkdir( $base_dir . '/export' ) ) {
		$yaml_parse_errors = '';
		$yaml_import_message = __( 'Failed to create export folder', 'radio-station' ) . ' ' . $base_dir . '/export';
		add_action( 'admin_notices', 'radio_station_yaml_import__failure' );
		return false;
	}

	// populate $image_index with all the show image data, indexed by image_ID
	foreach ( $shows as $show ) {

		//fetch all the metadata for this show
		$metadata = get_post_meta( $show->ID );

		foreach ( array('show_image_id', 'show_avatar', 'show_header') as $image_ref ) {
			if ( !wp_attachment_is_image($metadata[$image_ref][0]) ){
				// if no image present then move on and log a notice
				$yaml_parse_errors .= __( 'No image exported for', 'radio-station' ) . " $show->post_title ($image_ref) </br>";
			} else {
				$src = radio_station_get_image_path($metadata[$image_ref][0]);
				$dst = $base_dir . '/export/' . basename($src);
				if ( !copy($src, $dst ) ) {
					$yaml_parse_errors = "src: $src</br> dst: $dst</br>";
					$yaml_import_message = __( 'Failed to copy file. See below for details', 'radio-station' );
					add_action ('admin_notices', 'radio_station_yaml_import__failure' );
					return false;
				}
				// update the YAML index file
				// fix: use array for old PHP versions
				$image_index[$metadata[$image_ref][0]] = array( 'path' => $dst, 'file' => basename( $src ) );
			}
		}
	}

	// create the zip file
	$zip = new ZipArchive;
	if ( $zip->open( $base_dir . '/show_images.zip', ZipArchive::CREATE ) === true ) {
		if ( $handle = opendir($base_dir . '/export' ) ) {
			while (false !== ( $entry = readdir( $handle ) ) ) {
				if ( $entry != "." && $entry != ".." && !is_dir( $base_dir . "/" . $entry ) ) {
					$zip->addFile( $base_dir . "/export/" . $entry, $entry );
				}
			}
			closedir( $handle );
		}
		$zip->close();
	} else {
		$yaml_parse_errors = "";
		$yaml_import_message = __( 'Failed to create show_images.zip', 'radio-station' );
		add_action( 'admin_notices', 'radio_station_yaml_import__failure' );
		return false;
	}

	// create the tgz file
	$tgz = new PharData( $base_dir . '/show_images.tar' );
	$tgz->buildFromDirectory( $base_dir . '/export' );
	$tgz->compress( Phar::GZ );
	unset( $tgz );
	unlink( $base_dir . '/show_images.tar' );
	rename( $base_dir . '/show_images.tar.gz', $base_dir . '/show_images.tgz' );

	return $image_index;
}

// ------------------
// Validate Show Data
// ------------------
//this function validates the datastructure for a show and display's any error messages
function radio_station_show_is_valid( $show, &$sanitized_show = array() ) {

	$with_paragraph_tags = apply_filters( 'radio_station_valid_with_paragraph_tags', true );

	//validation proceeds field by field as noted below, until all have been checked. Errors are accumulated in
	//$errors. Successfully validating fields are copied into $sanitized_show as we go along
	//success is returned at the end if there are no errors. In case of failure, $sanitized_show
	//will still contain the fields that validated properly, but no bad data. This is intended to be failsafe.
	//The goal is to make it impossible for any bad data to be injected into the program/website via a
	//corrupt or maliciously crafted YAML file.
	$errors = '';

	// check for a minimum field set and return an error message if any required fields are missing.
	// if no fields are to be required, comment out the whole if statement and $show_keys variable assignment.
	$show_keys = array_keys($show);
	if ( !( in_array( 'show-title', $show_keys )
		// && in_array('show-description', $show_keys))
		// && in_array('show-schedule', $show_keys)
		// && in_array('show-active', $show_keys)
		) ) {

		$errors .= '<li>' . __( 'Each show in the YAML file must define at minimum the following keys: ' , 'radio-station' )
			. 'show-title'
			// . ', show-description'
			// . ', show-schedule'
			// . ', show-active'
			. '.</li>';
	}

	// validate title (make sure it's a string)
	if ( !is_null( $show['show-title'] ) ) {
		$sanitized_show['show-title'] = radio_station_keep_basic_html_only($show['show-title']);
	} else {
		$errors .= '<li>' . __( 'show-title: may not be null.', 'radio-station' ) . '</li>';
	}

	// validate description
	if ( !is_null( $show['show-description'] ) ) {
		// $sanitized_show['show-description'] = radio_station_keep_basic_html_only($show['show-description'], $with_paragraph_tags );
		$sanitized_show['show-description'] = wp_kses_post( $show['show-description'] );
	}
	// Uncomment if requiring show-description
	// else {
	//   $errors .= '<li>' . __( 'show-description: may not be null.', 'radio-station' ) . '</li>';
	// }

	// validate excerpt
	$sanitized_show['show-excerpt'] = radio_station_keep_basic_html_only( $show['show-excerpt'], $with_paragraph_tags );

	//validate image (make sure it's a URL or an integer)
	$tmp_var = filter_var( $show['show-image'], FILTER_VALIDATE_URL );
	if ($tmp_var){
		$sanitized_show['show-image'] = $tmp_var;
	} else {
		if ( !is_null( $show['show-image'] ) ) { // allow null
			$errors .= '<li>' . __('show-image: must be a URL reference to an existing image.', 'radio-station' ) . '</li>';
		}
	}

	// validate show-avatar
	$tmp_var = filter_var( $show['show-avatar'], FILTER_VALIDATE_URL );
	if ( $tmp_var ) {
		$sanitized_show['show-avatar'] = $tmp_var;
	} else {
		if ( !is_null( $show['show-avatar'] ) ) { //allow null
			$errors .= '<li>' . __( 'show-avatar: must be a URL reference to an existing image.', 'radio-station' ) . '</li>';
		}
	}

	// validate show-header
	$tmp_var = filter_var( $show['show-header'], FILTER_VALIDATE_URL );
	if ($tmp_var){
		$sanitized_show['show-header'] = $tmp_var;
	} else {
		if ( !is_null($show['show-header'] ) ) { //allow null
			$errors .= '<li>' . __( 'show-header: must be a URL reference to an existing image.', 'radio-station' ) . '</li>';
		}
	}

	// validate upload-images... true for "1", "true", "on", and "yes", false otherwise
	if ( !is_null($show['upload-images'] ) ) {
		$sanitized_show['upload-images'] = filter_var( $show['upload-images'], FILTER_VALIDATE_BOOLEAN );
	} else {
		$sanitized_show['upload-images'] = false; 
		// null defaults to inactive
		// $errors .= '<li>' . __( 'upload-images: may not be null.', 'radio-station' ) . '</li>';
	}

	// validate tagline
	$sanitized_show['show-tagline'] = radio_station_keep_basic_html_only( $show['show-tagline'] );

	// validate show-schedule
	if ( radio_station_schedule_is_valid( $show['show-schedule'], $errors ) ) {
		$sanitized_show['show-schedule'] = $show['show-schedule'];
	}

	// validate show-url (make sure it's a URL)
	$tmp_var = filter_var( $show['show-url'], FILTER_VALIDATE_URL );
	if ( $tmp_var ) {
		$sanitized_show['show-url'] = $tmp_var;
	} else {
		if ( !is_null($show['show-url'] ) ) { // allow null
			$errors .= '<li>' . __( 'show-url: must be a valid web address.', 'radio-station' ) . '</li>';
		}
	}

	// validate show-podcast (make sure it's a URL)
	$tmp_var = filter_var( $show['show-podcast'], FILTER_VALIDATE_URL );
	if ( $tmp_var ) {
		$sanitized_show['show-podcast'] = $tmp_var;
	} else {
		if ( !is_null($show['show-podcast'] ) ) { //allow null
			$errors .= '<li>' . __('show-podcast: must be a valid web address.', 'radio-station' ) . '</li>';
		}
	}

	// validate show-user-list
	$tmp_var = $show['show-user-list'];
	$sanitized_show['show-user-list'] = array();
	if (is_array( $tmp_var ) && !radio_station_is_associative($tmp_var ) ) {
		foreach ( $tmp_var as $email_address ) {
			$tmp_var2 = filter_var( $email_address, FILTER_VALIDATE_EMAIL );
			if ($tmp_var2){
				//push the email onto our output array if valid
				array_push( $sanitized_show['show-user-list'], $tmp_var2 );
			}
		}
	} else {
		// allow null values
		if ( !is_null( $show['show-user-list'] ) ) {
			$errors .= '<li>' . __( 'show-user-list: must be a simple array of valid email addresses.', 'radio-station' ) . '</li>';
		}
	}

	// validate show-producer-list
	$tmp_var = $show['show-producer-list'];
	$sanitized_show['show-producer-list'] = array();
	if ( is_array( $tmp_var ) && !radio_station_is_associative( $tmp_var ) ) {
		foreach ( $tmp_var as $email_address ) {
			$tmp_var2 = filter_var( $email_address, FILTER_VALIDATE_EMAIL );
			if ( $tmp_var2 ) {
				//push the email onto our output array if valid
				array_push( $sanitized_show['show-producer-list'], $tmp_var2 );
			}
		}
	} else {
		// allow null values
		if ( !is_null( $show['show-producer-list'] ) ) {
			$errors .= '<li>' . __( 'show-producer-list: must be a simple array of valid email addresses.', 'radio-station' ) . '</li>';
		}
	}

	// validate show-email
	$tmp_var = filter_var( $show['show-email'], FILTER_VALIDATE_EMAIL );
	if ( $tmp_var ) {
		$sanitized_show['show-email'] = $tmp_var;
	} else {
		//allow null values
		if ( !is_null($show['show-podcast'] ) ) {
			$errors .= '<li>' . __( 'show-email: must be a valid email address.', 'radio-station' ) . '</li>';
		}
	}

	//validate show-active... true for "1", "true", "on", and "yes", false otherwise
	if ( !is_null($show['show-active'] ) ) {
		$tmp_var = filter_var( $show['show-active'], FILTER_VALIDATE_BOOLEAN );
		if ( $tmp_var ) {
			$sanitized_show['show-active'] = "on";
		}else{
			$sanitized_show['show-active'] = null;
		}
	} else {
		$sanitized_show['show-active'] = false; 
		// null defaults to inactive
		// $errors .= '<li>' . __( 'show-active: may not be null.', 'radio-station' ) . '</li>';
	}

	//validate show-patreon slug
	$sanitized_show['show-patreon']= sanitize_title( $show['show-patreon'] );

	if ( $errors === '' ) {
		return true;
	} else {
		global $yaml_import_message;
		global $yaml_parse_errors;
		$yaml_import_message = __( 'YAML data parsed successfully, but contains formatting errors. See below for details.', 'radio-station' );
		$message = '<h2>' . $sanitized_show['show-title'] . '</h2>';
		$message .= __( 'Data file errors noted as follows:', 'radio-station' );
		$message .= '<ul style="padding-left: 20px; list-style: disc;">' . $errors . '</ul>';
		$yaml_parse_errors = $message;
		add_action( 'admin_notices', 'radio_station_yaml_import__failure' );
		return false;
	}
}

// ---------------------
// Convert Show Schedule
// ---------------------
//this function converts the show-schedule from the 24h time format used in the YAML file to the am/pm internal structure
function radio_station_convert_show_schedule( $show_schedule ) {

	//$show is the post ID of the show in question
	//$show_schdule is an associative array as documented in the contextual help under import/export (see also /help/show-schedule.php)
	//data is assumed to be validated. i.e. a valid show ID is passed and $show_schedule contains at least 1 valid timeblock
	$converted_schedule = array();

	// fix: added is_array check
	if ( is_array( $show_schedule ) ) {
		//loop through the days of the week
		foreach ( $show_schedule as $day => $times ) {
			//loop through each time block for a given day
			foreach( $times as $timeblock ) {
				$tmp = array();
				$tmp['day'] = radio_station_canonicalize_day( $day );
				// - ["05:30", "06:00", "disabled", "encore"]
				//convert start time in block
				$tmp['start_hour'] = substr( $timeblock[0], 0, 2 );
				$meridian = '';
				if ( intval( $tmp['start_hour'] ) > 12 ) {
					$tmp['start_hour'] = trim( strval( intval( $tmp['start_hour'] ) - 12 ), '0' );
					$meridian = 'pm';
				} else {
					$tmp['start_hour'] = trim( $tmp['start_hour'], '0' );
					$meridian = 'am';
				}
				$tmp['start_min'] = substr( $timeblock[0], 3, 2 );
				$tmp['start_meridian'] = $meridian;
				//convert end time in block
				$tmp['end_hour'] =  substr( $timeblock[1], 0, 2 );
				if ( intval( $tmp['end_hour']) > 12 ) {
					$tmp['end_hour'] = trim( strval( intval( $tmp['end_hour']) - 12 ), '0' );
					$meridian = 'pm';
				} else {
					$tmp['end_hour'] = trim( $tmp['end_hour'], '0' );
					$meridian = 'am';
				}
				$tmp['end_min'] =  substr( $timeblock[1], 3, 2 );
				$tmp['end_meridian'] = $meridian;
				if ( in_array('encore', $timeblock ) ) {
					$tmp['encore'] = 'on';
				}
				if ( in_array( 'disabled', $timeblock ) ) {
					$tmp['disabled'] = 'yes';
				}

				//push the converted structure on to the stack
				array_push( $converted_schedule, $tmp );
			}
		}
	}

	return $converted_schedule;
}

// -----------------
// Get Show Schedule
// -----------------
// this function returns a datastructure describing a show's schedule 
// (used by radio_station_export_helper() function via radio_station_get_published_shows())
function radio_station_show_schedule( $show_id ) {

	$schedule = get_post_meta( $show_id, 'show_sched', true );
	$converted_schedule = array();
	
	// fix: check we have schedule data
	if ( $schedule && is_array( $schedule ) ) {
		// loop through all time blocks
		foreach ( $schedule as $tb ) {

			$accumulator = array(); 
			
			//convert time block array to internal 24h format
			// fix: use yoda conditions
			if ( 'pm' == $tb['start_meridian'] ) {
				$start_time = sprintf( '%02u:%02u', $tb['start_hour'] + 12, $tb['start_min'] );
			} else {
				$start_time = sprintf( '%02u:%02u', $tb['start_hour'], $tb['start_min'] );
			}
			if ( 'pm' == $tb['end_meridian'] ) {
				$end_time   = sprintf( '%02u:%02u', $tb['end_hour'] + 12, $tb['end_min'] );
			}else{
				$end_time   = sprintf( '%02u:%02u', $tb['end_hour'], $tb['end_min'] );
			}
			array_push( $accumulator, $start_time, $end_time );
			if ($tb['disabled'] == 'yes') {
				array_push( $accumulator, 'disabled' );
			}
			if ($tb['encore'] == 'on') {
				array_push( $accumulator, 'encore' );
			}

			//add finished array to the $converted_schedule array under the correct day of the week
			if ( array_key_exists( $tb['day'], $converted_schedule ) ) {
				array_push( $converted_schedule[$tb['day']], $accumulator );
			} else {
				$converted_schedule[$tb['day']][0] = $accumulator;
			}
		}
	}

	return $converted_schedule;
}

// --------------------
// Delete All Show Data
// --------------------
//this function deletes all shows and associated data from the database
function radio_station_delete_show_data(){
	// get an array of all show CPT ids
	// fix: use show slug constant
	$parameters = array(
		'posts_per_page'     => -1,
		'post_type'          => RADIO_STATION_SHOW_SLUG,
	);
	$shows = get_posts( $parameters );
	foreach ( $shows as $show ){
		// delete the images associated with the given show
		$avatar_id = get_post_meta( $show->ID, 'show_avatar', true );
		wp_delete_attachment( $avatar_id );
		$thumbnail_id = get_post_meta( $show->ID, '_thumbnail_id', true );
		wp_delete_attachment( $thumbnail_id );
		$header_id = get_post_meta( $show->ID, 'show_header', true );
		wp_delete_attachment( $header_id );

		// now we delete the show itself (deletes metadata too)
		wp_delete_post( $show->ID, true );
	}
}

// ----------------
// Canonlcalize Day
// ----------------
// radio_station_convert_show_schedule helper function
// converts the various day formats to full-length capitalized form
function radio_station_canonicalize_day( $day ) {
	$days_lookup = array(
		'sun' => 'Sunday',
		'mon' => 'Monday',
		'tue' => 'Tuesday',
		'wed' => 'Wednesday',
		'thu' => 'Thursday',
		'fri' => 'Friday',
		'sat' => 'Saturday'
	);
	$key = substr( strtolower( $day ), 0, 3 );
	return $days_lookup[$key];
}

// ----------------------------
// Validate Schedule for Import
// ----------------------------
//validates the schedule portion of an imported YAML file. returns true if valid, false otherwise, with any error messages appended to $error_buffer
function radio_station_schedule_is_valid( $schedule, &$error_buffer ) {
	/* expected format for $schedule data passed in. the presence of at least one day/time-block is enforced

	show-schedule:
	mon: #expressed as one of [sun, mon, tue, wed, thu, fri, sat]. Spelling out days ("Monday" or "monday") is also supported
	- ["05:30", "06:00", "disabled", "encore"] #optional 3rd and 4th parameters supported as indicated. Present only if true.
	- ["05:00", "17:30", ] #all time expressed in 24h format. First time is start-time, last time is end-time.
	wednesday:
	- ["05:30", "06:00"]
	- ["17:00", "17:30"]
	Friday:
	- ["05:30", "06:00"]
	- ["17:00", "17:30"]

	*/
	$errors = '';
	
	$weekdays = array( "sun", "mon", "tue", "wed", "thu", "fri", "sat", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday" );

	$tmp_weekdays = array_keys( $schedule );
	if ( count( $tmp_weekdays ) > 0 ) { 
		// at least one weekday is defined
		foreach ( $tmp_weekdays as $day ) {
			if ( in_array( strtolower( $day ), $weekdays ) ) { 
				// weekday format is valid
				if ( count( $schedule[$day]) > 0 ) { 
					// at least one time pair is defined
					foreach( $schedule[$day] as $time_pair ) {
						// pull off the time pair itself
						$tmp_first_part = array_slice( $time_pair, 0, 2 ); 
						// validate the time pair proper
						if ( !( preg_match("/\d\d:\d\d/", $time_pair[0] ) && preg_match( "/\d\d:\d\d/", $time_pair[1] ) ) ) {
							$errors .= '<li>' . __( 'show-schedule[<weekday>] time blocks must be in 24h format and have the form "04:55" (note 0 padding).', 'radio-station' ) . '</li>';
						}
						// the rest will be flags if present
						$tmp_2nd_part = array_slice( $time_pair, 2 ); 
						// validate flags if present
						if ( !is_null( $tmp_2nd_part[0] ) ) {
							switch ( $tmp_2nd_part[0] ) {
								case 'disabled':
									if ( !is_null( $tmp_2nd_part[1] ) ) {
										if ( $tmp_2nd_part[1] != 'encore' ) {
											$errors .= '<li>' . __( 'Error, for show-schedule[<weekday>], only "disabled" and "encore" flags are allowed', 'radio-station' ) . '</li>';
										}
									}
									break;
								case 'encore':
									if ( !is_null( $tmp_2nd_part[1] ) ) {
										if ( $tmp_2nd_part[1] != 'disabled' ) {
											$errors .= '<li>' . __( 'Error, for show-schedule[<weekday>], only "disabled" and "encore" flags are allowed', 'radio-station' ) . '</li>';
										}
									}
									break;
								default:
									$errors .= '<li>' . __( 'Error, for show-schedule[<weekday>], only "disabled" and "encore" flags are allowed', 'radio-station' ) . '</li>';
							}
						}
					}
				} else {
					$errors .= '<li>' . __( 'show-schedule[<weekday>] must reference an array of time blocks containing at least one element.', 'radio-station' ) . '</li>';
				}
			} else {
				$errors .= '<li>' . __( 'Invalid weekday. show-schedule[<weekday>] must be one of "sun".."sat", or "sunday".."saturday" (case insensitive).', 'radio-station' ) . '</li>';
			}
		}
	} else {
		$errors .= '<li>' . __(' show-schedule: must define at least one weekday.', 'radio-station' ) . '</li>';
	}

	if ( '' == $errors ) {
		return true;
	} else {
		$error_buffer .= $errors;
		return false;
	}
}

// ---------------
// Basic HTML Only
// ---------------
//removes all html tags except for those explicitly defined below
function radio_station_keep_basic_html_only( $string, $with_paragraph = false ) {

	// fix: use str_ireplace for case insensitive tags
	#paragraph
	$tmp = $string;
	if ( $with_paragraph ) {
		$tmp = str_ireplace( '<p>', '&lt;p&gt;', $tmp );
		$tmp = str_ireplace( '</p>', '&lt;/p&gt;', $tmp );
	}
	#bold
	$tmp = str_ireplace( '<b>', '&lt;b&gt;', $tmp );
	$tmp = str_ireplace( '</b>', '&lt;/b&gt;', $tmp );
	#strong
	$tmp = str_ireplace( '<strong>', '&lt;strong&gt;', $tmp );
	$tmp = str_ireplace( '</strong>', '&lt;/strong&gt;', $tmp );
	#italic
	$tmp = str_ireplace( '<i>', '&lt;i&gt;', $tmp );
	$tmp = str_ireplace( '</i>', '&lt;/i&gt;', $tmp );
	#emphasis
	$tmp = str_ireplace( '<em>', '&lt;em&gt;', $tmp );
	$tmp = str_ireplace( '</em>', '&lt;/em&gt;', $tmp );
	#mark
	$tmp = str_ireplace( '<mark>', '&lt;mark&gt;', $tmp );
	$tmp = str_ireplace( '</mark>', '&lt;/mark&gt;', $tmp );
	#small
	$tmp = str_ireplace( '<small>', '&lt;small&gt;', $tmp );
	$tmp = str_ireplace( '</small>', '&lt;/small&gt;', $tmp );
	#deleted text
	$tmp = str_ireplace( '<del>', '&lt;del&gt;', $tmp );
	$tmp = str_ireplace( '</del>', '&lt;/del&gt;', $tmp );
	#inserted text
	$tmp = str_ireplace( '<ins>', '&lt;ins&gt;', $tmp );
	$tmp = str_ireplace( '</ins>', '&lt;/ins&gt;', $tmp );
	#subscript
	$tmp = str_ireplace( '<sub>', '&lt;sub&gt;', $tmp );
	$tmp = str_ireplace( '</sub>', '&lt;/sub&gt;', $tmp );
	#superscript
	$tmp = str_ireplace( '<sup>', '&lt;sup&gt;', $tmp );
	$tmp = str_ireplace( '</sup>', '&lt;/sup&gt;', $tmp );

	// cleanup and return
	$tmp = strip_tags( $tmp );
	$tmp = wp_strip_all_tags( $tmp );

	return htmlspecialchars_decode( trim( $tmp ) );
}


// ------------------------
// === Helper Functions ===
// ------------------------

// -------------
// Delete Folder
// -------------
// deletes a folder containing files
function radio_station_delete_folder($path) {
	if ( true === is_dir( $path ) ) {
		$files = array_diff( scandir( $path ), array( '.', '..' ) );
		foreach ( $files as $file ) {
			radio_station_delete_folder( realpath( $path ) . '/' . $file );
		}
		return rmdir( $path );
	} else if ( true === is_file( $path ) ) {
		return unlink( $path );
	}
	return false;
}

// --------------
// Get Image Path
// --------------
// retrieves the absolute filesystem path of the passed image_id if available.
// Can be used to retrieve intermediate sizes also.
function radio_station_get_image_path( $image_id, $size = 'full' ) {
    $file = get_attached_file( $image_id, true );
    if ( empty( $size ) || ( 'full' == $size ) ) {
        // for the original size get_attached_file is fine
        return realpath($file);
    }
    if ( !wp_attachment_is_image($image_id ) ) {
        return false; // the id is not referring to a media
    }
    $info = image_get_intermediate_size( $image_id, $size );
    if ( !is_array( $info ) || !isset($info['file'] ) ) {
        return false; // probably a bad size argument
    }

    return realpath( str_replace( wp_basename($file), $info['file'], $file ) );
}

// -------------------------
// Get Show Users Email List
// -------------------------
//this function returns an array of email addresses of show users for the given show ID
function radio_station_get_show_users( $show_id ) {
	$user_ids = get_post_meta( $show_id, 'show_user_list', true );
	$email_list = array();
	if ( $user_ids && is_array( $user_ids ) ) {
		foreach ( $user_ids as $user_id ) {
			$user = get_user_by( 'ID', $user_id );
			array_push( $email_list, $user->user_email );
		}
	}

	return $email_list;
}

// -----------------------------
// Get Show Producers Email List
// -----------------------------
//this function returns an array of email addresses of show producers for the given show ID
function radio_station_get_show_producers( $show_id ) {
	$producers = get_post_meta( $show_id, 'show_producer_list', true );
	$email_list = array();
	foreach ( $producers as $producer ) {
		$user = get_user_by( 'ID', $producer );
		array_push( $email_list, $user->user_email );
	}

	return $email_list;
}

// ----------------------
// Get Users IDs by Email
// ----------------------
// this function takes an array of email addresses, 
// and returns, if possible, an array of matching WordPress user ID's.
function radio_station_convert_user_list( $users_emails ){
	$user_ids = array();
	foreach ( $users_emails as $email ) {
		$user = get_user_by('email', $email );
		if ( $user ) {
		  array_push( $user_ids, $user->ID );
		}
	}

	return $user_ids;
}

// ------------------------
// Validate Image URL or ID
// ------------------------
// parses whether or not field passed is a valid URL or ID
function radio_station_is_url_or_ID( &$field, $nullOK = false ) {
	$tmp_var = filter_var( $field, FILTER_VALIDATE_URL );
	if ( $tmp_var ) {
		$field = $tmp_var;
		return true;
	} else {
		// show-image is not a URL so let's see if it's an integer
		$tmp_var = filter_var( $field, FILTER_VALIDATE_INT );
		if ( $tmp_var ) {
			$field = $tmp_var;
			return true;
		} else {
			if ( $nullOK && is_null( $field ) ) {
				return true;
			} else {
				return false;
			}
		}
	}
}

// --------------------
// Is Associative Array
// --------------------
//function returns true if associative array
function radio_station_is_associative( array $arr ) {
	if ( array() === $arr ) {
		return false;
	}
	return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
}

// -----------------------
// Get Maximum Upload Size
// -----------------------
// Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
function radio_station_file_upload_max_size() {

	static $max_size = -1;

	if ( $max_size < 0 ) {
		// Start with post_max_size.
		$post_max_size = radio_station_parse_size( ini_get( 'post_max_size' ) );
		if ( $post_max_size > 0 ) {
			  $max_size = $post_max_size;
		}

		// If upload_max_size is less, then reduce. Except if upload_max_size is
		// zero, which indicates no limit.
		$upload_max = radio_station_parse_size( ini_get( 'upload_max_filesize' ) );
		if ( ( $upload_max > 0 ) && ( $upload_max < $max_size ) ) {
			$max_size = $upload_max;
		}
	}

	return $max_size;
}

// ---------------
// Parse File Size
// ---------------
function radio_station_parse_size( $size ) {
	$unit = preg_replace( '/[^bkmgtpezy]/i', '', $size ); // Remove the non-unit characters from the size.
	$size = preg_replace( '/[^0-9\.]/', '', $size ); // Remove the non-numeric characters from the size.
	if ( $unit ) {
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round( $size * pow( 1024, stripos('bkmgtpezy', $unit[0] ) ) );
	} else {
		return round( $size );
	}
}

// ----------------------
// Get Readable File Size
// ----------------------
//returns human readable file size string
function radio_station_convert_filesize( $bytes, $decimals = 2 ){
    $size = array(' b',' Kb',' Mb',' Gb',' Tb',' Pb',' Eb',' Zb',' Yb');
    $factor = floor( ( strlen($bytes) - 1 ) / 3 );
    return sprintf( "%.{$decimals}f", $bytes / pow( 1024, $factor ) ) . @$size[$factor];
}

// ----------------
// Enqueue Semantic
// ----------------
function radio_station_enqueue_semantic() {

	// --- enqueue semantic/ui styles ---
	$suffix = '.min';
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$suffix = '';
	}

	// --- semantic styles ---
	// 2.3.2: modify style path
	// vendor/semantic/ui/dist/semantic
	$semantic_css_url = plugins_url( 'css/semantic' . $suffix . '.css', RADIO_STATION_PRO_FILE );
	wp_enqueue_style( 'semantic-ui-style', $semantic_css_url, array(), '2.4.1', 'all' );
	
	// --- semantic script ---
	// 2.3.2: modify script path
	// vendor/semantic/ui/dist/semantic
	$semantic_js_url = plugins_url( 'js/semantic' . $suffix . '.js', RADIO_STATION_PRO_FILE );
	wp_enqueue_script( 'semantic-ui-script', $semantic_js_url, array(), '2.4.1', true );
}

// -----------------------------
// Import Export Page Javascript
// -----------------------------
function radio_station_import_export_script() {

	// --- enqueue javascript for this page ---
	// fix: wrap in jQuery ready function
	// $js = <<< END_OF_JAVASCRIPT
	$js = "/* Radio Station Import Export Functions */
	jQuery(document).ready(function() {
		del = jQuery('#del-checkbox-div');
		adv = jQuery('#advanced-checkbox-div');

		/* set the initial state of the checkboxes */
		del.checkbox('uncheck');
		adv.checkbox('uncheck');

		/* show/hide the warning based on the state of the delete data checkbox */
		del.on('change', function() {
			if(del.checkbox('is checked') === true){
				jQuery('#delete-data-warning').attr('style', 'display: block;');
			} else {
				jQuery('#delete-data-warning').attr('style', 'display: none;');
			}
		});

		/* show/hide the advanced optiosn based on the state of the Advanced checkbox */
		adv.on('change', function() {
			if (adv.checkbox('is checked') === true) {
				jQuery('#advanced-options').attr('style', 'display: block;');
			} else {
				jQuery('#advanced-options').attr('style', 'display: none;');
			}
		});
	});

	/* populate the filename next to the Import button once a file is selected */
	jQuery('#yamlfileinput').on( 'change', function () {
		string = jQuery(this).val();
		string = string.replace(/^.+fakepath\\\\/, '');
		jQuery('#upload-file-name').html(string);
		jQuery('#upload-button').addClass('green');
	});

	function radio_enable_spinner(spinner_to_enable) {
	if (spinner_to_enable == 'import'){
		jQuery('#import-spinner').addClass('active');
	}
	if (spinner_to_enable == 'export'){
		jQuery('#export-spinner').addClass('active');
	}
	}"; 
	// END_OF_JAVASCRIPT;

	wp_add_inline_script( 'radio-station-admin', $js );
}

// ---------------------------
// Add Import/Export Menu Item
// ---------------------------
add_action( 'radio_station_admin_submenu_middle', 'radio_station_add_import_export_menu_item', 15 );
function radio_station_add_import_export_menu_item() {
	$rs = __( 'Radio Station', 'radio-station' );
	add_submenu_page( 'radio-station', $rs . ' ' . __( 'Import/Export Show Data', 'radio-station' ), __( 'Import/Export', 'radio-station' ), 'manage_options', 'import-export-shows', 'radio_station_import_export_page' );
}

// ---------------------------
// Import Export Page Template
// ---------------------------
function radio_station_import_export_page() {

	/*
	 * Import/Export Show admin screen template
	 * Author: Andrew DePaula
	 * (c) Copyright 2020
	 * Licence: GPL3
	 */

	// --- enquque semantic ---
	radio_station_enqueue_semantic();

	// --- enqueue import export page javascript ---
	radio_station_import_export_script();

	// --- set maximum upload size ---
	$max_upload_size = radio_station_file_upload_max_size();
	$max_upload_size = radio_station_convert_filesize( $max_upload_size );

?>
<!-- <div style="width: 620px; padding: 10px"> -->
<h2><?php echo esc_html( __( 'Import/Export Show Data', 'radio-station' ) ); ?></h2>
<!-- <div style="padding-left: 15px"> -->

<div class="ui three column grid">
  <div class="row left-padding">
    <!-- import pane -->
    <div class="seven wide column container">
      <div class="ui segment">
        <h2 class="ui header"><?php echo esc_html( __( 'Import', 'radio-station' ) ); ?></h2>
        <!-- export form -->
        <form class="ui form" method="POST" action="/wp-admin/admin.php?page=import-export-shows" enctype="multipart/form-data">
          <div style="height: 250px;">
            <p class="form-text">
            <?php echo esc_html( __( 'Import show data from a YAML file.', 'radio-station' ) ); ?>
            </p>

            <div id="del-checkbox-div" class="ui checkbox">
              <input type="hidden" value="0" name="delete_show_data" onclick=check()>
              <input id="delete-data-checkbox" type="checkbox" value="1" name="delete_show_data">
              <label for="delete-data-checkbox"> <?php echo esc_html( __('Delete existing show data', 'radio-station' ) ); ?> </label>
            </div>
            <input type="file" (change)="fileEvent($event)" id="yamlfileinput" class="inputfile" name="import_file"/>
            <label id="upload-button"for="yamlfileinput" class="ui basic button">
              <i class="ui upload icon"></i>
              Select file
            </label>
            <p></p>
            <p id="delete-data-warning" class="form-text" style="display: none;">
            <span style="color: red;"><strong><?php echo esc_html( __( 'WARNING', 'radio-station' ) ); ?></strong></span>, 
            <?php echo esc_html(  _( 'This will delete all show data you have currently configured, including associated images. We strongly suggest exporting a backup first.', 'radio-station' ) ); ?>
            </p>

          </div> <!-- style="height: 250px... -->
          <input type="hidden" name="action" value="radio_station_yaml_import_action" />
          <?php wp_nonce_field( 'yaml_import_nonce', 'yaml_import_nonce' ); ?>
          <button class="ui left floating button" type="submit" onclick="radio_enable_spinner('import')"><?php echo esc_html( __('Import', 'radio-station' ) ); ?></button>
          <div id="upload-file-name">
            No file selected for import.
          </div>
        </form>
        <div id="import-spinner" class="ui large centered floating loader" style="top:50%;"></div>
      </div>
    </div>

    <!-- export pane -->
    <div class="seven wide column container">
      <div class="ui segment">
        <h2 class="ui header"><?php echo esc_html( __( 'Export', 'radio-station' ) ); ?></h2>
        <!-- export form -->
        <form class="ui form" method="POST" action="/wp-admin/admin.php?page=import-export-shows" enctype="multipart/form-data">
          <div style="height: 250px;">
            <p class="form-text">
            <?php echo esc_html( __( 'Export show data to a downloadable file. Does not include images by default. Check Advanced for additional options.', 'radio-station' ) ); ?>
            </p>

            <div id="advanced-checkbox-div" class="ui checkbox">
              <input type="hidden" value="0" name="advanced_options">
              <input id="advanced-options-checkbox" type="checkbox" checked="" name="advanced_options">
              <label> <?php echo esc_html( __( 'Advanced', 'radio-station' ) ); ?> </label>
            </div>
            <p></p>
            <div id="advanced-options" style="display:none;">
                <div class="field">
                  <label><?php esc_html( __( 'YAML file name', 'radio-station' ) ); ?></label>
                  <?php
                  $tmp_date = new DateTime();
                  $export_filename = $tmp_date->format( 'Y-m-d-' ) . time() . '_show_data.yaml';
                  ?>
                  <input type="text" name="export_file_name" placeholder="<?php echo esc_attr( __('Default similar to', 'radio-station') ); echo " " . $export_filename; ?>">
                </div>
                <div class="field">
                  <label><?php _e('Image location URL', 'radio-station') ?></label>
                  <input type="text" name="image_prefix_url" placeholder="<?php echo esc_attr( __( 'URL where show images will be staged for import (see help)', 'radio-station') ); ?>">
                </div>
            </div>

          </div> <!-- style="height: 250px... -->
          <input type="hidden" name="action" value="radio_station_yaml_export_action" />
          <?php wp_nonce_field( 'yaml_export_nonce', 'yaml_export_nonce' ); ?>
          <button class="ui left floating button" type="submit" onclick="radio_enable_spinner('export')"><?php echo esc_html( __( 'Export', 'radio-station' ) ); ?></button>
        </form>
        <div id="export-spinner" class="ui large centered floating loader" style="top:50%;"></div>
      </div>
    </div>
  </div> <!-- three column grid -->

  <div class="row left-padding">
    <div class="fifteen wide column container">
      <?php
      //pull in any parsing error details for display to the user
      global $yaml_parse_errors;
      echo $yaml_parse_errors;
      ?>

    </div>
    <div class"one wide column">
    </div>
  </div>
</div> <!-- ui grid -->

<?php

}

