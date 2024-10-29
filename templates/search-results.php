<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package education_center
 */

get_header(); ?>

<?php AddSearch::get_instance()->get_script_for_v2( true ); ?>

<?php
get_footer();
