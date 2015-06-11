<?php

function fa_get_album($album_id) {
	
	$url = "https://api.flickr.com/services/rest/";
	
	// You will need to get a Flick API Key
	// Get it here: https://www.flickr.com/services/apps/create/
	$key = '<put your api key here>';

	$urlInfo = $url."?method=flickr.photosets.getInfo&api_key=".$key."&photoset_id=".$album_id;
	$urlListPhotos = $url."?method=flickr.photosets.getPhotos&api_key=".$key."&photoset_id=".$album_id;

	$cc = new cURL(false);

	$infoResult = $cc->get($urlInfo);
	$result = $cc->get($urlListPhotos);

	$infoXml = new SimpleXMLElement($infoResult);
	$xml = new SimpleXMLElement($result);

	$photoset["title"] = (string) $infoXml->photoset->title;
	$photoset["description"] = (string) $infoXml->photoset->description;

	//get album other atributes
	foreach ($xml->photoset->attributes() as $key => $value){
		$photoset[$key] = (string) $value;
	}

	//get all photos attributes
	foreach ($xml->photoset->photo as $photo){		
		foreach ($photo->attributes() as $key => $value){
			$atts[$key] = (string) $value;
		}
		$album_images[] = $atts;
	}

	$photoset["all_images"] = $album_images;

	return $photoset;
}

?>