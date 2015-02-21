<?php

// register the 'glift' shortcode with WordPress
add_shortcode( 'glift', 'glift_do_shortcode' );

// glift_do_shortcode is executed every time one of our shortcodes is found
// this function returns JSON formatted HTML so WordPress can load glift.js
function glift_do_shortcode( $atts, $content, $tag ) {

	// call glift_create and return the HTML if we receive any
	return glift_create( $atts, $content, $tag );
}
