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

function ev_get_meta_key() {
	return apply_filters( 'wpv_meta_key', 'Views' );
}

/**
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
