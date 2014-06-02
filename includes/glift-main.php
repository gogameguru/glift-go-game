<?php

require( $glift_path.'classes/glift.php' );
require( 'functions.php' );
include( 'shortcodes.php' );
#TODO? include( 'comments.php' );

#TODO plugin activation routine and configuration
#TODO settings page for WP users

// tell WordPress about SGF files
add_filter( 'upload_mimes', 'glift_mime_types', 1, 1 );

// add scripts
add_action( 'init', 'glift_register_scripts' );
add_action( 'wp_enqueue_scripts', 'glift_enqueue_scripts' );
