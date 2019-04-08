<?php 
	
	#$conn = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');
	$url = $_POST['url'];
	$userid = $_POST['userid'];
	$url = "http://cnctsocials.com/#access_token=11086710024.acbd4e0.fbdc3b2443a74af1bd22e4081dc22474";

	echo(explode('access_token=', $url)[1]);
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
    echo($instaId);
	$conn->query("UPDATE `users` SET `instagramId` = %s, `instagramToken` = %s WHERE `users`.`id` = %s;", 'i' . $instaId, $accessToken, $userid);