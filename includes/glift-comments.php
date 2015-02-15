<?php

/** This module adds support for displaying Glift in WordPress comments */

// hook into the comment text to access (and modify) comment contents
add_filter( 'comment_text', 'glift_comments' );

// function to process comment content and add our new shortcode
function glift_comments( $comment ) {
	
	// save existing registered shortcodes
	global $shortcode_tags;
	$original_tags = $shortcode_tags;

	// unregister existing shortcodes so they can't be used by commenters
	remove_all_shortcodes();

	// register the [go] shortcode
	add_shortcode( 'go', 'glift_comment_shortcode' );
	
	// process any [go] shortcodes found in the current comment
	$comment = do_shortcode( $comment );

	// unregister the [go] shortcode and restore any original shortcodes
	$shortcode_tags = $original_tags;

	// return the processed comment (WordPress will strip any undesirable tags)
	return $comment;
}


// this function processes any shortcodes that we've registered for comments
function glift_comment_shortcode ( $atts, $content, $tag ) {
	
	// at this stage we will only allow commenters to submit [go]$content[/go]
	// we will ignore any attributes that are manually set for the shortcode
	// it's expected commenters will later use a JavaScript tool to comment
	
	// if we're in the admin dashboard, display text instead of Glift
	if ( is_admin() ) return '[go]' . $content . '[/go]';

	// otherwise call glift_create and return any HTML (drop $atts, $tag)
	return glift_create( FALSE, $content, FALSE ); 
}
