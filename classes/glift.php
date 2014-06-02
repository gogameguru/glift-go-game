<?php

class Glift {

	protected $divId = 'glift_display1';
	protected $sgf;
	protected $display = array(
		theme => 'DEPTH'
	);

	public function __construct( $divId, $sgf ) {

		this->divId = $divId;
		this->sgf = $sgf;
	}
}
