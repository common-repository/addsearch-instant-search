<?php
/**
 * Theme specific functions.
 *
 * @since 2.1.0
 */

add_action(
	'et_search_form_fields',
	/**
	 * Change the name of the search field for DIVI theme.
	 *
	 * @since 2.1.0
	 */
	function(){
		$addsearch_settings     = get_option( 'addsearch_settings' );
		$addsearch_installation_method = $addsearch_settings['installation_method'];
		$remove_field = false;
		?>
			<script>
				(function($){
					$(document).ready(function(){
						<?php if ( in_array( $addsearch_installation_method, array( 'resultpagev2' ), true ) ) { ?>
							$('input[name="s"]').attr('name', 'addsearch');
						<?php } ?>
						<?php if ( in_array( $addsearch_installation_method, array( 'widgetv2' ), true ) && $remove_field ) { ?>
							$('.et-search-field').remove();
						<?php } ?>
					});
				})(jQuery);
			</script>
		<?php
		if ( in_array( $addsearch_installation_method, array( 'widgetv2' ), true ) ) {
			AddSearch::get_instance()->get_script_for_v2( true );
		}
	}
);

add_filter(
	'addsearch_replace_search_page',
	/**
	 * Check if the theme support replacement of the search page.
	 *
	 * Will return false for DIVI.
	 *
	 * Note: Divi does not work if we replace the search page 
	 * as the CSS does not get loaded in the footer after die().
	 *
	 * @since 2.1.0
	 */
	function( $default ) {
		return ! class_exists( 'ET_Theme_Builder_Request' );
	}
);