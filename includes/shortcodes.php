<?php

// register the glift shortcode
add_shortcode( 'glift', 'glift_do_shortcode' );

// glift_do_shortcode is executed every time one of our shortcodes is found
function glift_do_shortcode( $atts, $content, $tag ) {
	#TODO(dormerod): add shortcode_atts() defaults/masks

	// make the Glift object
	$glift_object = glift_objectify_shortcode( $atts, $content, $tag );
	// if we get an object back, transform it into JSON encoded HTML
	if ( $glift_object ) $html = glift_to_html( $glift_object );
	return $html;
}
