(function($) {

	"use strict";

	//Namespaces.
	window.adjustedBounceRate = window.adjustedBounceRate || {};
	window.adjustedBounceRate.views = window.adjustedBounceRate.views || {};

	/**
	 * Model
	 */
	window.adjustedBounceRate.views.OptionsTabView = Backbone.View.extend({

		// ---------- Backbone template properties/methods. ----------

		el: '#OptionsTab',

		events: {
			'submit form': 'onFormSubmit'
		},

		initialize: function(options){

			//In Backbone.js v1.1.0, we must explicitly set the options property
			//from the the constructor's parameter. (@see http://backbonejs.org/#changelog)
			this.options = options || {};

			this.render();

		},

		render: function() {

			//Compile the template using underscore.
			var template = _.template($("#OptionsTabView").html(), {
				pluginOptions: adjustedBounceRate.pluginOptions
			});

			//Load the compiled HTML into the Backbone "el".
			this.$el.html(template);

			return this;

		},




		// ---------- Custom properties. ----------

		/**
		 * Initial phone numbers.
		 */
		//incomingPhoneNumbers: null,




		// ---------- Event handlers. ----------

		onFormSubmit: function(e) {

			//Prevent default.
			e.preventDefault();
			e.stopPropagation();

			var self = this;

			var options = {
				'engagementInterval' : parseInt(self.$('#engagementInterval').val(), 10),
				'minimumEngagement' : parseInt(self.$('#minimumEngagement').val(), 10),
				'maximumEngagement' : parseInt(self.$('#maximumEngagement').val(), 10),
				'eventCategory' : self.$('#eventCategory').val(),
				'eventAction' : self.$('#eventAction').val(),
				'codePlacement' : self.$('[name="codePlacement"]:selected').val(),
				'debugMode' : self.$('#debugMode').is(':checked')
			};

			//DEBUG
			alert(JSON.stringify(options)); return;

			adjustedBounceRate.forms.buttonOnSaveBegin('#saveBtn');

			//Save.
			var promise = adjustedBounceRate.saveOptions(options);

			promise.then(function(options) {

				self.optionsSavedSuccess(options);

			}).catch(function(errors) {

				self.optionsSavedFail(errors);

			});

		},




		// ---------- Custom methods. ----------

		optionsSavedSuccess: function(options) {

			adjustedBounceRate.forms.buttonOnSaveSuccess('#saveBtn');

			adjustedBounceRate.showAlert('Your options were saved successfully!', 'success');

		},

		optionsSavedFail: function(errors) {

			adjustedBounceRate.forms.buttonOnSaveFail('#saveBtn');

			adjustedBounceRate.showAlert(errors, 'error');

		}

	});

})(jQuery);
