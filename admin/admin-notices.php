<?php

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show admin notice if Site Key have not been entered.
 *
 * @since  1.1.0
 * @return void
 */
function addsearch_admin_notices() {

	$addsearch_settings     = get_option( 'addsearch_settings' );
	$addsearch_customer_key = $addsearch_settings[ 'customer_key' ];

	/* Show admin notice if there is no customer key. */
	if ( empty( $addsearch_customer_key ) ) { ?>
		<div class="error">
			<p>
				<?php echo sprintf( __( 'Enter AddSearch Site Key in %ssettings%s page to enable AddSearch.', 'addsearch' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=addsearch-options' ) ) . '">', '</a>' ); ?>
			</p>
		</div>
	<?php }

}
add_action( 'admin_notices', 'addsearch_admin_notices' );
