<?php

namespace gkn_abr;

/**
 * 
 */
class AjaxActions {
    
    /**
     * Register the hooks for ajax actions.
     */
    public static function register_hooks() {

    	//Options.
	    add_action('wp_ajax_abr_save_options', array('\gkn_abr\AjaxActions', 'save_options'));
	    add_action('wp_ajax_abr_get_options', array('\gkn_abr\AjaxActions', 'get_options'));

	}




	/**
	 * ------------------------------------------------------------------------------
	 * Helper functions.
	 * ------------------------------------------------------------------------------
	 */

	/**
	 * Finish up the ajax call, echo the returned json object, and kill the response.
	 *
	 * @param Object $data
	 * @param UserMessage[]|UserMessage|String $userMessages
	 * @return void
	 */
	public static function return_json($data = null, $userMessages = null) {

		header('Content-type: application/json');

		if (isset($userMessages) && is_string($userMessages)) {
			$userMessages = new UserMessage($userMessages);
		}

		if (isset($userMessages) && !is_array($userMessages)) {
			//Messages should always be an array, or null.
			$userMessages = array($userMessages);
		}

		$response = new AjaxResponse($data, $userMessages);

		echo json_encode($response);

		//Make sure to die!
		die();

	}

	/**
	 * Finish up the ajax call, echo the returned html, and kill the response.
	 *
	 * @param null $html
	 * @return void
	 */
	public static function return_html($html = null) {

		header('Content-type: text/html');

		echo $html;

		//Make sure to die!
		die();

	}

	/**
	 * Returns an object with an error message & type, and null data.
	 *
	 * @param   string      $message
	 * @param   string      $messageType
	 * @return  void
	 */
	public static function return_error($message, $messageType = UserMessageTypes::ERROR) {

		self::return_json(null, new UserMessage($message, $messageType));

	}




    /**
     * ------------------------------------------------------------------------------
     * Options.
     * ------------------------------------------------------------------------------
     */

	/**
	 *
	 */
	function save_options() {

		//Check WP permissions.
		if (!current_user_can('manage_options')) {
			self::return_error('Access denied.');
		}

		global $adjustedBounceRate;
		$success = false;
		$options_json = $_POST['options'];

		//Check params.
		if (!empty($options_json)) {

			$success = $adjustedBounceRate->saveOptionsJSON($options_json);

		} else {

			self::return_error('There was a problem saving the options to the database.');

        }

		if ($success) {

			self::return_json($success);

		} else {

			self::return_error('There was a problem saving the options to the database.');

		}

	}

	/**
	 *
	 */
	function wp_ajax_getOptions() {

		//Check WP permissions.
		if (!current_user_can('manage_options')) {
			self::return_error('Access denied.');
		}

		global $adjustedBounceRate;

		$options_json = $adjustedBounceRate->getOptionsJSON();

		self::return_json($options_json);

	}

}