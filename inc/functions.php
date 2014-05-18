<?php
/**
 * Plugin functions.
 *
 * @package   EntryViews
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2014, Justin Tadlock
 * @link      http://themehybrid.com/plugins/entry-views
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Returns the meta key used throughout the plugin for getting/setting the post view count, which is 
 * saved as post metadata.  Developers can also filter this via the `ev_meta_key` hook if they need 
 * to alter it to match a meta key that was previously used by another plugin.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function ev_get_meta_key() {
	return apply_filters( 'ev_meta_key', 'Views' );
}

/**
 * Sets the post view count of specific post by adding +1 to the total count.  This function should only 
 * be used if you want to add an addtional +1 to the count.
 *
 * @since  1.0.0
 * @access public
 * @param  int    $post_id
 * @return void
 */
function ev_set_post_view_count( $post_id ) {

	/* Get the number of views the post currently has. */
	$old_views = get_post_meta( $post_id, ev_get_meta_key(), true );

	/* Add +1 to the number of current views. */
	$new_views = absint( $old_views ) + 1;

	/* Update the view count with the new view count. */
	update_post_meta( $post_id, ev_get_meta_key(), $new_views, $old_views );
}

/**
 * Gets the view count of a specific post.
 *
 * @since  1.0.0
 * @access public
 * @param  int    $post_id
 * @return int
 */
function ev_get_post_view_count( $post_id ) {

	/* Get the number of views the post has. */
	$views = get_post_meta( $post_id, ev_get_meta_key(), true );

	/* Return the view count and make sure it's an integer. */
	return !empty( $views ) ? number_format_i18n( absint( $views ) ) : 0;
}
