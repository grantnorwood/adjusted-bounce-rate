(function($) {

	"use strict";

	//Namespaces.
	window.adjustedBounceRate = window.adjustedBounceRate || {};
	window.adjustedBounceRate.models = window.adjustedBounceRate.models || {};

	/**
	 * Model
	 */
	window.adjustedBounceRate.models.OptionsModel = Backbone.Model.extend({

		/** ---------------------------------------------------------------------
		 * Custom properties.
		 --------------------------------------------------------------------- */




		/** ---------------------------------------------------------------------
		 * Backbone methods and properties.
		 --------------------------------------------------------------------- */

		defaults : {

			'twilioAccountSid' : null,
			'twilioAuthToken' : null,
			'defaultFromPhoneNumber' : null,
			'notifyAuthorNewComment' : true

		},

		initialize : function(){

			//

		}

	});

})(jQuery);
