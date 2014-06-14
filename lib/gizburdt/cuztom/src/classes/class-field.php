<?php

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Cuztom Field Class
 *
 * @author 	Gijs Jorissen
 * @since 	0.3.3
 *
 */
class Cuztom_Field
{
	var $id						= '';
	var $type					= null;
    var $label 					= '';
    var $description 			= '';
    var $explanation			= '';
    var $default_value 			= '';
	var $options 				= array(); // Only used for radio, checkboxes etc.
	var $args					= array(); // Specific args for the field
	var $required 				= false;
	var $repeatable 			= false;
	var $limit					= null;
	var $ajax 					= false;

	var $object 				= null;
	var $value 					= null;
	var $meta_type				= null;
	var $in_bundle				= false;

	var $data_attributes 		= array();
	var $css_classes			= array();
	
	var $show_admin_column 		= false;
	var $admin_column_sortable	= false;
	var $admin_column_filter	= false;
	
	var $before_name			= '';
	var $after_name				= '';
	var $before_id				= '';
	var $after_id				= '';

	var $_supports_repeatable 	= false;
	var $_supports_bundle		= false;
	var $_supports_ajax			= false;

	/**
	 * Constructs a Cuztom_Field
	 * 
	 * @param 	array 			$args
	 *
	 * @author  Gijs Jorissen
	 * @since 	0.3.3
	 * 
	 */
	function __construct( $args )
	{
		$properties = array_keys( get_class_vars( get_called_class() ) );
		
		// Set all properties
		foreach ( $properties as $property ) {
			$this->$property = isset( $args[ $property ] ) ? $args[ $property ] : $this->$property;
		}

		if( $this->is_repeatable() ) {
			$this->after_name = '[]';
			$this->value = @$args['value'];
		} else {
			$this->value = @$args['value'][0];
		}
	}
	
	/**
	 * Outputs a field based on its type
	 *
	 * @author 	Gijs Jorissen
	 * @since 	0.2
	 *
	 */
	function output( $value = null )
	{
		$value = $value ? $value : $this->value;

		if( $this->is_repeatable() ) {
			return $this->_output_repeatable( $value );
		} elseif( $this->is_ajax() ) {
			return $this->_output_ajax( $value );
		} else {
			return $this->_output( $value );
		}
	}

	/**
	 * Outputs a field row
	 *
	 * @author 	Gijs Jorissen
	 * @since 	0.2
	 *
	 */
	function output_row( $value = null )
	{
		echo '<tr>';
			echo '<th>';
				echo '<label for="' . $this->id . '" class="cuztom-label">' . $this->label . '</label>';
				echo $this->required ? ' <span class="cuztom-required">*</span>' : '';
				echo '<div class="cuztom-field-description">' . $this->description . '</div>';
			echo '</th>';
			echo '<td class="cuztom-field" id="' . $this->id . '" data-id="' . $this->id . '">';
				echo $this->output( $this->value );
			echo '</td>';
		echo '</tr>';
	}

	/**
	 * Output method
	 * Defaults to a normal text field
	 *
	 * @return  string
	 *
	 * @author 	Gijs Jorissen
	 * @since 	2.4
	 *
	 */
	function _output( $value = null )
	{
		return '<input type="text" ' . $this->output_name() . ' ' . $this->output_id() . ' ' . $this->output_css_class() . ' value="' . ( strlen( $value ) > 0 ? $value : $this->default_value ) . '" ' . $this->output_data_attributes() . ' />' . $this->output_explanation();
	}

