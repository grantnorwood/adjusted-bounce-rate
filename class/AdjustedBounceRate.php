<?php

namespace gkn_abr;

/**
 * Class AdjustedBounceRate
 *
 * @package gkn_abr
 */
class AdjustedBounceRate {

	public $version = '2.0.0-develop';
	public $plugin_base_path = ''; //set in __construct()
	public $plugin_base_url = ''; //set in __construct()
	public $plugin_title = 'Adjusted Bounce Rate';
	public $plugin_slug = 'adjusted-bounce-rate';
//	public $plugin_options_group = 'pressms'; //Still need this?
	public $options_key = 'adjusted-bounce-rate-options';
	public $text_domain = 'adjusted-bounce-rate';
	public $minify_js = false;

	/**
	 * Constructor.
	 */
	function __construct() {

		//Set some vars.
		$this->plugin_base_path = dirname(__FILE__);
		$this->plugin_base_url = plugins_url('adjusted-bounce-rate');

		//WP actions.
		add_action('init', array(&$this, 'init'));

		//Register ajax hooks.
		AjaxActions::register_hooks();

		if (is_admin()) {

			//Admin.

			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('admin_init', array(&$this, 'admin_init'));

		} else {

			//Not admin.

		}

	}

	/**
	 * WP hook.
	 */
	function activate() {

		//Init options in db if they don't already exist.
		$options = $this->getOptions();

		if (!$options) {
			$this->addOptions(new OptionsModel());
		}

		add_action('admin_notices', array(&$this, 'showAdminNoticeAfterActivation'));

	}

	/**
	 * WP hook.
	 */
	function deactivate() {

		//

	}

	/**
	 * WP hook.
	 */
	function init() {

		//

	}

	/**
	 * WP hook.
	 */
	function admin_init() {

		//

	}

	/**
	 *
	 */
	function admin_menu() {

		$options_page = new OptionsPage();
		$options_page_hook_suffix = add_options_page($this->plugin_title, $this->plugin_title, 'manage_options', $this->plugin_slug, array(&$options_page, 'render'));

		//Add settings link to plugins page.
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );

