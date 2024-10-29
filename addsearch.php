<?php
/*
Plugin Name:       AddSearch Instant Search
Plugin URI:        https://www.addsearch.com/product/wordpress-search-plugin/?utm_campaign=Wordpress%20Plugin&utm_source=wordpress_plugin
Description:       AddSearch is an instant site search engine for your website.
Version:           2.2.3
Author:            AddSearch Ltd.
Author URI:        https://www.addsearch.com/?utm_campaign=Wordpress%20Plugin&utm_source=wordpress_plugin
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Text Domain:       addsearch
Domain Path:       /languages

This program is free software; you can redistribute it and/or modify it under the terms of the GNU
General Public License as published by the Free Software Foundation; either version 2 of the License,
or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
You should have received a copy of the GNU General Public License along with this program; if not, write
to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

/**
 * @package    AddSearch
 * @version    2.2.0
 * @author     AddSearch Ltd. <support@addsearch.com>
 * @copyright  Copyright (c) 2014, AddSearch Ltd.
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up and initializes the AddSearch plugin.
 *
 * @since  1.1.0
 * @access public
 * @return void
 */
final class AddSearch {
	public $dir_path = '';

	public $dir_uri  = '';

    private static $instance = null;

	private static $instance_count = 1;

	const _V2_URL = 'https://cdn.addsearch.com/v5/addsearch-ui.min.js';

	/**
	 * Returns the instance.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( is_null(self::$instance)) {
			self::$instance = new AddSearch;
			self::$instance->setup();
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->setup_actions();
		}

		return self::$instance;
	}

	private function __construct() {}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.1.0
	 * @return void
	 */
	private function setup_constants() {

		/* Plugin version. */
		if ( ! defined( 'ADDSEARCHP_VERSION' ) ) {
			define( 'ADDSEARCHP_VERSION', '2.2.3' );
		}

	}


