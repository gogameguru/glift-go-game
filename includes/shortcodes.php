<?php

// register the glift shortcode
add_shortcode( 'glift', 'glift_do_shortcode' );

// glift_do_shortcode is executed every time one of our shortcodes is found
function glift_do_shortcode( $atts, $content, $tag ) {
	#TODO(dormerod): add shortcode_atts() defaults/masks

	// make Glift object and return as JSON
	$glift = new Glift();
	$glift->eat_shortcode( $atts, $content, $tag );
	$html = $glift->get_html();
	return $html;
}
