<?php

// =========================
// === Radio Station Pro ===
// =========================
// ----- Role Interface ----
// =========================

// === Role Interface ===
// - Filter Role Editor Message
// - Role Assignment Interface
// - Enqueue Role Interface Script
// - Update User Roles


// ----------------------
// === Role Interface ===
// ----------------------

// --------------------------
// Filter Role Editor Message
// --------------------------
add_filter( 'radio_station_role_editor_message', '__return_false' );

// -------------------------
// Role Assignment Interface
// -------------------------
add_action( 'radio_station_admin_page_section_permissions_bottom', 'radio_station_pro_role_editor' );
function radio_station_pro_role_editor() {

	// --- check admin capability ---
	// 2.4.1.4: change capability from manage_options
	if ( !current_user_can( 'edit_users' ) ) {
		return;
	}

	// --- remove role editor available for Pro message ---
	remove_action( 'radio_station_admin_page_section_permissions_bottom', 'radio_station_role_editor' );

	// --- get all users ---
	$users = get_users();
	$all_users = $hosts = $producers = $show_editors = array();
	foreach ( $users as $user ) {
		$user_id = $user->ID;
		$display_name = $user->display_name . ' (' . $user->user_login . ')';
		if ( in_array( 'dj', $user->roles ) ) {$hosts[$user_id] = $display_name;}
		if ( in_array( 'producer', $user->roles ) ) {$producers[$user_id] = $display_name;}
		if ( in_array( 'show-editor', $user->roles ) ) {$show_editors[$user_id] = $display_name;}
		$all_users[$user_id] = $display_name;
	}

	// --- assign role information ---
	$roles = array(
		'hosts'	=> array(
			'label' => __( 'DJs / Hosts', 'radio-station' ),
			'users' => $hosts,
		),
		'producers' => array(
			'label'	=> __( 'Producers', 'radio-station' ),
			'users'	=> $producers,
		),
		'show-editors' => array(
			'label'	=> __( 'Show Editors', 'radio-station' ),
			'users' => $show_editors,
		),
	);

	// --- role editor interface title ---
	echo '<br><h3>' . __( 'Role Assignment Interface', 'radio-station' ) . '</h3>' . PHP_EOL;

	// --- role assignment interface message ---
	echo esc_html( __( 'You can assign Radio Station roles to users directly here.', 'radio-station' ) ) . PHP_EOL;
	echo '<br>' . PHP_EOL;
	echo esc_html( __( 'They can also be assigned via the WordPress user editor.', 'radio-station' ) ) . PHP_EOL;

	// --- loop roles to create interfaces ---
	foreach ( $roles as $key => $role ) {

		echo '<div id="roles-' . $key . '-selection" class="role-selection">' . PHP_EOL;
			echo '<b>' . esc_attr( $role['label'] ) . '</b><br>' . PHP_EOL;

			// --- users with this role ---
			echo '<div style="display:inline-block;">' . PHP_EOL;
				$id = $name = 'rs-' . $key . '-users';
				echo '<select id="' . $id . '" class="role-select" multiple="multiple">' . PHP_EOL;
				foreach ( $role['users'] as $user_id => $label ) {
					echo '<option value="' . $user_id . '">' . $label . '</option>' . PHP_EOL;
				}
				echo '</select>' . PHP_EOL;
			echo '</div>' . PHP_EOL;

			echo '<div class="role-spacer">&nbsp;</div>' . PHP_EOL;

			// --- add and remove role buttons ---
			echo '<div style="display:inline-block; vertical-align:middle; text-align:center;">' . PHP_EOL;

				$onclick = "radio_role_change('add', '" . $key . "');";
				$label = '&larr; ' . esc_attr( __( 'Grant Role to User(s)', 'radio-station' ) ) . ' &larr;';
				echo '<input type="button" class="button button-secondary" onclick="' . $onclick . '" value="' . $label . '">' . PHP_EOL;
				echo '<br><br>';

				$onclick = "radio_role_change('remove', '" . $key . "');";
				$label = '&rarr; ' . esc_attr( __( 'Remove Role from User(s)', 'radio-station' ) ) . ' &rarr;';
				echo '<input type="button" class="button button-secondary" onclick="' . $onclick . '" value="' . $label . '">' . PHP_EOL;

			echo '</div>' . PHP_EOL;

			echo '<div class="role-spacer">&nbsp;</div>' . PHP_EOL;

			// --- users without this role ---
			echo '<div style="display:inline-block;">';
				$id = 'rs-' . $key . '-non-users';
				echo '<select id="' . $id . '" class="role-select" multiple="multiple">' . PHP_EOL;
				foreach ( $all_users as $user_id => $label ) {
					if ( !array_key_exists( $user_id, $role['users'] ) ) {
						echo '<option value="' . $user_id . '">' . $label . '</option>' . PHP_EOL;
					}
				}
				echo '</select>' . PHP_EOL;
			echo '</div>' . PHP_EOL;

			$name = 'rs-' . $key . '-userlist';
			$user_list = implode( ',', array_keys( $role['users'] ) );
			echo '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $user_list . '">' . PHP_EOL;
			echo '<input type="hidden" name="' . $name . '-old" id="' . $name . '-old" value="' . $user_list . '">' . PHP_EOL;
			// 2.4.1.6: change to track removed roies instead of old roles
			echo '<input type="hidden" name="' . $name . '-remove" id="' . $name . '-remove" value="">' . PHP_EOL;

		echo '</div><br>' . PHP_EOL;
	}

	// --- role update button ---
	// 2.4.1.4: added standalone role update button
	echo '<center><div class="role-update-button">' . PHP_EOL;
	echo '<input type="button" class="button-secondary" onclick="radio_update_roles();" value="' . __( 'Update User Roles', 'radio-station' ) . '">' . PHP_EOL;
	echo '</div><br>';

	// --- unsaved changes message ---
	echo '<div id="role-update-changes" style="display:none;">' . PHP_EOL;
	echo __( 'You have unsaved role changes.', 'radio-station' );
	echo '</div>';

	// --- role update message ---
	// 2.4.1.4: added role update message
	echo '<div id="role-update-result" style="display:none;"></div>';
	echo '</center><br>' . PHP_EOL;
	
	// --- AJAX role update iframe ---
	// 2.4.1.4: added iframe for AJAX update
	echo '<iframe src="javascript:void(0);" id="role-update-frame" name="role-update-frame" style="display:none;"></iframe>' . PHP_EOL;

	// --- role editor styles ---
	echo "<style>.role-selection .role-spacer {width:30px; display:inline-block;}
	.role-selection select.role-select {width:200px; height:150px;}</style>" . PHP_EOL;

}

