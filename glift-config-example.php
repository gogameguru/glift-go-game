<?php

/** This file can be used to set some basic default display settings for Glift.
 * To use it, just tweak the settings below (e.g. change DEPTH to TRANSPARENT).
 * Then copy or rename it from glift-config-example.php to glift-config.php.
 * The plugin will find your glift-config.php file and use your settings.
 * If something goes wrong afterwards, delete or rename glift-config.php
 */


// set the default theme
define('GLIFT_THEME', 'DEPTH');

// set the default goBoardBackground (change to an image URL on your website)
define('GO_BOARD_BACKGROUND', 'https://gogameguru.com/i/glift/purty_wood.jpg');

// coordinates are enabled by default, uncomment the line below to disable them
//define('GLIFT_COORDS', FALSE);

// disable zoom for mobile by default - this prevents accidental zooming
define('GLIFT_DISABLE_ZOOM', TRUE);

// set the message which is shown to people who don't have JavaScript enabled
define('GLIFT_NOSCRIPT', 'Please enable JavaScript to view this game.');

// set the text for the download SGF hyperlink
define('GLIFT_ANCHOR_TEXT', 'Download SGF');

