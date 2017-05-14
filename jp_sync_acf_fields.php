<?php
/**
 * Function that will automatically update ACF field groups via JSON file update.
 *
 * @link http://www.advancedcustomfields.com/resources/synchronized-json/
 */
function jp_sync_acf_fields() {
	$groups = acf_get_field_groups();
	if (empty($groups)) {
		return;
	}

	// find JSON field groups which have not yet been imported
	$sync 	= array();
	foreach ($groups as $group) {
		$local 		= acf_maybe_get($group, 'local', false);
		$modified 	= acf_maybe_get($group, 'modified', 0);
		$private 	= acf_maybe_get($group, 'private', false);

		// ignore DB / PHP / private field groups
		if ($local !== 'json' || $private) {
			// do nothing
		} elseif (! $group['ID']) {
			$sync[$group['key']] = $group;
		} elseif ($modified && $modified > get_post_modified_time('U', true, $group['ID'], true)) {
			$sync[$group['key']]  = $group;
		}
	}

	if (empty($sync)) {
		return;
	}

	foreach ($sync as $key => $group) { //foreach ($keys as $key) {
		// append fields
		if (acf_have_local_fields($key)) {
			$group['fields'] = acf_get_local_fields($key);
		}

		// import
		$field_group = acf_import_field_group($group);
	}
}
add_action('admin_init', 'jp_sync_acf_fields');