// -----------------------------
// Enqueue Role Interface Script
// -----------------------------
// 2.4.1.4: fix to incorrect script function hook name
add_action( 'admin_enqueue_scripts', 'radio_station_pro_role_interface_script' );
function radio_station_pro_role_interface_script() {

	// --- only load on plugin settings page ---
	if ( !isset( $_REQUEST['page'] ) || ( 'radio-station' != $_REQUEST['page'] ) ) {
		return;
	}

	// --- javascript for adding and removing roles ---
	// 2.4.1.6: remove/add from both user list input
	$js = "function radio_role_change(addremove, rolekey) {
		nonusers = document.getElementById('rs-'+rolekey+'-non-users');
		users = document.getElementById('rs-'+rolekey+'-users');
		if (addremove == 'add') {addto = users; removefrom = nonusers;}
		if (addremove == 'remove') {addto = nonusers; removefrom = users;}

		users = new Array(); remove = new Array();

		/* add to target selection */
		options = removefrom.options;
		for (i = j = k = 0; i < options.length; i++) {
			if (options[i].selected) {
				option = document.createElement('option');
				users[k] = options[i].value;
				option.value = options[i].value;
				option.innerHTML = options[i].innerHTML;
				addto.appendChild(option);
				/* resort users ? */
				remove[j] = i; j++; k++;
			}
		}

		/* remove from source selection */
		optiontags = removefrom.childNodes;
		for (i = optiontags.length - 1; i > -1; i--) {
			for (j = 0; j < remove.length; j++) {
				if (remove[j] == i) {removefrom.remove(i);}
			}
		}

		/* update user list input */
		addtoliststring = users.join(',');
		if (radio_admin.debug) {console.log(users);console.log(addtoliststring);}
		userlist = document.getElementById('rs-'+rolekey+'-userlist');
		userlistremove = document.getElementById('rs-'+rolekey+'-userlist-remove');
		if (addremove == 'add') {lista = userlist; listb = userlistremove;}
		if (addremove == 'remove') {lista = userlistremove; listb = userlist;}
		
		/* add to one list */
		if (lista.value == '') {lista.value = addtoliststring;}
		else {lista.value += ','+addtoliststring;}

		/* remove from the other */
		oldvalues = new Array(); newvalues = new Array();
		if (listb.value.indexOf(',') > -1) {
			oldvalues = listb.value.split(',');
		} else {oldvalues[0] = listb.value;}
		for (i = j = 0; i < oldvalues.length; i++) {
			found = false;
			for (k = 0; k < users.length; k++) {
				if (users[k] == oldvalues[i]) {found = true;}
			}
			if (!found) {newvalues[j] = oldvalues[i]; j++;}
		}
		listb.value = newvalues.join(',');
		radio_role_changes();
	}" . PHP_EOL;

	// --- check for role changes ---
	$js .= "function radio_role_changes() {
		rolekeys = ['hosts','producers','show-editors'];
		changed = false;
		for (i in rolekeys) {
			userlist = document.getElementById('rs-'+rolekeys[i]+'-userlist');
			userlistold = document.getElementById('rs-'+rolekeys[i]+'-userlist-old');
			if (userlist.value != userlistold.value) {changed = true;}
		}
		result = document.getElementById('role-update-result');
		changes = document.getElementById('role-update-changes');
		if (changed) {result.style.display = 'none'; changes.style.display = '';}
		else {changes.style.display = 'none'; result.style.display = '';}
	}" . PHP_EOL;
	
	// --- update roles via AJAX and iframe ---
	$js .= "function radio_update_roles() {		
		url = '" . esc_url( admin_url( 'admin-ajax.php' ) ) . "?action=radio_station_update_roles';" . PHP_EOL;
		$roles = array( 'hosts', 'producers', 'show-editors' );
		foreach ( $roles as $role ) {
			$js .= "url += '&rs-" . $role . "-userlist='+document.getElementById('rs-" . $role . "-userlist').value; " . PHP_EOL;
			$js .= "url += '&rs-" . $role . "-userlist-remove='+document.getElementById('rs-" . $role . "-userlist-remove').value; " . PHP_EOL;
		}
		$js .= "document.getElementById('role-update-frame').src = url;
	}" . PHP_EOL;
		
	// --- add inline script to radio-station-admin.js ---
	wp_add_inline_script( 'radio-station-admin', $js );

}

