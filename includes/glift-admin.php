<?php

/** This file defines a simple settings GUI in the WordPress admin dashboard
 * a site admin can use it to configure the default behavior for Glift and
 * for the plugin. This code makes heavy use of the WordPress Settings API,
 * which handles a lot of the drudge work (layout and security).
 * So it might look incomplete at first, but see the comments.
 */

/* add a menu for our options (settings) page in WordPress */
add_action( 'admin_menu', 'glift_add_page' );

function glift_add_page() {

	// create a native WordPress options page
	// parameters: (page title, menu title, capability, page slug, callback)
	add_options_page( 
		'Glift Settings', 
		'Glift', 
		'manage_options', 
		'glift', 
		'glift_options_page' 
	);
}


// draw the options page we just created
function glift_options_page() {
	
	?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Glift Settings</h2>
<form action="options.php" method="post">
<?php settings_fields('glift_options'); ?>
<?php do_settings_sections('glift'); ?>
<input name="Submit" type="submit" value="Save Changes" class="button-primary"/>
</form>
<?php glift_cheatsheet(); ?>
</div>
	<?php
}


/* register and define our settings on the options page we created above */
add_action( 'admin_init', 'glift_admin_init' );

function glift_admin_init() {

	global $glift_themes; // get our global array of supported Glift themes

	register_setting( 
		'glift_options', 
		'glift_options', 
		'glift_options_feedback'
	); 	// tell WordPress about our 'setting' which will be stored in the DB
		// for efficiency, we create one option and store an array of settings
		// parameters: (setting group name, option name (in DB), callback func)

	add_settings_section( 
		'glift_main', 
		'Customize Glift', 
		'glift_main_section_text', 
		'glift'
	);	// add a section to the settings page (we only need one in this case)
		// parameters: (HTML id tag, title <h3>, callback func, page slug)

	/* add all of our settings to the page */
	add_settings_field( 
		'glift_height', 
		'Maximum height in px', 
		'glift_setting_input', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'height', 
			'type' => 'number', 
			'value' => GLIFT_HEIGHT 
		)
	);	// now we add inputs to our HTML form (created by API) one by one
		// callback function (below) will output HTML using our arguments array
		// parameters: (HTML id, label, callback, page slug, section, arguments)

	add_settings_field( 
		'glift_width', 
		'Maximum width in px (enter 0 for automatic width)', 
		'glift_setting_input', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'width', 
			'type' => 'number', 
			'value' => GLIFT_WIDTH 
		)
	);

	add_settings_field( 
		'glift_theme', 
		'Choose your theme', 
		'glift_setting_dropdown', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'theme', 
			'value' => GLIFT_THEME, 
			'options' => $glift_themes 
		)
	);

	add_settings_field( 
		'glift_background', 
		'Background image (URL)', 
		'glift_setting_input', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'background', 
			'type' => 'url', 
			'value' => GLIFT_BACKGROUND 
		)
	);

	add_settings_field( 
		'glift_coords', 
		'Enable coordinates', 
		'glift_setting_checkbox', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'coords', 
			'value' => GLIFT_COORDS, 
			'label' => 'Check to enable coordinates' 
		)
	);

	add_settings_field( 
		'glift_disable_zoom', 
		'Disable zoom (for mobile browsers)', 
		'glift_setting_checkbox', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'disable_zoom', 
			'value' => GLIFT_DISABLE_ZOOM, 
			'label' => 'Check to disable browser zoom' 
		)
	);

	add_settings_field( 
		'glift_noscript', 
		'Message to display if JavaScript is disabled', 
		'glift_setting_input', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'noscript', 
			'type' => 'text', 
			'value' => GLIFT_NOSCRIPT 
		)
	);

	add_settings_field( 
		'glift_anchor_text', 
		'Anchor text for SGF download links', 
		'glift_setting_input', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'anchor_text', 
			'type' => 'text', 
			'value' => GLIFT_ANCHOR_TEXT 
		)
	);

	add_settings_field( 
		'glift_nolink', 
		'Disable link to SGF file', 
		'glift_setting_checkbox', 
		'glift', 
		'glift_main', 
		array( 
			'name' => 'nolink', 
			'value' => GLIFT_NOLINK, 
			'label' => 'Check to disable automatic links to SGF files' 
		)
	);
}

/* functions to draw all the settings we registered above */

// this callback function outputs the section header for add_settings_section()
function glift_main_section_text() {

	$text =  '<p>On this page, you can customize the way Glift is displayed. ';
	$text .= 'These are only your default settings. ';
	$text .= 'You can customize individual posts using the shortcode.</p>';
	echo $text;
}

