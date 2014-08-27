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
	
	if ( is_admin() ) return; // don't load scripts in admin dashboard

	// register scripts
	$glift_js_url = GLIFT_URL.'/js/glift.js';
	wp_register_script( 'glift', $glift_js_url, array(), GLIFT_JS_VERSION );
}


function glift_enqueue_scripts() {
	#TODO(dormerod): only load scripts when needed
	
	if ( is_admin() ) return;

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
						$new[$key] = call_user_func( $callback, $value ); 
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
				$new = call_user_func( $callback, $array ); 
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

