# Adjusted Bounce Rate <br>WordPress Plugin

A well-designed plugin that improves the accuracy of your bounce rate, time on page, and session duration metrics in Google Analytics.

## Description

Google Analytics does not properly track some important engagement metrics like Avg Session Duration, Avg Session Duration, and Bounce Rate.  This plugin uses a commonly-accepted JavaScript method of improving the accuracy of these stats.

This plugin addresses the issues as identified by the Google Analytics team at [http://analytics.blogspot.com/2012/07/tracking-adjusted-bounce-rate-in-google.html](http://analytics.blogspot.com/2012/07/tracking-adjusted-bounce-rate-in-google.html).

Others have also blogged about their own solutions at:
* [http://padicode.com/blog/analytics/the-real-bounce-rate/](http://padicode.com/blog/analytics/the-real-bounce-rate/)
* [http://briancray.com/posts/time-on-site-bounce-rate-get-the-real-numbers-in-google-analytics](http://briancray.com/posts/time-on-site-bounce-rate-get-the-real-numbers-in-google-analytics)

See [http://grantnorwood.com/wordpress/plugins/adjusted-bounce-rate/](http://grantnorwood.com/wordpress/plugins/adjusted-bounce-rate/) for more information about the plugin and the author, or visit the GitHub repo at [https://github.com/grantnorwood/adjusted-bounce-rate](https://github.com/grantnorwood/adjusted-bounce-rate) to fork my code or submit an issue.

## Features

1. Set the engagement tracking event interval.  (Defaults to 10 secs.)
1. Set the max engagement time, which allows you to customize when the session should be
considered abandoned.  (Defaults to 20 mins.)
1. Set the minimum engagement time, which can be used to set an initial amount of time
required to count the user has having engaged.  (Defaults to 10 secs.)
1. Customize the event Category, Action and Label names to be displayed in Google Analytics.
1. Create goals in Google Analytics based on the time on page. (GA goals only support session duration by default!)
1. Uses either the old pageTracker code, the newer asynchronous code, or the newest Universal Analytics code.
1. Choose header or footer placement for the code.
1. Compatible with Ajaxify jQuery plugin.
/n/n


## Installation

1. Download the zip file from WordPress' plugin site: http://wordpress.org/extend/plugins/adjusted-bounce-rate/
1. Unzip the file.
1. Upload your plugin directory to your server's `/wp-content/plugins/` directory.
1. Activate the plugin using WordPress' admin interface:
	* Regular sites:  Plugins
	* Sites using multisite networks:  My Sites > Network Admin > Plugins
1. Configure the plugin options in Settings > Adjusted Bounce Rate.




## Removal

1. Click "Uninstall" on the plugins page for Adjusted Bounce Rate.  All plugin options will be deleted upon uninstall, but not when simply deactivating the plugin.
1. That's it!




## Frequently Asked Questions

### Is this plugin multi-site compatible?
Maybe, but I have not tested this plugin on multi-site.  If you run into any issues, please fork my GitHub repo (see below) and send a pull request with your fixes.  I'd be very appreciative!

### Where can I submit bugs or request new features?
Create an issue on the GitHub repo at [https://github.com/grantnorwood/adjusted-bounce-rate](https://github.com/grantnorwood/adjusted-bounce-rate).




## Changelog

### 1.2.1 (2015-04-03)
* Added option to enable "debug mode", which shows helpful messages in the browser console when engagement hit events are fired.

### 1.2.0 (2015-03-27)
* Fixed issue where Yoast Google Analytics global vars were renamed in v5.0.7, and the script broke.  Currently, there is no good way to detect whether Yoast's GA plugin is loaded and tracking is not disabled in v5.0.7 and newer.

### 1.1.1 (2014-05-15)
* Added leading zero to minutes so that they sort better in Google Analytics.
* Added total number of elapsed seconds as the GA event's value so that GA goals can be created to measure time on page.
* Minified javascript for performance.

### 1.1.0 (2014-05-15)
* Added compatibility with Ajaxify (https://github.com/browserstate/ajaxify).

### 1.0.1 (2014-05-13)
* Fixed issue when detecting if Yoast's Google Analytics for WordPress plugin is active.

### 1.0.0 (2014-05-13)
* Initial public release.

### 0.9.1 (2014-05-05)
* Total rewrite to better handle advanced features.
* Now handles minimum and maximum engagement time.
* Custom event category and action variables added.
* Debug mode and better console logging added.

### 0.9.0 (2014-04-29)
* Beta release.
