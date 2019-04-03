<?php

    #$userid = $_POST['userid'];

	$conn = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');

	$cursor = $conn->query("SELECT `instagramToken` FROM `users` WHERE `id` = 1");

	$accessToken = $cursor->fetch_array()[0];

	$data = file_get_contents('https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $accessToken)['data'];
	
	foreach($data as $i) {
		echo(i);

		$conn->query("INSERT INTO `posts` (`id`, `userid`, `link`, `time`) VALUES (%s, %s, %s, %s) ON DUPLICATE KEY UPDATE `userid` = %s, `link` = %s, `time` = %s;", 'i' + $i['id'], 'i' + $i['user']['id'], $i['link'], gmdate("Y-m-d\TH:i:s\Z", (int)($i['created_time'])), 'i' + $i['user']['id'], $i['link'], gmdate("Y-m-d\TH:i:s\Z", (int)($i['created_time'])));
		
		$conn->query("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, %s, %s) ON DUPLICATE KEY UPDATE `postid` = %s, `link` = %s;", 'i' + $i['id'], $i['images']['standard_resolution']['url'], 'i' + $i['id'], $i['images']['standard_resolution']['url']);
		
		if ($i['type'] == 'carousel') { # Carousel means there is more than one image associated with one post
			foreach ($i['carousel_media'] as $j) {
				$conn->query("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, %s, %s) ON DUPLICATE KEY UPDATE `postid` = %s, `link` = %s;", 'i' + $i['id'], $j['images']['standard_resolution']['url'], 'i' + $i['id'], $j['images']['standard_resolution']['url']);
			}
		}
	}