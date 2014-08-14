<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Lookbooq_Cuztom_Field_Post_Select extends Lookbooq_Cuztom_Field
{
	/**
	 * Feature support
	 */
	var $_supports_repeatable 	= true;
	var $_supports_ajax			= true;
	var $_supports_bundle		= true;

	/**
	 * Attributes
	 */
	var $css_classes 			= array( 'cuztom-input cuztom-select cuztom-post-select' );

	/**
	 * Constructs Cuztom_Field_Post_Select
	 *
	 * @author 	Gijs Jorissen
	 * @since 	0.3.3
	 *
	 */
	function __construct( $field )
	{
		parent::__construct( $field );

		$this->args = array_merge(
			array(
				'post_type'			=> 'post',
				'posts_per_page'	=> -1,
				'cache_results' 	=> false,
				'no_found_rows' 	=> true,
			),
			$this->args
		);

		$this->posts = get_posts( $this->args );
	}

	/**
	 * Output method
	 *
	 * @return  string
	 *
	 * @author 	Gijs Jorissen
	 * @since 	2.4
	 *
	 */
	function _output( $value = null )
	{
		$output = '<select ' . $this->output_name() . ' ' . $this->output_id() . ' ' . $this->output_css_class() . '>';
			if( isset( $this->args['show_option_none'] ) ) {
				$output .= '<option value="0" ' . ( empty( $value ) ? 'selected="selected"' : '' ) . '>' . $this->args['show_option_none'] . '</option>';
			}

			if( is_array( $this->posts ) ) {
				foreach( $posts = $this->posts as $post ) {
					$output .= '<option value="' . $post->ID . '" ' . ( ! empty( $value ) ? selected( $post->ID, $value, false ) : selected( $this->default_value, $post->ID, false ) ) . '>' . $post->post_title . '</option>';
				}
			}
		$output .= '</select>';
		$output .= $this->output_explanation();

		return $output;
	}
}