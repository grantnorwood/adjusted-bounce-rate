(function($) {

	"use strict";

	/**
	 * App class.
	 */
	window.adjustedBounceRate = function() {

		//
		// Private vars.
		//
		var fadeDuration = 'fast';


		var _self = {

			//
			// Public properties.
			//

			errorMsg: 'Uh oh, an error occurred.',
			ajaxUrl: null,
			pluginOptions: null,

			//Views.
			optionsTabView: null,




			/**
			 * Init the plugin UI when the page is loaded.
			 */
			init: function() {

				//Init some properties.
				this.ajaxUrl = _adjustedBounceRate.ajaxUrl;
				this.pluginOptions = _adjustedBounceRate.initialOptions;

				//Init views.
				this.optionsTabView = new window.adjustedBounceRate.views.OptionsTabView();

			},




			/* ----------------------------------------------------------------------------------------------------------
			 * Event handlers.
			 * ---------------------------------------------------------------------------------------------------------- */




			/* ----------------------------------------------------------------------------------------------------------
			 * Public Methods.
			 * ---------------------------------------------------------------------------------------------------------- */

			/**
			 * Attempt to save options to the server.
			 *
			 * @param       options             {window.adjustedBounceRate.models.OptionsModel}
			 * @returns     {RSVP.Promise}
			 */
			saveOptions: function(options) {

				return new RSVP.Promise(function(resolve, reject) {

					if (typeof options === 'object') {

						var request = $.ajax({
							url: adjustedBounceRate.ajaxUrl,
							type: "POST",
							data: { action: 'abr_save_options', options: JSON.stringify(options) },
							dataType: "json"
						});

						request.done(function(response) {

							//Check for errors.
							if ($.isArray(response.errors) && response.errors.length > 0) {
								reject(response.errors);
								return;
							}

							var success = response.data;

							resolve(success);

						});

						request.fail(function(jqXHR, textStatus, errorMsg) {

							reject([errorMsg]);

						});

					} else {

						reject(Error('Options was not a valid object.'));

					}

				});

			},




			/* ----------------------------------------------------------------------------------------------------------
			 * Alerts.
			 * ---------------------------------------------------------------------------------------------------------- */

			/**
			 * Show an alert, or array of alerts.  All alerts must be of a single type (alertType).
			 *
			 * @param       messages        string[]
			 * @param       alertType       string
			 * @returns     void
			 */
			showAlert: function(messages, alertType) {

				if (!alertType || alertType === 'error') {
					alertType = 'danger';
				}

				var html = [],
					message;

				if (_.isArray(messages)) {

					//Reduce all messages to a single block of html.
					message = _.reduce(messages, function(memo, value, index) {

						if (index !== 0) {
							memo += '<br>';
						}

						if (_.isObject(value)) {
							memo += value.message;
						} else {
							//Treat it like a string.
							memo += value;
						}

						return memo;

					}, '');

				} else {

					//Treat "messages" as a string.
					message = messages;

				}

				//Build the alert html.
				html.push('<div class="col-md-6">');
				html.push('<div class="alert alert-' + alertType + ' alert-dismissible fade in" role="alert">');
				html.push('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>');
				html.push(message);
				html.push('</div>');
				html.push('</div>');

				$('#alert_container').empty().show().html(html.join(''));

			},

			/**
			 * Hide alert.
			 *
			 * @returns     void
			 */
			hideAlert: function() {

				$('#alert_container').alert('close');

			},




			/* ----------------------------------------------------------------------------------------------------------
			 * Forms.
			 * ---------------------------------------------------------------------------------------------------------- */

			/**
			 * Forms methods.
			 *
			 * @returns     void
			 */
			forms: {

				/**
				 *
				 *
				 * @param       btn         string|jQueryElement
				 * @returns     void
				 */
				buttonOnSaveBegin: function(btn) {

					if (!btn) {
						return;
					} else if (_.isString(btn)) {
						btn = $(btn);
					}

					btn.prop('disabled', true);
					btn.addClass('abr-saving');

				},

				/**
				 *
				 *
				 * @param       btn         string|jQueryElement
				 * @returns     void
				 */
				buttonOnSaveSuccess: function(btn) {

					if (!btn) {
						return;
					} else if (_.isString(btn)) {
						btn = $(btn);
					}

					btn.prop('disabled', false);
					btn.removeClass('abr-saving');

				},

				/**
				 *
				 *
				 * @param       btn         string|jQueryElement
				 * @returns     void
				 */
				buttonOnSaveFail: function(btn) {

					if (!btn) {
						return;
					} else if (_.isString(btn)) {
						btn = $(btn);
					}

					btn.prop('disabled', false);
					btn.removeClass('abr-saving');

				},

				/**
				 *
				 *
				 * @param       textbox         string|jQueryElement
				 * @returns     void
				 */
				textboxSuccess: function(textbox) {

					if (!textbox) {
						return;
					} else if (_.isString(textbox)) {
						textbox = $(textbox);
					}

					//Form group class.
					textbox.closest('.form-group').removeClass('has-error');
					textbox.closest('.form-group').addClass('has-success has-feedback');

					//Glyphicon.
					var glyph = textbox.next('span.glyphicon');

					if (glyph.length == 0) {
						glyph = $('<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
						glyph.insertAfter(textbox);
					} else {
						glyph.removeClass('glyphicon-remove');
						glyph.addClass('glyphicon-ok');
					}

				},

				/**
				 *
				 *
				 * @param       textbox         string|jQueryElement
				 * @returns     void
				 */
				textboxError: function(textbox) {

					if (!textbox) {
						return;
					} else if (_.isString(textbox)) {
						textbox = $(textbox);
					}

					//Form group class.
					textbox.closest('.form-group').removeClass('has-success');
					textbox.closest('.form-group').addClass('has-error has-feedback');

					//Glyphicon.
					var glyph = textbox.next('span.glyphicon');

					if (glyph.length == 0) {
						glyph = $('<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>');
						glyph.insertAfter(textbox);
					} else {
						glyph.removeClass('glyphicon-ok');
						glyph.addClass('glyphicon-remove');
					}

				},

				/**
				 *
				 *
				 * @param       textbox         string|jQueryElement
				 * @returns     void
				 */
				textboxDefault: function(textbox) {

					if (!textbox) {
						return;
					} else if (_.isString(textbox)) {
						textbox = $(textbox);
					}

					//Form group class.
					textbox.closest('.form-group').removeClass('has-error has-success has-feedback');

					//Glyphicon.
					var glyph = textbox.sibling('span.glyphicon');
					if (glyph) {
						glyph.remove();
					}

				}

			}

		};

		return _self;

	}();

	//Init the dashboard.
	$(document).ready(function() {
		adjustedBounceRate.init();
	});

})(jQuery);

//Alias it.
var adjustedBounceRate = window.adjustedBounceRate;