// outputs a short 'cheatsheet', at the bottom of the settings page
function glift_cheatsheet() {

	?>
<p>&nbsp;</p>
<h3>Glift shortcode cheat sheet</h3>
<p><em>Basic example:</em> <strong>[glift sgf="http://www.example.com/game.sgf"]
</strong></p>
<p>(The above [glift] shortcode embeds a single SGF in your post, using your default settings).</p>
<p><em>More advanced example:</em> <strong>
[glift sgf="http://www.example.com/problem.sgf" height=500 width=600 
widgetType="standard_problem" theme="textbook" 
goBoardBackground="http://www.example.com/wood-background.jpg"]
</strong></p>
<p>See <a href="https://gogameguru.com/glift/#examples">this page</a> for more examples of how to use the shortcode.</p>
	<?php
}


// this callback function adds inputs we created with add_settings_field()
function glift_setting_input( $atts ) {

	// get attributes from array argument
	$name = $atts['name'];
	$type = $atts['type'];
	$value = $atts['value'];

	// output the input field
	$html =  "<input id='$name' name='glift_options[$name]' ";
	$html .= "type='$type' value='$value'/>";
	echo $html;
}


// this callback function adds checkboxes we created with add_settings_field()
function glift_setting_checkbox( $atts ) {

	// get attributes from array argument
	$name = $atts['name'];
	if ( $atts['value'] ) {
		$checked = "checked='checked'"; // if value is TRUE check the box
	} else {
		$checked = "";
	}
	$label = $atts['label'];

	// output the checkbox field
	$html =  "<input id='$name' name='glift_options[$name]' ";
	$html .= "type='checkbox' value='1' $checked/>";
	$html .= "<label for='$name'>$label</label>";
	echo $html;
}


// this callback function adds dropdowns we created with add_settings_field()
function glift_setting_dropdown( $atts ) {
 
 	// get attributes from array argument
	$name = $atts['name'];
	$value = $atts['value'];
	$options = $atts['options'];
 
	$html = "<select id='$name' name='glift_options[$name]'>";
	
	// add <option> HTML tags for each option in our array 
	foreach ( $options as $option ) {
		
		$text = ucfirst( strtolower( $option ) );
		
		// if this option is our current setting, add the 'selected' attribute
		if ( $value == $option ) { 
			$selected = ' selected'; 
		} else { 
			$selected = '';
		}

		$html .= "<option value='$option'$selected>$text</option>";
	}
	
	$html .= "</select>";
	echo $html;
}


/* validate user settings and provide feedback for errors using the API */

// this is our callback function for register_setting() above
// it receives all the $_POST data from our settings page as an array
// the Settings API takes care of most DB security, nonces etc
function glift_options_feedback( $options ) {
	
	// call our option validation function (in glift-options.php)
	$valid = glift_validate_options( $options ); 

	// if our validation function amended something, let the user know
	if ( $valid['height'] != $options['height'] ) {

		add_settings_error(
			'height',
			'glift_height_error',
			"Maximum height should be a number. It's now been set to 500.",
			'error'
		);
	}

	// warn the user if they specify a very large div height
	if ( $valid['height'] > 1500 ) {
		
		add_settings_error(
			'height',
			'glift_height_error',
			"Your maximum height is very large, Glift may not display nicely.",
			'updated'
		);
	}

	// and so on...
	if ( $valid['height'] < 200 ) {
		
		add_settings_error(
			'height',
			'glift_height_error',
			"Your maximum height is very small, Glift may not display nicely.",
			'updated'
		);
	}

	if ( $valid['width'] != $options['width'] ) {
		
		add_settings_error(
			'width',
			'glift_width_error',
			"Maximum width should be a number, it's been set to 0 (automatic).",
			'error'
		);
	}

	if ( $valid['width'] > 1500 ) {
		
		add_settings_error(
			'width',
			'glift_width_error',
			"Your maximum width is very large, Glift may not display nicely.",
			'updated'
		);
	}

	if ( $valid['background'] != $options['background'] ) {
		
		add_settings_error(
			'background',
			'glift_background_error',
			'It looks like there was a problem with your background image URL.',
			'error'
		);
	}


	if ( $valid['noscript'] != $options['noscript'] ) {
		
		add_settings_error(
			'noscript',
			'glift_noscript_error',
			'Your noscript text has been amended. Please check it.',
			'error'
		);
	}


	if ( $valid['anchor_text'] != $options['anchor_text'] ) {
		
		add_settings_error(
			'anchor_text',
			'glift_anchor_text_error',
			"Your download anchor text has been amended. Please check it.",
			'error'
		);
	}

	// return our settings array to the API, it will now be saved in the DB
	return $valid;
}
