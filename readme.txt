=== Glift Go Game ===
Contributors: gogameguru
Donate link: http://gogameguru.com/donate/
Tags: go game, board games, baduk, igo, weiqi, 围棋, 囲碁, 바둑, Cờ vây, chess
Requires at least: 2.5
Tested up to: 4.0.0
Stable tag: 0.3.7
License: MIT (X11)
License URI: http://opensource.org/licenses/MIT

Bring the board game Go (围棋 weiqi, 囲碁 igo or 바둑 baduk) to your WordPress site. Integrates the Glift Go JavaScript library with WordPress.

== Description ==

Glift is a JavaScript client for the board game Go, which you can use to display Go games and lessons on your website.

After installing the Glift plugin, you can display game records and other Go content in your posts and pages by uploading them to the media library and using the Glift shortcode in your posts.

For example: [glift sgf="http://gogameguru.com/i/glift/example.sgf"]

== Installation ==

1. Download the Glift WordPress plugin.

2. Login to your WordPress dashboard.

3. Click on the ‘Plugins’ tab.

4. On the WordPress Plugins page, click ‘Add New’.

5. You’re now on the Install Plugins page, click ‘Upload’ and select the glift-go-game.zip file that you downloaded earlier.

6. Finally, click ‘Activate’.

Congratulations, the Glift plugin is now installed. 

== Frequently Asked Questions ==

= How do you I display a Go game in one of my posts? =

You can embed an SGF file which you've uploaded to the WordPress media library by adding a shortcode like [glift sgf="http://gogameguru.com/i/glift/example.sgf"] in one of your posts. Replace http://gogameguru.com/i/glift/example.sgf with a link to an SGF file which you've uploaded to your own website.

= Are there more advanced features and options for Glift? =

Yes, visit our [Glift page](http://gogameguru.com/glift/ "Glift Go Game WordPress Plugin") for more advanced examples.

= Is this plugin backwards compatible with the EidoGo for WordPress plugin? =

Yes, if you previously used EidoGo for WordPress, you have two options:

1. You can keep running EidoGo for WordPress and run Glift (this plugin) at the same time, if you want to. Glift won't interfere with the [sgf] shortcodes and will still allow EidoGo to load them.

2. If you want to remove EidoGo for WordPress and completely replace it with Glift, this plugin will detect that EidoGo isn't installed and will load the [sgf] shortcodes using Glift instead.

= Can I use Glift if I don't have a WordPress blog =

Yes, visit [gliftgo.com](http://www.gliftgo.com/ "Glift Go") for more information and sample code.

== Changelog ==

= 0.3.7 =
* Feature - Tested up to WordPress 4.0
* Fix - Encourage browser download when clicking links, with SGF mimetype and HTML5 download attribute

= 0.3.6 =
* Fix - Upgrade to Glift 0.17.7 -- Makes the SGF parser more lenient to invalid
  SGF properties.

= 0.3.5 =
* Fix - Upgrade to Glift 0.17.6 -- Fixes spacing issues for comments with
  newlines when displayed in the comment box.

= 0.3.4 =
* Fix - Upgrade to Glift 0.17.5 -- Fixes iOS delay for icon bar events and
  makes the disableZoomForMobile work. Add cap on jump-ahead/behind button.
  Slightly improve the unsupported text.

= 0.3.3 =
* Feature - Upgrade to Glift 0.17.2 -- Adds support for go board coordinate
  labels, zoom disabling for mobile, changes slightly how problems are
  calculated, removes dependency on jQuery.
* Feature - Added plugin support for new board coordinates (default on) and zoom disable options
* Fix - Removed code that loads jQuery, because it's not needed for this plugin anymore

= 0.3.2 =
* Feature - Upgrade to Glift 0.15.4. Improved buttons for problems and games.

= 0.3.1 =
* Feature - support EidoGo [sgf] shortcode when EidoGo plugin isn't installed
* Feature - Upgrade to Glift 0.15.0 - Glift now has support for tooltips
* Feature - basic support for default settings (see glift-config-sample.php)
* Feature - use [glift noDefaults="true" ...] to ignore default settings
* Fix - don't cache upgrade browser message when WordPress uses page caching
* Fix - other minor bug fixes
* Tweak - performance improvements and code tidy up.

= 0.2.3 =
* Cleanup.

= 0.2.2 =
* Minor bug fixes, remove noLink property from output.

= 0.2.1 =
* Initial release.

== Upgrade Notice ==

= 0.3.7 =
Glift now encourages browsers to download SGF whiles when links are clicked, rather than displaying them, and has been tested up to WordPress 4.0