		//Print scripts in the header for this plugin page.
		add_action('admin_print_styles-' . $options_page_hook_suffix, array(&$this, 'options_page_admin_styles'));
		add_action('admin_print_scripts-' . $options_page_hook_suffix, array(&$this, 'options_page_admin_scripts'));

	}

	/**
	 * Adds the Settings link to the plugin activate/deactivate page.
	 *
	 * @param $links
	 * @param $file
	 * @return array
	 */
	function filter_plugin_actions($links, $file) {
		$options_link = '<a href="admin.php?page=' . $this->plugin_slug . '">Settings</a>';
		array_unshift($links, $options_link); // before other links

		return $links;
	}

	/**
	 * WP hook.
	 */
	function options_page_admin_styles() {

		//Register plugin styles and scripts, enqueue these from the plugin options page.
		wp_enqueue_style('adjusted-bounce-rate', $this->plugin_base_url . '/css/adjusted-bounce-rate.css', array(), $this->version);

	}

	/**
	 * WP hook.
	 */
	function options_page_admin_scripts() {

		if ($this->minify_js) {

			//One big js file with libs and app code.
			wp_enqueue_script('adjusted-bounce-rate',
				$this->plugin_base_url . '/js/adjusted-bounce-rate.dist.js',
				array('jquery', 'backbone', 'rsvp'), $this->version, true);

		} else {

			//Lib files.
			wp_enqueue_script('rsvp', $this->plugin_base_url . '/bower_components/rsvp/rsvp.min.js',
				array(), $this->version, true);
			wp_enqueue_script('bootstrap', $this->plugin_base_url . '/bower_components/bootstrap/dist/js/bootstrap.min.js',
				array(), $this->version, true);

			//App files.
			wp_enqueue_script('adjusted-bounce-rate', $this->plugin_base_url . '/js/adjusted-bounce-rate.js',
				array('backbone', 'rsvp', 'bootstrap'), $this->version, true);
			wp_enqueue_script('adjusted-bounce-rate/views/PhoneBookTabView', $this->plugin_base_url . '/js/views/PhoneBookTabView.js',
				array('adjusted-bounce-rate'), $this->version, true);
			wp_enqueue_script('adjusted-bounce-rate/views/OptionsTabView', $this->plugin_base_url . '/js/views/OptionsTabView.js',
				array('adjusted-bounce-rate'), $this->version, true);

		}

		//Localized script.
		wp_localize_script('adjusted-bounce-rate', '_adjustedBounceRate', array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'currentUserId' => wp_get_current_user()->ID,
			'initialOptions' => $this->getOptions()
		));

	}








	/** ---------------------------------------------------------------------------------------
	 *  Options.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * Get an OptionsModel object with options from the db.
	 *
	 * @return  OptionsModel
	 */
	function getOptions($useDefaults = false) {

		//Check permissions.
		$this->checkCurrentUserPermissions();

		$options = null;
		$optionsJSON = $this->getOptionsJSON();

		if (!empty($optionsJSON)) {

			//Rehydrate.
			$options = OptionsModel::fromJSONString($optionsJSON);

		} else {

			//Get default options?
			if ($useDefaults) {
				$options = $this->getDefaultOptions();
			}

		}

		return $options;

	}

	/**
	 * Returns a JSON-encoded string saved as an option in the database.
	 *
	 * @return string
	 */
	function getOptionsJSON() {

		//Check permissions.
		$this->checkCurrentUserPermissions();

		return get_option($this->options_key);

	}

	/**
	 * Get an OptionsModel object with the default option values.  Used most often upon new installation/activation.
	 */
	function getDefaultOptions() {

		//Check permissions.
		$this->checkCurrentUserPermissions();

		return new OptionsModel();

	}

	/**
	 * Save the OptionsModel to the database.  It will be serialized as JSON and saved with the appropriate option key.
	 *
	 * @param   OptionsModel    $options
	 * @return  boolean         Returns true if the operation was successful, or false if there was an error.
	 */
	function saveOptions($options) {

		//Check permissions.
		$this->checkCurrentUserPermissions();

		if (empty($options)) {
			return false;
		}

		//Encode.
		$optionsJSON = json_encode($options, JSON_FORCE_OBJECT);

		return $this->saveOptionsJSON($optionsJSON);

	}

	/**
	 * Saves the JSON-encoded string as an option in the database.
	 *
	 * @param   string          $optionsJSON
	 * @return  boolean         Returns true if the operation was successful, or false if there was an error.
	 */
	function saveOptionsJSON($optionsJSON) {

		//Check permissions.
		$this->checkCurrentUserPermissions();

		//Check params.
		if (empty($optionsJSON) || !is_string($optionsJSON)) {
			throw new \Exception('$optionsJSON should be a string');
		}

		//Check if already exists.
		//Usually don't have to do this as update_option() already does this check, but makes it easier to catch when update_option() fails for another reason.
		$old_value = get_option($this->options_key);
		if ($optionsJSON === $old_value) {
			return true;
		}

		$success = update_option($this->options_key, $optionsJSON);

		return $success;

	}

	/**
	 * Save the OptionsModel to the database.  It will be serialized as JSON and saved with the appropriate option key.
	 *
	 * @param   OptionsModel    $options
	 * @return  boolean         Returns true if the operation was successful, or false if there was an error.
	 */
	function addOptions($options) {

		//Check permissions.
		$this->checkCurrentUserPermissions();

		if (empty($options)) {
			return false;
		}

		//Encode.
		$optionsJSON = $options->toJSON();

		return $this->addOptionsJSONToDB($optionsJSON);

	}

	/**
	 * Saves the JSON-encoded string as an option in the database.
	 *
	 * @param   string          $optionsJSON
	 * @return  boolean         Returns true if the operation was successful, or false if there was an error.
	 */
	function addOptionsJSONToDB($optionsJSON) {

		//Check permissions.
		$this->checkCurrentUserPermissions();

		if (empty($optionsJSON) || !is_string($optionsJSON)) {
			return false;
		}

		$success = add_option($this->options_key, $optionsJSON, '', 'no');

		return $success;

	}








	/** ---------------------------------------------------------------------------------------
	 *  Security.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * Do nothing if current user has permissions.
	 * Just "manage_options" role at this point, nothing fancy.
	 */
	function checkCurrentUserPermissions() {

		//Check WP permissions.
		if (!current_user_can('manage_options')) {
			die('Access denied.');
		}

	}








	/** ---------------------------------------------------------------------------------------
	 *  User meta.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * 
	 * 
	 * @param $user
	 */
	function add_user_profile_fields($user) {
		?>
		<h3><?php _e('Adjusted Bounce Rate User Options', $this->plugin_slug); ?></h3>

		<table class="form-table">
			<tr>
				<th>
					<label for="abr_phone_number"><?php _e('Mobile Phone Number', $this->text_domain); ?></label>
				</th>
				<td>
					<input type="text" name="abr_phone_number" id="abr_phone_number" value="<?php echo esc_attr( get_the_author_meta( 'abr_phone_number', $user->ID ) ); ?>" class="regular-text" />
					<br>
					<span class="description">
						Enter a valid mobile phone number to receive SMS text notifications when there is activity on your posts (e.g., new comments and replies).
						<br>
						For example, enter "<em>(555) 123-4567</em>", or "<em>+15551234567</em>".
					</span>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * 
	 * 
	 * @param $user_id
	 */
	function save_user_profile_fields($user_id) {

		//Check permissions.
		if (!current_user_can('edit_user', $user_id)) {
			return;
		}

		//Get input.
		$phone_number = sanitize_text_field($_POST['abr_phone_number']);

		//Update user meta.
		update_user_meta($user_id, 'abr_phone_number', $phone_number);

	}

	/**
	 *
	 *
	 * @return void
	 */
	function showAdminNoticeAfterActivation() {
		?>
		<div class="updated">
			<p><?php _e( 'Activated!', $this->text_domain ); ?></p>
		</div>
		<?php
	}

}