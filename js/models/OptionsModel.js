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

			'engagementInterval' : 10,
			'minimumEngagement' : 10,
			'maximumEngagement' : 1200,
			'eventCategory' : 'engagement-hit',
			'eventAction' : 'time-on-page',
			'codePlacement' : 'footer',
			'debugMode' : false

		},

		initialize : function(){

			//

		}

	});

})(jQuery);
