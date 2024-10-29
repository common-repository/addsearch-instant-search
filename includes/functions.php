<?php

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace WP search with AddSearch.
 *
 * @since  1.1.0
 * @param  string $form Search form.
 * @return string Modified search form
 */
function addsearch_search_form( $form ) {
	$addsearch_settings     = get_option( 'addsearch_settings' );
	$addsearch_customer_key = $addsearch_settings['customer_key'];
	$addsearch_installation_method = $addsearch_settings['installation_method'];

	/* Bail if there is no customer key. */
	if ( empty( $addsearch_customer_key ) ) {
		return $form;
	}

	// if we are using the new script
	if ( in_array( $addsearch_installation_method, array( 'widgetv2' ), true ) ) {
		// don't echo directly as it disrupts the theme in some cases
		return AddSearch::get_instance()->get_script_for_v2( false );
	}

	$current_root = get_site_url();

	$format = current_theme_supports( 'html5', 'search-form' ) ? 'html5' : 'xhtml';

	if ( 'html5' === $format ) {
		$form = sprintf( '<form role="search" class="search-form" action="%s" method="get">', $current_root );
		$form .= sprintf('
				<label>
					<span class="screen-reader-text">%s</span>
					<input type="search" class="addsearch search-field" placeholder="%s" value="%s" name="addsearch" title="%s" />
				</label>
			',
			_x( 'Search for:', 'label', 'addsearch' ),
			esc_attr_x( 'Search &hellip;', 'placeholder', 'addsearch' ),
			get_search_query(),
			esc_attr_x( 'Search for:', 'label', 'addsearch' )
		);

		if ( 'resultpage' === $addsearch_installation_method ) {
			$form .= sprintf(
				'<input type="submit" class="search-submit" value="%s" />',
				esc_attr_x( 'Search', 'submit button', 'addsearch' )
			);
		}

		$form .= '</form>';

		return $form;
	}

	$form = sprintf( '<form role="search" method="get" class="search-form" action="%s">
		<label>
			<span class="screen-reader-text">Search for:</span>
		</label>
        <input type="search" class="addsearch search-field" placeholder="Search …" value="" name="addsearch">',
		$current_root
	);

	if ( 'resultpage' === $addsearch_installation_method ) {
		$form .= '<button type="submit" class="search-submit">
			<svg class="icon icon-search" aria-hidden="true" role="img">
				<use href="#icon-search" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-search"></use>
			</svg>
			<span class="screen-reader-text">Search</span>
		</button>';
	}

	$form .= '</form>';

	return $form;
}

add_shortcode('addsearch', 'addsearch_search_form');
