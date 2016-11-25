<?php 
get_header();

$args = array( 'posts_per_page' => 10,
		'offset' => 0,		
		'orderby' => 'date',
		'order' => 'DESC',	
		'post_type' => 'products',
		'post_status' => 'publish'); 

$posts_array = get_posts( $args );
?>

<div class="container">
    <div id="products" class="row list-group">
    	<?php foreach ( $posts_array as $post ) {?>
        <div class="item  col-xs-4 col-lg-4 grid-group-item">
            <div class="thumbnail">

            <div class="caption">
                <a href="<?php the_permalink(); ?>">
                    <h4 class="group inner list-group-item-heading">
                        <?php the_title(); ?></h4>               
                </a>	            
                <?php 
	                $is_on_sale_value = get_post_meta( get_the_ID(), 'is on sale', true );
					// Check if the custom field has a value.
					if ( ! empty( $is_on_sale_value ) && $is_on_sale_value == 'yes') {
					    echo "<b>on sale</b>";
					}
				?>
            </div>

            <?php if ( has_post_thumbnail() ) : ?>
			    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			        <?php the_post_thumbnail(); ?>
			    </a>
			<?php endif; ?>
            

            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php get_footer(); ?>