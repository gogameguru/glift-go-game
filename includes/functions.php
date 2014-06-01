<?php

// add .sgf to allowable file extensions for upload
function glift_mime_types( $glift_mime_types ) {
	$glift_mime_types['sgf'] = 'application/x-go-sgf';
	return $glift_mime_types;
}

function glift_register_scripts( $glift_url ) {
	#TODO use google libraries for jquery?
	#TODO option to load scripts in footer, but not all themes support wp_footer() hook, so have to check first	
	// register javascript
	global $glift_js_version;
	global $glift_jquery_version;
	global $glift_url;
	$glift_jquery_url = $glift_url.'/js/jquery.js';
	$glift_js_url = $glift_url.'/js/glift.js';
	$glift_js_deps[0] = 'jquery'; //dependencies for glift - WP will try to enqueue them whenever glift.js is enqueued
	
	//deregister current version WP jquery, because it doesn't work with glift (we'll add it back in a moment)
	wp_deregister_script( 'jquery' );
	
	//now register scripts
	wp_register_script( 'jquery', $glift_jquery_url, false , $glift_jquery_version );
	wp_register_script( 'glift', $glift_js_url, $glift_js_deps, $glift_js_version ); //wp_register_script( $handle, $src, $deps, $ver, $in_footer )	
}

function glift_enqueue_scripts() {
	#TODO logic for when to add scripts
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'glift' );
}
