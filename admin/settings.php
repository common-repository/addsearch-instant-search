<?php

	/* Get customer key from settings. */
	$addsearch_settings     = get_option( 'addsearch_settings' );
	$addsearch_customer_key = $addsearch_settings['customer_key'];
	$addsearch_installation = $addsearch_settings['installation_method'];

	$noYes = array(
		'false' => __( 'No', 'addsearch' ),
		'true' => __( 'Yes', 'addsearch' ),
	);
	$yesNo = array(
		'true' => __( 'Yes', 'addsearch' ),
		'false' => __( 'No', 'addsearch' ),
	);

	?>
	<div class="wrap">

		<h2><?php _e( 'AddSearch Settings', 'addsearch' ); ?></h2>

		<form method="post" action="options.php">

			<?php settings_fields( 'addsearch_settings_group' ); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e( 'Your Site Key', 'addsearch' ); ?></label></th>
						<td>
							<label>
								<input type="text" name="addsearch_settings[customer_key]" id="customer_key" required class="regular-text" value="<?php echo esc_attr( $addsearch_customer_key ); ?>" />
								<p class="description"><?php echo sprintf( _x( 'Enter your Site Key. This will replace all search forms in your site with AddSearch. This means all instances off %s.', '%s stands for function get_search_form()', 'addsearch' ), '<code>get_search_form()</code>' ); ?></p>
							</label>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="form-table addsearch-settings">
				<tbody>

					<tr valign="top">
						<th scope="row"><label><?php _e( 'Installation method', 'addsearch' ); ?></label></th>
						<td>
							<label>
							<?php
							    $installation_types = array( 
									'widgetv2' => esc_html__('Search as you type (New version)', 'addsearch'),
									'resultpagev2' => esc_html__('Separate page for results (New version)', 'addsearch'),
									'widget' => esc_html__('Search as you type (Old version)', 'addsearch'),
									'resultpage' => esc_html__('Separate page for results (Old version)', 'addsearch'),
								);
							?>
								<select name="addsearch_settings[installation_method]" id="installation_method">
									<?php
										foreach( $installation_types as $type => $label ) {
									?>
									<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $addsearch_installation, $type ); ?>><?php echo $label; ?></option>
									<?php
										}
									?>
								</select>
								<p class="description">
									<?php _e('Select whether you want to have results as you type, or a separate page for results.', 'addsearch'); ?>
								</p>
							</label>
						</td>
					</tr>

					<?php include_once __DIR__ . '/settings-widget.php'; ?>
					<?php include_once __DIR__ . '/settings-srp.php'; ?>

				</tbody>
			</table>

			<?php submit_button(); ?>

			<table class="form-table addsearch-instructions">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e( 'Here is how to configure the AddSearch Plugin', 'addsearch' ); ?></label></th>
					</tr>
					<tr valign="top">
						<td>
							<ol>
								<li><?php echo sprintf( __( 'Sign-Up for <a href="%s" target="_blank">AddSearch Trial</a> using your WordPress email', 'addsearch' ),
								'https://app.addsearch.com/signup/user?utm_campaign=Wordpress%20Plugin&utm_source=wordpress_plugin' ); ?></li>
								<li><?php _e( 'When prompted by the installation wizard, click index my website and choose set up crawling option in the next step', 'addsearch' ); ?></li>
								<li><?php _e( 'Enter the URL for your website on the next step and click begin crawling', 'addsearch' ); ?></li>
								<li><?php _e( 'Open the AddSearch dashboard, go to Setup -> Keys and Installation and copy your public site key under Your Site Key', 'addsearch' ); ?></li>
								<li><?php _e( 'Install and activate the AddSearch plugin from the WordPress admin interface', 'addsearch' ); ?></li>
								<li><?php _e( 'Paste the Site Key in Settings -> AddSearch and click Save', 'addsearch' ); ?></li>
								<li><?php _e( 'Select whether you want to use search-as-you-type search widget (default), or separate results page', 'addsearch' ); ?></li>
								<li><?php _e( 'Test your website and see if the search is working', 'addsearch' ); ?></li>
								<li><?php _e( 'If you see the AddSearch bar being displayed in bottom left corner it means search is not enabled in your theme. Go to your theme settings and
enable search', 'addsearch' ); ?></li>
								<li><?php echo sprintf( __( 'You can also add %s shortcode to anyplace where you want to place the search bar', 'addsearch' ), '<code>[addsearch]</code>' ); ?></li>
								<li><?php echo sprintf( __( 'Please note that Gutenberg plug-in search component is not automatically replaced with AddSearch Search component. We advise Gutenberg users to use the %s shortcode.', 'addsearch' ), '<code>[addsearch]</code>' ); ?></li>
								<li><?php _e( 'You\'re done - congrats! :)', 'addsearch' ); ?></li>
								<li><?php echo sprintf( __( 'For more details, follow our instructions on %show to install AddSearch on WordPress%s.', 'addsearch' ), '<a href="https://www.addsearch.com/docs/installation/wordpress/?utm_campaign=Wordpress%20Plugin&utm_source=wordpress_plugin" target="_new">', '</a>' ); ?></li>
							</ol>
						</td>
					</tr>
				</tbody>
			</table>

		</form>

	</div>
	
