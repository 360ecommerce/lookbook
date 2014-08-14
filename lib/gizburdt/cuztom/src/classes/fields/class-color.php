<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Lookbooq_Cuztom_Field_Color extends Lookbooq_Cuztom_Field
{
	/**
	 * Feature support
	 */
	var $_supports_ajax			= true;
	var $_supports_bundle		= true;

	/**
	 * Attributes
	 */
	var $css_classes			= array( 'js-cztm-colorpicker', 'cuztom-colorpicker', 'colorpicker', 'cuztom-input' );
}