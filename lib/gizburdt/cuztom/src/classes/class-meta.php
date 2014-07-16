<?php

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Cuztom Meta for handling meta data
 *
 * @author 	Gijs Jorissen
 * @since 	1.5
 *
 */
class Lookbooq_Cuztom_Meta
{
	var $id;
	var $title;
	var $description;
	var $fields;
	var $data;
	var $callback;

	/**
	 * Construct for all meta types, creates title (and description)
	 * 
	 * @param 	string|array 	$title
	 *
	 * @author  Gijs Jorissen
	 * @since 	1.6.4
	 * 
	 */
	function __construct( $id, $data )
	{
		global $cuztom;

		$properties = array_keys( get_class_vars( get_called_class() ) );
		
		// Set all properties
		foreach ( $properties as $property ) {
			$this->$property = isset( $data[ $property ] ) ? $data[ $property ] : $this->$property;
		}

		$this->id 		= $id;
		$this->object 	= $this->get_object_id();
	}

	/**
	 * Main callback for meta
	 *
	 * @param 	object 			$post
	 * @param 	object 			$data
	 * @return 	mixed
	 *
	 * @author 	Gijs Jorissen
	 * @since 	0.2
	 *
	 */	
	function output()
	{
		// Nonce field for validation
		wp_nonce_field( 'cuztom_meta', 'cuztom_nonce' );

		if( ! empty( $this->data ) ) {
			echo '<input type="hidden" name="cuztom[__activate]" />';
			echo '<div class="cuztom data-object-id="' . $this->object . '" data-meta-type="' . $this->meta_type . '">';
				if( ! empty( $this->description ) ) {
					echo '<div class="cuztom-box-description">' . $this->description . '</div>';
				}

				echo '<table class="form-table cuztom-table cuztom-main-table">';
					foreach( $this->data as $id => $field ) {
						$this->output_row( $field );
					}
				echo '</table>';
			echo '</div>';
		}
	}

	/**
	 * Outputs a row in a meta table
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 */
	function output_row( $field )
	{
		if( ( $field instanceof Lookbooq_Cuztom_Tabs ) || ( $field instanceof Lookbooq_Cuztom_Bundle ) ) {
			$field->output_row();
		} else {
			if( $field instanceof Lookbooq_Cuztom_Field_Hidden ) :
				echo $field->output( $field->value );
			else :
				$field->output_row( $field->value );
			endif;
		}
	}

	/**
	 * Normal save method to save all the fields in a metabox
	 * Metabox and User Meta rely on this method
	 *
	 * @author 	Gijs Jorissen
	 * @since 	2.6
	 */
	function save( $object, $values )
	{
		// Loop through each meta box
		if( ! empty( $this->data ) && isset( $_POST['cuztom'] ) ) {
			foreach( $this->data as $id => $field ) {
				if( ( $field instanceof Lookbooq_Cuztom_Tabs || $field instanceof Lookbooq_Cuztom_Accordion ) && $tabs = $field ) :
					$tabs->save( $object, $values );
				elseif( $field instanceof Lookbooq_Cuztom_Bundle && $bundle = $field ) :
					$value = @$values[$id];
					$bundle->save( $object, $value );
				else :
					if( @$field->in_bundle ) {
						continue;
					}

					$value = @$values[$id];
					$field->save( $object, $value );
				endif;
			}
		}
	}

	/**
	 * Check what kind of meta we're dealing with
	 * 
	 * @return  string
	 *
	 * @author 	Gijs Jorissen
	 * @since 	2.3
	 * 
	 */
	function is_meta_type( $meta_type )
	{
		return $this->meta_type == $meta_type;
	}

	/**
	 * Get object ID
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 * 
	 */
	function get_object_id()
	{
		if( isset( $_GET['post'] ) ) :
			return $_GET['post'];
		elseif( isset( $_GET['user_id'] ) ) :
			return $_GET['user_id'];
		elseif( isset( $_GET['tag_ID'] ) ) :
			return $_GET['tag_ID'];
		else :
			return null;
		endif;

		// @TODO: Use get_current_screen()
	}

	/**
	 * Get value bases on field id
	 * 
	 * @return  field
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 * 
	 */
	function get_meta_values( $field = null, $args = array( 'taxonomy' => '' ) )
	{
		switch( $this->meta_type ) :
			case 'post' :
				return get_post_meta( $this->object );
				break;
			case 'user' :
				return get_user_meta( $this->object );
				break;
			case 'term' :
				return get_lookbooq_cuztom_term_meta( $this->object, isset( $object->taxonomy ) ? $object->taxonomy : null );
				break;
			default :
				return false;
				break;
		endswitch;
	}

	/**
	 * This method builds the complete array with the right key => value pairs
	 *
	 * @param 	array 			$data
	 * @return 	array
	 *
	 * @author 	Gijs Jorissen
	 * @since 	1.1
	 *
	 */
	function build( $data )
	{
		global $cuztom;

		$return 	= array();
		$values		= $this->get_meta_values();

		if( is_array( $data ) && ! empty( $data ) )
		{
			foreach( $data as $type => $field )
			{
				// Tabs / accordion
				if( is_string( $type ) && ( $type == 'tabs' || $type == 'accordion' ) )
				{
					$args = array_merge( $field, array( 'meta_type' => $this->meta_type, 'object' => $this->object ) );
					$tabs = $type == 'tabs' ? new Lookbooq_Cuztom_Tabs( $field ) : new Lookbooq_Cuztom_Accordion( $field );
					$tabs->build( $field['panels'], $values );

					$cuztom['data'][$this->id][$tabs->id] = $tabs;
					$cuztom['fields'][$tabs->id] = $tabs;
				}

				// Bundle
				elseif( is_string( $type ) && $type == 'bundle' )
				{
					$args 	=  array_merge( $field, array( 'meta_type' => $this->meta_type, 'object' => $this->object, 'value' => @$values[$field['id']][0] ) );
					$bundle = new Lookbooq_Cuztom_Bundle( $args );
					$bundle->build( $field['fields'], $values );
					
					$cuztom['data'][$this->id][$bundle->id] = $bundle;
					$cuztom['fields'][$bundle->id] = $bundle;
				}

				// Fields
				else
				{
					$args 	= array_merge( $field, array( 'meta_type' => $this->meta_type, 'object' => $this->object, 'value' => @$values[$field['id']][0] ) );
					$field 	= Lookbooq_Cuztom_Field::create( $args );

					$cuztom['data'][$this->id][$field->id] = $field;
					$cuztom['fields'][$field->id] = $field;
				}
			}

			// Set flat array of fields
			$this->fields = $cuztom['fields'];
		}

		return $cuztom['data'][$this->id];
	}

	/**
	 * Adds multipart support to form
	 *
	 * @return 	mixed
	 *
	 * @author 	Gijs Jorissen
	 * @since 	0.2
	 *
	 */
	static function edit_form_tag()
	{
		echo ' enctype="multipart/form-data"';
	}
}