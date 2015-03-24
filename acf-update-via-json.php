<?php

/**
 * Function that will automatically update ACF field groups via JSON file update.
 * 
 * @link http://www.advancedcustomfields.com/resources/synchronized-json/
 */
function jp_sync_acf_fields() {

	// vars
	$groups = acf_get_field_groups();
	$sync 	= array();

	// bail early if no field groups
	if( empty( $groups ) )
		return;

	// find JSON field groups which have not yet been imported
	foreach( $groups as $group ) {
		
		// vars
		$local 		= acf_maybe_get( $group, 'local', false );
		$modified 	= acf_maybe_get( $group, 'modified', 0 );
		$private 	= acf_maybe_get( $group, 'private', false );

		// ignore DB / PHP / private field groups
		if( $local !== 'json' || $private ) {
			
			// do nothing
			
		} elseif( ! $group[ 'ID' ] ) {
			
			$sync[ $group[ 'key' ] ] = $group;
			
		} elseif( $modified && $modified > get_post_modified_time( 'U', true, $group[ 'ID' ], true ) ) {
			
			$sync[ $group[ 'key' ] ]  = $group;
		}
	}

	// bail if no sync needed
	if( empty( $sync ) )
		return;

	if( ! empty( $sync ) ) { //if( ! empty( $keys ) ) {
		
		// vars
		$new_ids = array();
		
		foreach( $sync as $key => $v ) { //foreach( $keys as $key ) {
			
			// append fields
			if( acf_have_local_fields( $key ) ) {
				
				$sync[ $key ][ 'fields' ] = acf_get_local_fields( $key );
				
			}

			// import
			$field_group = acf_import_field_group( $sync[ $key ] );
		}
	}

}
add_action( 'admin_init', 'jp_sync_acf_fields' );