<?php

namespace gkn_abr;

/**
 * Class AdjustedBounceRate
 *
 * @package gkn_abr
 */
class AdjustedBounceRate {

	public $version = '2.0.0-develop';
	public $db_version = '2';
	public $plugin_base_path = ''; //set in __construct()
	public $plugin_base_url = ''; //set in __construct()
	public $plugin_title = 'Adjusted Bounce Rate';
	public $plugin_slug = 'adjusted-bounce-rate';
	public $options_key = 'adjusted-bounce-rate-options';
	public $db_version_options_key = 'adjusted-bounce-rate-db-version';
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

			//Not admin.  Init front-end.
			add_action('init', array(&$this, 'init_frontend'));

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

		//Check if db updates need to be run.
		$this->checkCurrentDBVersion();

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
	 * @param   string $optionsJSON
	 * @return  bool Returns true if the operation was successful, or false if there was an error.
	 * @throws  \Exception
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
	 *  DB versioning.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * Do updates if db version is out of date.
	 *
	 * @return void
	 */
	protected function checkCurrentDBVersion() {

		$db_version = $this->getDBVersion();

		if (version_compare($this->db_version, $db_version, '>')) {
			$this->updateDBOptionsToLatestVersion();
		}

	}

	/**
	 * Returns 0 if no db version found.
	 *
	 * @return int
	 */
	protected function getDBVersion() {

		if (is_multisite()) {
			switch_to_blog(1);
			$db_version = get_option($this->db_version_options_key);
			restore_current_blog();
		} else {
			$db_version = get_option($this->db_version_options_key);
		}

		return (int) $db_version;

	}

	/**
	 *
	 *
	 * @param   $db_version     int         The db version.
	 * @return  bool
	 */
	protected function setDBVersion($db_version) {

		if (!isset($db_version) || !is_int($db_version)) {
			return false;
		}

		if (is_multisite()) {
			switch_to_blog(1);
			$updated = update_option($this->db_version_options_key, $db_version);
			restore_current_blog();
		} else {
			$updated = update_option($this->db_version_options_key, $db_version);
		}

		return $updated;

	}

	/**
	 * Update options to latest version.
	 *
	 * @return void
	 */
	public function updateDBOptionsToLatestVersion() {

		switch ($this->db_version) {
			case 2:
				$this->updateDBOptionsToV2();
				break;

			default:
				//

				break;
		}

	}

	/**
	 * Update options to use JSON-encoded string (>2.0.0) instead of serialized PHP arrays (<=1.2.1).
	 *
	 * @return void
	 */
	public function updateDBOptionsToV2() {

		//Get current options string from db.
		$options = get_option($this->options_key);

		//Check if a serialized array.
		if (is_array($options) && count($options) > 0) {

			//Update code_placement data type from db v1.
			if ($options['code_placement'] == '0') {
				$options['code_placement'] = 'header';
			} else if ($options['code_placement'] == '1') {
				$options['code_placement'] = 'footer';
			}

			//Create a new OptionsModel instance.
			$options_v2 = new OptionsModel();

			//Copy the option values from the old serialized array.
			$options_v2->engagementInterval = $options['engagementInterval'] ? (int) $options['engagementInterval'] : $options_v2->engagementInterval;
			$options_v2->minimumEngagement = $options['minimumEngagement'] ? (int) $options['minimumEngagement'] : $options_v2->minimumEngagement;
			$options_v2->maximumEngagement = $options['maximumEngagement'] ? (int) $options['maximumEngagement'] : $options_v2->maximumEngagement;
			$options_v2->eventCategory = $options['eventCategory'] ? (string) $options['eventCategory'] : $options_v2->eventCategory;
			$options_v2->eventAction = $options['eventAction'] ? (string) $options['eventAction'] : $options_v2->eventAction;
			$options_v2->codePlacement = $options['codePlacement'] ? (string) $options['codePlacement'] : $options_v2->codePlacement;
			$options_v2->debugMode = $options['debugMode'] ? (bool) $options['debugMode'] : $options_v2->debugMode;

			//Save it back to the db as JSON-encoded string.
			$this->saveOptions($options_v2);

		}

		//Update db version in wp_options.
		$this->setDBVersion(2);

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
	 *  Front-end.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * Output the embed code.
	 */
	public function init_frontend() {

		//Only load if Google Analytics for WordPress is loaded.
		global $yoast_ga;
		$do_tracking = true;

		//Check for older versions of Yoast (<= 5.0.6).
		if (isset($yoast_ga) && $yoast_ga->do_tracking() === false) {
			$do_tracking = false;
		}

		//TODO: Find a way to check if tracking should be loaded or not for Yoast GA plugin >= 5.0.7.

		if (!$do_tracking) {
			return;
		}

		//Header or footer?
		$options = $this->getOptions();
		
		if ($options->codePlacement == 'header') {
			//header
			add_action('wp_head', array($this, 'render_code'));
		} else {
			//footer
			add_action('wp_footer', array($this, 'render_code'));
		}

	}

	/**
	 * Output the embed code.
	 */
	public function render_code() {

		$options = $this->getOptions();

		$js_script_srcs = array();

		if ($this->minify_js) {

			array_push($js_script_srcs, $this->plugin_base_url . "/js/adjusted-bounce-rate-frontend.min.js?v=" . $this->version);

		} else {

			array_push($js_script_srcs, $this->plugin_base_url . "/lib/ba-debug.min.js?v=" . $this->version);
			array_push($js_script_srcs, $this->plugin_base_url . "/js/adjusted-bounce-rate-frontend.js?v=" . $this->version);

		}

		?>

		<!-- adjusted bounce rate -->
		<?php
		foreach ($js_script_srcs as $src) {
			?>
			<script type="text/javascript" src="<?php echo $src; ?>"></script>
		<?php
		}
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				gkn.AdjustedBounceRate.init(<?php echo $options->toJSON() ?>);
			});
		</script>
		<!-- end adjusted bounce rate -->

	<?php

	}







	/** ---------------------------------------------------------------------------------------
	 *  Other.
	 *  -------------------------------------------------------------------------------------*/

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