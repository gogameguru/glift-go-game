<?php

/** This SGF object is for use in an sgfList and elsewhere.
 * Currently it is very basic and expects to receive clean data.
 * It doesn't yet support all the SGF properties supported by Glift.
 */

// This class isn't actually used yet because of some other plugin changes.

class Sgf implements JsonSerializable, ArrayAccess {

	private $container = []; // required to implement ArrayAccess above
	
	public $sgfString;
	public $url;
	public $initialPosition;
	public $widgetType;
	public $icons;
	public $componentsToUse;
	public $showVariations;

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

		$this->sgfString = $sgfString;
		$this->url = $url;
		$this->initialPosition = $initialPosition;
		$this->widgetType = $widgetType;
		$this->icons = $icons;
		$this->componentsToUse = $componentsToUse;
		$this->showVariations = $showVariations;
	}


	// this function will be accessed by json_encode()
	public function jsonSerialize() {
	
	$data = [
		'sgfString' => $this->sgfString, 
		'url' => $this->url, 
		'initialPosition' => $this->initialPosition, 
		'widgetType' => $this->widgetType, 
		'icons' => $this->icons, 
		'componentsToUse' => $this->componentsToUse, 
		'showVariations' => $this->showVariations
	];

	return $data;
	}


	/** the four methods that follow are required by objects
	 * which implement ArrayAccess.
	 */
	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->container[] = $value;
		} else {
			$this->container[$offset] = $value;
		}
	}
	
	public function offsetExists( $offset ) {
		return isset( $this->container[$offset] );
	}
	
	public function offsetUnset( $offset ) {
		unset( $this->container[$offset] );
	}
	
	public function offsetGet( $offset ) {
		return isset( $this->container[$offset] ) ?
		$this->container[$offset] : NULL;
	}
	/* end of ArrayAccess methods */
}
