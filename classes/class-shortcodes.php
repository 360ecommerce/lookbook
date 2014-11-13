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
			'post_type'			=> 'piqture',
			'orderby'			=> 'menu_order',
			'order'				=> 'ASC',
			'tax_query'			=> array( array(
				'taxonomy'			=> 'lookbooq',
				'field'				=> 'slug',
				'terms'				=> $name
			) ),
			'posts_per_page'	=> -1
		) );

		ob_start(); ?>

		<?php if( $piqtures ) : ?>
			<div class="lookbooq js-lookbooq">
				<div class="lookbooq-slider js-lookbooq-slider">
					<?php foreach( $piqtures as $piqture ) : ?>
						<?php echo do_shortcode('[piqture id="' . $piqture->ID . '"]'); ?>
					<?php endforeach; ?>
				</div>
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
			<div class="piqture js-piqture">
				<?php $pointers = get_post_meta( $piqture->ID, '_pointers', true ); ?>
				<div class="sqreen">

					<!-- .pointers -->
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
									<div class="tip-arrow arrow-up"></div>
									<div class="tip-content"><?php echo $description; ?></div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
					<!-- /.pointers -->

					<?php $image = wp_get_attachment_url( get_post_thumbnail_id( $piqture->ID ) ); ?>
					<a href="<?php echo $image ?>" class="lookbooq-fancybox js-lookbooq-fancybox" rel="lookbooq"><img src="<?php echo $image ?>" class="piqture-img"></a>
					<div class="piqture-caption hide-mobile">
						<h3 class="caption-title"><?php echo get_the_title( $piqture->ID ); ?></h3>
						<div class="caption-content"><?php echo apply_filters( 'the_content', $piqture->post_content ); ?></div>
					</div>
				</div>
				<div class="piqture-caption show-mobile">
					<h3 class="caption-title"><?php echo get_the_title( $piqture->ID ); ?></h3>
					<div class="caption-content"><?php echo apply_filters( 'the_content', $piqture->post_content ); ?></div>
					<div class="pointers-list show-mobile">
						<ul>
							<?php if( ! empty( $pointers ) ) : ?>
								<?php foreach( $pointers as $i => $pointer ) : ?>
									<?php
										$title 			= @$pointer['_title'];
										$description 	= @$pointer['_description'];
										$link 			= @$pointer['_link'];
										$i++;
									?>
									<li>
										<div class="pointer-left"><div class="pointer-bullet"><span><?php echo $i; ?></span></div></div>
										<div class="pointer-right">
											<h4 class="pointer-title"><?php echo $title; ?></h4>
											<p class="pointer-description"><?php echo $description; ?></p>
										</div>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php $output = ob_get_clean(); return $output;
	}
}