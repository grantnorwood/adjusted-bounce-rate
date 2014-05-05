<?php

/**
 * The user interface and activation/deactivation methods for administering
 * the Adjusted Bounce Rate plugin
 *
 * This plugin abstracts WordPress' Settings API to simplify the creation of
 * a settings admin interface.  Read the docblocks for the set_sections() and
 * set_fields() methods to learn how to create your own settings.
 *
 * A table is created in the activate() method and is dropped in the
 * deactivate() method.  If your plugin needs tables, adjust the table
 * definitions and removals as needed.  If you don't need a table, remove
 * those portions of the activate() and deactivate() methods.
 *
 * This plugin is coded to be installed in either a regular, single WordPress
 * installation or as a network plugin for multisite installations.  So, by
 * default, multisite networks can only activate this plugin via the
 * Network Admin panel.  If you want your plugin to be configurable for each
 * site in a multisite network, you must do the following:
 *
 * + Search admin.php and adjusted-bounce-rate.php
 *   for is_multisite() if statements.  Remove the true parts and leave
 *   the false parts.
 * + In adjusted-bounce-rate.php, go to the initialize() method
 *   and remove the $wpdb->get_blog_prefix(0) portion of the
 *   $this->table_login assignment.
 *
 * Beyond that, you're advised to leave the rest of this file alone.
 *
 * @package adjusted-bounce-rate
 * @link http://wordpress.org/extend/plugins/adjusted-bounce-rate/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @author Grant K Norwood
 * @copyright Grant K Norwood, 2014
 */

/**
 * The user interface and activation/deactivation methods for administering
 * the Adjusted Bounce Rate plugin
 *
 * @package adjusted-bounce-rate
 * @link http://wordpress.org/extend/plugins/adjusted-bounce-rate/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @author Grant K Norwood
 * @copyright Grant K Norwood, 2014
 */
class Adjusted_Bounce_Rate_Settings_Page extends Adjusted_Bounce_Rate {
	/**
	 * The WP privilege level required to use the admin interface
	 * @var string
	 */
	protected $capability_required;

	/**
	 * Metadata and labels for each element of the plugin's options
	 * @var array
	 */
	protected $fields;

	/**
	 * URI for the forms' action attributes
	 * @var string
	 */
	protected $form_action;

	/**
	 * Name of the page holding the options
	 * @var string
	 */
	protected $page_options;

	/**
	 * Metadata and labels for each settings page section
	 * @var array
	 */
	protected $settings;

	/**
	 * Title for the plugin's settings page
	 * @var string
	 */
	protected $text_settings;


	/**
	 * Sets the object's properties and options
	 *
	 * @return void
	 *
	 * @uses adjusted_bounce_rate::initialize()  to set the object's
	 *	     properties
	 * @uses adjusted_bounce_rate_admin::set_sections()  to populate the
	 *       $sections property
	 * @uses adjusted_bounce_rate_admin::set_fields()  to populate the
	 *       $fields property
	 */
	public function __construct() {
		$this->initialize();

        $this->set_sections();
        $this->set_fields();

        // Translation already in WP combined with plugin's name.
        $this->text_settings = self::NAME . ' ' . __('Settings');

        if (is_multisite()) {
            $this->capability_required = 'manage_network_options';
            $this->form_action = '../options.php';
            $this->page_options = 'settings.php';
        } else {
            $this->capability_required = 'manage_options';
            $this->form_action = 'options.php';
            $this->page_options = 'options-general.php';
        }

	}

	/*
	 * ===== ADMIN USER INTERFACE =====
	 */

