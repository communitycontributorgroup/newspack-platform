<?php

// =========================
// === Radio Station Pro ===
// =========================
// ----- Genre Images ------
// =========================
// - Filter Genre Image
// - Get Genre Image
// - Enqueue Media for Genre Images
// - Add Genre Image Form Fields
// - Save Genre Image
// - Edit Genre Image Form Fields
// - Genre Images Updated
// - Add Genre Images Script


// --------------------
// === Genre Images ===
// --------------------
// ref: https://pluginrepublic.com/adding-an-image-upload-field-to-categories/

// ------------------
// Filter Genre Image
// ------------------
add_filter( 'radio_station_genre_image', 'radio_station_pro_genre_image', 10, 2 );
function radio_station_pro_genre_image( $genre_image, $term ) {
	$genre_image = radio_station_pro_get_genre_image( $term['id'] );
	return $genre_image;
}

// ---------------
// Get Genre Image
// ---------------
function radio_station_pro_get_genre_image( $genre_id ) {
	$genre_image = false;
	$image_id = get_term_meta( $genre_id, 'genre-taxonomy-image-id', true );
	if ( $image_id ) {
		$atts = array( 'class' => 'genre-thumbnail-image' );
		$genre_image = wp_get_attachment_image( $image_id, 'thumbnail', false, $atts );
	}
	return $genre_image;
}

// ------------------------------
// Enqueue Media for Genre Images
// ------------------------------
add_action( 'admin_enqueue_scripts', 'radio_station_pro_genres_images_load_media' );
function radio_station_pro_genres_images_load_media() {
	if ( !isset( $_GET['taxonomy'] ) || ( $_GET['taxonomy'] != 'genres' ) ) {return;}
	wp_enqueue_media();
}

// ---------------------------
// Add Genre Image Form Fields
// ---------------------------
add_action( 'genres_add_form_fields', 'radio_station_pro_genres_add_fields', 10, 2 );
function radio_station_pro_genres_add_fields( $taxonomy ) {
	
	echo '<div class="form-field term-group">';
		echo '<label for="genre-taxonomy-image-id">' . esc_html( __( 'Image', 'radio-station' ) ) . '</label>' . PHP_EOL;
		echo '<input type="hidden" id="genre-taxonomy-image-id" name="genre-taxonomy-image-id" class="custom_media_url" value="">' . PHP_EOL;
		echo '<div id="category-image-wrapper"></div>';
		echo '<p>';
			echo '<input type="button" class="button button-secondary genre_tax_media_button" id="genre_tax_media_button" name="genre_tax_media_button" value="' . esc_attr( __( 'Add Genre Image', 'radio-station' ) ) . '">';
			echo '<input type="button" class="button button-secondary genre_tax_media_remove" id="genre_tax_media_remove" name="genre_tax_media_remove" value="' . esc_attr( __( 'Remove Genre Image', 'radio-station' ) ) . '">';
		echo '</p>';
	echo '</div>';
}

// ----------------
// Save Genre Image
// ----------------
add_action( 'created_genres', 'radio_station_pro_genres_save_image', 10, 2 );
function radio_station_pro_genres_save_image( $term_id, $tt_id ) {
	if ( isset( $_POST['genre-taxonomy-image-id'] ) && ( '' !== $_POST['genre-taxonomy-image-id'] ) ) {
		add_term_meta( $term_id, 'genre-taxonomy-image-id', absint( $_POST['genre-taxonomy-image-id'] ), true );
	}
}

