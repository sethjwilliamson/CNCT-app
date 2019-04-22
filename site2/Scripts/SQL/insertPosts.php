<?php
	# Establish connection to mysql database
	$conn = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');
	
	# Get parameters from POST request
    $userid = $_POST['userid'];

    # Query gets instagram access token matching userid
	$cursor = $conn->query("SELECT `instagramToken` FROM `users` WHERE `username` = '$userid'");
	$accessToken = $cursor->fetch_array()[0];
	
	$url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='. $accessToken;
    $ch = curl_init();
    # Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    # Set the url
    curl_setopt($ch, CURLOPT_URL,$url);
    # Execute
    $data=json_decode(curl_exec($ch), true)['data'];
    # Closing
    curl_close($ch);
    
    $conn->close();
    
    # Establish connection to mysql database
	$conn2 = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');
	
	foreach($data as $i) {
	    # Set variables  
		$id = 'i' . $i['id'];
		$userid = 'i' . $i['user']['id'];
		$link = $i['link'];
		$time = gmdate('Y-m-d H:i:s', (int)($i['created_time']));
		$imageLink = $i['images']['standard_resolution']['url'];

        # Insert each post into posts table
		$conn2->query("INSERT INTO `posts` (`id`, `userid`, `link`, `time`) VALUES ('$id', '$userid', '$link', '$time') ON DUPLICATE KEY UPDATE `userid` = '$userid', `link` = '$link', `time` = '$time';");
		
		# Insert each image into media table
		$conn2->query("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, '$id', '$imageLink') ON DUPLICATE KEY UPDATE `postid` = '$id', `link` = '$imageLink';");
		if ($i['type'] == 'carousel') { # Carousel means there is more than one image associated with one post
			foreach ($i['carousel_media'] as $j) {
				$imageLink = $j['images']['standard_resolution']['url'];
				$conn2->query("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, '$id', '$imageLink') ON DUPLICATE KEY UPDATE `postid` = '$id', `link` = '$imageLink';");
			}
		}
	}
	
	$conn2->commit();
	$conn2->close();