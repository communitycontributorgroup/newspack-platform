// ------------------------
// Radio Station Pro Player
// ------------------------

/* === Add Event Listeners === */

/* --- add player loading event listener --- */
document.addEventListener('rp-loading', function(e) {
	if (radio_player.debug) {console.log(e.detail);}
	instance = e.detail.instance; script = e.detail.script; windowid = radio_player_window_guid();
	wintop.current_radio = {player:radio_data.players[instance], script:script, windowid:windowid, instance: instance, playing: false}
	radio_player_set_class('loading',instance);
}, false);

/* --- add player loaded event listener --- */
document.addEventListener('rp-loaded', function(e) {
	if (radio_player.debug) {console.log(e.detail);}
	instance = e.detail.instance; script = e.detail.script; windowid = radio_player_window_guid();
	wintop.current_radio = {player:radio_data.players[instance], script:script, windowid:windowid, instance: instance, playing: false}
	radio_player_set_class('loaded');
}, false);

/* --- add player play event listener --- */
document.addEventListener('rp-play', function(e) {
	if (radio_player.debug) {console.log(e.detail);}
}, false);

/* --- add player playing event listener --- */
document.addEventListener('rp-playing', function(e) {
	if (radio_player.debug) {console.log(e.detail);}
	instance = e.detail.instance; script = e.detail.script; windowid = radio_player_window_guid();
	wintop = radio_player_top_window();
	wintop.current_radio = {player:radio_data.players[instance], script:script, windowid:windowid, instance: instance, playing: true}
	if (radio_player.debug) {console.log(wintop.current_radio);}
	radio_player_set_class('playing');
}, false);

/* --- add player paused event listener --- */
document.addEventListener('rp-paused', function(e) {
	if (radio_player.debug) {console.log(e.detail);}
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio == 'object') && (wintop.current_radio.instance == e.detail.instance)) {
		wintop.current_radio.playing = false;
		radio_player_set_class('paused');
		if (radio_player.debug) {console.log('Top Player State set to Paused');}
	}
}, false);

/* --- add player stop event listener --- */
document.addEventListener('rp-stopped', function(e) {
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio == 'object') && (wintop.current_radio.instance == e.detail.instance)) {
		wintop.current_radio = null;
		radio_player_set_class('stopped');
		if (radio_player.debug) {console.log('Top Player State Nulled');}
	}
}, false);

/* --- add player volume change event listener --- */
document.addEventListener('rp-volume-changed', function(e) {
	if (radio_player.debug) {console.log('Volume Changed Event'); console.log(e.detail);}
}, false);

/* --- add player muted event listener --- */
document.addEventListener('rp-muted', function(e) {
	if (radio_player.debug) {console.log(e.detail);}
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio == 'object') && (wintop.current_radio.instance == e.detail.instance)) {
		radio_player_set_state('muted', true);
		radio_player_set_class('muted');
		if (radio_player.debug) {console.log('Muted Top Player');}
	}
}, false);

/* --- add player unmuted event listener ---*/
document.addEventListener('rp-unmuted', function(e) {
	if (radio_player.debug) {console.log(e.detail);}
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio == 'object') && (wintop.current_radio.instance == e.detail.instance)) {
		radio_player_set_state('muted', false);
		radio_player_set_class('unmuted');
		if (radio_player.debug) {console.log('Unmuted Top Player');}
	}
}, false);

/* --- add additional sysend listeners --- */
document.addEventListener('rp-sysend', function() {
	sysend.on('radio-autoresume', function(message) {radio_player_check_if_playing(message);} );
	sysend.on('radio-playing', function(message) {radio_player_cancel_autoresume(message);} );
}, false);

