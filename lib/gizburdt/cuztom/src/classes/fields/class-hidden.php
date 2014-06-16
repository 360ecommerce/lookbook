<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Lookbooq_Cuztom_Field_Hidden extends Lookbooq_Cuztom_Field
{
	var $css_classes			= array( 'cuztom-input' );

	function _output( $value = null )
	{
		return '<input type="hidden" ' . $this->output_name() . ' ' . $this->output_id() . ' ' . $this->output_css_class() . ' ' . $this->output_value( $value ) . ' ' . $this->output_data_attributes() . ' />' . $this->output_explanation();
	}
}