	/**
	 * Sets the metadata and labels for each settings page section
	 *
	 * Settings pages have sections for grouping related fields.  This plugin
	 * uses the $sections property, below, to define those sections.
	 *
	 * The $sections property is a two-dimensional, associative array.  The top
	 * level array is keyed by the section identifier (<sid>) and contains an
	 * array with the following key value pairs:
	 *
	 * + title:  a short phrase for the section's header
	 * + callback:  the method for rendering the section's description.  If a
	 *   description is not needed, set this to "section_blank".  If a
	 *   description is helpful, use "section_<sid>" and create a corresponding
	 *   method named "section_<sid>()".
	 *
	 * @return void
	 * @uses adjusted_bounce_rate_admin::$sections  to hold the data
	 */
	protected function set_sections() {
		$this->sections = array(
			'tracking_intervals' => array(
				'title' => __("Tracking Intervals", self::ID),
				'callback' => 'section_tracking_intervals',
			),
			'event_options' => array(
				'title' => __("Event Options", self::ID),
				'callback' => 'section_event_options',
			),
            'code_options' => array(
                'title' => __("Code Options", self::ID),
                'callback' => 'section_code_options',
            ),
		);
	}

	/**
	 * Sets the metadata and labels for each element of the plugin's
	 * options
	 *
	 * The $fields property is a two-dimensional, associative array.  The top
	 * level array is keyed by the field's identifier and contains an array
	 * with the following key value pairs:
	 *
	 * + section:  the section identifier (<sid>) for the section this
	 *   setting should be displayed in
	 * + label:  a very short title for the setting
	 * + text:  the long description about what the setting does.  Note:
	 *   a description of the default value is automatically appended.
	 * + type:  the data type ("int", "string", or "bool").  If type is "bool,"
	 *   the following two elements are also required:
	 * + bool0:  description for the button indicating the option is off
	 * + bool1:  description for the button indicating the option is on
	 *
	 * WARNING:  Make sure to keep this propety and the
	 * adjusted_bounce_rate_admin::$options_default
	 * property in sync.
	 *
	 * @return void
	 * @uses adjusted_bounce_rate_admin::$fields  to hold the data
	 */
	protected function set_fields() {
		$this->fields = array(
            'engagement_interval_seconds' => array(
                'section' => 'tracking_intervals',
                'label' => __("Engagement Interval", self::ID),
                'text' => __("The number of seconds in between engagement tracking events.", self::ID),
                'type' => 'int',
            ),
            'min_engagement_seconds' => array(
                'section' => 'tracking_intervals',
                'label' => __("Minimum Engagement", self::ID),
                'text' => __("The number of seconds to wait before firing the first engagement tracking event.", self::ID),
                'type' => 'int',
            ),
            'max_engagement_seconds' => array(
                'section' => 'tracking_intervals',
                'label' => __("Maximum Engagement", self::ID),
                'text' => __("The max number of seconds to track sessions using engagement tracking events.", self::ID),
                'type' => 'int',
            ),
			'engagement_event_category' => array(
				'section' => 'event_options',
				'label' => __("Event Category", self::ID),
//				'text' => __("See how to set a string value.", self::ID),
				'type' => 'string',
			),
            'engagement_event_action' => array(
                'section' => 'event_options',
                'label' => __("Event Action", self::ID),
//                'text' => __("See how to set a string value.", self::ID),
                'type' => 'string',
            ),
			'code_placement' => array(
				'section' => 'code_options',
				'label' => __("Code Placement", self::ID),
				'text' => __("Where should the tracking code be rendered in the HTML?", self::ID),
				'type' => 'bool',
				'bool0' => __("Header", self::ID),
				'bool1' => __("Footer (recommended)", self::ID),
			),
//			'deactivate_deletes_data' => array(
//				'section' => 'misc',
//				'label' => __("Deactivation", self::ID),
//				'text' => __("Should deactivating the plugin remove all of the plugin's data and settings?", self::ID),
//				'type' => 'bool',
//				'bool0' => __("No, preserve the data for future use.", self::ID),
//				'bool1' => __("Yes, delete the damn data.", self::ID),
//			),
//			'example_int' => array(
//				'section' => 'misc',
//				'label' => __("Integer", self::ID),
//				'text' => __("An example for storing an integer value.", self::ID),
//				'type' => 'int',
//			),
//			'example_string' => array(
//				'section' => 'misc',
//				'label' => __("String", self::ID),
//				'text' => __("See how to set a string value.", self::ID),
//				'type' => 'string',
//			),
		);
	}