/* --- add player autoresume and continuous loaders --- */
document.addEventListener('rp-state-loaded', function() {
	if ((typeof radio_player.settings.autoresume != 'undefined') && radio_player.settings.autoresume) {
		if (radio_player.debug) {console.log('Loading Autoresume Player Functions');}
		radio_player_autoresume_loader();
	}
	if ((typeof radio_player.settings.continuous != 'undefined') && radio_player.settings.continuous) {
		if (radio_player.debug) {console.log('Loading Continuous Player Functions');}
		radio_player_continuous_loader();

		/* maybe clone current player classes */
		wintop = radio_player_top_window();
		if (wintop != window.self) {
			radio_player_set_bar_classes();
			/* maybe clone volume width display */
			if ((typeof wintop.current_radio != 'object') || (wintop.current_radio == null)) {return;}
			if (!radio_player_is_current_playing()) {return;}
			cinstance = wintop.current_radio.instance;
			volume = radio_player_get_volume(cinstance);
			instance = jQuery('#radio-station-player-bar .radio-container').attr('id').replace('radio_container_','');
			radio_player_volume_slider(instance, volume);
		}
	}
}, false);

/* --- set player bar classes --- */
function radio_player_set_bar_classes() {
	if (typeof wintop.player_classes != 'undefined') {
		classes = wintop.player_classes;
		if (radio_player.debug) {console.log('Syncing Player Bar Classes: '+classes);}
		if (classes.length) {
			for (i in classes) {jQuery('#radio-station-player-bar .radio-container').addClass(classes[i]);}
		}
	}
}

/* === Top Window Helper === */

/* --- get top window --- */
function radio_player_top_window() {
	try {test = window.top.location; return window.top;} catch(e) {
		return radio_player_get_window_parent(window.self);
	}
}

/* --- get window parent --- */
function radio_player_get_window_parent(win) {
	parentwindow = false;
	try {test = win.parent.location; parentwindow = win.parent;} catch(e) {return false;}
	if (parentwindow) {
		if (parentwindow == win) {return win;}
		maybe = radio_player_get_window_parent(parentwindow);
		if (maybe) {return maybe;}
		return parentwindow;
	}
	return win;
}

/* === Continous Player === */

/* --- continuous playback loader --- */
function radio_player_continuous_loader() {

	/* only for subwindows where teleporter is loaded */
	if (!window.parent || (typeof teleporter == 'undefined')) {return;}

	/* start check loop if not loaded yet */
	if (radio_player.loading) {
		var radio_player_continuous_check;
		radio_player_continous_check = setInterval(function() {
			if (!radio_player.loading) {
				clearInterval(radio_player_continous_check);
				radio_player_continuous_loader();
			}
		}, 1000);
		return;
	}

	/* get top window player */
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio != 'object') || (wintop.current_radio == null)) {return;}
	if (!radio_player_is_current_playing()) {return;}
	topradio = wintop.current_radio;
	
	/* get debug state from top window */
	radio_player.debug = wintop.radio_player.debug;

	/* copy top player states to default instance */
	if (radio_player.debug) {console.log('Duplicating Current Radio Player States');}
	cinstance = topradio.instance; instance = radio_player_default_instance();
	container = jQuery('#radio_container_'+instance);
	container.addClass('loaded playing').removeClass('loading paused stopped error');
	volume = radio_player_get_current_volume();
	if (volume == 100) {container.addClass('maxed');} else {container.removeClass('maxed');}
	radio_player_volume_slider(instance, volume);
	if (wintop.jQuery('#radio_container_'+cinstance).hasClass('muted')) {container.addClass('muted');}
	if (wintop.jQuery('#radio_container_'+cinstance).attr('pre-muted-volume')) {
		premutedvolume = wintop.jQuery('#radio_container_'+cinstance).attr('pre-muted-volume');
		container.attr('pre-muted-volume', premutedvolume);
	}

	/* copy show image/titles */
	setTimeout(function() {
		wintop = radio_player_top_window();
		if (typeof wintop.radio_data.metadata.show_title != 'undefined') {jQuery('#radio_container_'+instance+' .rp-show-title').html(wintop.radio_data.show_title);}
		if (typeof wintop.radio_data.metadata.show_image_url != 'undefined') {jQuery('#radio_container_'+instance+' .rp-show-image').css('background-image',wintop.radio_data.metadata.show_image_url).removeClass('no-image');}
		if (typeof wintop.radio_data.metadata.now_playing != 'undefined') {jQuery('#radio_container_'+instance+' .rp-now-playing').html(wintop.radio_data.metadata.now_playing);}
		if (radio_player.debug) {
			console.log('Show Title: '+jQuery('#radio_container_'+instance+' .rp-show-title').html());
			console.log('Show Image URL: '+jQuery('#radio_container_'+instance+' .rp-show-image').css('background-image'));
			console.log('Now Playing: '+jQuery('#radio_container_'+instance+' .rp-now-playing').html());
		}
	}, 1000);

	detail = {instance: instance}
	radio_player_custom_event('rp-continuous', detail);
}

