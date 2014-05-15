/*
 * JavaScript Debug - v0.4 - 6/22/2010
 * http://benalman.com/projects/javascript-debug-console-log/
 *
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 *
 * With lots of help from Paul Irish!
 * http://paulirish.com/
 */
if (typeof debug === 'undefined') {
    window.debug=(function(){var i=this,b=Array.prototype.slice,d=i.console,h={},f,g,m=9,c=["error","warn","info","debug","log"],l="assert clear count dir dirxml exception group groupCollapsed groupEnd profile profileEnd table time timeEnd trace".split(" "),j=l.length,a=[];while(--j>=0){(function(n){h[n]=function(){m!==0&&d&&d[n]&&d[n].apply(d,arguments)}})(l[j])}j=c.length;while(--j>=0){(function(n,o){h[o]=function(){var q=b.call(arguments),p=[o].concat(q);a.push(p);e(p);if(!d||!k(n)){return}d.firebug?d[o].apply(i,q):d[o]?d[o](q):d.log(q)}})(j,c[j])}function e(n){if(f&&(g||!d||!d.log)){f.apply(i,n)}}h.setLevel=function(n){m=typeof n==="number"?n:9};function k(n){return m>0?m>n:c.length+m<=n}h.setCallback=function(){var o=b.call(arguments),n=a.length,p=n;f=o.shift()||null;g=typeof o[0]==="boolean"?o.shift():false;p-=typeof o[0]==="number"?o.shift():n;while(p<n){e(a[p++])}};return h})();
}

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
        var debugMode = true,
            options = {},
            startTime,
            elapsedTime,
            timer,
            hitCount = 0,
            elapsedSecs = 0,
            gaTracking;

        if (debugMode === false) {
            debug.setLevel(0);
        }

        var _self = {

            /**
             * Initializes the singleton class.
             *
             * @param       _options
             * @return      void
             */
            init: function(_options) {

                if (typeof pageTracker !== "undefined" || typeof _gaq !== "undefined" || debugMode === true) {

                    //Init vars.
                    options = _options;

                    //Log.
                    debug.log('Adjusted_Bounce_Rate.init(): options=' + JSON.stringify(options));

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

                debug.log('Adjusted_Bounce_Rate.start()');

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

                debug.log('Adjusted_Bounce_Rate.restart()');

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
                debug.log('Adjusted_Bounce_Rate.stop(): stopped after "' + elapsedTime + '" (' + elapsedSecs + ' seconds).');

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

                debug.log('Adjusted_Bounce_Rate.trackEvent(): ' + hitCount + ' hits' + ', elapsedSecs=' + elapsedSecs + ', elapsedTime="' + elapsedTime + '"');

                if (typeof gaTracking === 'undefined' || gaTracking == '') {
                    //Detect GA version by script vars.
                    if (typeof window.pageTracker !== 'undefined') {
                        gaTracking = 'pageTracker';
                    }
                    if (typeof _gaq !== 'undefined') {
                        gaTracking = '_gaq';
                    }
                }


                if (gaTracking == 'pageTracker') {

                    pageTracker._trackEvent(
                        options.engagement_event_category,
                        options.engagement_event_action,
                        elapsedTime
                    );

                } else if (gaTracking == '_gaq') {

                    _gaq.push([
                        '_trackEvent',
                        options.engagement_event_category,
                        options.engagement_event_action,
                        elapsedTime
                    ]);

                } else {

                    //No supported analytics script loaded.
                    debug.warn('Adjusted_Bounce_Rate: [warning] No supported version of Google Analytics script seems to be loaded.');

                }

            },

            /**
             * Convert to "mins:secs" format.
             */
            formatElapsedTime: function(totalSecs) {

                var mins = Math.floor(totalSecs / 60);
                var secs = totalSecs % 60;

                //Add leading zero.
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