	/**
	 * Setup plugin working directory and URI.
	 *
	 * @access private
	 * @return void
	 */
	private function setup() {

		$this->dir_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->dir_uri  = trailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Plugin actions.
	 *
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

		/* Enqueue scripts. */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Register init functions. */
		add_action( 'init', array( $this, 'init' ) );

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action( 'admin_init', array( $this, 'activation_redirect' ) );

		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

		add_action( 'admin_menu', array( $this, 'add_setting_page' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	function register_settings() {
		register_setting( 'addsearch_settings_group', 'addsearch_settings', array( $this, 'settings_sanitize' ) );
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @since  1.1.0
	 * @param  array $input Contains all settings fields as array keys.
	 * @return array $new_input Returns all sanitized settings as array
	 */
	function settings_sanitize( $input ) {
		$required_fields = [ 'customer_key', 'installation_method' ];

		$new_input = array();

		foreach ( $required_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$new_input[ $field ] = sanitize_text_field( $input[ $field ] );
			}
		}

		if ( strpos( $new_input['installation_method'], 'v2' ) !== false && isset( $input[ 'config' ] ) ) {
			foreach ( $input[ 'config' ][$new_input['installation_method']] as $field => $value ) {
				$new_input['config'][ $field ] = sanitize_text_field( $input['config'][$new_input['installation_method']][ $field ] );
			}
		}

		return $new_input;

	}

	/**
	 * Add options page under Settings >> AddSearch.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	function add_setting_page() {
		add_options_page( __( 'AddSearch settings', 'addsearch' ), __( 'AddSearch', 'addsearch' ), apply_filters( 'addsearch_settings_capability', 'manage_options' ), 'addsearch-options', array( $this, 'add_options_page' ) );
	}

	function add_options_page(){
		if ( ! current_user_can( apply_filters( 'addsearch_settings_capability', 'manage_options' ) ) ) {
			wp_die();
		}
		include_once $this->dir_path . 'admin/settings.php';
		include_once $this->dir_path . 'admin/admin-notices.php';
	}

	/**
	 * Register the addsearch parameter in the query parameters.
	 * 
	 * This is for the search results page.
	 *
	 * @access public
	 * @return array
	 */
	public function add_query_vars( $query_vars ){
		if ( ! isset( $query_vars['addsearch'] ) ) {
			$query_vars[] = 'addsearch';
		}
		return $query_vars;
	}

	/**
	 * Redirect to settings page on activation.
	 *
	 * @access public
	 * @return void
	 */
	public function activation_redirect() {
		if ( 'yes' === get_option( 'addsearch_redirect', false ) ) {
			delete_option( 'addsearch_redirect' );
			wp_safe_redirect( add_query_arg( array( 'page' => 'addsearch-options' ), admin_url( 'options-general.php' ) ) );
			exit;
		}
	}

	/**
	 * Load all includes for frontend and admin.
	 *
	 * @access private
	 * @return void
	 */
	private function includes() {
		/* Load admin functions. */
		if ( ! is_admin() ) {
			/* Load functions for the plugin. */
			require_once( $this->dir_path . 'includes/functions.php' );

			/* Load functions for the theme specific changes. */
			require_once( $this->dir_path . 'includes/theme-functions.php' );
		}
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return void
	 */
	public function i18n() {
		load_plugin_textdomain( 'addsearch', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Loads the script for the plugin.
	 *
	 * @since 1.1.0
	 * @access public
	 * @return void
	 */
	public static function enqueue_scripts() {
		$type = self::get_installation_method();

		// Don't queue scripts if we have selected installation method anything but "widget".
		if ( ! in_array( $type, array( 'widget' ), true ) ) {
			return;
		}

		$customer_key = self::get_customer_key();
		$query_args = [
			'key' => rawurlencode( esc_attr( $customer_key ) ),
		];


		$query_args = apply_filters( 'addsearch_query_args', $query_args, $type );

		wp_enqueue_script( 'addsearch-settings-js', esc_url( add_query_arg( $query_args, 'https://addsearch.com/js/' ) ), array(), null, true );

	}

	/**
	 * Loads the script for the plugin on the settings page.
	 *
	 * @since 2.1.0
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( $hook === 'settings_page_addsearch-options' ) {
			wp_enqueue_script( 'addsearch', $this->dir_uri . '/assets/js/settings.js', array( 'jquery' ), ADDSEARCHP_VERSION );
			wp_localize_script( 'addsearch', 'addsearch_config', array(
			) );

			wp_enqueue_style( 'addsearch', $this->dir_uri . '/assets/css/settings.css', array(), ADDSEARCHP_VERSION );
		}
	}

	/**
	 * Gets the installation type of plugin, meaning whether to use
	 * search-as-you-type functionality (default), or separate results page.
	 *
	 * @since 1.2.0
	 * @access public
	 * @return string
	 */
	public static function get_installation_method() {
		$addsearch_settings = get_option( 'addsearch_settings' );
		$installation_method = $addsearch_settings['installation_method'];

		// Fallback to search-as-you-type.
		if ( empty( $installation_method ) ) {
			return 'widgetv2';
		}

		return $installation_method;
	}

	/**
	 * Gets the customer key from settings
	 *
	 * @since 1.2.0
	 * @access public
	 * @return string
	 */
	public static function get_customer_key() {
		$addsearch_settings = get_option( 'addsearch_settings' );
		$addsearch_customer_key = $addsearch_settings['customer_key'];

		/* Bail if there is no customer key. */
		if ( empty( $addsearch_customer_key ) ) {
			return;
		}

		return $addsearch_customer_key;
	}

	/**
	 * Filter plugin action links.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function init() {

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'addsearch_settings_link' ) );

		if ( ! is_admin() ) {
			add_action( 'wp', [ $this, 'override_search_page' ] );
		}

		add_filter( 'get_search_form', 'addsearch_search_form', 20 );

		add_filter( 'render_block', array( $this, 'replace_core_search_block' ), null, 2 );

		add_filter( 'search_template', array( $this, 'search_results_template' ), 10, 1 );

		add_filter( 'init', array( $this, 'change_search_parameter' ) );

		add_filter( 'request', array( $this, 'search_request' ) );
	}

	/**
	 * Replace the core search block with our HTML.
	 * 
	 * This is for the search as you type widget.
	 *
	 * @param string $block_content The block content.
	 * @param array $block The full block, including name and attributes.
	 *
	 * @return string
	 *
	 * @since 2.3.0
	 */
	function replace_core_search_block( $block_content, $block ) {
		if (
			$block['blockName'] === 'core/search' && 
			!is_admin() &&
			!wp_is_json_request()
		) {
			$html = do_shortcode('[addsearch]');	
			return $html;
		}
	
		return $block_content;
	}

	/**
	 * Change the search results template.
	 *
	 * @param string $template The path of the template.
	 *
	 * @return string
	 *
	 * @since 2.1.0
	 */
	public function search_results_template( $template ) {
		$type = self::get_installation_method();
		if ( 'resultpagev2' === $type ) {
			return $this->dir_path . 'templates/search-results.php';
		}
		return $template;
	}

	/**
	 * Remove 's' as the search query parameter and use 'addsearch' instead.
	 * 
	 * Note: This does not work with block themes.
	 *
	 * @since 2.1.0
	 */
	public function change_search_parameter() {
		global $wp;
		$wp->add_query_var( 'addsearch' );
		$wp->remove_query_var( 's' );
	}

	/**
	 * Set the 's' parameter internally corresponding to the value of the 'addsearch' parameter.
	 * 
	 * Note: For block themes, this sets the 'addsearch' parameter on the basis of the 's' parameter.
	 *
	 * @param array $request The array of requested query variables.
	 *
	 * @return array
	 *
	 * @since 2.1.0
	 */
	public function search_request( $request ) {
		if ( isset( $_REQUEST['addsearch'] ) ){
			$request['s'] = $_REQUEST['addsearch'];
		} elseif ( isset( $_REQUEST['s'] ) ){
			$request['addsearch'] = $_REQUEST['s'];
		}
		return $request;
	}

	/**
	 * Add Settings page to plugin action links in the Plugins table.
	 *
	 * @since  1.1.0
	 * @param array $links Existing list of links under Settings category.
	 * @return string
	 */
	public static function addsearch_settings_link( $links ) {

		$addsearch_setting_link = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( 'page' => 'addsearch-options' ), admin_url( 'options-general.php' ) ) ), __( 'Settings', 'addsearch' ) );
		array_unshift( $links, $addsearch_setting_link );
		return $links;

	}

	/**
	 * On plugin activation, delete old setting and save it as new.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return void
	 */
	public static function activation() {

		/* Get old and new settings. */
		$addsearch_customer_key_old = get_option( 'addSearchCustomerKey' );
		$addsearch_settings         = get_option( 'addsearch_settings' );
		$addsearch_customer_key     = $addsearch_settings['customer_key'];

		/* If there is old setting but not new one, delete old one and add new one. */
		if ( ! empty( $addsearch_customer_key_old ) && empty( $addsearch_customer_key ) ) {

			$addsearch_customer_key = $addsearch_customer_key_old;

			$addsearch_settings_updated = array(
				'customer_key' => $addsearch_customer_key,
			);
			add_option( 'addsearch_settings', $addsearch_settings_updated );
			delete_option( 'addSearchCustomerKey' );
		}

		// is this a brand new install or was there a prexising install?
		$prexisting = empty( $addsearch_settings ) ? 'no' : 'yes';
		update_option( 'addsearch_prexisting', $prexisting );

		// add the current version being installed
		update_option( 'addsearch_currentversion', ADDSEARCHP_VERSION );

		// Don't redirect when multiple plugins are bulk activated
		if (
			( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) &&
			( isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) {
			return;
		}
		add_option( 'addsearch_redirect', 'yes' );
	}

	/**
	 * Returns if this install is a brand new one or not.
	 *
	 * @since 2.1.0
	 * @access public
	 * @return bool
	 */
	public function has_prexisting_install() {
		return 'yes' === get_option( 'addsearch_prexisting' );
	}

	/**
	 * Returns the script to echo on the page.
	 *
	 * @since 2.1.0
	 * @access public
	 * @return string
	 */
	public function get_script_for_v2( $echo = false ) {
		$type = self::get_installation_method();
		$id = null;

		$query_args = [
			'key' => rawurlencode( esc_attr( self::get_customer_key() ) ),
		];

		switch ( $type ) {
			case 'resultpagev2':
				$query_args['type'] = 'search_results_page';
				$id = 'arp_wp_01';
				break;
			case 'widgetv2':
				$suffix = str_pad( self::$instance_count++, 2, '0', STR_PAD_LEFT );
				$id = "asw_wp_{$suffix}";
				break;
		}

		if ( $id ) {
			$query_args['id'] = $id;
		}

		$query_args = apply_filters( 'addsearch_query_args', $query_args, $type );

		$script = sprintf( '
			<script id="addsearch-config" type="text/javascript">
			 %s
			</script>
			<script src="%s" id="addsearch" type="text/javascript"></script>', AddSearch::get_instance()->get_script_settings_for( $type, $id ), esc_url( add_query_arg( $query_args, AddSearch::_V2_URL ) ) ); 

		if ( $echo ) {
			echo $script;
		}
		return $script;
	}

	
	/**
	 * Override search page with our own functionality
	 *
	 * @since 1.2.0
	 * @access public
	 * @return void
	 */
	public function override_search_page() {

		global $wp;

		if ( ! apply_filters( 'addsearch_replace_search_page', true ) ) {
			//return;
		}

		$type = self::get_installation_method();
		
		// not relevant for search as you type
		if ( ! in_array( $type, array( 'resultpage', 'resultpagev2' ), true ) ) {
			return;
		}

		// not relevant for old search page method
		if ( $type === 'resultpagev2' && ! apply_filters( 'addsearch_replace_search_page', true ) ) {
			return;
		}
		
		if ( isset( $_REQUEST['addsearch'] ) || ( $wp && isset( $wp->query_vars['addsearch'] ) ) ) {
			$addsearch_customer_key = self::get_customer_key();
			$query_args = [
				'key' => rawurlencode( esc_attr( $addsearch_customer_key ) ),
				'type' => 'resultpage',
			];
			$query_args = apply_filters( 'addsearch_query_args', $query_args, $type );
			if ( in_array( $type, array( 'resultpagev2' ), true ) ) {
				include( $this->dir_path . 'templates/searchv2.php' );
				// In our template we add header and footer ourselves,
				// so we need to stop execution here to avoid re-rendering
				// them after our footer.
				die();
			}
			wp_enqueue_script(
				'addsearch-results-js',
				esc_url( add_query_arg( $query_args, 'https://addsearch.com/js/' ) ),
				array(),
				null,
				true
			);
			include( $this->dir_path . 'templates/search.php' );
			// In our template we add header and footer ourselves,
			// so we need to stop execution here to avoid re-rendering
			// them after our footer.
			die();
		}
	}

	/**
	 * Adds the inline script for the new widget/SRP.
	 *
	 * @since 2.1.0
	 * @access public
	 * @return string
	 */
	function get_script_settings_for( $type, $id ) {
		$addsearch_settings     = get_option( 'addsearch_settings' );
		$config_settings = $addsearch_settings['config'];

		$config = array();
		foreach ( $config_settings as $field => $value ) {
			$pattern = "%s: '%s'";
			if ( in_array( $value, array( 'true', 'false' ), true ) ) {
				$pattern = "%s: %s";
			} elseif ( is_numeric( $value ) ) {
				$pattern = "%s: %d";
			} elseif( empty( $value ) ) {
				continue;
			}
			$config[] = sprintf( $pattern, $field, $value );
		}

		$search_query_parameter = 'addsearch';
		$config[] = "search_query_parameter: '" . $search_query_parameter . "'";

		return sprintf('
			window.addsearch_settings = {
				  "%s": {%s}
			};
		', $id, implode( ', ', $config ) );
	}

	/**
	 * Displays all the fields with the given configuration.
	 * 
	 * @since  2.2.1
	 * @return The HTML output containing all the fields.
	 */
	public static function display_settings( $fields, $type ) {
		?>
		<tr valign="top" style="display: none" class="v2config <?php echo $type; ?>v2config">
			<th colspan="2" scope="row"><label><?php _e( 'Settings', 'addsearch' ); ?></label></th>
		</tr>
		<?php
		foreach ( $fields as $id => $field ) {
		?>
			<tr valign="top" style="display: none" class="v2config <?php echo $type; ?>v2config">
				<th><label><?php echo $field['label']; ?></label></th>
    			<td>
					<?php self::display_field( $id, $field, $type ); ?>
    			</td>
			</tr>
		<?php
		}
	}

	/**
	 * Displays the field.
	 * 
	 * @since  2.2.1
	 * @return The HTML output containing the field.
	 */
	static function display_field( $id, $field, $type ) {
		$addsearch_settings     = get_option( 'addsearch_settings' );
		$config_settings = $addsearch_settings['config'];
		$field_name = sprintf( 'addsearch_settings[config][%sv2][%s]', $type, $id );
		$field_value = $config_settings[ $id ] ?? '';
		if ( empty( $field_value ) && isset( $field['default'] ) ) {
			$field_value = $field['default'];
		}

		$attributes = '';
		if ( isset( $field['attributes'] ) ) {
			$atts = array();
			foreach ( $field['attributes'] as $name => $value ) {
				$atts[] = sprintf( '%s="%s"', $name, esc_attr( $value ) );
			}
			$attributes = implode( ' ', $atts );
		}

		switch ( $field['type'] ) {
			case 'number':
				?>
				<input 
					type="number" 
					<?php echo $attributes; ?> 
					name="<?php echo esc_attr( $field_name ); ?>" 
					value="<?php echo esc_attr( $field_value ); ?>"
				>
				<?php
				break;
			case 'text':
				?>
				<input 
					type="text" 
					<?php echo $attributes; ?> 
					name="<?php echo esc_attr( $field_name ); ?>" 
					value="<?php echo esc_attr( $field_value ); ?>"
				>
				<?php
				break;
			case 'select':
				?>
				<select name="<?php echo esc_attr( $field_name ); ?>">
				<?php
            		foreach ( $field['options'] as $value => $label ) {
        		?>
            		<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $field_value, $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
        		<?php
            		}
        		?>
				</select>
				<?php
				break;
		}
	}

}

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class hence, needs to be called outside and the
 * function also needs to be static.
 */
register_activation_hook( __FILE__, array( 'AddSearch', 'activation' ) );

function addsearch() {
	return AddSearch::get_instance();
}

addsearch();