// ----------------------------
// Edit Genre Image Form Fields
// ----------------------------
add_action( 'genres_edit_form_fields', 'radio_station_pro_genres_edit_fields', 10, 2 );
function radio_station_pro_genres_edit_fields( $term, $taxonomy ) {

	echo '<tr class="form-field term-group-wrap">' . PHP_EOL;
		echo '<th scope="row">' . PHP_EOL;
			echo '<label for="genre-taxonomy-image-id">' . esc_html( __( 'Image', 'radio-station' ) ) . '</label>' . PHP_EOL;
		echo '</th>' . PHP_EOL;
		echo '<td>' . PHP_EOL;
			$image_id = get_term_meta( $term->term_id, 'genre-taxonomy-image-id', true );
			echo '<input type="hidden" id="genre-taxonomy-image-id" name="genre-taxonomy-image-id" value="' . esc_attr( $image_id ) . '">' . PHP_EOL;
			echo '<div id="category-image-wrapper">' . PHP_EOL;
			if ( $image_id ) {
				echo wp_get_attachment_image( $image_id, 'thumbnail' );
			}
			echo '</div>' . PHP_EOL;
			echo '<p>' . PHP_EOL;
				echo '<input type="button" class="button button-secondary genre_tax_media_button" id="genre_tax_media_button" name="genre_tax_media_button" value="' . esc_attr( __( 'Add Genre Image', 'radio-station' ) ) . '">' . PHP_EOL;
				echo '<input type="button" class="button button-secondary genre_tax_media_remove" id="genre_tax_media_remove" name="genre_tax_media_remove" value="' . esc_attr( __( 'Remove Genre Image', 'radio-station' ) ) . '">' . PHP_EOL;
			echo '</p>'  . PHP_EOL;
		echo '</td>';
	echo '</tr>';

}

// -------------------
// Genre Image Updated
// -------------------
add_action( 'edited_genres', 'radio_station_pro_genres_update_image', 10, 2 );
function radio_station_pro_genres_update_image( $term_id, $tt_id ) {
	if( isset( $_POST['genre-taxonomy-image-id'] ) && ( '' !== $_POST['genre-taxonomy-image-id'] ) ) {
		update_term_meta( $term_id, 'genre-taxonomy-image-id', absint( $_POST['genre-taxonomy-image-id'] ) );
	} else {
		delete_term_meta( $term_id, 'genre-taxonomy-image-id' );
	}
}

// -----------------------
// Add Genre Images Script
// -----------------------
add_action( 'admin_footer', 'radio_station_pro_genres_add_script' );
function radio_station_pro_genres_add_script() {

	// TODO: maybe add pagenow value check ?
	// global $pagenow;

	if ( !isset( $_GET['taxonomy'] ) || ( $_GET['taxonomy'] != 'genres' ) ) {
		return;
	}
	
	echo "<script>
	jQuery(document).ready(function($) {
		_wpMediaViewsL10n.insertIntoPost = '" . __( 'Insert', 'radio-station' ) . "';
		function ct_media_upload(button_class) {
			var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment;
			$('body').on('click', button_class, function(e) {
				var button_id = '#'+$(this).attr('id');
				var send_attachment_bkp = wp.media.editor.send.attachment;
				var button = $(button_id);
				_custom_media = true;
				wp.media.editor.send.attachment = function(props, attachment) {
					if (_custom_media) {
						$('#genre-taxonomy-image-id').val(attachment.id);
						$('#category-image-wrapper').html('<img class=\"custom_media_image\" src=\"\" style=\"margin:0;padding:0;max-height:100px;float:none;\">');
						$( '#category-image-wrapper .custom_media_image' ).attr( 'src',attachment.url ).css( 'display','block' );
					} else {
						return _orig_send_attachment.apply( button_id, [props, attachment] );
					}
				}
				wp.media.editor.open(button); return false;
			});
		}

		ct_media_upload('.genre_tax_media_button.button');
		$('body').on('click','.genre_tax_media_remove',function(){
			$('#genre-taxonomy-image-id').val('');
			$('#category-image-wrapper').html('<img class=\"custom_media_image\" src=\"\" style=\"margin:0;padding:0;max-height:100px;float:none;\">');
		});

		$(document).ajaxComplete(function(event, xhr, settings) {
			var queryStringArr = settings.data.split('&');
			if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
				var xml = xhr.responseXML;
				$response = $(xml).find('term_id').text();
				if ($response != ''){
					$('#category-image-wrapper').html('');
				}
			}
		});
	});</script>";

}

