<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Lookbooq_Shortcodes
{
	function __construct()
	{
		add_shortcode( 'lookbooq', array( &$this, 'lookbooq' ) );
		add_shortcode( 'piqture', array( &$this, 'piqture' ) );
	}

	function lookbooq( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'name'			=> null,
			'class' 		=> '',
		), $atts ) );

		$piqtures = get_posts( array(
			'post_type'		=> 'piqture',
			'tax_query'		=> array( array(
				'taxonomy'		=> 'lookbooq',
				'field'			=> 'slug',
				'terms'			=> $name
			) )
		) );

		ob_start(); ?>

		<?php if( $piqtures ) : ?>
			<div class="lookbooq js-lookbooq">
				<?php foreach( $piqtures as $piqture ) : ?>
					<?php echo do_shortcode('[piqture id="' . $piqture->ID . '"]'); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php $output = ob_get_clean(); return $output;
	}

	function piqture( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'id'			=> null,
			'class' 		=> '',
		), $atts ) );

		$piqture = get_post( $id );

		ob_start(); ?>

		<?php if( $piqture ) : ?>
			<div class="piqture">
				<div class="pointers">
					<?php $pointers = get_post_meta( $piqture->ID, '_pointers', true ); ?>
					<?php if( ! empty( $pointers ) ) : ?>
						<?php foreach( $pointers as $i => $pointer ) : ?>
							<?php
								$left 			= @$pointer['_left'] . '%';
								$top 			= @$pointer['_top'] . '%';
								$title 			= @$pointer['_title'];
								$description 	= @$pointer['_description'];
								$link 			= @$pointer['_link'];
								$i++;
							?>
							<div class="pointer" style="left: <?php echo $left; ?>; top: <?php echo $top; ?>">
								<div class="pointer-bullet"><span><?php echo $i; ?></span></div>
								<div class="tip">
									<div class="tip-content"><?php echo $description; ?></div>
									<div class="tip-arrow"></div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="piqture-caption">
					<h3 class="caption-title"><?php echo get_the_title( $piqture->ID ); ?></h3>
					<div class="caption-content"><?php echo apply_filters( 'the_content', $piqture->post_content ); ?></div>
				</div>
				<?php echo get_the_post_thumbnail( $piqture->ID, 'full', array( 'class' => 'piqture-img' ) ); ?>
			</div>
		<?php endif; ?>

		<?php $output = ob_get_clean(); return $output;
	}
}