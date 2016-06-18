=== Switch Video Quality ===
Contributors: team-ok
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y9ZJCWAFT4QF2
Tags: change, select, bitrate, quality, video, HD, 4K, multiple resolution, playlist, self-hosted, html5 video, mediaelement, mejs, svq
Requires at least: 3.6
Tested up to: 4.5.2
Stable tag: 1.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds quality switch functionality to the wordpress video player (choose between different resolutions of a self-hosted html5-compatible video).

== Description ==
Switch Video Quality adds a quality switch button to the native wordpress video player (mediaelement.js), that allows to choose between different resolutions (e.g., SD and HD) and/or formats (mp4, webm, ogv) of a self-hosted html5-compatible video.

= Features: =
* serve your videos in **multiple qualities** to let your viewers choose (just like youtube or vimeo)
* upload your video files directly to the wordpress media library or use a separate web server to host your files (direct URLs have to be entered manually)
* create a **playlist** when you have more than one video on a page (only one player needed). The playlist is placed below the player as a grid layout with thumbnails for each video
* reorder playlist items with **drag and drop**
* **quality level labels** show the video's height value by default, but you can set custom ones easily
* HD and 4K quality flags are added to the labels automatically
* optionally show an **info overlay** in the player that contains title and duration information
* turn on/off on a per-post basis
* fully translatable (english and german language files already included)

= How to use: =
* In the post editor, use the normal video shortcode (width and height set to your needs) to place the video player in your content area, like this:
`[video width="1024" height="576"]`
* There are no other shortcode attributes necessary, but you may add a src and a poster attribute to ensure normal shortcode functionality when Switch Video Quality is not used or deactivated.
* Select the checkbox *Turn on/off* to activate the plugin for the current post.
* In the playlist item box enter some information about your video such as titles, names, locations or dates. This information will be displayed in the playlist and in the information overlay of the video player.
* Upload a poster image or select one from the media library
* Upload your videos with different resolutions and/or different formats (mp4, webm or ogv).
* You can select multiple video files in the media library and add them to the current playlist item all at once, but the first file you select will be the one that is loaded into the player by default (on page load or when a user clicks on a playlist item).
* The selection order of the other files (quality levels) doesn’t matter as they will be sorted by their height value when they're loaded into the player.
* All video details (URL, label, MIME-type, duration and height) are inserted automatically, but you can change the default label text to whatever you like (e.g., low, medium, high)
* If your video files are hosted on an external server, enter the URLs manually (the plugin will try to fill out the other fields automatically)
* To add another playlist item, click on the plus icon at the top of the current playlist item box
* To change the order of the playlist items, click on *Playlist Position* and drag and drop the box below or above another one
* Empty a playlist item box by clicking on the rubber icon, remove the whole box completely by clicking on the X icon (not possible when there is only one box left)


== Installation ==

* Upload the unzipped folder `switch-video-quality` to the `/wp-content/plugins/` directory.
* Activate the plugin through the *Plugins* menu in WordPress.
* Switch Video Quality has no global settings. It all happens in the post editing screen.

== Frequently Asked Questions ==

= Wordpress won't let me upload files bigger than 2 MB. How can I change that? =

Normal users: Ask your server administrator to increase the max filesize limit.
Admins: Edit the php.ini file and change the values of *upload_max_filesize*, *post_max_size* and *memory_limit*.

= To start a video I have to wait until it's fully loaded. Jumping to a part of the video that is not yet loaded isn't possible. Why? =
Maybe your webserver doesn't support pseudo streaming over http. Ask your server administrator to install the missing module. Also make sure your video files are encoded properly. MP4s must have the MOOV Atom placed at the beginning of the file.

= I have uploaded my video as mp4 and webm, but I can select only one of them in the player =

At page load the plugin checks the types of html5 video your browser can play and then only the first supported file format is used.

= Can I use more than one player on the same post/page?  =

No, sorry, the plugin is meant to be used with only one player instance per post/page. But you can have multiple posts (each with Switch Video Quality activated) displayed on a page, e.g., if you query by a certain category or tag.

= I don't want to use Switch Video Quality with a certain post type. How can I remove it from the admin screen of that post type? =

There's a filter hook for doing that. Copy the following code into your functions.php and change the content of the `$to_be_excluded array with the registered names of the post types you want to exclude:
```
add_filter('svq_exclude_post_types', 'custom_svq_exclude_post_types');
function custom_svq_exclude_post_types($excluded){
	$to_be_excluded = array('post_type_name', 'another_post_type_name');
	$excluded = array_merge($excluded, $to_be_excluded);
	return $excluded;
}
```
If you don't know the registered name of a custom post type: it's shown in your browser's adress bar when you look at the post type's managing screen (after `edit.php?post_type=`).

== Screenshots ==

1. The player with the switch video quality button, the optional info-overlay and the playlist (twenty-fifteen theme).
2. Use the normal video shortcode (only width and height attributes needed).
3. All settings are made within the post edit screen.


== Changelog ==

= 1.0 = 
* Initial Version
= 1.1 = 
* Added compatibility with older php versions (< 5.4)
