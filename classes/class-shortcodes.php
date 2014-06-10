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
					<?php $pointers = array( '20-20' ); //'30-30', '40-40' ?>
					<?php if( ! empty( $pointers ) ) : ?>
						<?php foreach( $pointers as $pointer ) : ?>
							<?php
								$measure = explode( '-', $pointer );
								$left = $measure[0] . '%';
								$top = $measure[1] . '%';
							?>
							<div class="pointer" style="left: <?php echo $left; ?>; top: <?php echo $top; ?>">
								<div class="pointer-icon"></div>
								<div class="tip">
									<div class="tip-content">
										Hier een korte uitleg over wat je hier ziet. Zo kun je kleine onderdelen van een foto even uitlichten.
									</div>
									<div class="tip-arrow"></div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="caption">
					<h3 class="caption-title"><?php echo get_the_title( $piqture->ID ); ?></h3>
					<div class="caption-content"><?php echo apply_filters( 'the_content', $piqture->post_content ); ?></div>
				</div>
				<?php echo get_the_post_thumbnail( $piqture->ID, 'full', array( 'class' => 'piqture-img' ) ); ?>
			</div>
		<?php endif; ?>

		<?php $output = ob_get_clean(); return $output;
	}
}