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
	    
	    //SMS.
	    add_action('wp_ajax_abr_send_sms', array('\gkn_abr\AjaxActions', 'send_sms'));
	    add_action('wp_ajax_abr_check_twilio_credentials', array('\gkn_abr\AjaxActions', 'check_twilio_credentials'));
	    
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
		$options_json = json_encode($_POST['options'], JSON_FORCE_OBJECT);

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

	/**
	 *
	 */
	function send_sms() {

		//Check WP permissions.
		if (!current_user_can('manage_options')) {
			self::return_error('Access denied.');
		}

		global $adjustedBounceRate;

		//TODO: Can't use sanitize_text_field() because it strips newlines.  Is there a better sanitization method?
		$toPhoneNumber = stripslashes($_POST['toPhoneNumber']);
		$message = stripslashes($_POST['message']);

		//Validate input.
		if (empty($toPhoneNumber)) {

			self::return_json('Please enter the mobile phone to send the message to.');
			return;

		}

		if (empty($message)) {

			self::return_error('Please enter the message to send.');
			return;

		}

		//Handle a line-delimited string of numbers.
		$sms_numbers = null;
		if (strpos($toPhoneNumber, "\n") > 0) {
			$sms_numbers = explode("\n", $toPhoneNumber);
		} else {
			$sms_numbers[] = $toPhoneNumber;
		}

		//Send.
		$user_msgs = $adjustedBounceRate->sendSMS($sms_numbers, $message);

		self::return_json($user_msgs);

	}

	/**
	 *
	 */
	function check_twilio_credentials() {

		//Check WP permissions.
		if (!current_user_can('manage_options')) {
			self::return_error('Access denied.');
		}

		global $adjustedBounceRate;

		$account_sid = sanitize_text_field($_POST['account_sid']);
		$auth_token = sanitize_text_field($_POST['auth_token']);

		//Validate input.
		if (empty($account_sid) || empty($auth_token)) {

			self::return_error('You must enter your valid Twilio API credentials.');
			return;

		}

		try {

			$phone_numbers = $adjustedBounceRate->checkTwilioCredentials($account_sid, $auth_token);

			self::return_json($phone_numbers);

		} catch (\Services_Twilio_RestException $e) {

			if ($e->getStatus() == 401) {

				//Unauthorized.
				self::return_error('Invalid Twilio credentials.');
				return;

			} else if ($e->getStatus() == 403) {

				//Resource not accessible with Test Account Credentials.
				self::return_error('You may not use your Test Account Credentials to retrieve incoming phone numbers.');
				return;

			} else {

				self::return_error($e->getStatus() . ': ' . $e->getMessage());
				return;

			}

		}

	}

}