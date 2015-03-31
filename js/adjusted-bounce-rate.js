/*!
 * Adjusted Bounce Rate
 */

/**
 * gkn namespace
 */
if (typeof gkn === 'undefined' || !gkn) {
    var gkn = {};
}

(function($) {

    /**
     * AdjustedBounceRate class.
     */
    gkn.AdjustedBounceRate = function() {

        //Private properties.
        var version = '1.2.0',
            sandboxMode = false, //if true, do NOT actually fire the tracking event (disable in production!)
            options = {},
            startTime,
            elapsedTime,
            timer,
            hitCount = 0,
            elapsedSecs = 0,
            gaTracking;

        var _self = {

            /**
             * Initializes the singleton class.
             *
             * @param       _options
             * @return      void
             */
            init: function(_options) {

                if (
	                typeof window.pageTracker !== "undefined" //Old urchin tracking
	                || typeof window._gaq !== "undefined" //Less old ga.js tracking
	                || typeof window.ga !== "undefined" || typeof window.__gaTracker !== "undefined" //Newer Universal tracking
	                || options.debug_mode === true //Debug mode, skip detection
                ) {

                    //Init vars.
                    options = _options;

	                if (options.debug_mode !== true) {
		                debug.setLevel(0);
	                }

                    //Log.
	                if (options.debug_mode) {
		                debug.log('Adjusted_Bounce_Rate.init(): options=' + JSON.stringify(options));
	                }

                    //If ajaxify is being used, restart on state change complete event.
                    _self.initAjaxify();

                    //Wait to start?
                    if (options.min_engagement_seconds > 0) {
                        setTimeout(this.start, options.min_engagement_seconds * 1000);
                    } else {
                        this.start();
                    }

                } else {

                    debug.log('Adjusted Bounce Rate: GA is not loaded.');

                }

            },

            /**
             * If ajaxify is being used, restart on statechangecomplete event.
             */
            initAjaxify: function() {

                $(window).on('statechangecomplete', function() {
                    _self.restart();
                });

            },

            /**
             *
             */
            start: function() {

	            if (options.debug_mode) {
                    debug.log('Adjusted_Bounce_Rate.start()');
	            }

                //Init vars.
                startTime = new Date();
                elapsedTime = startTime;
                hitCount = 0;
                elapsedSecs = 0;

                //Initial tick, then tick on interval.
                _self.tick(true);
                timer = setInterval(_self.tick, 500);

            },

            /**
             *
             */
            restart: function() {

	            if (options.debug_mode) {
		            debug.log('Adjusted_Bounce_Rate.restart()');
	            }

                _self.stop();

                //Wait to start?
                if (options.min_engagement_seconds > 0) {
                    setTimeout(this.start, options.min_engagement_seconds * 1000);
                } else {
                    this.start();
                }

            },

            /**
             *
             */
            stop: function() {

                var elapsedTime = _self.formatElapsedTime(elapsedSecs);

	            if (options.debug_mode) {
		            debug.log('Adjusted_Bounce_Rate.stop(): stopped after "' + elapsedTime + '" (' + elapsedSecs + ' seconds).');
	            }

                clearInterval(timer);

            },

            /**
             *
             */
            tick: function(firstTick) {

                if (typeof firstTick === 'undefined') {
                    firstTick = false;
                }

                var now = new Date();
                var elapsedDiff = _self.dateDiff(elapsedTime, now, 'seconds');

                //Keep tickin'.
                if (elapsedDiff >= options.engagement_interval_seconds || firstTick === true) {

                    //Increment the engagement hit counter.
                    hitCount++;
                    elapsedSecs = hitCount * options.engagement_interval_seconds;

                    /*debug.log('Adjusted_Bounce_Rate.tick(): startTime=' + startTime.getTime()
                        + ', elapsedTime=' + elapsedTime.getTime()
                        + ', elapsedDiff=' + elapsedDiff);*/

                    //Track the event.
                    _self.trackEvent();

                    //Reset elapsed time each engagement interval.
                    elapsedTime = new Date();

                }

                //Stop the ticking?
                if (elapsedSecs >= options.max_engagement_seconds) {

                    //Stop.
                    _self.stop();
                }

            },

            /**
             * Call the GA event tracker.
             *
             */
            trackEvent: function() {

                var elapsedTime = _self.formatElapsedTime(elapsedSecs);

	            if (options.debug_mode) {
		            debug.log('Adjusted_Bounce_Rate.trackEvent(): ' + hitCount + ' hits' + ', elapsedSecs=' + elapsedSecs + ', elapsedTime=' + elapsedTime);
	            }

                if (typeof gaTracking === 'undefined' || gaTracking == '') {
                    //Detect GA version by script vars.
                    if (typeof window.pageTracker !== 'undefined') {
	                    gaTracking = 'pageTracker';
                    } else if (typeof window._gaq !== 'undefined') {
                        gaTracking = '_gaq';
                    } else if (typeof window.ga !== 'undefined') {
	                    gaTracking = 'ga';
                    } else if (typeof window.__gaTracker !== 'undefined') {
	                    gaTracking = '__gaTracker';
                    }
                }

                if (!sandboxMode) {
                    if (gaTracking == 'pageTracker') {

	                    //Old Urchin.js tracking.

                        window.pageTracker._trackEvent(
                            options.engagement_event_category,
                            options.engagement_event_action,
                            elapsedTime,
                            elapsedSecs || 0
                        );

                    } else if (gaTracking == '_gaq') {

	                    //Old ga.js tracking.

                        window._gaq.push([
                            '_trackEvent',
                            options.engagement_event_category,
                            options.engagement_event_action,
                            elapsedTime,
                            elapsedSecs || 0
                        ]);

                    } else if (gaTracking == 'ga') {

	                    //Newer Universal analytics.js tracking.

	                    ga('send', {
		                    'hitType': 'event',                                     // Required.
		                    'eventCategory': options.engagement_event_category,     // Required.
		                    'eventAction': options.engagement_event_action,         // Required.
		                    'eventLabel': elapsedTime,
		                    'eventValue': elapsedSecs || 0
	                    });

                    }else if (gaTracking == '__gaTracker') {

	                    //Newer Universal analytics.js tracking (Yoast's Google Analytics for WordPress uses a different global variable name).

	                    __gaTracker('send', {
		                    'hitType': 'event',                                     // Required.
		                    'eventCategory': options.engagement_event_category,     // Required.
		                    'eventAction': options.engagement_event_action,         // Required.
		                    'eventLabel': elapsedTime,
		                    'eventValue': elapsedSecs || 0
	                    });

                    } else {

                        //No supported analytics script loaded.
                        debug.warn('Adjusted_Bounce_Rate: [warning] No supported version of Google Analytics script seems to be loaded.');

                    }
                }

            },

            /**
             * Convert to "mins:secs" format.
             */
            formatElapsedTime: function(totalSecs) {

                var mins = Math.floor(totalSecs / 60);
                var secs = totalSecs % 60;

                //Add leading zeros.
                if (mins < 10) {
                    mins = '0' + mins;
                }

                if (secs < 10) {
                    secs = '0' + secs;
                }

                return mins + ":" + secs;

            },

            /*
             * DateFormat month/day/year hh:mm:ss
             * ex.
             * datediff('01/01/2011 12:00:00','01/01/2011 13:30:00','seconds');
             */
            dateDiff: function(fromDate, toDate, interval) {

                var second=1000, minute=second*60;

                if (!interval) {
                    interval = 'milliseconds'
                }

                if (typeof fromDate === 'string') {
                    fromDate = new Date(fromDate);
                }
                if (typeof toDate === 'string') {
                    toDate = new Date(toDate);
                }

                var timeDiff = toDate - fromDate;
                if (isNaN(timeDiff)) return NaN;
                switch (interval) {
                    case "years": return toDate.getFullYear() - fromDate.getFullYear();
                    case "months": return (
                        ( toDate.getFullYear() * 12 + toDate.getMonth() )
                            -
                            ( fromDate.getFullYear() * 12 + fromDate.getMonth() )
                        );
                    case "weeks"  : return Math.floor(timeDiff / week);
                    case "days"   : return Math.floor(timeDiff / day);
                    case "hours"  : return Math.floor(timeDiff / hour);
                    case "minutes": return Math.floor(timeDiff / minute);
                    case "seconds": return Math.floor(timeDiff / second);
                    case "milliseconds": return timeDiff;
                    default: return undefined;
                }

            }

        };

        return _self;

    }();

})(jQuery);
