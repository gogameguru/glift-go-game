<?php

add_shortcode( 'sgf', 'eidogo_do_shortcode' );

// sanitize, explicitly validate and arrange shortcode data
// this is a lightweight version of the Glift object's eat_shortcode method 
function eidogo_do_shortcode( $atts, $content, $tag ) {
	
	// did our shortcode send any data?
	if ( $atts ) {
		// if so then clean it up
		$clean_atts = array_map( 'sanitize_text_field', $atts );

		// look for sgf data
		if ( array_key_exists( 'sgfurl', $clean_atts ) ) {
			$properties['sgf'] = $clean_atts['sgfurl'];
			
		// no sgfurl, so do we have $content?
		} elseif ( $content ) {
			$clean_content = sanitize_text_field( $content );
			$properties['sgf'] = $clean_content;

		} else {
			// we don't have any data, so return false
			return FALSE;
		}
	
		/* sgfDefaults */
		if ( array_key_exists ( 'theme', $clean_atts ) && 
		'problem' == $clean_atts['theme'] ) 
		$properties['sgfDefaults'] = 
		array( 'widgetType' => 'STANDARD_PROBLEM' );
		

	// we didn't receive any shortcode atts, so let's look for content
	} elseif ( $content ) {
		$clean_content = sanitize_text_field( $content );
		$properties['sgf'] = $clean_content;

	} else {
		// we don't have any data, so return false
		return FALSE;
	}

	// we have some data, so create the Glift object and output as HTML
	$glift = new Glift( $properties );
	$html = $glift->get_html(); // our shortcode was good, so get the data

	return $html;
}
