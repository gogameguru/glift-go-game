<?php

// add .sgf to allowable file extensions for upload
function glift_mime_types( $glift_mime_types ) {
	$glift_mime_types['sgf'] = 'application/x-go-sgf';
	return $glift_mime_types;
}

function glift_register_scripts( $glift_url ) {
	#TODO use google libraries for jquery
	#TODO option to load scripts in footer, but not all themes support wp_footer() hook, so have to check first	
	// register glift javascript
	global $glift_js_version;
	global $glift_url;
	$glift_js_url = $glift_url.'/js/glift.js';
	wp_register_script( 'glift', $glift_js_url, 'jquery', $glift_js_version ); //wp_register_script( $handle, $src, $deps, $ver, $in_footer )
}

function glift_enqueue_scripts() {
	#TODO logic for when to add scripts
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'glift' );
}
