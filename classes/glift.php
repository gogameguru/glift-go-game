<?php

/** This Glift object (class) is basically a wrapper for glift.js and uses
 * the same nomenclature. The main purpose of the object is to structure data
 * for output in JSON.
 */

class Glift implements JsonSerializable {

	public $divId = 'glift_display1';
	protected $_sgf;
	protected $_display = array(
		theme => 'DEPTH'
	);
	#TODO add other properties here

	public function __construct( $divId, $sgf ) {

		$this->divId = $divId;
		$this->_sgf = $sgf;
	}

	// this function will be accessed by json_encode()
	public function jsonSerialize() {

	$data = ['divId' => $this->divId,
		'sgf' => $this->_sgf,
		'display' => $this->_display];
		#TODO extend to other Glift properties

	return $data;
	}
}
