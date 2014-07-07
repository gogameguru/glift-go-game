<?php

// register the 'glift' shortcode with WordPress
add_shortcode( 'glift', 'glift_do_shortcode' );

// glift_do_shortcode is executed every time one of our shortcodes is found
// this function returns JSON formatted HTML so WordPress can load glift.js
function glift_do_shortcode( $atts, $content, $tag ) {
	#TODO(dormerod): add shortcode_atts() defaults/masks
	
	// check whether Glift will work with the user's browser, return msg if not
	if ( !glift_browser_ok() ) return 
	'<p>Your web browser is very old and our Go game diagrams require a newer '.
	'browser to work properly. Please consider upgrading your web browser. '.
	'We strongly recommend doing so: '.
	'<a href="http://whatbrowser.org/">click here to learn more</a></p>';

	// the user's browser was fine, so let's display the shortcode
	$glift = new Glift();
	
	if ( $glift->eat_shortcode( $atts, $content, $tag ) ) {
		$html = $glift->get_html(); // our shortcode was good, so get the data
	
	} else {
		return FALSE; // the shortcode was indigestible, so don't return HTML
	}

	return $html; // we ate the shortcode, return an HTML snippet to WordPress
}
