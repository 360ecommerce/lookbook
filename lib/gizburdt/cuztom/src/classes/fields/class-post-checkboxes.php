<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Lookbooq_Cuztom_Field_Post_Checkboxes extends Lookbooq_Cuztom_Field
{
	var $_supports_bundle		= true;
	
	var $css_classes 			= array( 'cuztom-input' );

	function __construct( $field )
	{
		parent::__construct( $field );

		$this->args = array_merge(
			array(
				'post_type'			=> 'post',
				'posts_per_page'	=> -1
			),
			$this->args
		);
		
		$this->default_value 	 = (array) $this->default_value;
		$this->posts 			 = get_posts( $this->args );
		$this->after			.= '[]';
	}
	
	function _output( $value = null )
	{		
		$output = '<div class="cuztom-checkboxes-wrap">';
			if( is_array( $this->posts ) )
			{
				foreach( $this->posts as $post )
				{
					$output .= '<input type="checkbox" ' . $this->output_name() . ' ' . $this->output_id( $this->id . $this->after_id . '_' . Lookbooq_Cuztom::uglify( $post->post_title ) ) . ' ' . $this->output_css_class() . ' value="' . $post->ID . '" ' . ( is_array( $this->value ) ? ( in_array( $post->ID, $this->value ) ? 'checked="checked"' : '' ) : ( ( $this->value == '-1' ) ? '' : in_array( $post->ID, $this->default_value ) ? 'checked="checked"' : '' ) ) . ' /> ';
					$output .= '<label for="' . $this->id . $this->after_id . '_' . Lookbooq_Cuztom::uglify( $post->post_title ) . '">' . $post->post_title . '</label>';
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