<?php

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles');
function salient_child_enqueue_styles() {

		$nectar_theme_version = nectar_get_theme_version();

    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('font-awesome'), $nectar_theme_version);

    if ( is_rtl() )
   		wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
}

add_action('wp_head', 'myoverride', 1);
function myoverride() {
  if ( class_exists( 'Vc_Manager' ) ) {
    remove_action('wp_head', array(visual_composer(), 'addMetaData'));
  }
}

/**
 * Remove password strength check.
 */
function iconic_remove_password_strength() {
    wp_dequeue_script( 'wc-password-strength-meter' );
}
add_action( 'wp_print_scripts', 'iconic_remove_password_strength', 10 );


// Change order social media $icon_class
function nectar_header_social_icons( $location ) {
	global $nectar_options;

	$social_networks    = array(
		'facebook'      => 'fa fa-facebook',
		'vimeo'         => 'fa fa-vimeo',
		'pinterest'     => 'fa fa-pinterest',
		'linkedin'      => 'fa fa-linkedin',
		'youtube'       => 'fa fa-youtube-play',
		'tumblr'        => 'fa fa-tumblr',
		'dribbble'      => 'fa fa-dribbble',
		'rss'           => 'fa fa-rss',
		'github'        => 'fa fa-github-alt',
		'google-plus'   => 'fa fa-google-plus',
		'instagram'     => 'fa fa-instagram',
		'twitter'       => 'fa fa-twitter',
		'stackexchange' => 'fa fa-stackexchange',
		'soundcloud'    => 'fa fa-soundcloud',
		'flickr'        => 'fa fa-flickr',
		'spotify'       => 'icon-salient-spotify',
		'vk'            => 'fa fa-vk',
		'vine'          => 'fa fa-vine',
		'behance'       => 'fa fa-behance',
		'houzz'         => 'fa fa-houzz',
		'yelp'          => 'fa fa-yelp',
		'snapchat'      => 'fa fa-snapchat',
		'mixcloud'      => 'fa fa-mixcloud',
		'bandcamp'      => 'fa fa-bandcamp',
		'tripadvisor'   => 'fa fa-tripadvisor',
		'telegram'      => 'fa fa-telegram',
		'slack'         => 'fa fa-slack',
		'medium'        => 'fa fa-medium',
		'phone'         => 'fa fa-phone',
		'email'         => 'fa fa-envelope',
	);
	$social_output_html = '';

	if ( $location == 'main-nav' ) {
		$social_link_before = '';
		$social_link_after  = '';
	} else {
		$social_link_before = '<li>';
		$social_link_after  = '</li>';
	}

	if ( $location == 'secondary-nav' ) {
		$social_output_html .= '<ul id="social">';
	}

	foreach ( $social_networks as $network_name => $icon_class ) {

		if ( $network_name == 'rss' ) {
			if ( ! empty( $nectar_options[ 'use-' . $network_name . '-icon-header' ] ) && $nectar_options[ 'use-' . $network_name . '-icon-header' ] == 1 ) {
				$nectar_rss_url_link = ( ! empty( $nectar_options['rss-url'] ) ) ? $nectar_options['rss-url'] : get_bloginfo( 'rss_url' );
				$social_output_html .= $social_link_before . '<a target="_blank" href="' . esc_url( $nectar_rss_url_link ) . '"><i class="' . $icon_class . '"></i> </a>' . $social_link_after;
			}
		} else {
			$target_attr = ($network_name != 'email' && $network_name != 'phone') ? 'target="_blank"' : '';
			if ( ! empty( $nectar_options[ 'use-' . $network_name . '-icon-header' ] ) && $nectar_options[ 'use-' . $network_name . '-icon-header' ] == 1 ) {
				$social_output_html .= $social_link_before . '<a '.$target_attr.' href="' . esc_url( $nectar_options[ $network_name . '-url' ] ) . '"><i class="' . $icon_class . '"></i> </a>' . $social_link_after;
			}
		}
	}

	if ( $location == 'secondary-nav' ) {
		$social_output_html .= '</ul>';
	}

	echo $social_output_html; // WPCS: XSS ok.
}

// Remove OpenGraph from Theme
function nectar_add_opengraph() {

}

// Remove WooCommerce Reviews Tab
add_filter( 'woocommerce_product_tabs', '_remove_reviews_tab', 98 );
function _remove_reviews_tab( $tabs ) {
  unset( $tabs[ 'reviews' ] );
  return $tabs;
}

function woocommerce_template_product_reviews() {
woocommerce_get_template( 'single-product-reviews.php' );
}
add_action( 'woocommerce_after_single_product_summary', 'comments_template', 50 );

// Merge Description and Additional Information Tabs
add_filter('woocommerce_product_tabs', 'change_product_tab', 98);
function change_product_tab($tabs){
    global $product;

    // Save the tabs to keep
    $reviews = $tabs['reviews'];

    // Remove tabs
    unset($tabs['description']);
    unset($tabs['additional_information']);
    unset($tabs['reviews']);

    // Add a new tab
    $tabs['other_details'] = array(
        'title'     => __( 'Details', 'woocommerce' ),
        'priority'  => 10,
        'callback'  => 'other_details_tab_content'
    );

    // Set the good priority to existing "reviews" tab
    $reviews['priority'] = 20;

    // Add back "reviews" tab
    $tabs['reviews'] = $reviews;

    return $tabs;
}