/* --- toggle current player play/pause --- */
function radio_player_toggle_current(instance) {
	/* TODO: maybe bug out if playing custom file/stream source ? */
	wintop = radio_player_top_window();
	done = false;
	if ((typeof wintop.current_radio == 'object') && (wintop.current_radio != null)) {
		topradio = wintop.current_radio; /* playing = topradio.playing; */
		playing = radio_player_is_current_playing();
		cinstance = topradio.instance;
		if (radio_player.debug) {console.log('Instance: '+instance+' - Current Instance: '+cinstance); console.log(topradio);}
		cwin = topradio.windowid; windowid = radio_player_window_guid();
		if ((cwin == windowid) && (instance != cinstance)) {
			if (playing) {
				if (radio_player.debug) {console.log('Pausing Same Window Top Player.');}
				/* radio_data.data[instance] = radio_data.data[cinstance]; */
				radio_player_pause_instance(cinstance);
				done = true;
			} /* else if (radio_data.data[instance] == radio_data.data[cinstance]) {
				if (radio_player.debug) {console.log('(Re)Playing Same Window Top Player.');}
				radio_player_play_instance(cinstance); return true;
			} */ else {
				if (radio_player.debug) {console.log('Pausing Same Window Top Player and starting player.');}
				radio_player_pause_instance(cinstance);
			}
		} else if (cwin != windowid) {
			if (playing) {
				if (radio_player.debug) {console.log('Pausing Different Window Top Player.');}
				/* radio_data.data[instance] = wintop.radio_data.data[cinstance]; */
				radio_player_broadcast_request(cwin, cinstance, 'pause', 0);
				done = true;
			} /* else if (radio_data.data[instance] == wintop.radio_data.data[cinstance]) {
				if (radio_player.debug) {console.log('(Re)Playing Different Window Top Player.');}
				radio_player_broadcast_request(cwin, cinstance, 'play', 0); return true;
			} */ else {
				if (radio_player.debug) {console.log('Pausing Different Window Top Player and starting player.');}
				radio_player_broadcast_request(cwin, cinstance, 'pause', 0);			
			}
		}
		/* TODO: ensure current window class matches playback state */
		radio_player_set_bar_classes();
	}
	return done;
}

/* --- sync current player volume --- */
function radio_player_sync_volume(instance, volume, mute) {
	if (radio_player.debug) {console.log('Sync Volume : Instance:'+instance+' : Volume:'+volume+' : Mute:'+mute);}
	/* TODO: bug out if playing custom file/stream source ? */
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio == 'object') && (wintop.current_radio != null)) {
		topradio = wintop.current_radio; cinstance = topradio.instance;
		cwin = topradio.windowid; windowid = radio_player_window_guid();
		if ((cwin == windowid) && (instance != cinstance)) {
			if (radio_player.debug) {console.log('Syncing Volume to Same Window Top Player');}
			if (volume !== null) {radio_player_change_volume(cinstance, volume);}
			if (mute !== null) {radio_player_mute_unmute(cinstance, mute);}
		} else if (cwin != windowid) {
			if (radio_player.debug) {console.log('Syncing Volume to Different Window Top Player');}
			if (volume !== null) {radio_player_broadcast_request(cwin, cinstance, 'volume', volume);}
			if (mute !== null) {
				if (mute) {mute = '1';} else {mute = '0';}
				radio_player_broadcast_request(cwin, cinstance, 'mute', mute);
			}
		}
	}
}

