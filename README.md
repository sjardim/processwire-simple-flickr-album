# Processwire Simple Flickr Album

A simple script to get a album downloaded to a page using ProcessWire API

## Instructions

1. Download the files
1. Put the `templates/get_flickr_sets.php` on your `templates/` folder.
1. Create a page on Processwire using this template
1. Create or edit the page where the albums will be created. In my case it was `/pictures/`
1. IMPORTANT: Add guest edit/create permissions to this `/pictures/` page, otherwise the script won't work
1. Create a temp dir on your server and set it on the previous file
1. Get a Flickr API on https://www.flickr.com/services/apps/create/
1. Add one or more Flickr album IDs on `get_flickr_sets.php`
1. Open the page using the `get_flickr_sets.php` template on your browser

## Screenshot of ProcessWire Admin

![](https://raw.githubusercontent.com/sjardim/processwire-simple-flickr-album/master/Screenshot.png)
