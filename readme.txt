=== New Users Monitor ===
Contributors: WPGear
Donate link: http://wpgear.xyz/new-users-monitor
Tags: security,login,user,confirm,registration,dashboard,members,users,accept
Requires at least: 4.1
Tested up to: 6.5.3
Requires PHP: 5.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 3.15

Ext Security. Automatic scanning of the Users list, and detect unauthorized addition to the DB. Informs immediately Admin by email.

== Description ==

Ext Security.
You may not know that your site has already been hacked.
There are many ways to add a new user to the system, without your knowledge.
This plugin will promptly notify you that a new user has registered on your site.
Now the console has a widget that displays all new users.

All new users will be highlighted in red until Admin confirm each of them in User-Profile.
With the active Option: "Deny Login if User is not confirmed", you will sleep much more peacefully.

* This plugin has already helped out many times when some of our sites were hacked. But we quickly found out about it. And we were able to fast stop the problem.

= Features =
	
* Automatic scanning of the Users list on a schedule, and detect unauthorized addition to the DB.
* Notification to Administrator by e-mail.
* The "Users" Table has a sortable Column "Confirm" ON/OFF. Users who are not Verified are highlighted in red.
* Option: "Allow to change Settings - only for Admin". Default = ON
* Option: "Deny Login if User is not confirmed". Default = ON
* <a href="https://wordpress.org/plugins/adaptive-login-action">Integration with "Adaptive Login Action"</a>
	
== Installation ==

1. Upload 'new-users-monitor' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Make sure your system has iconv set up right, or iconv is not installed at all. If you have any problems - please ask for support.

== Screenshots ==
 
1. screenshot-1.png This is the example 'New Users Monitor' widget on Console.
2. screenshot-2.png New Users Unconfirmed highlighted in red.
3. screenshot-3.png Setup options 'New Users Monitor' and List of Unconfirmed New Users.
4. screenshot-4.png User-Profile page. Option: 'Confirmation (new User)'.
5. screenshot-5.png The Page "Users" whith active plugin 'New Users Monitor'.

== Frequently Asked Questions ==

NA

== Changelog ==
= 3.15 =
	2024.05.09
	* Added the possibility of internationalization.
	* Tested to WP: 6.5.3
	
= 2.14 =
	2022.06.09
	* Fix Minor Error-Notice.
	* Tested to WP 5.9.3
	
= 2.13 =
	2021.10.15
	* Fix Widget horizontal scrollbar.
	* Tested to WP 5.8.1
	
= 2.12 =
	2021.10.01
	* Fix "Deny Login if User is not confirmed".
	
= 2.11 =
	2021.09.29
	* Fix size Fields-Input on Options Page.
	
= 2.10 =
	2021.09.28
	* Added Option: "Allow to change Settings - only for Admin".
	* Added Option: "Deny Login if User is not confirmed".
	* Integration with "Adaptive Login Action".
		
= 1.9 =
	2021.09.02
	* A new sortable Column "Confirm" ON/OFF has been added to the "Users" table.
	* Tested with WP5.8
	
= 1.8 =
	2021.05.14
	* Update Widget. Added horizontal scrollbar.
	
= 1.7 =
	2021.05.13
	* Added Setup-Page link to Widget Header.
	
= 1.6 =
	2021.03.05
	* Tested to WP 5.7-RC2-50482
	* Fix style for Options page & Fix small issue.
	
= 1.5 =
	2021.02.24
	* Fix SQL Query.
	
= 1.4 =
	2021.02.22
	* Fix Search in Options page for Cyrillic.
	
= 1.3 =
	2021.02.18
	* Corrected for the requirements of the current WP versions.
	
= 1.2 =
	2018.09.06
	* Added Site-Title into notification email Subject.
	
= 1.1 =
	2018.08.14
	* Clearing debug-pre-relize code.

= 1.0 =
	2018.08.07
	* Initial release	
	
== Upgrade Notice ==
= 1.6 =
	* Upgrade please.
	
= 1.5 =
	* Upgrade please.

= 1.4 =
	Fix Search in Options page for Cyrillic.
	
= 1.3 =
	* 2021.02.18 ReOpen this Plugin. Enjoy.