/* --- get current radio player --- */
function radio_player_current_radio() {
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio != 'object') || (wintop.current_radio == null)) {return false;}
	return wintop.current_radio;
}

/* --- is current player playing --- */
function radio_player_is_current_playing() {
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio != 'object') || (wintop.current_radio == null)) {return false;}
	instance = wintop.current_radio.instance;
	player = wintop.radio_data.players[instance]; script = wintop.radio_data.scripts[instance];
	if (script == 'amplitude') {
		state = player.getPlayerState();
		if (state == 'playing') {playing = true;} else {playing = false;}
	} else if (script == 'howler') {
		playing = player.playing();
	} else if (script == 'jplayer') {
		/* ? possible bug: get status not working ? */
		try {playing = !player.jPlayer.status.paused;}
		catch(e) {playing = !player.data().jPlayer.status.paused;}
	}
	if (radio_player.debug) {
		if (playing) {console.log('Top Player Instance '+instance+' ('+script+') is playing.');}
		else {console.log('Top Player Instance '+instance+' ('+script+') is not playing.');}
	}
	return playing;
}

/* --- get top current radio volume --- */
function radio_player_get_current_volume() {
	topradio = radio_player_current_radio();
	if (!topradio) {return false;}
	wintop = radio_player_top_window(); cinstance = topradio.instance;
	player = wintop.radio_data.players[cinstance]; script = wintop.radio_data.scripts[cinstance];
	if (script == 'amplitude') {volume = player.getVolume();}
	else if (script == 'howler') {volume = (player.volume() * 100);}
	else if (script == 'jplayer') {volume = (player.jPlayer('volume') * 100);}
	return volume;
}

/* --- store player class --- */
function radio_player_set_class(action) {
	wintop = radio_player_top_window();
	if (typeof wintop.player_classes != 'undefined') {
		remove = new Array();
		if (action == 'loading') {remove = ['loaded','playing','stopped','paused','error'];}
		else if (action == 'loaded') {remove = ['loading','error'];}
		else if (action == 'paused') {remove = ['playing','stopped'];}
		else if (action == 'playing') {remove = ['paused','stopped','error'];}
		else if (action == 'stopped') {remove = ['playing','paused','loaded','loading'];}
		else if (action == 'error') {remove = ['playing','paused','loaded','loading'];}
		else if (action == 'muted') {remove = ['unmuted'];}
		else if (action == 'unmuted') {remove = ['muted'];}
		if (remove.length) {
			for (i in remove) {
				j = wintop.player_classes.indexOf(remove[i]);
				if (j > -1) {wintop.player_classes.splice(j,1);}
			}
		}
	} else {wintop.player_classes = [];}
	found = false;
	if (wintop.player_classes.length) {
		for (i in wintop.player_classes) {
			if (wintop.player_classes[i] == action) {found = true;}
		}
	}
	if (!found) {wintop.player_classes[wintop.player_classes.length] = action;}
}


/* === Player Autoresume === */