	/**
	 * Outputs the field, ready for repeatable functionality
	 *
	 * @author  Gijs Jorissen
	 * @since   2.0
	 * 
	 */
	function _output_repeatable( $value = null )
	{
		$values 	= $value;
		$x 			= 0;

		$output = '<div class="cuztom-repeatable">';
			$output .= $this->_output_repeatable_control( $value );
			$output .= '<ul class="cuztom-sortable js-cuztom-sortable">';
				if( is_array( $value ) ) {
					foreach( $values as $value ) {
						$x++;
						$output .= $this->_output_repeatable_item( $value );

						if( $x >= $this->limit ) {
							break;
						}
					}
				} else {
					$output .= '<li class="cuztom-field cuztom-sortable-item js-cuztom-sortable-item"><div class="cuztom-handle-sortable js-cuztom-handle-sortable"><a href="#" tabindex="-1"></a></div>' . $this->_output( $value ) . '</li>';		
				}
			$output .= '</ul>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Outputs repeatable item
	 *
	 * @author  Gijs Jorissen
	 * @since   3.0
	 * 
	 */
	function _output_repeatable_item( $value = null )
	{
		return '<li class="cuztom-field cuztom-sortable-item js-cuztom-sortable-item"><div class="cuztom-handle-sortable js-cuztom-handle-sortable"><a href="#" tabindex="-1"></a></div>' . $this->_output( $value ) . ( count( $value ) > 1 ? '<div class="js-cuztom-remove-sortable cuztom-remove-sortable"><a href="#" tabindex="-1"></a></div>' : '' ) . '</li>';
	}

	/**
	 * Outputs repeatable control
	 *
	 * @author  Gijs Jorissen
	 * @since   3.0
	 * 
	 */
	function _output_repeatable_control( $value )
	{
		$output = '<div class="cuztom-control">';
			$output .= '<a class="button-secondary button button-small cuztom-button js-cuztom-add-sortable" href="#" data-sortable-type="repeatable" data-field-id="' . $this->id . '">' . sprintf( '+ %s', __( 'Add item', 'cuztom' ) ) . '</a>';
			if( $this->limit ) {
				$output .= '<div class="cuztom-counter">';
					$output .= '<span class="current">' . count( $value ) . '</span>';
					$output .= '<span class="divider"> / </span>';
					$output .= '<span class="max">' . $this->limit . '</span>';
				$output .= '</div>';
			}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Outputs the field, ready for ajax save
	 *
	 * @author  Gijs Jorissen
	 * @since   2.0
	 * 
	 */
	function _output_ajax( $value = null )
	{
		return $this->_output( $value ) . $this->_output_ajax_button();
	}

	/**
	 * Outputs ajax save button
	 *
	 * @author  Gijs Jorissen
	 * @since   3.0
	 * 
	 */
	function _output_ajax_button()
	{
		return '<a class="cuztom-ajax-save js-cuztom-ajax-save button button-secondary button-small" href="#" data-button-for="' . $this->id . '" data-object="' . $this->object . '" data-meta-type="' . $this->meta_type . '">' . __( 'Save', 'cuztom' ) . '</a>';
	}

	/**
	 * Output save value
	 * 
	 * @param  	string 			$value
	 *
	 * @author 	Ante Primorac
	 * @since  	2.8
	 * 
	 */
	function save_value( $value )
	{
		return $value;
	}

	/**
	 * Save meta
	 * 
	 * @param  	int 			$object_id
	 * @param  	string 			$value
	 *
	 * @author 	Gijs Jorissen
	 * @since  	1.6.2
	 * 
	 */
	function save( $object, $value )
	{
		// Maybe parse it through filters
		$value = $this->save_value( $value );

		switch( $this->meta_type ) :
			case 'user' :
				update_user_meta( $object, $this->id, $value );
				return true;
			break;
			case 'term' :
				// Because we need an array
				return $value;
			break;
			case 'post' : default :
				update_post_meta( $object, $this->id, $value );
				return true;
			break;
		endswitch;

		return false;
	}

	/**
	 * Outputs the fields name attribute
	 * 
	 * @author  Gijs Jorissen
	 * @since  	2.4
	 * 
	 */
	function output_name( $overwrite = null )
	{
		return apply_filters( 'cuztom_field_output_name', ( $overwrite ? 'name="' . $overwrite . '"' : 'name="cuztom' . $this->before_name . '[' . $this->id . ']' . $this->after_name . '"' ), $overwrite, $this );
	}

	/**
	 * Outputs the fields id attribute
	 * 
	 * @author  Gijs Jorissen
	 * @since  	2.4
	 * 
	 */
	function output_id( $overwrite = null )
	{
		return apply_filters( 'cuztom_field_output_id', ( $overwrite ? 'id="' . $overwrite . '"' : 'id="' . $this->before_id . $this->id . $this->after_id . '"' ), $overwrite, $this );
	}

	/**
	 * Outputs the fields css classes
	 *
	 * @param 	array 			$extra
	 * 
	 * @author  Gijs Jorissen
	 * @since  	2.4
	 * 
	 */
	function output_css_class( $extra = array() )
	{
		return apply_filters( 'cuztom_field_output_css_classes', ( 'class="' . implode( ' ', array_merge( $this->css_classes, $extra ) ) . '"' ), $extra, $this );
	}

	/**
	 * Outputs the fields data attributes
	 *
	 * @param 	array 			$extra
	 * 
	 * @author  Gijs Jorissen
	 * @since  	2.4
	 * 
	 */
	function output_data_attributes( $extra = array() )
	{
		foreach( array_merge( $this->data_attributes, $extra ) as $attribute => $value )
		{
			if( ! is_null( $value ) ) {
				$output = 'data-' . $attribute . '="' . $value . '"';
			} elseif( ! $value && isset( $this->args[Cuztom::uglify( $attribute )] ) ) {
				$output = 'data-' . $attribute . '="' . $this->args[Cuztom::uglify( $attribute )] . '"';
			}
		}

		return apply_filters( 'cuztom_field_output_data_attributes', @$output, $extra, $this );
	}

	/**
	 * Outputs the for attribute
	 * 
	 * @author  Gijs Jorissen
	 * @since  	2.4
	 * 
	 */
	function output_for_attribute( $for = null )
	{
		return apply_filters( 'cuztom_field_output_for_attribute', ( $for ? 'for="' . $for . '"' : '' ), $for, $this );
	}

	/**
	 * Outputs the fields explanation
	 * 
	 * @author  Gijs Jorissen
	 * @since  	2.4
	 * 
	 */
	function output_explanation()
	{
		return apply_filters( 'cuztom_field_output_explanation', ( ! $this->is_repeatable() && $this->explanation ? '<em class="cuztom-field-explanation">' . $this->explanation . '</em>' : '' ), $this );
	}

	/**
	 * Outputs the fields column content
	 * 
	 * @author  Gijs Jorissen
	 * @since  	3.0
	 * 
	 */
	function output_column_content( $post_id )
	{
		$meta = get_post_meta( $post_id, $this->id, true );

		if( $this->is_repeatable() ) {
			echo implode( $meta, ', ' );
		} else {
			echo $meta;
		}
	}

	/**
	 * Check what kind of meta we're dealing with
	 * 
	 * @return  string
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 * 
	 */
	function is_meta_type( $meta_type )
	{
		return $this->meta_type == $meta_type;
	}

	/**
	 * Check if the field is in ajax mode
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 * 
	 */
	function is_ajax()
	{
		return $this->ajax && $this->_supports_ajax;
	}

	/**
	 * Check if the field is in repeatable mode
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 * 
	 */
	function is_repeatable()
	{
		return $this->repeatable && $this->_supports_repeatable;
	}

	/**
	 * Creates and returns a field object
	 *
	 * @param 	array 			$args
	 * @return 	object|bool
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 *
	 */
	static function create( $args )
	{
		$class = 'Cuztom_Field_' . str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $args['type'] ) ) );

		if( class_exists( $class ) ) {
			return new $class( $args );
		} else {
			return false;
		}
	}
}