	/**
	 * Declares a menu item and callback for this plugin's settings page
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_menu() {
		add_submenu_page(
			$this->page_options,
			$this->text_settings,
			self::NAME,
			$this->capability_required,
			self::ID,
			array(&$this, 'page_settings')
		);
	}

	/**
	 * Declares the callbacks for rendering and validating this plugin's
	 * settings sections and fields
	 *
	 * NOTE: This method is automatically called by WordPress when
	 * any admin page is rendered
	 */
	public function admin_init() {
		register_setting(
			$this->option_name,
			$this->option_name,
			array(&$this, 'validate')
		);

		// Dynamically declares each section using the info in $sections.
		foreach ($this->sections as $id => $section) {
			add_settings_section(
				self::ID . '-' . $id,
				$this->hsc_utf8($section['title']),
				array(&$this, $section['callback']),
				self::ID
			);
		}

		// Dynamically declares each field using the info in $fields.
		foreach ($this->fields as $id => $field) {
			add_settings_field(
				$id,
				$this->hsc_utf8($field['label']),
				array(&$this, $id),
				self::ID,
				self::ID . '-' . $field['section']
			);
		}
	}

	/**
	 * The callback for rendering the settings page
	 * @return void
	 */
	public function page_settings() {
		if (is_multisite()) {
			// WordPress doesn't show the successs/error messages on
			// the Network Admin screen, at least in version 3.3.1,
			// so force it to happen for now.
			include_once ABSPATH . 'wp-admin/options-head.php';
		}

		echo '<h2>' . $this->hsc_utf8($this->text_settings) . '</h2>';
		echo '<form action="' . $this->hsc_utf8($this->form_action) . '" method="post">' . "\n";
		settings_fields($this->option_name);
		do_settings_sections(self::ID);
		submit_button();
		echo '</form>';
	}

	/**
	 * The callback for "rendering" the sections that don't have descriptions
	 * @return void
	 */
	public function section_blank() {
	}

	/**
	 * The callback for rendering the "Tracking Intervals" section description
	 * @return void
	 */
	public function section_tracking_intervals() {
		echo '<p>';
		echo $this->hsc_utf8(__("Set the intervals below for engagement tracking events.", self::ID));
		echo '</p>';
	}

    /**
     * The callback for rendering the "Event Options" section description
     * @return void
     */
    public function section_event_options() {
        echo '<p>';
        echo $this->hsc_utf8(__("Set the options for tracking events.", self::ID));
        echo '</p>';
    }

    /**
     * The callback for rendering the "Code Options" section description
     * @return void
     */
    public function section_code_options() {
        echo '<p>';
        echo $this->hsc_utf8(__("Set the options for how the tracking code is rendered to the page.", self::ID));
        echo '</p>';
    }

	/**
	 * The callback for rendering the fields
	 * @return void
	 *
	 * @uses adjusted_bounce_rate_admin::input_int()  for rendering
	 *       text input boxes for numbers
	 * @uses adjusted_bounce_rate_admin::input_radio()  for rendering
	 *       radio buttons
	 * @uses adjusted_bounce_rate_admin::input_string()  for rendering
	 *       text input boxes for strings
	 */
	public function __call($name, $params) {
		if (empty($this->fields[$name]['type'])) {
			return;
		}
		switch ($this->fields[$name]['type']) {
			case 'bool':
				$this->input_radio($name);
				break;
			case 'int':
				$this->input_int($name);
				break;
			case 'string':
				$this->input_string($name);
				break;
		}
	}

	/**
	 * Renders the radio button inputs
	 * @return void
	 */
	protected function input_radio($name) {
		echo $this->hsc_utf8($this->fields[$name]['text']) . '<br/>';
		echo '<input type="radio" value="0" name="'
			. $this->hsc_utf8($this->option_name)
			. '[' . $this->hsc_utf8($name) . ']"'
			. ($this->options[$name] ? '' : ' checked="checked"') . ' /> ';
		echo $this->hsc_utf8($this->fields[$name]['bool0']);
		echo '<br/>';
		echo '<input type="radio" value="1" name="'
			. $this->hsc_utf8($this->option_name)
			. '[' . $this->hsc_utf8($name) . ']"'
			. ($this->options[$name] ? ' checked="checked"' : '') . ' /> ';
		echo $this->hsc_utf8($this->fields[$name]['bool1']);
	}

