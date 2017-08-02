=== Advanced Drafts & Reviews Dashboard Widget ===
Contributors: WP-Henne
Tags: dashboard, widgets, dashboard widget, pending reviews, draft, drafts, posts, pages, reviews, author
Requires at least: 2.7.0
Tested up to: 3.7.1
Stable tag: 0.9.8.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Widget for the WordPress dashboard to display the current drafts and/or pending reviews of post and pages from all wp-users.

== Description ==

The Widget shows a list of drafts and/or of pending reviews by selection from posts and pages (you can deselect pages if you don't need). <br />
In the configuration part of the widget you can set, how many pending entrys you would like to display, display or hide the date or author information. Role of author is showing hover authors name.<br />
Also it is possible to change listing by date, id or author by selecting asc/desc.<br /><br />

Shows Status and Typ (Post, Page) and Excerpt for Post and Page, if no excerpt, use few words from content. All full settings changeable.
If you hover the date/time of Post creating, you will see last changing date/time.<br />
By entering ID of Post/Pages, this content will not be shown.<br /><br />

The Configuration is reachable by hover right top the widget title (beside the V for closing the widget)

== Installation ==

###Updgrading From A Previous Version###

To upgrade from a previous version of this plugin, delete the entire folder and files from the previous version of the plugin and then follow the installation instructions below.
Or just use the 'Automatic  upgrade' feature from WordPress.

###Installing The Plugin###

Extract all files from the ZIP file, making sure to keep the file structure intact, and then upload it to `/wp-content/plugins/` or using uplouad-function from your WP.
Then just visit your admin area and activate the plugin.

###Using The Plugin###

The new widget will show up automatically on your dashboard. Click Configure on the widget to set up the list entrys. 

== Screenshots ==

1. custom dashboard widget view (hover shows last modified)
2. configuration options

== Frequently Asked Questions ==

= Why should i use this instead of other Widgets? =

This widget dismiss any rule of userroles in WP. This means: drafts are normaly shown only for author of the draft. Pending revisions only shown for user with right "editor". With this widget you will show drafts as well to other users even show pages in Dashboard. And many mor, try and find out.

Need more information? Wee http://codex.wordpress.org/Roles_and_Capabilities or http://en.support.wordpress.com/user-roles/

= If you change code for me? =

Yes, i would do this, if i will have experience to do this ;-)
Please ask.

= Does this plugin support other languages? =

Yes, it does. See the [WordPress Codex](http://codex.wordpress.org/Translating_WordPress) for details on how to make a translation file. 
In furter versions some languages included (german, spanish).



== ChangeLog ==

* Version 0.9.8
	* Bugfix: if user has more than one role, name displayed mor times. Now: Hover displays more roles, if any
	* Bugfix: some letters in config screen

* Version 0.9.8
	* New: exclude Post/Pages by ID 
	* New: show role of user by hover the name

* Version 0.9.7
	* Bugfix: Page not shown by selected (shown by not selected)
	* Bugfix: some Code fixed
	* New: Show Status and Typ (Post, Page)
	* New: Show last changing date/time hover the displayed date/time of creating post/page
	* New: Excerpt for Post and Page, if no excerpt, use few words from content. All full settings changeable

* Version 0.9.3
	* Initial public release.

== Upgrade Notice ==

* You don't need to do anything, just put the files into the folder or use wp update. Some new settings in database are set - if you like, deselect default new informations.

== Other Notes ==

This Plugin is 	based on "Pendig Reviews Dashboard Widget" (c) 2008-2009 by Stefan Brandt http://wordpress.org/extend/plugins/pendig-reviews-dashboard-widget/