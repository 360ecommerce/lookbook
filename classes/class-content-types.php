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
		add_action( 'add_meta_boxes', array( &$this, 'add_pointers_meta_box' ) );

		// Save
		add_action( 'save_post', array( &$this, 'save_post' ) );
	}

	/**
	 * Add post types
	 *
	 * @author 	{TODO:AUTHOR}
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
	 * @author 	{TODO:AUTHOR}
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

	function add_pointers_meta_box()
	{
		add_meta_box(
			'lookbooq_pointers',
			__( 'Pointers', 'lookbooq' ),
			array( &$this, '_add_pointers_meta_box' ),
			'piqture'
		);
	}

	function _add_pointers_meta_box()
	{
		wp_nonce_field( 'lookbooq_meta', 'lookbooq_nonce' ); ?>
			<input type="hidden" name="lookbooq[__activate]" />
			<div class="lookbooq">
				<div class="lookbooq-bundles">
					<ul>
						<li>
							<input type="text" name="lookbooq[0][left]" />
						</li>
						<li>
							<input type="text" name="lookbooq[0][top]" />
						</li>
					</ul>
				</div>
			</div>
		<?php
	}

	function save_post()
	{
		// Deny the wordpress autosave function
		if( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		// Verify nonce
		if( ! ( isset( $_POST['lookbooq_nonce'] ) && wp_verify_nonce( $_POST['lookbooq_nonce'], 'lookbooq_meta' ) ) ) {
			return;
		}

		// Is the current user capable to edit this post
		foreach( array( 'piqture' ) as $post_type ) {
			if( ! current_user_can( get_post_type_object( $post_type )->cap->edit_post, $post_id ) ) {
				return;
			}
		}

		// Values
		$values = isset( $_POST['lookbooq'] ) ? $_POST['lookbooq'] : array();

		// Save
		if( ! empty( $values ) ) {
			// Save
		}
	}
}