<?php

/*
Plugin Name: myPh3 preview image
Plugin URI: http://myph.sf.net
Description: Displays the thumbnails of a selected image or whole album from a myPh3 image gallery album to preview.
Version: 2.1
Author: Eric Kok
Author URI: http://myph.sf.net
*/

/*  Copyright 2007  ElmoTheElk  (email : erickok@gmail.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

The included mootools and slimbox libraries are copyrighted under MIT-style 
licence:

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

*/

add_action('wp_head', 'myPh3_add_slimbox_js');
add_action('admin_menu', 'myPh3_add_menu');
add_filter('the_content', 'myPh3_replace_images');

// Including the needed javascripts of mootools and slimbox
function myPh3_add_slimbox_js() {

  $slimbox_path = get_option('siteurl')."/wp-content/plugins/myPh3.preview/";
  $slimboxscript.= "<link rel=\"stylesheet\" href=\"".$slimbox_path."slimbox.css\" type=\"text/css\" media=\"screen\" />\n";
  $slimboxscript.= "<script type=\"text/javascript\" src=\"".$slimbox_path."mootools.js\"></script>\n";
  $slimboxscript.= "<script type=\"text/javascript\" src=\"".$slimbox_path."slimbox.js\"></script>\n";
  $slimboxscript.= "<style type=\"text/css\">.entry img.myPh3 { border: 1px solid #e6e6df; padding: 10px; margin: 0 10px 10px 0; }</style>\n";
  print($slimboxscript);

}

// Replace [preview][/preview] tags in posts with the correct myPh3 
// image (and thumbnail) thumnail, as well as show a link to the album
// Also replaces [album][/album] tags in posts to show all image of an album
function myPh3_replace_images($content = '') {

  // Get the site settings of the myPh3 gallery
  $siteDir = get_option('myPh3.siteDir');
  if (substr($siteDir, -1) != '/') 
    $siteDir .= '/';
  require($siteDir . 'myPh3.config.php');
  
  // Run through the post contents to replace [preview] tags untill there are no more
  while (strpos($content, '[preview]') !== FALSE) {

    // Get positions and strings
    $ppos_open = strpos($content, '[preview]');
    $ppos_close = strpos($content, '[/preview]');
    if (!($ppos_close >= 0)) {
      print('Error parsing myPh3 preview: no ending tag after [preview]');
      break;
    }
    $preview_sub = substr($content, $ppos_open + 9, $ppos_close - $ppos_open - 9);
    // Revert the replacement of the duble dash (--) by an en-dash (– or &#8211; as html char)
    $preview = str_replace('&#8211;', '--', $preview_sub);

    // Make full and thumb paths
    if (strpos($preview, $m3['config']['thumbSize'] . '--') == FALSE) {
      $pfull = $preview;
      $pthumb = str_replace($m3['config']['fullSize'] . '--', $m3['config']['thumbSize'] . '--', $preview);
    } else {
      $pfull = str_replace($m3['config']['thumbSize'] . '--', $m3['config']['fullSize'] . '--', $preview);
      $pthumb = $preview;
    }
    
    // Replace the tag with the actual image links
    $pimg = '<a href="' . $pfull . '" rel="lightbox[myPh3]"><img src="' . $pthumb . '" alt="" class="myPh3"/></a>';
    $content = str_replace('[preview]' . $preview_sub . '[/preview]', $pimg, $content);

  }
  
  // Run through the post contents to replace [album] tags untill there are no more
  while (strpos($content, '[album]') !== FALSE) {

    // Get positions and strings
    $apos_open = strpos($content, '[album]');
    $apos_close = strpos($content, '[/album]');
    if (!($apos_close >= 0)) {
      print('Error parsing myPh3 preview: no ending tag after [album]');
      break;
    }
    $album_tag = substr($content, $apos_open + 7, $apos_close - $apos_open - 7);
    // Create the string used to find this album in the thumbnails dir
    $album_text = strtr($album_tag, '/()', '---');
    if (substr($album_text, 0, 1) != '-') $album_text = '-' . $album_text;
    
    // Look for all images in this album
    $album_content = '';
    // Try to read from the myPh3 thumbnail directory
    $dirList = @opendir($siteDir . $m3['config']['thumbDir']);
    while (($file = readdir($dirList)) !== false) {
      // Try to match thumb/full image name to the album (and only use thumb images for this)
      $album_text_pos = strpos($file, $m3['config']['thumbSize'] . '-' . $album_text);
      if ($album_text_pos !== false) {
        // Make full and thumb paths
        $athumb = $m3['config']['siteUrl'] . '/' . $m3['config']['thumbDir'] . '/' . $file;
        $afull = str_replace($m3['config']['thumbSize'] . '--', $m3['config']['fullSize'] . '--', $athumb);
        $album_content .= '<a href="' . $afull . '" rel="lightbox[myPh3]"><img src="' . $athumb . '" alt="" class="myPh3" /></a>';
      }
    }

    // Replace the tag with the actual image links
    $content = str_replace('[album]' . $album_tag . '[/album]', $album_content, $content);

  }
  
  return $content;
  
}

// Admin screen to set the site url
if(!function_exists('myPh3_conf')){
  function myPh3_conf() {
  
    // Store new siteDir setting
  	if ( isset($_POST['submit']) ) {
  	  update_option('myPh3.siteDir', $_POST['myPh3_siteDir']);
  	  echo '
        <div id="message" class="updated fade"><p><strong>' . __('Configuration stored.') . '</strong></p></div>';
  	}
  	// Get stored siteDir setting
  	$siteDir = get_option('myPh3.siteDir');
  	if ($siteDir != '') {
  	  $urlMessage = __('myPh should now be configured! Easy, huh? (You can change it here if you move your gallery.)');
  	} else {
  	  $urlMessage = __('Please enter the full site path where your myPh3 gallery is situated. This usally is something like \'/var/www/gallery/\' (Linux) or \'C:\\www\gallery\\\' (Windows).');
  	}
  	
  	// Show admin screen
  ?>
    <div class="wrap">
      <h2><?php _e('myPh3 configuration'); ?></h2>
      <div class="narrow">
        <form action="" method="post" id="akismet-conf" style="margin: auto; width: 400px; ">
          <p><?php echo $urlMessage; ?></p>
          <p><input id="myPh3_siteDir" name="myPh3_siteDir" type="text" maxlength="100" value="<?php echo $siteDir; ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em; width: 390px;" /></p>
          <p class="submit"><input type="submit" name="submit" value="<?php _e('Update configuration &raquo;'); ?>" /></p>
        </form>
      </div>
    </div>
  <?php
  
  }
}

if(!function_exists('myPh3_add_menu')){
  function myPh3_add_menu() {
    add_options_page('myPh3', 'myPh3', 10, 'myPh3.preview.php', 'myPh3_conf');
  }
}

?>