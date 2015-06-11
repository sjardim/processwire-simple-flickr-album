<?php

include "../lib/curl.class.php";

// You will need to get a Flick API Key
// Get it here: https://www.flickr.com/services/apps/create/
// Load it on the file below
include "../lib/flickr_album_utils.php";


function download_file($url) {


  // Save the image on local filesystem (You need to create this folder first)
  // On your server it can be /var/www/name_of_folder/
  $tempdir = '/Users/XXX/phpFlickrCache/';
  
  $fp = $tempdir . basename(parse_url($url, PHP_URL_PATH));

  // if we already downloaded the images for some reason (like testing), just return it
  if (!file_exists($fp)) {

    $fh = fopen($fp, 'wb');
    
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FILE, $fh);

    curl_exec($curl);
    curl_close($curl);
    fclose($fh);

  }

  return $fp;
}

// Get some public album id to test
$albumID = ["72157636029541784"];

//$albumID = ["72157649576832173", "72157633296236495", "72157644132091553", "72157636029541784"];

// Load ProcessWire API
$pages = wire('pages')->get('/pictures/');

 /* 
  --------------------------
  GET ALBUM INFO FROM FLICKR
  --------------------------
  */
 
foreach($albumID as $album) {

  // Via GET, return album and its photos info
	$album = fa_get_album($album);
  
  // create a new post
  $page = new Page();
  $page->template = 'picture_album';
  $page->parent = $pages;
    
  // disable page output formatting
  $page->of(false);

  
  $page->name = wire('sanitizer')->pageName($album['title'], true);  
  
  $page->flickr_album_id = $album['id'];
  $page->title = $album['title'];
  $page->generic_integer = $album['total']; //total number of photos on flickr album – OPTIONAL


  //My client albums descriptions have two phrases, one in English and other in Portuguese
  // let's separate then by the period, but sometimes there is no period, so the PT description will remain blank
  $description = explode('.', $album['description']);

  $en = $languages->get("default");  
  $page->summary->setLanguageValue($en, $description[0]);

  $pt = $languages->get("portuguese");  
  $page->summary->setLanguageValue($pt, $description[1]);
  
  $page->set("status$pt", 1); //activate portuguese page

  $page->save(); // We need to save the page BEFORE adding images
  
  /* 
  ------------------------------------
  DOWNLOAD AND SAVE IMAGES FROM FLICKR
  ------------------------------------
  */

  $images = array();

  $i=1;
  $maxImages = 11;

  foreach($album["all_images"] as $f) {

    //we do not want all the photos, just a little bit
    if ($i >= $maxImages) break;
    
    // mount the flickr photo url using its attributes
    $photo_url = 'https://farm'.$f["farm"].'.staticflickr.com/'.$f["server"].'/'.$f["id"].'_'.$f["secret"].'_b.jpg';
    
    // download and return the image file in the filesystem
    $images[$album['id']][$i] = download_file($photo_url);
    
    // add images to the current page in the loop
    $page->images->add($images[$album['id']][$i]);
    
    $i++;
  }

  $page->save();

  echo "<p>Created page for album: <strong>".$album['title']. "</strong></p>";

  // Tip: Now, after we saved the images to ProcessWire /site/assets/files/, we can safely delete them from the $tempdir if needed
 
}
print "<pre>";
print_r($images);
print "</pre>";


 