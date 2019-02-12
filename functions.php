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
?>
