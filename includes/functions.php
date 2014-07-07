<?php

// add SGF and JSON to allowable file extensions for upload
#TODO(dormerod): document these functions properly using PHPDoc
function glift_mime_types( $mime_types ) {
	$mime_types['sgf'] = 'application/x-go-sgf';
	$mime_types['json'] = 'application/json';
	return $mime_types;
}


// registers JavaScript with WordPress
function glift_register_scripts() {
	
	if ( is_admin() ) return;

	global $glift_js_version;
	global $glift_js_deps;
	global $glift_url;

	// get current WordPress jQuery version so we know which Library to load
	wp_enqueue_script( 'jquery' );
	$wp_jquery_ver = $GLOBALS['wp_scripts']->registered['jquery']->ver;

	// now deregister native WP jQuery, because it does not work with Glift
	wp_deregister_script( 'jquery' );

	// now register scripts
	$glift_jquery_url =
	"//ajax.googleapis.com/ajax/libs/jquery/$wp_jquery_ver/jquery.min.js";
	$glift_js_url = $glift_url.'/js/glift.js';
	wp_register_script( 'jquery', $glift_jquery_url, false, $wp_jquery_ver );
	wp_register_script( 
		'glift', 
		$glift_js_url, 
		$glift_js_deps, 
		$glift_js_version 
	);
}


function glift_enqueue_scripts() {
	#TODO(dormerod): only load scripts when needed
	
	// don't load scripts in admin dashboard
	if ( is_admin() ) return;

	wp_enqueue_script( 'jquery' ); // enqueue our jquery
	wp_enqueue_script( 'glift' );
}


// test whether a string is a url, return boolean
function glift_is_url( $url ) {
	
	$result = filter_var( $url, FILTER_VALIDATE_URL ) ? TRUE : FALSE;
	return $result;
}


// get the lowercase file extension from a url
function glift_get_filetype( $url ) {

	$extension = pathinfo( $url, PATHINFO_EXTENSION );
	$extension = strtolower( $extension );
	return $extension;
}


// test whether a string looks like an SGF literal, return boolean
function glift_is_sgf( $sgf_data ) {
	
	#TODO(dormerod): make this SGF format regex less restrictive/more robust
	$sgf_pattern = '/^\(;GM\[1\]FF\[4\].*\)$/';
	$result = preg_match( $sgf_pattern, $sgf_data ) ? TRUE : FALSE;
	return $result;
}


// escape a string and return it for output to browser
function glift_escape( $input ) {
	
	if ( glift_is_url( $input ) ) {
		$escaped = esc_url( $input );
	
	} else {
		$escaped = esc_js( $input );
	}
	return $escaped;
}


// recursively maps a function against arrays and objects
// returns an array containing the mapped values
// any object properties will be copied to an array in the process
// null values are omitted
function glift_mega_map( $callback, $array, $args ) {

	$new = array(); // new array to return results
	
	// check if we have a collection that can be accessed as an array
	if ( is_array( $array ) || $array instanceof ArrayAccess ) {
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) || $value instanceof ArrayAccess ) {
				$new[$key] = glift_mega_map( $callback, $value, $args );
						
			} else {
				// $value isn't an array, so we need to do some more work
				if ( !is_array( $args ) || empty( $args ) ) {
					if ( isset( $value ) ) { 
						// execute our callback function on the current $value
                    	$value = array( $value ); // we need an array argument
						$new[$key] = call_user_func_array( $callback, $value ); 
					}

				} elseif ( isset( $value ) ) {
					// prepend the current $value to $args and call function
					$args =	array_unshift( $args, $value );
					$new[$key] = call_user_func_array( $callback, $args ); 
				}
				// if $value and $args are empty, then we'll omit this element
			}
		}
	
	// if we don't have a collection, run our function directly on $array
	// the logic is the same as above except we assign to $new directly
	} else {
		if ( !is_array( $args ) || empty( $args ) ) {
        	if ( isset( $array ) ) { 
		    	$new = call_user_func_array( $callback, $array ); 
			}
		                                                                 
		} elseif ( isset( $array ) ) {
			// prepend the current $array to $args and call function
			$args =	array_unshift( $args, $array );
			$new = call_user_func_array( $callback, $args ); 
		}
		// if $array and $args are empty, then we omit this variable
	}

	return $new;
}


/** Checks the user's browser to see if they can support glift.js
 * Blacklist IE 8 and earlier (we only check 4-8 because 1-3 are really old).
 * If someone is using IE 1, they have bigger problems than Glift not working.
 * Blacklist Android 2.X and earlier.
 *
 * Returns TRUE if browser is ok, FALSE if not.
 */
function glift_browser_ok() {

	// Blacklist array containing regex patterns for browsers we don't like :)
	$blacklist = array( '/msie [4-8]/i', '/android [1-2]/i' );
	
	// Note: This regex will have to be updated when Android reaches version 10,
	// or when Internet Explorer reaches version 40.
	// The simplest solution will be to just change it to '/android 2/i' later.	
	// It could also be changed to something more robust in the meantime.

	$user_agent = $_SERVER['HTTP_USER_AGENT'];

	foreach ( $blacklist as $browser ) {
		
		// return false if we find any blacklisted browsers
		if ( preg_match( $browser, $user_agent ) ) return FALSE;
	}

	return TRUE; // we didn't find a blacklisted browser, so return true
}
