<?php

// register the sgf shortcode for displaying sgf files
add_shortcode( 'sgf', 'glift_display_sgf' );

// function to display glift for sgf shortcode
function glift_display_sgf( $attr ) {
#TODO go problems
#TODO backwards compatibility for blogs updating from eidogo plugin
#TODO sanitize publisher inputs
#TODO support for multiple glift_display div ids on same page
#TODO height from WP or Glift options

	// very crude implementation for proof of concept only
	$sgfurl = $attr['sgfurl']; 
	// note: the sgfUrl attribute contains uppercase letters in the old eidogo format, but WP seems to force lower case, so we can ignore it.
	// the old attributes were possible because the eidogo plugin uses a complicated filter hack instead of the the cleaner WP shortcodes API.
	
	// quick and dirty html to display glift until we get the syntax right - will totally change this later
	$glift_display_html = '	<div id="glift_display1" style="height:500px; width:100%; position:relative;"></div>
	<script type="text/javascript">
		gliftWidget = glift.create({
			sgf: "'.$sgfurl.'",
			divId: "glift_display1",
			display: {
				theme: "DEPTH"
			}
		});
	</script>';

	return $glift_display_html;
}
