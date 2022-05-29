/* === Now Playing Metadata === */
var radio_player_metadata_cycler;
jQuery(document).ready(function() {		
	radio_player_metadata_cycler = setInterval(function(instance) {
		metadata_url = radio_player.settings.metadata_url;
		if (radio_player.debug) {console.log('Getting stream metadata via URL: '+metadata_url);}
		jQuery.get(metadata_url, function(data,status) {
			if (status != 'success') {return;}
			/* Update Now Playing Track */
			if (radio_player.debug) {console.log('Now Playing:'); console.log(data.broadcast.now_playing);}
			if (radio_player.settings.nowplaying && data.broadcast.now_playing) {
				jQuery('#radio-station-player-bar').addClass('now_playing');
				if (data.broadcast.now_playing.title && data.broadcast.now_playing.artist) {
					jQuery('#radio-station-player-bar .rp-now-playing-title').html(data.broadcast.now_playing.title).attr('title',data.broadcast.now_playing.title);
					jQuery('#radio-station-player-bar .rp-now-playing-artist').html(data.broadcast.now_playing.artist).attr('title',data.broadcast.now_playing.artist);
				} else {
					jQuery('#radio-station-player-bar .rp-now-playing-title').html(data.broadcast.now_playing.text).attr('title',data.broadcast.now_playing.text);
					jQuery('#radio-station-player-bar .rp-now-playing-artist').html('').attr('title','');
				}
				/* TODO: now playing album info and display */
				/* if (data.broadcast.now_playing.album) {
					jQuery('#radio-station-player-bar .rp-now-playing-album').html(data.broadcast.now_playing.album).attr('title',data.broadcast.now_playing.album);
				} else {jQuery('#radio-station-player-bar .rp-now-playing-album').html('').attr('title','');} */
			} else {jQuery('#radio-station-player-bar').removeClass('now_playing');}
			/* Update Current Show Display */
			if (radio_player.debug) {console.log('Current Show:'); console.log(data.broadcast.current_show);}
			if (radio_player.settings.currentshow && data.broadcast.current_show) {
				jQuery('#radio-station-player-bar').addClass('current_show');
				show = data.broadcast.current_show.show;
				if (show.url) {
					if (radio_player.settings.continuous) {click = ' onclick=\"return teleporter_transition_page(this);\"';} else {click = '';}
					link = '<a href=\"'+show.url+'\"'+click+'>'+show.name+'</div>';
					jQuery('#radio-station-player-bar .rp-show-title').html(link).attr('title',show.name);
				} else {jQuery('#radio-station-player-bar .rp-show-title').html(show.name).attr('title',show.name);}
				if (show.avatar_url == '') {
					jQuery('#radio-station-player-bar .rp-show-image').css('background-image','none');
					if (!jQuery('#radio-station-player-bar .rp-show-image').hasClass('no-image')) {jQuery('#radio-station-player-bar .rp-show-image').addClass('no-image');}
				} else {jQuery('#radio-station-player-bar .rp-show-image').removeClass('no-image').css('background-image','url('+data.broadcast.current_show.show.avatar_url+')');}
			} else {jQuery('#radio-station-player-bar').removeClass('current_show');}
			/* Sync Current Data to Top Window */
			if (typeof radio_player_top_window == 'function') {
				wintop = radio_player_top_window();
				/* if (topwin.current_radio == instance) { */
					wintop.radio_data.metadata.show_title = jQuery('#radio-station-player-bar .rp-show-image').css('background-image');
					wintop.radio_data.metadata.show_image_url = jQuery('#radio-station-player-bar .rp-show-image').html();
					wintop.radio_data.metadata.now_playing = jQuery('#radio-station-player-bar .rp-now-playing').html();
				/* } */
			}
		});
	}, radio_player.settings.metadata_cycle);
});
		
/* TODO: multiple metadata events in shortcodes/widgets */
/* document.addEventListener('rp-playing', function(e) {	
	instance = e.detail.instance;
	if (jQuery('#radio-station-player-bar .radio_player_'+instance').length) {return;}
	radio_player_metadata_cycle[instance] = setInterval(function(instance) {
		href = jQuery('#radio_player_'+instance).attr('data-href');
		console.log('Getting stream metadata for URL: '+href);
		metadata_url = radio_player.settings.metadata_url;
		if (metadata_url.indexOf('?') > -1) {metadata_url += '&';} else {metadata_url += '?';}
		metadata_url += 'stream_url'+encodeURIComponent(href);
		jQuery.get(metadata_url, function(data,status) {
			console.log('Data: '+data); console.log('Status:'+status);
		});
	}, radio_player.settings.metadata_cycle);
}, false); */

/* TODO: pause metadata retrieval for instance --- */
/* document.addEventListener('rp-pause', function(e) {	
	instance = e.detail.instance;
	if (typeof radio_player_metadata_cycle[instance] != 'undefined') {
		clearInterval(radio_player_metadata_cycle[instance]);
	}
}, false); */
