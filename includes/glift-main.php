<?php

require( 'glift-functions.php' );
require( 'glift-options.php' );
require( GLIFT_PATH.'classes/glift.php' );
include( 'glift-admin.php' );
include( 'glift-shortcodes.php' );
include( 'glift-comments.php' );
// add [sgf] compatibility if EidoGo for WordPress plugin isn't installed
if ( !function_exists( 'WpEidoGoPlugin' ) ) include( 'glift-eidogo.php' );

// tell WordPress about file types that users can upload for use with Glift
add_filter( 'upload_mimes', 'glift_mime_types', 1, 1 );

// hook the functions that place our scripts in <head> to relevant actions
add_action( 'init', 'glift_register_scripts' );
add_action( 'wp_enqueue_scripts', 'glift_enqueue_scripts' );


