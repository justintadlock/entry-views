<?php
/**
 * Template functions for the plugin.
 *
 * @package   EntryViews
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2014, Justin Tadlock
 * @link      http://themehybrid.com/plugins/entry-views
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Updates the number of views when on a singular view of a post.  This function uses post meta to store
 * the number of views per post.  By default, the meta key is 'Views', but you can filter this with the 
 * 'entry_views_meta_key' hook.
 *
 * @since  1.0.0
 * @access public
 * @param  int    $post_id The ID of the post to update the meta for.
 * @return void
 */
function entry_views_update( $post_id = '' ) {

	/* If we're on a singular view of a post, calculate the number of views. */
	if ( !empty( $post_id ) ) {

		/* Allow devs to override the meta key used. By default, this is 'Views'. */
		$meta_key = apply_filters( 'entry_views_meta_key', 'Views' );

		/* Get the number of views the post currently has. */
		$old_views = get_post_meta( $post_id, $meta_key, true );

		/* Add +1 to the number of current views. */
		$new_views = absint( $old_views ) + 1;

		/* Update the view count with the new view count. */
		update_post_meta( $post_id, $meta_key, $new_views, $old_views );
	}
}

/**
 * Gets the number of views a specific post has.  It also doubles as a shortcode, which is called with the 
 * [entry-views] format.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Attributes for use in the shortcode.
 * @return string
 */
function entry_views_get( $attr = '' ) {

	/* Merge the defaults and the given attributes. */
	$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'post_id' => get_the_ID() ), $attr );

	/* Allow devs to override the meta key used. */
	$meta_key = apply_filters( 'entry_views_meta_key', 'Views' );

	/* Get the number of views the post has. */
	$views = intval( get_post_meta( $attr['post_id'], $meta_key, true ) );

	/* Returns the formatted number of views. */
	return $attr['before'] . number_format_i18n( $views ) . $attr['after'];
}