/* --- autoresume player loader --- */
function radio_player_autoresume_loader() {

	/* maybe just continue playing if in a subwindow ? */
	wintop = radio_player_top_window();
	if ((wintop.current_radio == 'object') && (wintop.current_radio != null)) {return;}

	/* set player data */
	data = radio_data.state.data;
	if (data.script) {script = data.script;} else {script = radio_player.settings.script;}

	/* get player instance */
	instance = false; station = radio_data.state.station;
	if (station > 0) {
		jQuery('.radio_player.script-'+script).each(function() {
			if (!instance && (jQuery(this).attr('station-id') == station)) {
				instance = parseInt(jQuery(this).attr('id').replace('radio_player_', ''));
			}
		});
	}
	if (!instance) {instance = radio_player_default_instance();}

	/* load the player instance */
	if (radio_player.debug) {console.log('Loading stream script '+script+' for autoresume...');}
	loaded = radio_player_check_script(script);
	if (loaded) {
		if (script == 'amplitude') {player = radio_player_amplitude(instance, data.url, data.format, data.fallback, data.fformat);}
		else if (script == 'jplayer') {player = radio_player_jplayer(instance, data.url, data.format, data.fallback, data.fformat);}
		else if (script == 'howler') {player = radio_player_howler(instance, data.url, data.format, data.fallback, data.fformat);}
		if (player) {radio_player_set_data_state(script, instance, data.url, data.format, data.fallback, data.fformat, data.start);}
	}

	/* players gonna play */
	radio_player_autoresume_check(instance);
}

/* --- send autoresume check request --- */
function radio_player_autoresume_check(instance) {
	radio_player.autoresume = {'instance': instance, 'unlocked': false, 'source': false, 'cancelled': false}
	if (radio_player.settings.singular && (typeof sysend != 'undefined')) {
		windowid = radio_player_window_guid();
		message = 'Window '+windowid+' intends to autoresume playing.';
		if (radio_player.debug) {console.log(message);}
		sysend.broadcast('radio-autoresume', {message: message});
	}
	radio_player.autoresumer = setInterval(function() {
		if (radio_player.autoresume.cancelled) {
			radio_player_remove_unlock_events();
			clearInterval(radio_player.autoresumer); return;
		}
		if (!radio_player.autoresume.unlocked) {
			source = radio_player.autoresume.source;
			if (!source) {
				try {
					AudioContext = window.AudioContext || window.webkitAudioContext;
					testContext = new AudioContext();
					buffer = testContext.createBuffer(1, 1, 22050);
					source = testContext.createBufferSource();
					source.buffer = buffer;
					source.connect(testContext.destination);
			        if (typeof source.start === 'undefined') {source.noteOn(0);} else {source.start(0);}
			        if (typeof testContext.resume === 'function') {testContext.resume();}
			        radio_player.autoresume.source = source;
			        if (radio_player.debug) {console.log('Test Audio Source Context Created');}
				} catch(e) {
				   /* could not create audio context test */
				   radio_player_remove_unlock_events();
				   clearInterval(radio_player.autoresumer); return;
		   		}
			}
		}
		if (radio_player.autoresume.unlocked) {

			/* load player if not already loaded */
			if (radio_player.debug) {console.log('Attempting Autoresuming of Playback');}
			instance = radio_player.autoresume.instance;
			if (typeof radio_data.players[instance] == 'undefined') {
				jQuery('#radio_container_'+instance).addClass('loading');
				loaded = radio_player_check_script(script);
				if (loaded) {
					if (script == 'amplitude') {player = radio_player_amplitude(instance, data.url, data.format, data.fallback, data.fformat);}
					else if (script == 'jplayer') {player = radio_player_jplayer(instance, data.url, data.format, data.fallback, data.fformat);}
					else if (script == 'howler') {player = radio_player_howler(instance, data.url, data.format, data.fallback, data.fformat);}
					if (player) {
						radio_player_set_data_state(script, instance, data.url, data.format, data.fallback, data.fformat, data.start);
						details = data; data.script = script; data.instance = instance;
						radio_player_custom_event('rp-loaded', details);
					}
					clearInterval(radio_player.autoresumer);
				} else {return;}
			} else {clearInterval(radio_player.autoresumer); return;}

			/* start playing the auto resumed player */
			/* TODO: maybe fade in playback ? */
			if (radio_data.state.playing) {
				if (radio_player.debug) {console.log('Attempting to Autoresume Player Audio');}
				clearInterval(radio_player.autoresumer);
				instance = radio_player.autoresume.instance; station = radio_data.state.station;
				if (station > 0) {jQuery('#radio_player_'+instance).attr('station-id', station);}
				radio_player_pause_others(instance);
				/* radio_player_play_instance(radio_player.autoresume.instance); */
				radio_player_play_on_load(radio_data.players[instance], radio_data.scripts[instance], instance);
			}
		}
	}, 1000);
	radio_player_add_unlock_events();
}

