<?php

namespace gkn_abr;

class OptionsPage {

    public function __construct() {

        //

    }

	/**
	 *
	 *
	 * @return void
	 */
	public function render() {

		global $adjustedBounceRate;

		?>

		<?php /* Page container */ ?>

		<div class="bootstrap-wrapper">
			<div class="container-fluid">

				<h2><?php echo $adjustedBounceRate->plugin_title; ?></h2>
				<h5>v<?php echo $adjustedBounceRate->version; ?></h5>

				<br>

				<div id="alert_container" class="row"></div>

				<div role="tabpanel">

					<ul id="main_nav" class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#OptionsTab" aria-controls="OptionsTab" role="tab" data-toggle="tab">Options</a></li>
						<?php /*<li role="presentation"><a href="#PhoneBookTab" aria-controls="PhoneBookTab" role="tab" data-toggle="tab">Phone Book</a></li>*/ ?>
					</ul>
					<div class="tab-content">
						<div id="OptionsTab" role="tabpanel" class="tab-pane active"></div>
						<?php /*<div id="PhoneBookTab" role="tabpanel" class="tab-pane"></div>*/ ?>
					</div>

				</div>

			</div>
		</div>




		<?php
		/**
		 * Underscore templates
		 */
		?>

		<?php /* PhoneBookTabView */ ?>
		<script type="text/template" id="PhoneBookTabView">

			<div class="col-md-6">

				<p>
					Users with mobile phone numbers saved to their profiles are listed below.
				</p>


			</div>

		</script>

		<?php /* OptionsTabView */ ?>
		<script type="text/template" id="OptionsTabView">

			<div class="col-md-6">

				<form>
					<div class="form-group">

						<h3>Tracking Intervals</h3>
						<em>Set the intervals below for engagement tracking events.</em>

					</div>
					<div class="row form-group">
						<label for="engagementInterval" class="col-sm-3 control-label">Engagement Interval</label>
						<div class="col-sm-9">
							<input id="engagementInterval" type="text" class="form-control" placeholder="" autocomplete="off" maxlength="100" aria-describedby="" value="<%= pluginOptions ? pluginOptions.engagementInterval : '' %>">
							<span class="help-block">The number of seconds in between engagement tracking events.&nbsp;&nbsp;<em>Default: 10</em></span>
						</div>
					</div>
					<div class="row form-group">
						<label for="minimumEngagement" class="col-sm-3 control-label">Minimum Engagement</label>
						<div class="col-sm-9">
							<input id="minimumEngagement" type="text" class="form-control" placeholder="" autocomplete="off" maxlength="100" aria-describedby="" value="<%= pluginOptions ? pluginOptions.minimumEngagement : '' %>">
							<span class="help-block">The number of seconds to wait before firing the first engagement tracking event.&nbsp;&nbsp;<em>Default: 10</em></span>
						</div>
					</div>
					<div class="row form-group">
						<label for="maximumEngagement" class="col-sm-3 control-label">Maximum Engagement</label>
						<div class="col-sm-9">
							<input id="maximumEngagement" type="text" class="form-control" placeholder="" autocomplete="off" maxlength="100" aria-describedby="" value="<%= pluginOptions ? pluginOptions.maximumEngagement : '' %>">
							<span class="help-block">The max number of seconds to track sessions using engagement tracking events.&nbsp;&nbsp;<em>Default: 1200</em></span>
						</div>
					</div>
					<div class="form-group">

						<h3>Event Options</h3>
						<em>Set the options for tracking events.</em>

					</div>
					<div class="row form-group">
						<label for="eventCategory" class="col-sm-3 control-label">Event Category</label>
						<div class="col-sm-9">
							<input id="eventCategory" type="text" class="form-control" placeholder="" autocomplete="off" maxlength="100" aria-describedby="" value="<%= pluginOptions ? pluginOptions.eventCategory : '' %>">
							<span class="help-block">Read more about <a href="https://support.google.com/analytics/answer/1033068?hl=en" target="_blank">event tracking in GA</a>.&nbsp;&nbsp;<em>Default: engagement-hit</em></span>
						</div>
					</div>
					<div class="row form-group">
						<label for="eventAction" class="col-sm-3 control-label">Event Action</label>
						<div class="col-sm-9">
							<input id="eventAction" type="text" class="form-control" placeholder="" autocomplete="off" maxlength="100" aria-describedby="" value="<%= pluginOptions ? pluginOptions.eventAction : '' %>">
							<span class="help-block">Read more about <a href="https://support.google.com/analytics/answer/1033068?hl=en" target="_blank">event tracking in GA</a>.&nbsp;&nbsp;<em>Default: engagement-hit</em></span>
						</div>
					</div>
					<div class="form-group">

						<h3>Code Options</h3>
						<em>Set the options for how the tracking code is rendered to the page.</em>

					</div>
					<div class="row form-group">
						<label for="codePlacement" class="col-sm-3 control-label">Code Placement</label>
						<div class="col-sm-9">
							<input id="codePlacement" type="text" class="form-control" placeholder="" autocomplete="off" maxlength="100" aria-describedby="" value="<%= pluginOptions ? pluginOptions.codePlacement : '' %>">
							<span class="help-block">Where should the tracking code be rendered in the HTML?</span>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-offset-3 col-sm-9">
							<div class="checkbox">
								<label>
									<input id="debugMode" type="checkbox"<%= pluginOptions ? (pluginOptions.debugMode === true || pluginOptions.debugMode === 'true' ? ' checked' : '') : '' %>> Debug Mode
								</label>
							</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-offset-3 col-sm-9">
							<button id="saveBtn" type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>Save Options</button>
						</div>
					</div>
				</form>

			</div>

		</script>

		<?php
	}

    /**
     *
     *
     * @return void
     */
    public function save() {

    }

}