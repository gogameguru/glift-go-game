<?php

/** This SGF object is for use in an sgfList and elsewhere.
 * Currently it is very basic and expects to receive clean data.
 * It doesn't yet support all the SGF properties supported by Glift.
 */

// This class isn't yet used at all.

class Sgf implements JsonSerializable {

	protected $_sgfString;
	protected $_url;
	protected $_initialPosition;
	protected $_widgetType;
	protected $_icons;
	protected $_componentsToUse;
	protected $_showVariations;

	public function __construct( 
		$sgfString, 
		$url, 
		$initialPosition, 
		$widgetType, 
		$icons, 
		$componentsToUse, 
		$showVariations 
	)
	{

		$this->_sgfString = $sgfString;
		$this->_url = $url;
		$this->_initialPosition = $initialPosition;
		$this->_widgetType = $widgetType;
		$this->_icons = $icons;
		$this->_componentsToUse = $componentsToUse;
		$this->_showVariations = $showVariations;
	}

	// this function will be accessed by json_encode()
	public function jsonSerialize() {
	
	$data = [
		'sgfString' => $this->_sgfString, 
		'url' => $this->_url, 
		'initialPosition' => $this->_initialPosition, 
		'widgetType' => $this->_widgetType, 
		'icons' => $this->_icons, 
		'componentsToUse' => $this->_componentsToUse, 
		'showVariations' => $this->_showVariations
	];

	return $data;
	}
}
