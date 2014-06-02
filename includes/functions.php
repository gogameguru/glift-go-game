<?php

// add .sgf to allowable file extensions for upload
function glift_mime_types( $glift_mime_types ) {
	$glift_mime_types['sgf'] = 'application/x-go-sgf';
	return $glift_mime_types;
}

function glift_register_scripts( $glift_url ) {
	#TODO use google libraries for jquery?
	#TODO option to load scripts in footer, but not all themes support wp_footer() hook, so have to check first

	// register javascript
	global $glift_js_version;
	global $glift_js_deps;
	global $glift_url;

	// get the current WordPress jquery version so we know which Google Library to load
	wp_enqueue_script( 'jquery' );
	$wp_jquery_ver = $GLOBALS['wp_scripts']->registered['jquery']->ver;

	// deregister current version WP jquery, because it doesn't work with glift (we'll add jquery back in a moment)
	wp_deregister_script( 'jquery' );

	// now register scripts
	$glift_jquery_url = "//ajax.googleapis.com/ajax/libs/jquery/$wp_jquery_ver/jquery.min.js"; // use Google Libraries to decreases latency, and improve parallelism and browser caching
	$glift_js_url = $glift_url.'/js/glift.js';
	$in_footer = false; //not working code: $in_footer = has_action( 'wp_footer' ); // if the current theme supports the wp_footer hook, load scripts in the footer - #TODO figure footer loading out
	wp_register_script( 'jquery', $glift_jquery_url, false, $wp_jquery_ver, $in_footer );
	wp_register_script( 'glift', $glift_js_url, $glift_js_deps, $glift_js_version, $in_footer ); // wp_register_script( $handle, $src, $deps, $ver, $in_footer )
}

function glift_enqueue_scripts() {
	#TODO logic for when to add scripts
	wp_enqueue_script( 'jquery' ); // enqueue our new jquery
	wp_enqueue_script( 'glift' );
}

// cleans up shortcode inputs and returns a Glift object
function glift_objectify_shortcode( $atts, $content, $tag ) {

#TODO fix this cleanup function, last thing for basic functionality

	// did our shortcode send any data?
	if ( $atts ) {
		// if so then, clean it up
		$clean_atts = array_map( 'sanitize_text_field', $atts );
		// in this case we will ignore the shortcode $content (data between the tags) for now because it should only contain sgf data

	} elseif ( !$content ) {
		// if we don't have any data, then return nothing
		return;
	} // end of shortcode checks if block

	static $id = 1; // keep track of div id within this function to support multiple instances of Glift per page
	$divId = "glift_display$id";

	// do we have an sgf url?
	if ( $clean_atts['sgfurl'] ) {
		$sgf = esc_url($clean_atts['sgfurl']);
	} elseif ( $content ) {
		// if not, do we have $content? clean it instead
		$clean_content = sanitize_text_field( $content );
		$sgf = preg_match( '/^[a-zA-Z0-9;:()\[\]-_]+$/', $clean_content ); #TODO this regex is probably too strict, consider alternative;
	} else {
		// we don't have enough data, so return nothing
		return;
	}

	$glift_object = new Glift( $divId, $sgf );

	$id++; // increment the div_id for next time

	return $glift_object;
}

// JSON encodes Glift object and returns it as HTML for glift.js to process
function glift_to_html ( $glift_object ) {
	#TODO move some of the style info to somewhere more reusable

	$divId = $glift_object->divId;
	$json = json_encode( $glift_object, JSON_PRETTY_PRINT );
	$html = "<div id='$divId' style='height:500px; width:100%; position:relative;'></div>
<script type='text/javascript'>
gliftWidget = glift.create($json);
</script>
&nbsp;";
	return $html;
}