/* --- add unlock event listeners --- */
function radio_player_add_unlock_events() {
	if (radio_player.debug) {console.log('Adding User Audio Unlock Events');}
	document.addEventListener('touchstart', radio_player_autoresume_unlock, true);
	document.addEventListener('touchend', radio_player_autoresume_unlock, true);
	document.addEventListener('click', radio_player_autoresume_unlock, true);
}

/* --- add unlock event listeners --- */
function radio_player_remove_unlock_events() {
	if (radio_player.debug) {console.log('Removing User Audio Unlock Events');}
	document.removeEventListener('touchstart', radio_player_autoresume_unlock, true);
	document.removeEventListener('touchend', radio_player_autoresume_unlock, true);
	document.removeEventListener('click', radio_player_autoresume_unlock, true);
}

/* --- unlock autoresume on user event --- */
function radio_player_autoresume_unlock() {
	if (radio_player.debug) {console.log('User Event Unlocked Audio');}
	radio_player_remove_unlock_events();
	/* slight delay on check to allow for cancellation */
	setTimeout(function() {
		if (radio_player.debug) {console.log(radio_player.autoresume.cancelled);}
		if (!radio_player.autoresume.cancelled) {radio_player.autoresume.unlocked = true;}
	}, 500);
}

/* --- reply to autoresume check request --- */
function radio_player_check_if_playing(message) {
	if (!radio_player.settings.singular) {return;}
	wintop = radio_player_top_window();
	if ((typeof wintop.current_radio == 'object') && (wintop.current_radio != null)) {
		/* TODO: maybe also check if muted or zero volume ? */
		if (radio_player.debug) {console.log(wintop.current_radio);}
		playing = radio_player_is_current_playing();
		if (playing) {
			windowid = radio_player_window_guid();
			message = 'Window '+windowid+' is Playing';
			if (radio_player.debug) {console.log(message);}
			sysend.broadcast('radio-playing', {message: message});
		}
	}
}

/* --- cancel autoresume request if playing --- */
function radio_player_cancel_autoresume(message) {
	if ((typeof radio_player.settings.autoresume == 'undefined') || !radio_player.settings.autoresume) {return;}
	if (radio_player.debug) {console.log(message.message);}
	if (radio_player.debug) {console.log('Autoresume of Player cancelled.');}
	radio_player.autoresume.cancelled = true;
}


/* === Window Resize Fix === */

/* --- reset window height on resize --- */
function radio_player_window_resize() {
	/* attempt to fix for iOS bottom bar */
	document.body.style.height = window.innerHeight+'px';
}

/* --- debounce delay callback --- */
var radio_player_debounce = (function () {
	var debounce_timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) {uniqueId = "nonuniqueid";}
		if (debounce_timers[uniqueId]) {clearTimeout (debounce_timers[uniqueId]);}
		debounce_timers[uniqueId] = setTimeout(callback, ms);
	};
})();

/* --- pageload functions --- */
if (typeof jQuery == 'function') {
	jQuery(document).ready(function() {
		radio_player_window_resize();
		jQuery(window).resize(function () {
			radio_player_debounce(radio_player_window_resize, 500, 'playerpage');
		});
	});
} else {
	if (window.addEventListener) {
		document.body[addEventListener]('load', radio_player_window_resize, false);
		document.body[addEventListener]('resize', radio_player_window_resize, false);
	} else {
		document.body[attachEvent]('onload', radio_player_window_resize, false);
		document.body[attachEvent]('onresize', radio_player_window_resize, false);
	}
}
