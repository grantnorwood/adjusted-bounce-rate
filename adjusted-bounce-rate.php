<?php

/**
 * Plugin Name: Adjusted Bounce Rate
 *
 * Description: A well-designed plugin that helps track the Adjusted Bounce Rate in Google Analytics.
 *
 * Plugin URI: http://wordpress.org/extend/plugins/adjusted-bounce-rate/
 * Version: 1.1.0
 * Author: Grant K Norwood
 * Author URI: http://grantnorwood.com/
 * License: GPLv2
 * @package adjusted-bounce-rate
 */

/**
 * The instantiated version of this plugin's class.
 */
$GLOBALS['adjusted_bounce_rate'] = new Adjusted_Bounce_Rate;

/**
 * Adjusted Bounce Rate main class.
 *
 * @package adjusted-bounce-rate
 * @link http://wordpress.org/extend/plugins/adjusted-bounce-rate/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @author Grant K Norwood
 * @copyright Grant K Norwood, 2014
 */
class Adjusted_Bounce_Rate {

	/**
	 * This plugin's identifier
	 */
	const ID = 'adjusted-bounce-rate';

	/**
	 * This plugin's name
	 */
	const NAME = 'Adjusted Bounce Rate';

	/**
	 * This plugin's version
	 */
	const VERSION = '1.1.0';




	/**
	 * This plugin's table name prefix
	 * @var string
	 */
	protected $prefix = 'adjusted_bounce_rate_';


	/**
	 * Has the internationalization text domain been loaded?
	 * @var bool
	 */
	protected $loaded_textdomain = false;

	/**
	 * This plugin's options
	 *
	 * Options from the database are merged on top of the default options.
	 *
	 * @see adjusted_bounce_rate::get_options()  to obtain the saved
	 *      settings
	 * @var array
	 */
	protected $options = array();

	/**
	 * This plugin's default options
	 * @var array
	 */
	protected $options_default = array(
		'engagement_interval_seconds' => 10, //10 secs
        'min_engagement_seconds' => 10, //10 secs
        'max_engagement_seconds' => 1200, //20 mins
        'engagement_event_category' => 'engagement-hit',
        'engagement_event_action' => 'time-on-page',
        'code_placement' => 'footer', //"header" or "footer"
	);

	/**
	 * Our option name for storing the plugin's settings
	 * @var string
	 */
	protected $option_name;





	/**
	 * Declares the WordPress action and filter callbacks
	 *
	 * @return void
	 * @uses adjusted_bounce_rate::initialize()  to set the object's
	 *       properties
	 */
	public function __construct() {

		$this->initialize();

		if (is_admin()) {
			$this->load_plugin_textdomain();

			require_once dirname(__FILE__) . '/pages/settings.php';
			$settings_page = new Adjusted_Bounce_Rate_Settings_Page();

			if (is_multisite()) {
				$admin_menu = 'network_admin_menu';
//				$admin_notices = 'network_admin_notices';
				$plugin_action_links = 'network_admin_plugin_action_links_adjusted-bounce-rate/adjusted-bounce-rate.php';
			} else {
				$admin_menu = 'admin_menu';
//				$admin_notices = 'admin_notices';
				$plugin_action_links = 'plugin_action_links_adjusted-bounce-rate/adjusted-bounce-rate.php';
			}

			add_action($admin_menu, array(&$settings_page, 'admin_menu'));
			add_action('admin_init', array(&$settings_page, 'admin_init'));
			add_filter($plugin_action_links, array(&$settings_page, 'plugin_action_links'));

			register_activation_hook(__FILE__, array(&$this, 'activate'));
		} else {

            add_action('init', array($this, 'init_frontend'));

        }
	}

	/**
	 * Sets the object's properties and options
	 *
	 * This is separated out from the constructor to avoid undesirable
	 * recursion.  The constructor sometimes instantiates the admin class,
	 * which is a child of this class.  So this method permits both the
	 * parent and child classes access to the settings and properties.
	 *
	 * @return void
	 *
	 * @uses adjusted_bounce_rate::get_options()  to replace the default
	 *       options with those stored in the database
	 */
	protected function initialize() {

		$this->option_name = self::ID . '-options';

		$this->get_options();

	}




	/*
	 * ===== ACTION & FILTER CALLBACK METHODS =====
	 */

    /**
     * Establishes the tables and settings when the plugin is activated
     * @return void
     */
    public function activate() {

        if (is_multisite() && !is_network_admin()) {
            die($this->hsc_utf8(sprintf(__("%s must be activated via the Network Admin interface when WordPress is in multistie network mode.", self::ID), self::NAME)));
        }

        //Save this plugin's options to the database.
        if (is_multisite()) {
            switch_to_blog(1);
        }
        update_option($this->option_name, $this->options);
        if (is_multisite()) {
            restore_current_blog();
        }

    }

    
    
    
    
	/*
	 * ===== INTERNAL METHODS ====
	 */

	/**
	 * Sanitizes output via htmlspecialchars() using UTF-8 encoding
	 *
	 * Makes this program's native text and translated/localized strings
	 * safe for displaying in browsers.
	 *
	 * @param string $in   the string to sanitize
	 * @return string  the sanitized string
	 */
	protected function hsc_utf8($in) {
		return htmlspecialchars($in, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * A centralized way to load the plugin's textdomain for
	 * internationalization
	 * @return void
	 */
	protected function load_plugin_textdomain() {
		if (!$this->loaded_textdomain) {
			load_plugin_textdomain(self::ID, false, self::ID . '/languages');
			$this->loaded_textdomain = true;
		}
	}

	/**
	 * Replaces all whitespace characters with one space
	 * @param string $in  the string to clean
	 * @return string  the cleaned string
	 */
	protected function sanitize_whitespace($in) {
		return preg_replace('/\s+/u', ' ', $in);
	}

	/**
	 * Replaces the default option values with those stored in the database
	 * @uses login_security_solution::$options  to hold the data
	 */
	protected function get_options() {
		if (is_multisite()) {
			switch_to_blog(1);
			$options = get_option($this->option_name);
			restore_current_blog();
		} else {
			$options = get_option($this->option_name);
		}
		if (!is_array($options)) {
			$options = array();
		}
		$this->options = array_merge($this->options_default, $options);
	}

    /**
     * Output the embed code.
     */
    public function init_frontend() {

	    //Only load if Google Analytics for WordPress is loaded.
	    global $yoast_ga;
	    if (!isset($yoast_ga) || $yoast_ga->do_tracking() === false) {
	    	return;
	    }

        //Header or footer?
        if ($this->options['code_placement'] == '0') {
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

        ?>

        <!-- adjusted bounce rate -->
        <script type="text/javascript" src="<?php echo plugins_url(Adjusted_Bounce_Rate::ID) ?>/js/<?php echo Adjusted_Bounce_Rate::ID; ?>.js?v=<?php echo self::VERSION ?>"></script>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                gkn.AdjustedBounceRate.init({
                    engagement_interval_seconds: <?php echo $this->options['engagement_interval_seconds']; ?>,
                    min_engagement_seconds: <?php echo $this->options['min_engagement_seconds']; ?>,
                    max_engagement_seconds: <?php echo $this->options['max_engagement_seconds']; ?>,
                    engagement_event_category: '<?php echo $this->options['engagement_event_category']; ?>',
                    engagement_event_action: '<?php echo $this->options['engagement_event_action']; ?>'
                });
            });
        </script>
        <!-- end adjusted bounce rate -->

        <?php

    }

}
