=== Plugin Name ===
Contributors: elmotheelk
Donate link: http://myph3.sf.net/
Tags: images, gallery, preview
Requires at least: 2.0.0
Tested up to: 2.3.3
Stable tag: 2.1

A WordPress plugin to include a myPh3 album image into your posts or pages

== Description ==

A WordPress plugin to include a myPh3 album or single image into your posts or pages. It makes it very easy to insert photo thumbnails just like myPh3 works.

== Installation ==

To install, place the files included in the .zip into your WordPress plugin folder (that is /wp-content/plugins/). Now, go to your WordPress blog admin dashboard and enable the plugin. Finally, go to the options - myPh3 page to set the your gallery details.

In a WordPress post, you can type [preview]&lt;photo_thumb_url&gt;[/preview] to include a thumnail and full size photo. &lt;photo_thumb_url&gt; needs to be replaced here by the full URL of one of your album image thumbnails. (Right-click on an image in your gallery and choose 'Copy Shortcut'.)

Alternatively, you can include all images of an album at once. In a post type [album]&lt;path_to_album&gt;[/album] to include all images that reside in this myPh3 album. The &lt;path_to_album&gt; needs to be replaced by the album's full name as it appears on the top of every myPh3 album. For example if you have an album 'Holidays' and herein an album 'Spain 2007' your post should say include the text [album]Holidays/Spain 2007[/album].

== Changes ==

1.1 changes:
- Now, both the thumb and full image links can be used to put between [preview] tags
- Fixed bug no showing the full image in later posts
- Less error sensitive

2.0 changes:
- Added support for [album] tag, showing all image that reside within this album (recursive) in one statement.

2.1 changes:
- Optical change: now show the same light-grey border on all myPh3 preview images in blog posts
- Bugfix in [album]-style previews incorrectly linking the full size image
