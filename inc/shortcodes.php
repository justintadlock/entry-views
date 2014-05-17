<?php
/**
 * Shortcodes for the plgin.
 *
 * @package   EntryViews
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2014, Justin Tadlock
 * @link      http://themehybrid.com/plugins/entry-views
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register shortcodes. */
add_action( 'init', 'entry_views_register_shortcodes' );

/**
 * Registers shortcodes for the plugin.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function entry_views_register_shortcodes() {

	add_shortcode( 'entry-views', 'entry_views_shortcode' );
}

/**
 * Gets the number of views a specific post has.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr  Attributes for use in the shortcode.
 * @return string
 */
function entry_views_shortcode( $attr = '' ) {

	$defaults = array(
		'before'  => '',
		'after'   => '',
		'post_id' => get_the_ID()
	);

	$attr = shortcode_atts( $defaults, $attr, 'entry-views' );

	return entry_views_get( $attr );
}
