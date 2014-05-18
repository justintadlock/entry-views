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
 * @since  1.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function ev_post_views( $args = array() ) {
	echo ev_get_post_views( $args );
}

/**
 *
 * To use the 'text' argument, either pass a nooped plural using _n_noop() or a single text string.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function ev_get_post_views( $args = array() ) {

	$defaults = array(
		'post_id' => get_the_ID(),
		'before'  => '',
		'after'   => '',
		/* Translators: %s is the number of views a post has. */
		'text'    => _n_noop( '%s View', '%s Views', 'entry-views' ),
		'wrap'    => '<span %s>%s</span>'
	);

	$args = wp_parse_args( $args, $defaults );

	$views = ev_get_post_view_count( $args['post_id'] );

	$text = is_array( $args['text'] ) ? translate_nooped_plural( $args['text'], $views ) : $args['text'];

	$html = sprintf(
		$args['wrap'], 
		'class="entry-views" itemprop="interactionCount" itemscope="itemscope" itemtype="http://schema.org/UserPageVisits"', 
		sprintf( $text, $views )
	);

	return $args['before'] . $html . $args['after'];
}
