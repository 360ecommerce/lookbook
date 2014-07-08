<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Lookbooq_Cuztom_Bundle extends Lookbooq_Cuztom_Field
{
	var $type 		= 'bundle';
	var $fields 	= array();

	/**
	 * Construct for bundle
	 *
	 * @param   array  		$bundle
	 *
	 * @author  Gijs Jorissen
	 * @since 	2.8.4
	 * 
	 */
	function __construct( $bundle )
	{
		parent::__construct( $bundle );
	}

	/**
	 * Output a row
	 *
	 * @author  Gijs Jorissen
	 * @since 	3.0
	 * 
	 */
	function output_row( $value = null )
	{
		echo $this->output_control();

		echo '<tr class="cuztom-bundle">';
			echo '<td class="cuztom-field" id="' . $this->id . '" dataid="' . $this->id . '" colspan="2">';
				echo '<div class="cuztom-bundles cuztom-bundles-' . $this->id . '">';
					echo '<ul class="js-cz-sortable cuztom-sortable" data-cuztom-sortable-type="bundle">';
						$this->output();
					echo '</ul>';
				echo '</div>';
			echo '</td>';
		echo '</tr>';
		
		echo $this->output_control( 'bottom' );
	}

	/**
	 * Outputs a bundle
	 * 
	 * @param  	object 			$object
	 *
	 * @author  Gijs Jorissen
	 * @since   1.6.5
	 *
	 */
	function output( $value = null )
	{
		$i = 0;

		if( ! empty( $this->value ) && isset( $this->value[0] ) ) :
			foreach( $this->value as $bundle ) {
				echo $this->output_item( $i );
				$i++;
			}
		elseif( ! empty( $this->default_value ) ) :
			foreach( $this->default_value as $default ) {
				echo $this->output_item( $i );
				$i++;
			}
		else :
			echo $this->output_item();
		endif;
	}

	/**
	 * Outputs bundle item
	 *
	 * @param   int  		$index
	 *
	 * @author  Gijs Jorissen
	 * @since 	3.0
	 * 
	 */
	function output_item( $index = 0 )
	{
		$output = '<li class="cuztom-sortable-item">';
			$output .= '<div class="cuztom-handle-sortable js-cuztom-handle-sortable"><a href="#"></a></div>';
			$output .= '<fieldset class="cuztom-fieldset">';
				$output .= '<table border="0" cellading="0" cellspacing="0" class="form-table cuztom-table">';
					foreach( $this->fields as $id => $field ) {
						$field->before_name 	= '[' . $this->id . '][' . $index. ']';
						$field->after_id 		= '_' . $index;
						$field->default_value 	= isset( $this->default_value[$index][$id] ) ? $this->default_value[$index][$id] : $field->default_value;
						$value 					= isset( $this->value[$index][$id] ) ? $this->value[$index][$id] : '';
						
						if( ! $field instanceof Lookbooq_Cuztom_Field_Hidden ) {
							$output .= '<tr>';
								$output .= '<th class="cuztom-th">';
									$output .= '<label for="' . $id . $field->after_id . '" class="cuztom-label">' . $field->label . '</label>';
									$output .= '<div class="cuztom-field-description">' . $field->description . '</div>';
								$output .= '</th>';
								$output .= '<td class="cuztom-td">';

									if( $field->_supports_bundle ) {
										$output .= $field->output( $value );
									} else {
										$output .= '<em>' . __( 'This input type doesn\'t support the bundle functionality (yet).', 'cuztom' ) . '</em>';
									}

								$output .= '</td>';
							$output .= '</tr>';
						} else {
							$output .= $field->output( $value );
						}
					}
				$output .= '</table>';
			$output .= '</fieldset>';
			$output .= count( $this->value ) > 1 ? '<div class="cuztom-remove-sortable js-cz-remove-sortable"><a href="#"></a></div>' : '';
		$output .= '</li>';

		return $output;
	}

	/**
	 * Output a control row for a bundle
	 *
	 * @return 	void
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 *
	 */
	function output_control( $class = 'top' )
	{
		echo '<tr class="cuztom-control cuztom-control-' . $class . '" data-control-for="' . $this->id . '">';
			echo '<td colspan="2">';
				echo '<a class="button-secondary button button-small cuztom-button js-cz-add-sortable" data-sortable-type="bundle" data-field-id="' . $this->id . '" href="#">';
					echo sprintf( '+ %s', __( 'Add item', 'cuztom' ) );
				echo '</a>';

				if( $this->limit ) {
					echo '<div class="cuztom-counter">';
						echo '<span class="current">' . count( $this->value ) . '</span>';
						echo '<span class="divider"> / </span>';
						echo '<span class="max">' . $this->limit . '</span>';
					echo '</div>';
				}
			echo '</td>';
		echo '</tr>';
	}

	/**
	 * Save bundle meta
	 * 
	 * @param  	int 			$post_id
	 * @param  	string 			$value
	 *
	 * @author 	Gijs Jorissen
	 * @since 	1.6.2
	 * 
	 */
	function save( $object, $values )
	{
		$values = is_array( $values ) ? array_values( $values ) : array();

		foreach( $values as $row => $fields ) {
			foreach( $fields as $id => $value ) {
				$values[$row][$id] = $this->fields[$id]->save_value( $value );
			}
		}

		parent::save( $object, $values );
	}

	/**
	 * This method builds the complete array for a bundle
	 *
	 * @param 	array 		$data
	 * @param 	array 		$values
	 * @return 	void
	 *
	 * @author 	Gijs Jorissen
	 * @since 	3.0
	 *
	 */
	function build( $data, $values = null )
	{
		// Unset fields with array
		$this->fields = array();

		// Build fields with objects
		foreach( $data as $type => $field ) {
			if( is_string( $type ) && $type == 'tabs' ) {
				// $tab->fields = $this->build( $fields );
			} else {
				$args 	= array_merge( 
					$field, 
					array( 
						'meta_type' => $this->meta_type, 
						'object' 	=> $this->object
					) 
				);
				
				$field = Lookbooq_Cuztom_Field::create( $args );
				$field->repeatable 	= false;
				$field->ajax 		= false;
				$field->in_bundle 	= true;

				$this->fields[$field->id] = $field;
			}
		}
	}
}