<?php

namespace gkn_abr;


/**
 * A class containing the various plugin options which gets JSON-encoded and saved to the db.
 *
 * @package gkn_abr
 */
class OptionsModel {

	/** ---------------------------------------------------------------------------------------
	 *  Properties.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * The Twilio account SID.
	 *
	 * @var string
	 */
	public $twilioAccountSid = null;

	/**
	 * The Twilio auth token.
	 *
	 * @var string
	 */
	public $twilioAuthToken = null;

	/**
	 * The default Twilio phone number to send from.
	 *
	 * @var string
	 */
	public $defaultFromPhoneNumber = null;

	/**
	 * When true, authors are notified via SMS when new comments are posted.  The author must have an SMS number saved to their user profile.
	 *
	 * @var boolean
	 * @default true
	 */
	public $notifyAuthorNewComment = true;




	/** ---------------------------------------------------------------------------------------
	 *  Constructor.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * Constructor
	 */
	public function __construct() {

		//

	}




	/** ---------------------------------------------------------------------------------------
	 *  Static methods.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * Returns a new OptionsModel instance from the specified JSON string.
	 *
	 * @param       $jsonObject             Object      Valid, JSON-encoded string to be rehydrated.  If values are missing, they will inherit the default values.
	 * @return      \gkn_abr\OptionsModel
	 */
	public static function fromJSONObject($jsonObject) {

		if (empty($jsonObject) || !is_object($jsonObject)) {
			return null;
		}

		$optionsModel = new OptionsModel();

		$optionsModel->twilioAccountSid = $jsonObject->twilioAccountSid ? $jsonObject->twilioAccountSid : $optionsModel->twilioAccountSid;
		$optionsModel->twilioAuthToken = $jsonObject->twilioAuthToken ? $jsonObject->twilioAuthToken : $optionsModel->twilioAuthToken;
		$optionsModel->defaultFromPhoneNumber = $jsonObject->defaultFromPhoneNumber ? $jsonObject->defaultFromPhoneNumber : $optionsModel->defaultFromPhoneNumber;
		$optionsModel->notifyAuthorNewComment = $jsonObject->notifyAuthorNewComment ? $jsonObject->notifyAuthorNewComment : $optionsModel->notifyAuthorNewComment;

		return $optionsModel;

	}

	/**
	 * Returns a new OptionsModel instance from the specified JSON string.
	 *
	 * @param       $json                   string      Valid, JSON-encoded string to be rehydrated.
	 * @return      \gkn_abr\OptionsModel
	 */
	public static function fromJSONString($json) {

		if (empty($json) || !is_string($json)) {
			return null;
		}

		//Fix escaped quotes and then decode.
		$json = stripslashes($json);
		$jsonObject = json_decode($json);

		$optionsModel = self::fromJSONObject($jsonObject);

		return $optionsModel;

	}




	/** ---------------------------------------------------------------------------------------
	 *  Instance methods.
	 *  -------------------------------------------------------------------------------------*/

	/**
	 * Returns a JSON-encoded string of the instance.
	 *
	 * @return      string
	 */
	public function toJSON() {

		return json_encode($this);

	}

}