// Tab content (two columns)
function other_details_tab_content() {
    global $product;

    $heading = esc_html( apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) ) );
    $heading2 = esc_html( apply_filters( 'woocommerce_product_additional_information_heading', __( 'Additional information', 'woocommerce' ) ) );

    ?>
    <!-- Temporary styles (to be removed and inserted in the theme styles.css file) -->
    <style>
        .single-product .half-col{float:left; width:48%;}
        .single-product .half-col.first{margin-right:4%;}
        .single-product .half-col > h3{font-size:1.3em;}
    </style>

    <!-- 1. Product description -->

    <div class="half-col first">

    <?php if ( $heading ) : ?>
      <h3><?php echo $heading; ?></h3>
    <?php endif; ?>

    <?php the_content(); ?>

		<!-- Display Categories, Brands, Characters -->

		<?php
				global $post;
				$terms = get_the_terms( $post->ID, 'product_cat' );
				foreach ($terms as $term) {
						$product_cat_id = $term->term_id;
    				break;
				}
		?>

		<div class="product_meta">
				<?php echo $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Type:', 'Categories:', sizeof( get_the_terms( $post->ID, 'product_cat' ) ), 'woocommerce' ) . ' ', '</span>' ); ?>

				<span><?php echo 'Brand: '?>
							<?php $terms = get_the_terms( $post->ID , 'brands' );
		              foreach ( $terms as $term ) {
		                  $term_link = get_term_link( $term, 'brands' );
		                  if( is_wp_error( $term_link ) )
		                  continue;
		              echo '<a href="' . $term_link . '">' . $term->name . '</a>';
		              }
							?>
				</span>

				<span><?php echo 'Character: '?>
							<?php $terms = get_the_terms( $post->ID , 'characters' );
		              foreach ( $terms as $term ) {
		                  $term_link = get_term_link( $term, 'characters' );
		                  if( is_wp_error( $term_link ) )
		                  continue;
		              echo '<a href="' . $term_link . '">' . $term->name . '</a>';
		              }
							?>
				</span>



		</div>

    </div>

    <!-- 2. Product Additional information -->

    <div class="half-col last">

    <?php if ( $heading2 ) : ?>
    <h3><?php echo $heading2; ?></h3>
    <?php endif; ?>

    <?php do_action( 'woocommerce_product_additional_information', $product ); ?>

    </div>
    <?php
}

/* Remove product meta */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );


/* Remove Featured Image on Single Product Page for All Products */
add_filter( 'iconic_woothumbs_all_image_ids', 'iconic_remove_featured_on_single', 10, 2 );

function iconic_remove_featured_on_single( $all_images, $id ) {
    unset( $all_images['featured'] );
    return $all_images;
}

// Add Limited Edition Text
add_filter( 'woocommerce_get_availability_text', 'bbloomer_custom_get_availability_text', 99, 2 );

function bbloomer_custom_get_availability_text( $availability, $product ) {
  $stock = $product->get_stock_quantity();
  if ( $product->is_in_stock() && $product->managing_stock() && get_option( 'woocommerce_stock_format' ) == '' ) $availability = __('Limited Edition: ' . $stock, 'woocommerce');
  return $availability;
}

// Remove variable price in products and add From:
function wc_varb_price_range( $wcv_price, $product ) {

    $prefix = sprintf('%s: ', __('From', 'wcvp_range'));

    $wcv_reg_min_price = $product->get_variation_regular_price( 'min', true );
    $wcv_min_sale_price    = $product->get_variation_sale_price( 'min', true );
    $wcv_max_price = $product->get_variation_price( 'max', true );
    $wcv_min_price = $product->get_variation_price( 'min', true );

    $wcv_price = ( $wcv_min_sale_price == $wcv_reg_min_price ) ?
        wc_price( $wcv_reg_min_price ) :
        '<del>' . wc_price( $wcv_reg_min_price ) . '</del>' . '<ins>' . wc_price( $wcv_min_sale_price ) . '</ins>';

    return ( $wcv_min_price == $wcv_max_price ) ?
        $wcv_price :
        sprintf('%s%s', $prefix, $wcv_price);
}


add_filter( 'woocommerce_variable_sale_price_html', 'wc_varb_price_range', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_varb_price_range', 10, 2 );

/** Disable All WooCommerce  Styles and Scripts Except Shop Pages*/
add_action( 'wp_enqueue_scripts', 'dequeue_woocommerce_styles_scripts', 99 );
function dequeue_woocommerce_styles_scripts() {
if ( function_exists( 'is_woocommerce' ) ) {
if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
# Styles
wp_dequeue_style( 'woocommerce-general' );
wp_dequeue_style( 'woocommerce-layout' );
wp_dequeue_style( 'woocommerce-smallscreen' );
wp_dequeue_style( 'woocommerce_frontend_styles' );
wp_dequeue_style( 'woocommerce_fancybox_styles' );
wp_dequeue_style( 'woocommerce_chosen_styles' );
wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
# Scripts
wp_dequeue_script( 'wc_price_slider' );
wp_dequeue_script( 'wc-single-product' );
wp_dequeue_script( 'wc-add-to-cart' );
wp_dequeue_script( 'wc-cart-fragments' );
wp_dequeue_script( 'wc-checkout' );
wp_dequeue_script( 'wc-add-to-cart-variation' );
wp_dequeue_script( 'wc-single-product' );
wp_dequeue_script( 'wc-cart' );
wp_dequeue_script( 'wc-chosen' );
wp_dequeue_script( 'woocommerce' );
wp_dequeue_script( 'prettyPhoto' );
wp_dequeue_script( 'prettyPhoto-init' );
wp_dequeue_script( 'jquery-blockui' );
wp_dequeue_script( 'jquery-placeholder' );
wp_dequeue_script( 'fancybox' );
wp_dequeue_script( 'jqueryui' );
}
}
}
?>
