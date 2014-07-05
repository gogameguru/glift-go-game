<?php

// add SGF and JSON to allowable file extensions for upload
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

// get the file extension from a url
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