	/**
	 * Renders the text input boxes for editing integers
	 * @return void
	 */
	protected function input_int($name) {
		echo '<input type="text" size="3" name="'
			. $this->hsc_utf8($this->option_name)
			. '[' . $this->hsc_utf8($name) . ']"'
			. ' value="' . $this->hsc_utf8($this->options[$name]) . '" /> ';
		echo $this->hsc_utf8($this->fields[$name]['text']
				. ' ' . __('Default:', self::ID) . ' '
				. $this->options_default[$name] . '.');
	}

	/**
	 * Renders the text input boxes for editing strings
	 * @return void
	 */
	protected function input_string($name) {
		echo '<input type="text" size="75" name="'
			. $this->hsc_utf8($this->option_name)
			. '[' . $this->hsc_utf8($name) . ']"'
			. ' value="' . $this->hsc_utf8($this->options[$name]) . '" /> ';
		echo '<br />';
		echo $this->hsc_utf8($this->fields[$name]['text']
				. ' ' . __('Default:', self::ID) . ' '
				. $this->options_default[$name] . '.');
	}

	/**
	 * Validates the user input
	 *
	 * NOTE: WordPress saves the data even if this method says there are
	 * errors.  So this method sets any inappropriate data to the default
	 * values.
	 *
	 * @param array $in  the input submitted by the form
	 * @return array  the sanitized data to be saved
	 */
	public function validate($in) {
		$out = $this->options_default;
		if (!is_array($in)) {
			// Not translating this since only hackers will see it.
			add_settings_error($this->option_name,
					$this->hsc_utf8($this->option_name),
					'Input must be an array.');
			return $out;
		}

		$gt_format = __("must be >= '%s',", self::ID);
		$default = __("so we used the default value instead.", self::ID);

		// Dynamically validate each field using the info in $fields.
		foreach ($this->fields as $name => $field) {
			if (!array_key_exists($name, $in)) {
				continue;
			}

			if (!is_scalar($in[$name])) {
				// Not translating this since only hackers will see it.
				add_settings_error($this->option_name,
						$this->hsc_utf8($name),
						$this->hsc_utf8("'" . $field['label'])
								. "' was not a scalar, $default");
				continue;
			}

			switch ($field['type']) {
				case 'bool':
					if ($in[$name] != 0 && $in[$name] != 1) {
						// Not translating this since only hackers will see it.
						add_settings_error($this->option_name,
								$this->hsc_utf8($name),
								$this->hsc_utf8("'" . $field['label']
										. "' must be '0' or '1', $default"));
						continue 2;
					}
					break;
				case 'int':
					if (!ctype_digit($in[$name])) {
						add_settings_error($this->option_name,
								$this->hsc_utf8($name),
								$this->hsc_utf8("'" . $field['label'] . "' "
										. __("must be an integer,", self::ID)
										. ' ' . $default));
						continue 2;
					}
					if (array_key_exists('greater_than', $field)
						&& $in[$name] < $field['greater_than'])
					{
						add_settings_error($this->option_name,
								$this->hsc_utf8($name),
								$this->hsc_utf8("'" . $field['label'] . "' "
										. sprintf($gt_format, $field['greater_than'])
										. ' ' . $default));
						continue 2;
					}
					break;
			}
			$out[$name] = $in[$name];
		}

		return $out;
	}

    /**
     * A filter to add a "Settings" link in this plugin's description
     *
     * NOTE: This method is automatically called by WordPress for each
     * plugin being displayed on WordPress' Plugins admin page.
     *
     * @param array $links  the links generated thus far
     * @return array
     */
    public function plugin_action_links($links) {

        //TODO: Fix $this->page_options so that it detects if currently in multi-site.
        if (empty($this->page_options)) {
            $this->page_options = 'options-general.php';
        }

        $links[] = '<a href="' . $this->hsc_utf8($this->page_options)
            . '?page=' . self::ID . '">'
            . $this->hsc_utf8(__('Settings')) . '</a>';
        return $links;
    }

}
