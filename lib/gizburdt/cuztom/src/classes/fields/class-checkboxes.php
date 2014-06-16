<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Lookbooq_Cuztom_Field_Checkboxes extends Lookbooq_Cuztom_Field
{
	var $_supports_bundle			= true;
	
	var $css_classes				= array( 'cuztom-input' );

	function __construct( $field )
	{
		parent::__construct( $field );

		$this->default_value = (array) $this->default_value;
		$this->after 		.= '[]';
	}

	function _output( $value = null )
	{
		$output = '<div class="cuztom-checkboxes-wrap">';
			if( is_array( $this->options ) )
			{
				foreach( $this->options as $slug => $name )
				{
					$output .= '<input type="checkbox" ' . $this->output_name() . ' ' . $this->output_id( $this->id . $this->after_id . '_' . Lookbooq_Cuztom::uglify( $slug ) ) . ' ' . $this->output_css_class() . ' value="' . $slug . '" ' . ( is_array( $this->value ) ? ( in_array( $slug, $this->value ) ? 'checked="checked"' : '' ) : ( ( $this->value == '-1' ) ? '' : in_array( $slug, $this->default_value ) ? 'checked="checked"' : '' ) ) . ' /> ';
					$output .= '<label ' . $this->output_for_attribute( $this->id . $this->after_id . '_' . Lookbooq_Cuztom::uglify( $slug ) ) . '>' . Lookbooq_Cuztom::beautify( $name ) . '</label>';
					$output .= '<br />';
				}
			}
		$output .= '</div>';

		$output .= $this->output_explanation();

		return $output;
	}

	function save_value( $value )
	{
		return empty( $value ) ? '-1' : $value;
	}
}