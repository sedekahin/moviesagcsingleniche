=== Web Ninja Auto Tagging System ===
Contributors: Josh Fowler
Donate link: http://josh-fowler.com/
Plugin Home: http://josh-fowler.com/?page_id=230
Tags: automatic, tag, tagging, tags, auto tag, auto tagging, tagthe.net, yahoo, retag 
Requires at least: 3.0.0
Tested up to: 3.3.2
Stable tag: 1.0.4

This will automatically make tags with the tagthe.net and yahoo yql services when you save or update a post. Has the option for retagging all post.

== Description ==

This plugin uses the new Yahoo Term Extraction YQL and tagthe.net API to find the most relevant keywords from the content of your post. It will then remove any keywords that you preset and add any global keywords that you have set in the settings screen. It will then add your keywords as tags when you save or update a post. With this you also have the ability to re-tag all posts or tag all post with no tags with the click of a button.

The auto tag system is also completely automatic. It will work anytime you save or update your post and it also works with any autoblogging software you may have that automatically inserts post from RSS feeds.

Read through the list of all the features below to get a feeling of what this plugin can do.

*Features*

* Ability to re-tag all posts with the click of a button.
* Ability to tag all post with no tags with the click of a button.
* Uses the Yahoo Term Extraction YQL instead of the Yahoo.com Tag API for relevant keywords. The Yahoo.com Tag API is to be discontinued at the end of December 2010.
* Also uses the tagthe.net API for relevant keywords.
* Has the options to enable or disable each API service.
* There are options for how many of the most relevant tags you want from each API service.
* Add global tags to each post that are added in the Add Tags field in the options.
* Removes any suggested tags that are added in the Remove Tags field in the options.
* Has an option for API Timeout for when one one of the services may be down or slow.
* Has the option to replace or apprend to existing tags.

Check out http://josh-fowler.com for more plugins.

== Installation ==

Installing is as simple as downloading the file from this site, placing it in your wp-content/plugins directory and activating the plugin. For the more detailed instructions read on.

1. Download the Web Ninja Auto Tagging System ZIP file
3. Extract the zipfile and place the PHP file in the wp-content/plugins directory of your WordPress installation
4. Go to the administration page of your WordPress installation (normally at http://www.yourblog.com/wp-admin)
5. Click on the Plugins tab and search for Web Ninja Auto Tagging System in the list
6. Activate the Web Ninja Auto Tag System plugin
7. You can now find an Web Ninja ATS page under Options to set the options of the plug-in
8. It works out of the box as soon as it is activated but you can change any settings you like

== Screenshots ==

1. Screenshot of the options screen.


== Frequently Asked Questions ==

= Where can I get support for this plugin? =

Support can be found here: http://josh-fowler.com/forum/

I try to watch this often and can answer any problems you have when I have time.

== Change Log ==

= 1.0.4 =

* Forgot to take out some old code.

= 1.0.3 =

* Fixed a warning that comes up if your php warning settings are set high while getting no responses from either api.

= 1.0.2 =

* Fixed an error that randomly caused a "Parse error: syntax error, unexpected ';'" when the Yahoo YQL returns no tags on an API call.

= 1.0.1 =

* First Public Release