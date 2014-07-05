<?php

require( $glift_path.'classes/glift.php' );
require( 'functions.php' );
include( 'shortcodes.php' );

#TODO(dormerod): plugin activation routine and default configuration
#TODO(dormerod): settings page for WP admins

// tell WordPress about file types that users can upload for use with Glift
add_filter( 'upload_mimes', 'glift_mime_types', 1, 1 );

// hook the functions that place our scripts in <head> to relevant actions
add_action( 'init', 'glift_register_scripts' );
add_action( 'wp_enqueue_scripts', 'glift_enqueue_scripts' );
