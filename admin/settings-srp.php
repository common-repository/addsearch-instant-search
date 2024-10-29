<?php
/**
 * This file contains the fields configuration for search results page.
 * 
 * @since  2.2.1
 */

$fields = array(
	'display_sortby' => array(
		'label' => __( 'Display Sort by', 'addsearch' ),
		'type' => 'select',
		'options' => $yesNo,
	),
	'default_sortby' => array(
		'label' => __( 'Sort by', 'addsearch' ),
		'type' => 'select',
		'options' => array(
			'relevance' => __( 'Relevance', 'addsearch' ),
			'date' => __( 'Date', 'addsearch' ),
		),
	),
	'display_date' => array(
		'label' => __( 'Display Date', 'addsearch' ),
		'type' => 'select',
		'options' => $noYes,
	),
	'display_category' => array(
		'label' => __( 'Display Category', 'addsearch' ),
		'type' => 'select',
		'options' => $yesNo,
	),
	'display_results_count' => array(
		'label' => __( 'Display Results Count', 'addsearch' ),
		'type' => 'select',
		'options' => $yesNo,
	),
	'placeholder' => array(
		'label' => __( 'Placeholder', 'addsearch' ),
		'type' => 'text',
		'default' => __( 'Search', 'addsearch' ),
	),
	'show_search_suggestions' => array(
		'label' => __( 'Show Suggestions', 'addsearch' ),
		'type' => 'select',
		'options' => $yesNo,
	),
	'search_suggestion_position' => array(
		'label' => __( 'Suggestion position', 'addsearch' ),
		'type' => 'select',
		'options' => array(
			'left' => __( 'Left', 'addsearch' ),
			'right' => __( 'Right', 'addsearch' ),
		),
	),
	'number_of_results' => array(
		'label' => __( 'Number of results', 'addsearch' ),
		'type' => 'number',
		'default' => 10,
		'attributes' => array(
			'min' => 0,
			'placeholder' => 10
		),
	),
	'display_result_image' => array(
		'label' => __( 'Show Image', 'addsearch' ),
		'type' => 'select',
		'options' => $yesNo,
	),
	'hide_logo' => array(
		'label' => __( 'Hide Logo', 'addsearch' ),
		'type' => 'select',
		'options' => $noYes,
	),
	'analytics_enabled' => array(
		'label' => __( 'Google Analytics Enabled', 'addsearch' ),
		'type' => 'select',
		'options' => $noYes,
	),
	'default_view' => array(
		'label' => __( 'Default View', 'addsearch' ),
		'type' => 'select',
		'options' => array( 
			'grid' => __( 'Grid View', 'addsearch'),
			'' => __( 'List View', 'addsearch'),
		),
	),
	'layout_theme_name' => array(
		'label' => __( 'Theme', 'addsearch' ),
		'type' => 'select',
		'options' => array( 
			'dark' => __( 'Dark', 'addsearch'),
			'' => __( 'Light', 'addsearch'),
		),
	),
	'automatic_filter_results_by_site_language' => array(
		'label' => __( 'Filter Results by Language', 'addsearch' ),
		'type' => 'select',
		'options' => $noYes,
	),
);
?>

<?php AddSearch::display_settings( $fields, 'resultpage' ); ?>