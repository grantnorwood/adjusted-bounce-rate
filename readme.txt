=== Adjusted Bounce Rate ===
Contributors: grantnorwood
Donate link: http://grantnorwood.com/
Tags: google analytics, analytics, bounce rate, avg time on page, avg time on site
Requires at least: 3.5
Tested up to: 3.9
Stable tag: 0.9

A well-designed plugin that helps track the Adjusted Bounce Rate in Google Analytics.


== Description ==

Google Analytics does not properly track some important engagement metrics like
Avg Time on Site, Avg Session Duration, and Bounce Rate.  This plugin uses a
commonly-accepted JavaScript method of improving accuracy of these stats.

Addresses the issues as identified by the Google Analytics team at:
* http://analytics.blogspot.com/2012/07/tracking-adjusted-bounce-rate-in-google.html

Others have also blogged about their solutions at:
* http://padicode.com/blog/analytics/the-real-bounce-rate/
* http://briancray.com/posts/time-on-site-bounce-rate-get-the-real-numbers-in-google-analytics

See http://grantnorwood.com/wordpress/plugins/adjusted-bounce-rate/ for more information about
the plugin and the author.

= Features =

1. Set the engagement tracking event interval. (Defaults to 10 secs.)
1. Set the max engagement time, which allows you to customize when the session should be
considered abandoned.
1. Set the minimum engagement time, which can be used to set an initial amount of time
required to count the user has having engaged.  This allows you to set a minimum amount of time
on a single page before the user is counted as being engaged, and can be different than
the normal tracking event interval.
1. Customize the event Category, Action and Label names to be displayed in Google Analytics.
1. Uses either the Asynchronous code, or the new Universal Analytics code.
1. Choose header or footer placement for the code.
1. Compatible with Yoast's Google Analytics for WordPress. For example, detects if analytics
were loaded, or if they are disabled because of the currently logged in user's role.


== Installation ==

1. Download the zip file from WordPress' plugin
    site: `http://wordpress.org/extend/plugins/adjusted-bounce-rate/`

1. Unzip the file.

1. Upload your plugin directory to your server's `/wp-content/plugins/`
    directory

1. Activate the plugin using WordPress' admin interface:
    * Regular sites:  Plugins
    * Sites using multisite networks:  My Sites | Network Admin | Plugins

= Removal =

1. Click "Uninstall" on the plugins page for Adjusted Bounce Rate.

1. That's it!


== Frequently Asked Questions ==

= Is this plugin multi-site compatible? =

Yes, for multi-site networks you must activate this plugin via the Network Admin
panel.


== Changelog ==

= 0.9 (2014-04-29) =
* Beta release.
