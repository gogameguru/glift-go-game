<?php

// register the shortcodes for Go data (actual function is in /includes/functions.php if you're looking for it)
add_shortcode( 'glift', 'glift_do_shortcode' );
add_shortcode( 'sgf', 'glift_do_shortcode' ); // add sgf shortcode for backwards compatibility with old EidoGo for WordPress plugin

// glift_do_shortcode is executed every time the shortcode is found - it cleans up user input and displays Glift
function glift_do_shortcode( $atts, $content, $tag ) {

	// make the Glift object
	$glift_object = glift_objectify_shortcode( $atts, $content, $tag );
	// if we get an object back, transform it into JSON encoded HTML
	if ( $glift_object ) $html = glift_to_html( $glift_object );
	return $html;
}
