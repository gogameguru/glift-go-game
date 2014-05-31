<?php

// register the sgf shortcode for displaying sgf files
add_shortcode( 'sgf', 'glift_display_sgf' );

// function to display glift for sgf shortcode
function glift_display_sgf( $attr ) {
#TODO backwards compatibility for blogs updating from eidogo plugin
#TODO sanitize publisher inputs

	// very crude implementation for proof of concept only
	$sgfurl = $attr['sgfurl']; 
	// note: the sgfUrl attribute contains uppercase letters in the old eidogo format, but WP seems to force lower case, so we can ignore it.
	// the old attributes were possible because the eidogo plugin uses a complicated filter hack instead of the the cleaner WP shortcodes API.
	
	// quick and dirty html to display glift
	$glift_display_html = '<script type="text/javascript">
gliftWidget = glift.create({
  sgf: \''.$sgfurl.'\',
  divId: \'glift_display1\',
  display: {
    theme: \'DEPTH\',
    goBoardBackground: \'images/purty_wood.png\'
});
</script>
<div id="glift_display1"></div>';

	return $glift_display_html;
}
