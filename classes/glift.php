<?php

/** This Glift object is basically a wrapper for glift.js and uses
 * the same nomenclature. The main purpose of the object is to validate data
 * and structure it for output in JSON.
 */

class Glift {

	protected $divId;
	protected $height;
	protected $width;
	protected $sgf;
	protected $sgfCollection;
	protected $allowWrapAround;
	protected $sgfDefaults;
	protected $display;
	protected $nolink;
	protected $noDefaults;
	/* ADD ANY NEW GLIFT PROPERTIES HERE (and in glift_eat_shortcode below)*/

	public function __construct( $properties = array( 'sgf' => NULL ) ) {

		static $id = 1; // keep track of unique div id within this function
		if ( is_int( $id ) ) $this->divId = ( "glift_display$id" );
		$id++;

		// add other properties from function parameter, if we received any
		if ( func_num_args() > 0 ) $this->add_properties( $properties );
	}


	// Add properties to this object
	private function add_properties( $properties = array( 'sgf' => NULL ) ) {

		// Validate SGF data. If we have no data, set to zero length string.
		if ( isset( $properties['sgf'] ) ) {

			if ( glift_is_url( $properties['sgf'] ) ) {
				$this->assign_url( $properties['sgf'] );

			} elseif ( glift_is_sgf( $properties['sgf'] ) ) {
				$this->sgf = ( $properties['sgf'] );

			} elseif ( is_array( $properties['sgf'] ) ||
			$properties['sgf'] instanceof ArrayAccess )	{
			$this->sgfCollection = $properties['sgf'];

			} // end of sgf if block

		// if we don't have $sgf, try $sgfCollection
		} elseif ( isset( $properties['sgfCollection'] ) ) {

			if ( glift_is_url( $properties['sgfCollection'] ) ) {
			$this->assign_url( $properties['sgfCollection'] );

			} elseif ( is_array( $properties['sgfCollection'] ) ||
			$properties['sgfCollection'] instanceof ArrayAccess ) {
			$this->sgfCollection = $properties['sgfCollection'];

			} // end of sgfCollection elseif block

		// we have no sgf data, so leave sgf as NULL

		} // end of SGF data if block

		// we've finished with sgf and sgfCollection, so drop them from array
		unset( $properties['sgf'], $properties['sgfCollection'] );

		// set remaining properties to value that was passed
		foreach( $properties as $key => $value ) {
			$this->$key = $properties[$key];
		}

		/* now add default options if they haven't already been specified */

		// we need height and width even if defaults are disabled
		if ( !isset( $this->height ) )
		$this->height = GLIFT_HEIGHT;
		if ( !isset( $this->width ) )
		$this->width = GLIFT_WIDTH;

		// continue with these default options too, if they're not disabled
		if ( TRUE != $this->noDefaults ) {

			if ( !isset( $this->display['theme'] ) )
			$this->display['theme'] = GLIFT_THEME;

			if ( !isset( $this->display['goBoardBackground'] ) )
			$this->display['goBoardBackground'] = GLIFT_BACKGROUND;

			// show coordinates by default, because WP usually has comments
			if ( !isset( $this->display['drawBoardCoords'] ) ) {
				if ( FALSE == GLIFT_COORDS ) {
					// do nothing
				} else {
					$this->display['drawBoardCoords'] = TRUE;
				}
			}

			// don't change zoom by default because auto-disable is overplay
			if ( !isset( $this->display['disableZoomForMobile'] ) ) {
				if ( TRUE == GLIFT_DISABLE_ZOOM ) {
					$this->display['disableZoomForMobile'] = TRUE;
				}
			}

			if ( !isset( $this->nolink ) )
			$this->nolink = GLIFT_NOLINK;
		}
	}


	// check the file extension of an 'sgf' url and assign it to a property
	private function assign_url( $url ) {

		switch ( glift_get_filetype( $url ) ) {
			case 'sgf':
				$this->sgf = ( $url );
				break;
			case 'json':
			default:
				$this->sgfCollection = ( $url );
		}
	}


	// encode this object as JSON and return string with result
	public function get_json() {

		// convert this object to an array
		$glift_data = get_object_vars( $this );

		// remove properties that are only used internally (by this plugin)
		unset(
			$glift_data['height'],
			$glift_data['width'],
			$glift_data['nolink'],
			$glift_data['noDefaults']
		);

		// escape all array elements for output and drop any null properties
		$glift_data = glift_mega_map( 'glift_escape', $glift_data, array() );

		$json = json_encode( $glift_data );

		return $json;
	}


