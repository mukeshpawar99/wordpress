<?php 

wp_enqueue_style( 'product', get_stylesheet_directory_uri() . '/css/product.css',false,'1.1','all');
wp_enqueue_script( 'productJQ', get_stylesheet_directory_uri() . '/js/jquery.min.js', '', '', true);
wp_enqueue_script( 'productJs', get_stylesheet_directory_uri() . '/js/product.js', '', '', true);

function hide_admin_bar( $show ) {
	$dont_show_for_users = array( 2 );
	
	if ( in_array( get_current_user_id(), $dont_show_for_users ) ) :
		return false;
	endif;
	return $show;
}
add_filter( 'show_admin_bar', 'hide_admin_bar' );


// Our custom post type function
function create_posttype() {

	register_post_type( 'products',
	// CPT Options
		array(
			'labels' => array(
				'name' => __( 'Products' ),
				'singular_name' => __( 'Product' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'products'),
			'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' )
		)
	);
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );




// Hooking for setup taxonomy
add_action( 'init', 'create_my_taxonomies', 0 );
 
// function for create taxonomy
function create_my_taxonomies() {
    register_taxonomy(
        'category',
        'products',
        array(
            'labels' => array(
                'name' => 'Category',
                'add_new_item' => 'Add New Category',
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        )
    );
} 


function get_related_posts_by_tax( $taxonomy = '', $args = array() )
{
    /*
     * Before we do anything and waste unnecessary time and resources, first check if we are on a single post page
     * If not, bail early and return false
     */
    if ( !is_single() )
        return false;

    /*
     * Check if we have a valid taxonomy and also if the taxonomy exists to avoid bugs further down.
     * Return false if taxonomy is invalid or does not exist
     */
    if ( !$taxonomy ) 
        return false;

    $taxonomy = filter_var( $taxonomy, FILTER_SANITIZE_STRING );
    if ( !taxonomy_exists( $taxonomy ) )
        return false;

    /*
     * We have made it to here, so we should start getting our stuff togther. 
     * Get the current post object to start of
     */
    $current_post = get_queried_object();

    /*
     * Get the post terms, just the ids
     */
    $terms = wp_get_post_terms( $current_post->ID, $taxonomy, array( 'fields' => 'ids') );

    /*
     * Lets only continue if we actually have post terms and if we don't have an WP_Error object. If not, return false
     */
    if ( !$terms || is_wp_error( $terms ) )
        return false;

    /*
     * Set the default query arguments
     */
    $defaults = array(
        'post_type' => $current_post->post_type,
        'post__not_in' => array( $current_post->ID),
        'tax_query' => array(
            array(
                'taxonomy' => $taxonomy,
                'terms' => $terms,
                'include_children' => false
            ),
        ),
    );

    /*
     * Validate and merge the defaults with the user passed arguments
     */
    if ( is_array( $args ) ) {
        $args = wp_parse_args( $args, $defaults );
    } else {
        $args = $defaults;
    }

    /*
     * Now we can query our related posts and return them
     */
    $q = get_posts( $args );

    return $q;
}


function getProduct($atts) {
   	extract(shortcode_atts(array(
      'pid' => 1,
      'bgcolor' => 'black',
   	), $atts));

   	$postData = get_post( $pid );

	$price = get_post_meta( $pid, 'price', true );

	$dataFinal['bgcolor'] = $bgcolor;
	$dataFinal['pid'] = $pid;
	$dataFinal['post_title'] = $postData->post_title;
	$dataFinal['price'] = $price;

	$value = apply_filters( 'shortcode_atts_product', $dataFinal);

	return $value;
}

add_shortcode('product', 'getProduct');


function filter_product_shortcode( $out, $pairs, $atts ) {
	//$out['bgcolor'] = 'black'; // override color

	$data = $out['post_title']."</br>";
	if ( ! empty( $out['price'] ) ) {
		$data .= 'Price: '.$out['price']."</br>";
	}
	$data .= get_the_post_thumbnail( $out['pid'], 'thumbnail' );
	$dataFinal = '<div style="background-color:'.$out['bgcolor'].'"><a href="'.get_permalink($out['pid']).'">'.$data.'</a></div>';
    return $dataFinal;
}
add_filter( 'shortcode_atts_product', 'filter_product_shortcode', 10, 3 );


function address_mobile_address_bar() {
	$color = "#008509";
	//this is for Chrome, Firefox OS, Opera and Vivaldi
	echo '<meta name="theme-color" content="'.$color.'">';
	//Windows Phone **
	echo '<meta name="msapplication-navbutton-color" content="'.$color.'">';
	// iOS Safari
	echo '<meta name="apple-mobile-web-app-capable" content="yes">';
	echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">';
}
add_action( 'wp_head', 'address_mobile_address_bar' );



function wp_json_endpoint() {
	add_rewrite_tag( '%pid%', '([0-9]+)' );
	add_rewrite_rule( 'getProdData/([0-9]+)/?', 'index.php?pid=$matches[1]', 'top' );
}
add_action( 'init', 'wp_json_endpoint' );


function wp_endpoint_data() {
 
    global $wp_query;
 
  	$pid = $wp_query->get( 'pid' );

    if ( ! $pid ) {
        return;
    }
 
    $postData = get_post( $pid );

	$price = get_post_meta( $pid, 'price', true );
	$sale_price = get_post_meta( $pid, 'sale price', true );
	$is_on_sale = get_post_meta( $pid, 'is on sale', true );
	$image 	= get_the_post_thumbnail( $pid, 'thumbnail' );

	$dataFinal = array();
	$dataFinal['title'] = $postData->post_title;
	$dataFinal['description'] = $postData->post_content;
	$dataFinal['image'] = $image;
	$dataFinal['price'] = $price;
	$dataFinal['sale_price'] = $sale_price;
	$dataFinal['is_on_sale'] = $is_on_sale;

    wp_send_json( $dataFinal );
}
add_action( 'template_redirect', 'wp_endpoint_data' );
?>