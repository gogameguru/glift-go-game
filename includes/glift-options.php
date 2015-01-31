<?php

/** This code determines and validates Glift's default settings.
 * It will check the database for settings (user defined defaults).
 * If settings aren't found, we'll use the hardcoded defaults instead.
 * Once settings are validated, they'll be stored in global constants for later.
 */

// Note: the hardcoded default options are in [plugin root]/glift-go-game.php

// get our default settings from the WordPress database (if there are any)
// (actually WordPress autoloads it, so we're really getting it from memory)
$glift_options = get_option( 'glift_options' );

// did we receive options from DB? If so then validate, otherwise set defaults
if ( !( FALSE == $glift_options ) ) {
	
	// we received options, so validate them
	$glift_valid_options = glift_validate_options( $glift_options );
	
	// now define our options as constants for later use
	glift_define_options( $glift_valid_options );

} else {

// no options were received, so use use the 'global' defaults instead
	glift_define_options( $glift_default_options ); // using 'global' variable
}


// validate the options received from database or user input one by one
function glift_validate_options( $options ) {

	// get our global options ready
	global $glift_default_options;
	$defaults = $glift_default_options;
	global $glift_themes; // and the global list of valid themes

	// height and width should be integers
	if ( isset( $options['height'] ) ) {
		$height = preg_replace( '/[^0-9]/', '', $options['height'] );
		$valid['height'] = is_numeric($height) ? $height : $defaults['height'];
	}
		
	if ( isset( $options['width'] ) ) {
		$width = preg_replace( '/[^0-9]/', '', $options['width'] );
		$valid['width'] = is_numeric( $width ) ? $width : $defaults['width'];
	}

	// validate the theme setting against the global list of themes
	if ( isset( $options['theme'] ) ) {
		$valid['theme'] = in_array( $options['theme'], $glift_themes ) 
		? $options['theme'] : 'DEFAULT';
	}

	// the goBoardBackground should be a URL (we don't check if it's an image)
	if ( isset( $options['background'] ) ) {
		$valid['background'] = glift_is_url( $options['background'] )
		? $options['background'] : NULL;
	}

	// the checkbox will return 1 if coordinates are enabled
	if ( isset( $options['coords'] ) ) {
		$valid['coords'] = TRUE == $options['coords'] ? TRUE : FALSE;
	} else {
		$valid['coords'] = FALSE;
	}

	// the checkbox will return 1 if disableZoomForMobile is enabled
	if ( isset( $options['disable_zoom'] ) ) {
		$valid['disable_zoom'] = TRUE == $options['disable_zoom'] ? TRUE:FALSE;
	} else {
		$valid['disable_zoom'] = FALSE;
	}

	// the noscript message can contain HTML etc, so we need to sanitize it
	if ( isset( $options['noscript'] ) ) {
		
		$allowed = array(
			'a' => array(
				'href' => true,
				'title' => true,
			),
			'b' => array(),
			'em' => array(),
			'i' => array(),
			'p' => array(),
			'strong' => array(),
		); // array of allowed tags
		
		$noscript = wp_kses( $options['noscript'], $allowed ); // strip bad tags
		$noscript = force_balance_tags( $noscript ); // balance remaining tags	
		$valid['noscript'] = $noscript; 
	}

	// escape any unwanted HTML in the anchor text
	if ( isset( $options['anchor_text'] ) ) {
		$anchor_text = sanitize_text_field( $options['anchor_text'] );
		$valid['anchor_text'] = $anchor_text; 
	}

	// the checkbox will return 1 if nolink is selected
	if ( isset( $options['nolink'] ) ) {
		$valid['nolink'] = TRUE == $options['nolink'] ? TRUE : FALSE;
	} else {
		$valid['nolink'] = FALSE;
	}

	return $valid;
}


// define current options as constants so that we can access them easily
function glift_define_options( $options ) {

// get the default options
global $glift_default_options;
$defaults = $glift_default_options;

	// if an option isn't specified for some reason, use the default option
	foreach ( $defaults as $key => $value ) {
		if ( !isset( $options[$key] ) ) $options[$key] = $defaults[$key];
	}
	
	// now define our constants one by one...
	define('GLIFT_HEIGHT', $options['height']);
	define('GLIFT_WIDTH', $options['width']);
	define('GLIFT_THEME', $options['theme']);
	define('GLIFT_BACKGROUND', $options['background']);
	define('GLIFT_COORDS', $options['coords']);
	define('GLIFT_DISABLE_ZOOM', $options['disable_zoom']);
	define('GLIFT_NOSCRIPT', $options['noscript']);
	define('GLIFT_ANCHOR_TEXT', $options['anchor_text']);
	define('GLIFT_NOLINK', $options['nolink']);
}

