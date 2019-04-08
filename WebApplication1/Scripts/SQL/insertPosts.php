<?php
    $userid = $_POST['userid'];
    #$userid = 'CNCTSocials';
    
	$conn = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');

	$cursor = $conn->query("SELECT `instagramToken` FROM `users` WHERE `username` = '$userid'");

	$accessToken = $cursor->fetch_array()[0];
	
	$url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='. $accessToken;
    $ch = curl_init();
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL,$url);
    // Execute
    $data=json_decode(curl_exec($ch), true)['data'];#['data'];
    // Closing
    curl_close($ch);
    #echo($data);
    $conn->close();
	$conn2 = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');
	
	foreach($data as $i) {
		$id = 'i' . $i['id'];
		$userid = 'i' . $i['user']['id'];
		$link = $i['link'];
		$time = gmdate('Y-m-d H:i:s', (int)($i['created_time']));
		$imageLink = $i['images']['standard_resolution']['url'];

		$conn2->query("INSERT INTO `posts` (`id`, `userid`, `link`, `time`) VALUES ('$id', '$userid', '$link', '$time') ON DUPLICATE KEY UPDATE `userid` = '$userid', `link` = '$link', `time` = '$time';");
		#echo(sprintf("INSERT INTO `posts` (`id`, `userid`, `link`, `time`) VALUES ('%s', '%s', '%s', '%s') ON DUPLICATE KEY UPDATE `userid` = '%s', `link` = '%s', `time` = '%s';", 'i' . $i['id'], 'i' . $i['user']['id'], $i['link'], gmdate('Y-m-d H:i:s', (int)($i['created_time'])), 'i' . $i['user']['id'], $i['link'], gmdate('Y-m-d H:i:s', (int)($i['created_time']))));
		

		$conn2->query("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, '$id', '$imageLink') ON DUPLICATE KEY UPDATE `postid` = '$id', `link` = '$imageLink';");
		#echo(sprintf("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, '%s', '%s') ON DUPLICATE KEY UPDATE `postid` = '%s', `link` = '%s';", 'i' . $i['id'], $i['images']['standard_resolution']['url'], 'i' . $i['id'], $i['images']['standard_resolution']['url']));
		
		if ($i['type'] == 'carousel') { # Carousel means there is more than one image associated with one post
			foreach ($i['carousel_media'] as $j) {
				$imageLink = $j['images']['standard_resolution']['url'];
				$conn2->query("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, '$id', '$imageLink') ON DUPLICATE KEY UPDATE `postid` = '$id', `link` = '$imageLink';");
				#echo(sprintf("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, '%s', '%s') ON DUPLICATE KEY UPDATE `postid` = '%s', `link` = '%s';", 'i' . $i['id'], $j['images']['standard_resolution']['url'], 'i' . $i['id'], $j['images']['standard_resolution']['url']));
			}
		}
	}
	$conn2->commit();
	$conn2->close();