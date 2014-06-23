<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Lookbooq_Content_Types
{
	function __construct()
	{
		// Init
		add_action( 'init', array( &$this, 'register_post_types' ) );
		add_action( 'init', array( &$this, 'register_taxonomies' ) );

		// Meta boxes
		add_action( 'init', array( &$this, 'add_meta' ) );
	}

	/**
	 * Add post types
	 *
	 * @author 	Gijs Jorissen
	 * @since 	0.1
	 */
	function register_post_types() 
	{
		$labels = array(
		    'name' 					=> sprintf( _x( '%s', 'post type general name', 'lookbooq' ), 'Pictures' ),
			'singular_name' 		=> sprintf( _x( '%s', 'post type singular title', 'lookbooq' ), 'Picture' ),
			'menu_name' 			=> sprintf( __( '%s', 'lookbooq' ), 'Lookbooq' ),
			'all_items' 			=> sprintf( __( 'All %s', 'lookbooq' ), 'Pictures' ),
			'add_new' 				=> sprintf( _x( 'Add New', '%s', 'lookbooq' ), 'Picture' ),
			'add_new_item' 			=> sprintf( __( 'Add New %s', 'lookbooq' ), 'Picture' ),
			'edit_item' 			=> sprintf( __( 'Edit %s', 'lookbooq' ), 'Picture' ),
			'new_item' 				=> sprintf( __( 'New %s', 'lookbooq' ), 'Picture' ),
			'view_item' 			=> sprintf( __( 'View %s', 'lookbooq' ), 'Picture' ),
			'items_archive'			=> sprintf( __( '%s Archive', 'lookbooq' ), 'Picture' ),
			'search_items' 			=> sprintf( __( 'Search %s', 'lookbooq' ), 'Pictures' ),
			'not_found' 			=> sprintf( __( 'No %s found', 'lookbooq' ), 'Pictures' ),
			'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'lookbooq' ), 'Pictures' ),
			'parent_item_colon'		=> sprintf( __( '%s Parent', 'lookbooq' ), 'Picture' )
		);

		$args = apply_filters( 'lookbooq_piqture_args', array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'query_var'          => false,
			'rewrite'            => array( 'slug' => 'piqture' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => apply_filters( 'lookbooq_piqture_supports', array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ) )
		) );

		register_post_type( 'piqture', $args );
	}

	/**
	 * Add taxonomies
	 *
	 * @author 	Gijs Jorissen
	 * @since 	0.1
	 */
	function register_taxonomies()
	{
		$labels = array(
			'name' 					=> sprintf( _x( '%s', 'taxonomy general name', 'lookbooq' ), 'Lookbooks' ),
			'singular_name' 		=> sprintf( _x( '%s', 'taxonomy singular name', 'lookbooq' ), 'Lookbook' ),
		    'search_items' 			=> sprintf( __( 'Search %s', 'lookbooq' ), 'Lookbooks' ),
		    'all_items' 			=> sprintf( __( 'All %s', 'lookbooq' ), 'Lookbooks' ),
		    'parent_item' 			=> sprintf( __( 'Parent %s', 'lookbooq' ), 'Lookbook' ),
		    'parent_item_colon' 	=> sprintf( __( 'Parent %s:', 'lookbooq' ), 'Lookbook' ),
		    'edit_item' 			=> sprintf( __( 'Edit %s', 'lookbooq' ), 'Lookbook' ), 
		    'update_item' 			=> sprintf( __( 'Update %s', 'lookbooq' ), 'Lookbook' ),
		    'add_new_item' 			=> sprintf( __( 'Add New %s', 'lookbooq' ), 'Lookbook' ),
		    'new_item_name' 		=> sprintf( __( 'New %s Name', 'lookbooq' ), 'Lookbook' ),
		    'menu_name' 			=> sprintf( __( '%s', 'lookbooq' ), 'Lookbooks' )
		);

		$args = apply_filters( 'lookbooq_lookbooq_args', array(
			'labels'             	=> $labels,
			'public'             	=> false,
			'publicly_queryable' 	=> false,
			'show_ui'            	=> true,
			'query_var'          	=> false,
			'rewrite'          		=> array( 'slug' => 'slider' ),
			'has_archive'        	=> false,
			'hierarchical'       	=> true,
		) );

		register_taxonomy( 'lookbooq', array( 'piqture' ), $args );
	}

	function add_meta()
	{
		$piqture = new Lookbooq_Cuztom_Post_Type( 'piqture' );

		$piqture->add_meta_box( 'pointers', array(
			'title' => __('Pointers', 'lookbooq'),
			'fields' => array(
				'bundle' => array(
					'id' 	 => '_pointers',
					'fields' => array(
						array(
							'id' 			=> '_left',
							'label'			=> __('Form left', 'lookbooq'),
							'description'	=> __('Position from left (%)', 'lookbooq'),
							'type'			=> 'text'
						),
						array(
							'id' 			=> '_top',
							'label'			=> __('Form top', 'lookbooq'),
							'description'	=> __('Position from top (%)', 'lookbooq'),
							'type'			=> 'text'
						),
						array(
							'id' 			=> '_title',
							'label'			=> __('Title', 'lookbooq'),
							'type'			=> 'text'
						),
						array(
							'id' 			=> '_description',
							'label'			=> __('Description', 'lookbooq'),
							'type'			=> 'textarea'
						),
						array(
							'id' 			=> '_link',
							'label'			=> __('Link', 'lookbooq'),
							'type'			=> 'text'
						)
					)
				)
			)
		) );
	}
}