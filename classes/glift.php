<?php

/** This Glift object is basically a wrapper for glift.js and uses
 * the same nomenclature. The main purpose of the object is to validate data
 * and structure it for output in JSON.
 */

class Glift implements JsonSerializable {

	protected $_divId;
	protected $_sgf;
	protected $_sgfCollection;
	protected $_allowWrapAround;
	protected $_sgfDefaults;
	protected $_display;

	public function __construct( 
		$sgf = '', 
		$sgfCollection = NULL, 
		$allowWrapAround = FALSE, 
		$widgetType = 'GAME_VIEWER', 
		$theme = 'DEPTH', 
		$goBoardBackground = '' 
	)
	{
		static $id = 1; // keep track of unique div id 
		if ( is_int( $id ) ) $this->_divId = ( "glift_display$id" );
		$id++;
		
		$this->add_properties(
			$sgf, 
			$sgfCollection, 
			$allowWrapAround, 
			$widgetType, 
			$theme, 
			$goBoardBackground 
		);
	}

	// this function will be accessed by json_encode()
	public function jsonSerialize() {

	$data = [
		'divId' => $this->_divId, 
		'sgf' => $this->_sgf, 
		'sgfCollection' => $this->_sgfCollection, 
		'allowWrapAround' => $this->_allowWrapAround, 
		'sgfDefaults' => $this->_sgfDefaults, 
		'display' => $this->_display
	];

	return $data;
	}

	// validate and add properties to this object
	private function add_properties( 
		$sgf, 
		$sgfCollection, 
		$allowWrapAround, 
		$widgetType, 
		$theme, 
		$goBoardBackground 
	)
	{
		
		// Validate SGF data. If we have no data, set to zero length string.
		if ( $sgf ) {
			
			if ( glift_is_url( $sgf ) ) {
				$this->assign_url( $sgf );

			} elseif ( glift_is_sgf( $sgf ) ) {
				$this->_sgf = esc_js( $sgf );
				
			} elseif ( is_array ( $sgf ) ) {		
				$escaped_sgf = $this->esc_sgf_array( $sgf );
				$this->_sgfCollection = $escaped_sgf;
			
			} else {
				// we have data, but it's invalid - _sgf set to ZLS
				$this->_sgf = '';
			} // end of 'if ( $sgf )' block

		// if we don't have $sgf, try $sgfCollection
		} elseif ( $sgfCollection ) {

			if ( glift_is_url( $sgfCollection ) ) {
			$this->assign_url( $sgfCollection );

			} elseif ( is_array( $sgfCollection ) ) {		
				$escaped_sgf = $this->esc_sgf_array( $sgfCollection );
				$this->_sgfCollection = $escaped_sgf;
			
			} else {
				$this->_sgf = '';
			} // end of 'if ( $sgfCollection )' block
		
		} else {
			// we have no sgf data, so set sgf to zero length string
			$this->_sgf = '';
		
		} // end of SGF data if block
		
		// set remaining properties to value that was passed
		$this->_allowWrapAround = 
		$allowWrapAround ? esc_js( $allowWrapAround ) : FALSE;

		if ( $widgetType ) $this->_sgfDefaults['widgetType'] = 
		esc_js( $widgetType );

		if ( $theme ) $this->_display['theme'] = esc_js( $theme );

		if ( $goBoardBackground ) $this->_display['goBoardBackground'] = 
		esc_js( $goBoardBackground );
	}
	
	// check the file extension of an 'sgf' url and assign it to a property
	private function assign_url( $url ) {

		switch ( glift_get_filetype( $url ) ) {
			case 'sgf':
				$this->_sgf = esc_url( $url );
				break;
			case 'json':
				$this->_sgfCollection = esc_url( $url );
				break;
			default:
				$this->_sgf = '';
		}
	}
	
	// escapes all text data in arrays/objects for output in javascript
	// returns escaped array or null (if we didn't receive an array)
	private function esc_sgf_array( $sgfs ) {
		if ( !is_array( $sgfs ) ) return;
		
		foreach ( $sgfs as &$sgf ) {
			
			if ( is_string( $sgf ) ) {
				$sgf = esc_js( $sgf );
			
			} elseif ( is_object( $sgf ) || is_array( $sgf ) ) {
				// if the element is an array or object, recurse
				$sgf = $this->esc_sgf_array( $sgf );
			}
		}
		return $sgfs;
	}

	// JSON encodes Glift object and returns it as HTML for glift.js
	public function to_html() {
		#TODO(dormerod): move div style info to somewhere more reusable

		$divId = esc_attr( $this->_divId );
		$json = json_encode( $this, JSON_PRETTY_PRINT );
		$style = "height:500px; width:100%; position:relative;";
		$html =
				"<div id='$divId' style='$style'></div>
				&nbsp;
				<script type='text/javascript'>
				gliftWidget = glift.create($json);
				</script>";
		return $html;
	}
	
	// validate and arrange shortcode data
	public function eat_shortcode( $atts, $content, $tag ) {

		// did our shortcode send any data?
		if ( $atts ) {
			// if so then clean it up
			$clean_atts = array_map( 'sanitize_text_field', $atts );

				// imma find me some sgf data! (O_o)
				if ( array_key_exists( 'sgf', $clean_atts ) ) {
					$sgf = $clean_atts['sgf'];
					$sgfCollection = NULL;	
				
				// no sgf data so far, do we have an sgfCollection then?
				} elseif ( array_key_exists( 'sgfcollection', $clean_atts ) ) {
					$sgf = NULL;
					$sgfCollection = $clean_atts['sgfcollection'];
				
				// still no sgf data, so do we have $content?
				// content is the data between the [glift]$content[/glift] tags
				// using a closing tag and providing content is optional
				} elseif ( $content ) {
					$clean_content = sanitize_text_field( $content );
					$sgf = $clean_content;

				} else {
					// we don't have any sgf data, so return nothing
					return;
				}
		
				// we sgf, so let's grab any remaining shortcode attributes
				$allowWrapAround = 
				array_key_exists( 'allowwraparound', $clean_atts ) ?
				$clean_atts['allowwraparound'] : NULL;
				
				$widgetType = array_key_exists( 'widgettype', $clean_atts ) ? 
				$clean_atts['widgettype'] : NULL;

				$theme = array_key_exists( 'theme', $clean_atts ) ? 
				$clean_atts['theme'] : NULL;
				
				$goBoardBackground =
				array_key_exists( 'goboardbackground', $clean_atts ) ? 
				$clean_atts['goboardbackground'] : NULL;
		
		// we didn't receive any shortcode atts, so let's look for content
		} elseif ( $content ) {
			$clean_content = sanitize_text_field( $content );
			$sgf = $clean_content;

			// there were no atts, so the remaining parameters are all null
			#TODO(dormerod): change these arguments to a single array
			$sgfCollection = NULL;
			$allowWrapAround = NULL;
			$widgetType = NULL;
			$theme = NULL;
			$goBoardBackground = NULL;

		} else {
			// we don't have any sgf data, so return nothing
			return;
		}

		// and finally, add the shortcode attributes as properties
		$this->add_properties(
			$sgf, 
			$sgfCollection, 
			$allowWrapAround, 
			$widgetType, 
			$theme, 
			$goBoardBackground 
		);

		return;
	}
}
