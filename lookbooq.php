<?php

/*
Plugin Name: 	Lookbooq
Plugin URI: 	http://gizburdt.com
Description: 	Image overlay pointers
Version: 		0.1
Author: 		Gizburdt
Author URI: 	http://gizburdt.com
License: 		GPL2
*/

if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Lookbooq' ) ) :

/**
 * Lookbooq
 */
class Lookbooq
{
	private static $instance;

	public static function instance()
	{
		if ( ! isset( self::$instance ) ) 
		{
			self::$instance = new Lookbooq;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->add_hooks();
			self::$instance->execute();
		}
		
		return self::$instance;
	}

	function setup_constants()
	{
		if( ! defined( 'LOOKBOOQ_VERSION' ) ) 
			define( 'LOOKBOOQ_VERSION', '0.1' );

		if( ! defined( 'LOOKBOOQ_DIR' ) ) 
			define( 'LOOKBOOQ_DIR', plugin_dir_path( __FILE__ ) );

		if( ! defined( 'LOOKBOOQ_URL' ) ) 
			define( 'LOOKBOOQ_URL', plugin_dir_url( __FILE__ ) );
	}

	function includes()
	{
		// Vendor
		include( LOOKBOOQ_DIR . 'vendor/gizburdt/cuztom/cuztom.php' );

		// Lookbooq
		include( LOOKBOOQ_DIR . 'classes/class-content-types.php' );
		include( LOOKBOOQ_DIR . 'classes/class-shortcodes.php' );
	}

	function add_hooks()
	{
		// Styles
		add_action( 'wp_enqueue_scripts',	array( &$this, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', 	array( &$this, 'enqueue_styles' ) );
		
		// Scripts
		add_action( 'wp_enqueue_scripts', 	array( &$this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', 	array( &$this, 'enqueue_scripts' ) );
	}

	function execute()
	{
		self::$instance->content_types 		= new Lookbooq_Content_Types;
		self::$instance->shortcodes 		= new Lookbooq_Shortcodes;
	}

	function register_styles()
	{		
		wp_register_style( 'lookbooq', LOOKBOOQ_URL . 'assets/css/lookbooq.css', false, LOOKBOOQ_VERSION, 'screen' );
	}

	function enqueue_styles()
	{
		wp_enqueue_style( 'lookbooq' );
	}

	function register_scripts()
	{
		wp_register_script( 'lookbooq', LOOKBOOQ_URL . 'assets/js/lookbooq.js', null, LOOKBOOQ_VERSION );
	}
	
	function enqueue_scripts()
	{
		wp_enqueue_script( 'lookbooq' );
		
		self::localize_scripts();
	}

	function localize_scripts()
	{
		wp_localize_script( 'lookbooq', 'Lookbooq', array(
			'home_url'			=> get_home_url(),
			'ajax_url'			=> admin_url( 'admin-ajax.php' ),
			'wp_version'		=> get_bloginfo( 'version' )
		) );
	}
}

endif; // End class_exists check

Lookbooq::instance();