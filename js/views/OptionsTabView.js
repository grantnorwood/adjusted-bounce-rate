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
		incomingPhoneNumbers: null,




		// ---------- Event handlers. ----------

		onFormSubmit: function(e) {

			//Prevent default.
			e.preventDefault();
			e.stopPropagation();

			var self = this;

			var options = {
				'twilioAccountSid' : self.$('#twilioAccountSid').val(),
				'twilioAuthToken' : self.$('#twilioAuthToken').val(),
				'defaultFromPhoneNumber' : self.$('#defaultFromPhoneNumber > option:selected').val(),
				'notifyAuthorNewComment' : self.$('#notifyAuthorNewComment').is(':checked')
			};

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

		},

		checkTwilioCredentialsSuccess: function(phoneNumbers) {


			adjustedBounceRate.forms.textboxSuccess('#twilioAccountSid');
			adjustedBounceRate.forms.textboxSuccess('#twilioAuthToken');

			//Show phone numbers.
			this.showPhoneNumbers(phoneNumbers, (adjustedBounceRate.pluginOptions ? adjustedBounceRate.pluginOptions.defaultFromPhoneNumber : null));

		},

		checkTwilioCredentialsFail: function(errors) {

			adjustedBounceRate.forms.textboxError('#twilioAccountSid');
			adjustedBounceRate.forms.textboxError('#twilioAuthToken');

			adjustedBounceRate.showAlert(errors, 'error');

		},

		showPhoneNumbers: function(phoneNumbers, defaultFromPhoneNumber) {

			var select = this.$('#defaultFromPhoneNumber');

			if (_.isArray(phoneNumbers)) {

				//Populate options.
				var optionsHtml = ['<option value=""></option>'];

				_.each(phoneNumbers, function(phoneNumber) {
					optionsHtml.push('<option value="' + phoneNumber + '"');
					if (phoneNumber && phoneNumber === defaultFromPhoneNumber) {
						optionsHtml.push(' selected');
					}
					optionsHtml.push('>' + phoneNumber + '</option>');
				});

				select.empty().html(optionsHtml.join(''));

			}

		}

	});

})(jQuery);
