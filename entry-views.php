<?php
/**
 * Plugin Name: Entry Views
 * Plugin URI:  http://themehybrid.com/plugins/entry-views
 * Description: A WordPress plugin for tracking the number of post views.
 * Version:     1.0.0
 * Author:      Justin Tadlock
 * Author URI:  http://justintadlock.com
 * Text Domain: entry-views
 * Domain Path: /languages
 *
 * Entry views is a script for calculating the number of views a post gets.  It is meant to be basic and 
 * not a full-featured solution.  The idea is to allow theme/plugin authors to quickly load this file and 
 * build functions on top of it to suit their project needs.  This is an AJAX-based solution, so only visitors 
 * to your site with JavaScript enabled in their browser will update the view count.  It is possible to do this
 * without AJAX but not recommend (see notes below).
 *
 * Not using AJAX: You can call up ev_set_post_view_count() at any time and pass it a post ID to update the 
 * count, but this has problems.  Any links with rel="next" or rel="prefetch" will cause some browsers to prefetch
 * the data for that particular page.  This can cause the view count to be skewed.  To try and avoid this 
 * issue, you need to disable/remove adjacent_posts_rel_link_wp_head().  However, this is not bullet-proof 
 * as it cannot control links it doesn't know about.
 * @link http://core.trac.wordpress.org/ticket/14568
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   EntryViews
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2014, Justin Tadlock
 * @link      http://themehybrid.com/plugins/entry-views
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Plugin loader class.
 *
 * @since 1.0.0
 */
final class Entry_Views_Plugin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * The post ID to update the entry views for.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    int
	 */
	public $post_id = 0;

	/**
	 * Sets up needed actions/filters for the plugin to initialize.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'plugins_loaded',             array( $this, 'i18n'              ), 2  );
		add_action( 'plugins_loaded',             array( $this, 'includes'          ), 3  );
		add_action( 'init',                       array( $this, 'post_type_support' ), 10 );
		add_action( 'widgets_init',               array( $this, 'register_widgets'  ), 10 );
		add_action( 'template_redirect',          array( $this, 'load'              ), 99 );
		add_action( 'wp_ajax_entry_views',        array( $this, 'update_ajax'       ), 10 );
		add_action( 'wp_ajax_nopriv_entry_views', array( $this, 'update_ajax'       ), 10 );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function i18n() {
		load_plugin_textdomain( 'entry-views', false, 'entry-views/languages' );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function includes() {
		$path = trailingslashit( plugin_dir_path( __FILE__ ) );

		require_once( "{$path}inc/functions.php"          );
		require_once( "{$path}inc/template.php"           );
		require_once( "{$path}inc/shortcodes.php"         );
		require_once( "{$path}inc/widget-entry-views.php" );
	}

	/**
	 * Adds support for 'entry-views' to the 'post', 'page', and 'attachment' post types (default WordPress 
	 * post types).  For all other post types, the theme should explicitly register support for this feature.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function post_type_support() {

		/* Core post types. */
		add_post_type_support( 'post',       array( 'entry-views' ) );
		add_post_type_support( 'page',       array( 'entry-views' ) );
		add_post_type_support( 'attachment', array( 'entry-views' ) );

		/* Plugin post types. */
		add_post_type_support( 'literature',      array( 'entry-views' ) );
		add_post_type_support( 'portfolio_item',  array( 'entry-views' ) );
		add_post_type_support( 'recipe',          array( 'entry-views' ) );
		add_post_type_support( 'restaurant_item', array( 'entry-views' ) );
	}

	/**
	 * Registers the plugin's widgets.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register_widgets() {

		register_widget( 'EV_Widget_Entry_Views' );
	}

	/**
	 * Checks if we're on a singular post view and if the current post type supports the 'entry-views'
	 * extension.  If so, set the $post_id variable and load the needed JavaScript.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function load() {

		/* Check if we're on a singular post view. */
		if ( is_singular() && !is_preview() ) {

			/* Get the post object. */
			$post = get_queried_object();

			/* Check if the post type supports the 'entry-views' feature. */
			if ( post_type_supports( $post->post_type, 'entry-views' ) ) {

				/* Set the post ID for later use because we wouldn't want a custom query to change this. */
				$this->post_id = get_queried_object_id();

				/* Enqueue the jQuery library. */
				wp_enqueue_script( 'jquery' );

				/* Load the entry views JavaScript in the footer. */
				add_action( 'wp_footer', array( $this, 'load_scripts' ) );
			}
		}
	}

	/**
	 * Callback function hooked to 'wp_ajax_entry_views' and 'wp_ajax_nopriv_entry_views'.  It checks the
	 * AJAX nonce and passes the given $post_id to the entry views update function.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function update_ajax() {

		/* Check the AJAX nonce to make sure this is a valid request. */
		check_ajax_referer( 'entry_views_ajax' );

		/* If the post ID is set, set it to the $post_id variable and make sure it's an integer. */
		if ( isset( $_POST['post_id'] ) )
			$post_id = absint( $_POST['post_id'] );

		/* If $post_id isn't empty, pass it to the ev_set_post_view_count() function to update the view count. */
		if ( !empty( $post_id ) )
			ev_set_post_view_count( $post_id );
	}

	/**
	 * Displays a small script that sends an AJAX request for the page.  It passes the $post_id to the AJAX 
	 * callback function for updating the meta.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function load_scripts() {

		/* Create a nonce for the AJAX request. */
		$nonce = wp_create_nonce( 'entry_views_ajax' );

		/* Display the JavaScript needed. */
		echo '<script type="text/javascript">/* <![CDATA[ */ jQuery(document).ready( function() { jQuery.post( "' . admin_url( 'admin-ajax.php' ) . '", { action : "entry_views", _ajax_nonce : "' . $nonce . '", post_id : ' . absint( $this->post_id ) . ' } ); } ); /* ]]> */</script>' . "\n";
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Entry_Views_Plugin::get_instance();
