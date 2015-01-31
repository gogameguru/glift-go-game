<?php

// register the 'glift' shortcode with WordPress
add_shortcode( 'glift', 'glift_do_shortcode' );

// glift_do_shortcode is executed every time one of our shortcodes is found
// this function returns JSON formatted HTML so WordPress can load glift.js
function glift_do_shortcode( $atts, $content, $tag ) {
	#TODO(dormerod): add shortcode_atts() defaults/masks
	
	// create new Glift object and populate it with shortcode data
	$glift = new Glift();
	
	if ( $glift->eat_shortcode( $atts, $content, $tag ) ) {
		$html = $glift->get_html(); // our shortcode was good, so get the data
	
	} else {
		return FALSE; // the shortcode was indigestible, so don't return HTML
	}

	return $html; // we ate the shortcode, return an HTML snippet to WordPress
}