// -----------------
// Update User Roles
// -----------------
add_action( 'wp_ajax_radio_station_update_roles', 'radio_station_pro_update_roles' );
function radio_station_pro_update_roles() {

	// 2.4.1.4: change capability from manage_options
	if ( !current_user_can( 'edit_users' ) ) {
		return;
	}

	global $wp_roles;
	$results = array();
	$roles = array( 'host', 'producer', 'show-editor' );
	foreach ( $roles as $role ) {
	
		// 2.4.1.6: get translated role label
		$rolekey = ( 'host' == $role ) ? 'dj' : $role;
		$role_label = translate_user_role( $wp_roles->roles[$rolekey]['name'] );

		$postkey = 'rs-' . $role . 's-userlist';
		if ( isset( $_REQUEST[$postkey] ) ) {
			// 2.4.1.5: fix by moving check empty condition
			$user_ids = trim( $_REQUEST[$postkey] );
			if ( !empty( $user_ids ) ) {
				if ( strstr( $user_ids, ',' ) ) {
					$user_ids = explode( ',', $user_ids );
				} else {
					$user_ids = array( $user_ids );
				}
				if ( count( $user_ids ) > 0 ) {
					foreach ( $user_ids as $user_id ) {
						$user = get_user_by( 'ID', $user_id );
						$user_roles = $user->roles;
						if ( !in_array( $rolekey, $user_roles ) ) {
							$user->add_role( $rolekey );
							$results[] = $role_label. ' ' . __( 'role added to user', 'radio-station' ) . ' ' . $user->display_name;
						}
					}
				}
			}
		}

		// 2.4.1.6: changed to explicit use remove IDs
		$removekey = 'rs-' . $role . 's-userlist-remove';
		if ( isset( $_REQUEST[$removekey] ) ) {
			// 2.4.1.5: fix by moving check empty condition
			$remove_user_ids = trim( $_REQUEST[$removekey] ) ;
			if ( !empty( $remove_user_ids ) ) {
				if ( strstr( $remove_user_ids, ',' ) ) {
					$remove_user_ids = explode( ',', $remove_user_ids );
				} else {
					$remove_user_ids = array( $remove_user_ids );
				}
				if ( count( $remove_user_ids ) > 0 ) {
					foreach ( $remove_user_ids as $remove_user_id ) {
						$user = get_user_by( 'ID', $remove_user_id );
						$user_roles = $user->roles;
						// 2.4.1.6: only remove if user has role
						if ( in_array( $rolekey, $user_roles ) ) {
							$user->remove_role( $rolekey );
							$results[] = $role_label . ' ' . __( 'role removed from user', 'radio-station' ) . ' ' . $user->display_name;
						}
					}
				}
			}
		}
	}
	
	// --- show message in parent window ---
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		echo "<script>result = '";
		if ( count( $results ) > 0 ) {
			foreach( $results as $result ) {
				echo esc_js( $result ) . '<br>';
			}
		}
		echo "';
		parent.document.getElementById('role-update-result').innerHTML = result;
		parent.document.getElementById('role-update-result').style.display = '';
		parent.document.getElementById('role-update-changes').style.display = 'none';" . PHP_EOL;
		// 2.4.1.6: sync new values to old inputs
		$rolekeys = array( 'hosts', 'producers', 'show-editors' );
		foreach ( $rolekeys as $rolekey ) {
			echo "parent.document.getElementById('rs-" . $rolekey . "-userlist-old').value = parent.document.getElementById('rs-" . $rolekey . "-userlist').value;" . PHP_EOL;
		}
		echo "</script>";
		exit;
	}	
}

