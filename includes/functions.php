<?php

// add SGF to allowable file extensions for upload
function glift_mime_types( $mime_types ) {
	$mime_types['sgf'] = 'application/x-go-sgf';
	return $mime_types;
}

// registers JavaScript with WordPress
function glift_register_scripts() {

	global $glift_js_version;
	global $glift_js_deps;
	global $glift_url;

	// get current WordPress jQuery version so we know which Library to load
	wp_enqueue_script( 'jquery' );
	$wp_jquery_ver = $GLOBALS['wp_scripts']->registered['jquery']->ver;

	// now deregister native WP jQuery, because it doesn't work with Glift
	wp_deregister_script( 'jquery' );

	// now register scripts
	$glift_jquery_url =
	"//ajax.googleapis.com/ajax/libs/jquery/$wp_jquery_ver/jquery.min.js";
	$glift_js_url = $glift_url.'/js/glift.js';
	wp_register_script( 'jquery', $glift_jquery_url, false, $wp_jquery_ver );
	wp_register_script( 'glift', $glift_js_url, $glift_js_deps, $glift_js_version );
}

function glift_enqueue_scripts() {
	#TODO(dormerod): only load scripts when they're needed
	wp_enqueue_script( 'jquery' ); // enqueue our new jquery
	wp_enqueue_script( 'glift' );
}

// cleans up shortcode inputs and returns a Glift object
function glift_objectify_shortcode( $atts, $content, $tag ) {

#TODO(dormerod): turn this into a Glift.method and build out properties

	// did our shortcode send any data?
	if ( $atts ) {
		// if so then clean it up
		$clean_atts = array_map( 'sanitize_text_field', $atts );
		// ignore $content for now
		#TODO(dormerod): swap around $atts and $content checking

	} elseif ( !$content ) {
		// if we don't have any data, then return nothing
		return;
	} // end of shortcode checks if block

	static $id = 1; // keep track of unique div id within this function
	$divId = esc_attr( "glift_display$id" );
	$id++;

	// do we have an sgf url?
	if ( $clean_atts['sgfurl'] ) {
		$sgf = esc_url($clean_atts['sgfurl']);
	} elseif ( $content ) {
		// if not, do we have $content? clean it instead
		$clean_content = sanitize_text_field( $content );
		$sgf = $clean_content );
	} else {
		// we don't have enough data, so return nothing
		return;
	}

	$glift_object = new Glift( $divId, $sgf );

	return $glift_object;
}

// JSON encodes Glift object and returns it as HTML for glift.js to process
function glift_to_html ( $glift_object ) {
	#TODO(dormerod): move some of the style info to somewhere more reusable

	$divId = esc_attr( $glift_object->divId );
	$json = json_encode( $glift_object, JSON_PRETTY_PRINT );
	$html =
"<div id='$divId' style='height:500px; width:100%; position:relative;'></div>
&nbsp;
<script type='text/javascript'>
gliftWidget = glift.create($json);
</script>";
	return $html;
}