	// JSON encodes Glift object and returns it as HTML for glift.js
	public function get_html() {

		global $glift_default_options;

		$divId = esc_attr( $this->divId );

		// get the div height and width
		$height = absint( $this->height ) . 'px'; //cast height as integer > 0
		$width = ( '100%' == $this->width || 0 == $this->width )
		? '100%' : absint( $this->width ) . 'px';

		$download = glift_is_url( $this->sgf ) ? esc_url( $this->sgf ) : FALSE;

		$json = $this->get_json();

		$style = "height:$height; width:$width; position:relative;";

		$noscript = GLIFT_NOSCRIPT; // can contain HTML, we already sanitized

		$anchor = esc_attr( GLIFT_ANCHOR_TEXT );

		// create the HTML snippet to load Glift
		$html =
			"\n\r<div id='$divId' style='$style'></div>".
			"\n\r<script type='text/javascript'>".
			"gliftWidget = glift.create($json);</script>\n\r<p>&nbsp;</p>\n\r".
			"<div align ='center'><noscript>$noscript</noscript> ";

			// add a hyperlink to download the SGF if appropriate
			if ( $download && TRUE != $this->nolink )
			$html .= 	"<a href='$download' download>$anchor</a>";

			// close the <div> tag and add some white space
			$html .= "</div>\n\r<p>&nbsp;</p>";

		return $html;
	}


	// sanitize, explicitly validate and arrange shortcode data
	public function eat_shortcode( $atts, $content, $tag ) {

		// did our shortcode send any data?
		if ( $atts ) {
			// if so then clean it up
			$clean_atts = array_map( 'sanitize_text_field', $atts );

				// find some sgf data
				if ( isset( $clean_atts['sgf'] ) ) {
					$properties['sgf'] = $clean_atts['sgf'];

				// no sgf data so far, do we have an sgfCollection then?
				} elseif ( isset( $clean_atts['sgfcollection'] ) )
				{
					$properties['sgfCollection'] = $clean_atts['sgfcollection'];

				// still no sgf data, so do we have $content?
				// content is the data between the [glift]$content[/glift] tags
				// using a closing tag and providing content is optional
				} elseif ( $content ) {
					$clean_content = sanitize_text_field( $content );
					$properties['sgf'] = $clean_content;

				} else {
					// we don't have any data, so return false
					return FALSE;
				}

				// explicitly grab any remaining shortcode attributes

				/* height and width for glift_display div*/
				if ( isset( $clean_atts['height'] ) )
				$properties['height'] = $clean_atts['height'];
				if ( isset( $clean_atts['width'] ) )
				$properties['width'] = $clean_atts['width'];

				/* allowWraparound */
				if ( ( isset( $clean_atts['allowwraparound'] ) ) &&
				( FALSE != $clean_atts['allowwraparound'] ) )
				$properties['allowWrapAround'] = TRUE;

				/* sgfDefaults */
				if ( isset( $clean_atts['widgettype'] ) )
				$sgfDefaults['widgetType'] =
				strtoupper( $clean_atts['widgettype'] ); // change to upper case

				if ( isset( $sgfDefaults ) )
				$properties['sgfDefaults'] = $sgfDefaults;

				/* display */
				if ( isset( $clean_atts['theme'] ) )
				$display['theme'] = strtoupper( $clean_atts['theme'] ); // upper

				if ( isset( $clean_atts['goboardbackground'] ) )
					$display['goBoardBackground'] =
					$clean_atts['goboardbackground'];

				if ( ( isset( $clean_atts['drawboardcoords'] ) ) &&
				( FALSE != $clean_atts['drawboardcoords'] ) )
				$display['drawBoardCoords'] = TRUE;

				if ( ( isset( $clean_atts['disablezoomformobile'] ) ) &&
				( FALSE != $clean_atts['disablezoomformobile'] ) )
				$display['disableZoomForMobile'] = TRUE;

				// if we have any display properties then save them
				if ( isset( $display ) ) $properties['display'] = $display;

				/* nolink * - this property is only used by this plugin */
				if ( ( isset( $clean_atts['nolink'] ) ) &&
				( FALSE != $clean_atts['nolink'] ) )
				$properties['nolink'] = TRUE;

				/* noDefaults * - this property is only used by this plugin */
				$properties['noDefaults'] =
				( ( isset( $clean_atts['nodefaults'] ) ) &&
				( FALSE != $clean_atts['nodefaults'] ) ) ? TRUE : FALSE;

				/* ADD ANY NEW GLIFT PROPERTIES HERE */
				/* Note: shortcode $atts are always returned in lower case*/

		// we didn't receive any shortcode atts, so let's look for content
		} elseif ( $content ) {
			$clean_content = sanitize_text_field( $content );
			$properties['sgf'] = $clean_content;

		} else {
			// we don't have any data, so return false
			return FALSE;
		}

		// and finally, add the shortcode attributes as properties
		$this->add_properties( $properties );

		return TRUE;
	}
}
