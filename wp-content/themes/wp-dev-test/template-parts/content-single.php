<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php twentysixteen_excerpt(); ?>

	<?php twentysixteen_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
			

			$price = get_post_meta( get_the_ID(), 'price', true );
			echo "<b>Price</b>: ".$price."</br>";

			$is_on_sale_value = get_post_meta( get_the_ID(), 'is on sale', true );
			// Check if the custom field has a value.
			if ( ! empty( $is_on_sale_value ) && $is_on_sale_value == 'yes') {
				$sale_price = get_post_meta( get_the_ID(), 'sale price', true );
			    echo "<b>Sale Price</b>: ".$sale_price;
			}

			the_content();
			
			$video = get_post_meta( get_the_ID(), 'video', true );
			if ( ! empty( $video ) ) { ?>
				<iframe width="420" height="315"
				src="<?php echo $video; ?>" frameborder="0" allowfullscreen> 
				</iframe>

			<?php }

			if ( function_exists( 'get_related_posts_by_tax' ) ) {
			    $related_posts = get_related_posts_by_tax( 'category', array( 'posts_per_page' => 1) );
			    if ( $related_posts ) {
			    	echo '<h2>Related Post</h2>';
			        foreach ( $related_posts as $post ) {
			            setup_postdata( $post ); 
			            // Use your template tags and html mark up as normal like
			            ?>
			            <a href="<?php the_permalink(); ?>">
		                    <h4 class="group inner list-group-item-heading">
		                        <?php the_title(); ?></h4>               
		                </a>	


		                <?php if ( has_post_thumbnail() ) : ?>
			    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			        <?php the_post_thumbnail(); ?>
			    </a>
			<?php endif; ?>
			
			            <?php
			        }
			        wp_reset_postdata();
			    }
			}
			
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
