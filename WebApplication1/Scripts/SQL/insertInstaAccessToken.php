<?php 
	# Establish connection to mysql database
	$conn = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');
	
	# Get parameters from POST request
	$url = $_POST['url'];
	$userid = $_POST['userid'];

    # Get substring after access_token=
	$accessToken = explode('access_token=', $url)[1];

	$ch = curl_init();
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL,'https://api.instagram.com/v1/users/self/?access_token=' . $accessToken);
    // Execute
    $instaId = json_decode(curl_exec($ch), true)['data']['id'];
    // Closing
    curl_close($ch);
    
    # Query to update instagram values where userid matches
	$conn->query("UPDATE `users` SET `instagramId` = 'i$instaId', `instagramToken` = '$accessToken' WHERE `username` = '$userid';");
	$conn->commit();
	$conn->close();