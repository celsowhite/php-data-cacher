<?php

	/*---------------------------------
    Return data from the Eventbrite API.
    ---
   	The data cacher will work in the background to check if the cache for the data needs to be refreshed and then 
	regenerate a local json file.
	
	A good way to use this file is to call it via a js ajax call so it can do the caching work in the background.

	To get the data you can either:
	1. Parse the returned json string in the ajax callback
	2. Do a separate ajax call to the static json file that the data cacher generates.
	--- 
    @return  string  The json of the file as a string.
	---------------------------------*/
	
	include('./config.php');

	include('./data_cacher.php'); 

    $DataCacher = new DataCacher();

    // Create a query string from our Eventbrite parameters.

    $queryParams = array('token' => $eventbrite_token, 'location.latitude' => '35.6895', 'location.longitude' => '139.6917');

    $queryString = 'https://www.eventbriteapi.com/v3/events/search/?' . http_build_query($queryParams);

	// Get the json data. The data cacher will do all the behind the scenes work to query and return the cached data.
	
	$jsonData = $DataCacher->save_json('eventbrite_data.json', $queryString, 60, 5);
	
	// Returned data.

	return $jsonData;

?>