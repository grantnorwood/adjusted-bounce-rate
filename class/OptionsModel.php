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
	 *
	 *
	 * @var int
	 */
	public $engagementInterval = 10;

	/**
	 *
	 *
	 * @var int
	 */
	public $minimumEngagement = 10;

	/**
	 *
	 *
	 * @var int
	 */
	public $maximumEngagement = 1200;

	/**
	 *
	 *
	 * @var string
	 */
	public $eventCategory = 'engagement-hit';

	/**
	 *
	 *
	 * @var string
	 */
	public $eventAction = 'time-on-page';

	/**
	 *
	 *
	 * @var string
	 */
	public $codePlacement = 'footer';

	/**
	 *
	 *
	 * @var boolean
	 * @default false
	 */
	public $debugMode = false;




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

		$optionsModel->engagementInterval = $jsonObject->engagementInterval ? (int) $jsonObject->engagementInterval : $optionsModel->engagementInterval;
		$optionsModel->minimumEngagement = $jsonObject->minimumEngagement ? (int) $jsonObject->minimumEngagement : $optionsModel->minimumEngagement;
		$optionsModel->maximumEngagement = $jsonObject->maximumEngagement ? (int) $jsonObject->maximumEngagement : $optionsModel->maximumEngagement;
		$optionsModel->eventCategory = $jsonObject->eventCategory ? (string) $jsonObject->eventCategory : $optionsModel->eventCategory;
		$optionsModel->eventAction = $jsonObject->eventAction ? (string) $jsonObject->eventAction : $optionsModel->eventAction;
		$optionsModel->codePlacement = $jsonObject->codePlacement ? (string) $jsonObject->codePlacement : $optionsModel->codePlacement;
		$optionsModel->debugMode = $jsonObject->debugMode ? (bool) $jsonObject->debugMode : $optionsModel->debugMode;

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