<?php
	$conn = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');
	$start = $conn->real_escape_string($_POST['start']);
	$limit = $conn->real_escape_string($_POST['limit']);
	#$query = stripslashes($conn->real_escape_string($_POST['query']));
	#$users = stripslashes($conn->real_escape_string($_POST['users']));

	#$sqlUser = $conn->query("SELECT instagramId, facebookId, twitterId FROM `users` WHERE `id` = $user");
	#$instagramId = $sqlUser->fetch_array();
	#$facebookId = $sqlUser->fetch_array();
	#$twitterId = $sqlUser->fetch_array();

	# Add query
	$sqlPosts = $conn->query("SELECT * FROM `posts`
		ORDER BY `time` DESC
		LIMIT $start, $limit");
		
	if ($sqlPosts->num_rows > 0) 
	{
		$response = "";

		while($data = $sqlPosts->fetch_array()) {
			$response .= '
			<div class="row">
				<div class="panel panel-default" href = "' . $data['link'] . '" style="margin-left:auto; margin-right:auto; width:90%">';

			switch($data['id'][0]) {
				case 'i':
					$response .= '
					<div class="row">
						Username here
					</div>
					<div class="row">
						<img srcset="https://scontent.cdninstagram.com/vp/41f1ab488ce2d6cc31d9ef0de471a14c/5D3C2B28/t51.2885-15/sh0.08/e35/s640x640/51960952_325260398338412_792144657913854242_n.jpg?_nc_ht=scontent.cdninstagram.com"
					</div>
					<div class="row">
						' . $data['time'] . '
					</div>
					';
					break;
				case 't':
					# Twitter Post
					break;
				case 'f':
					# Facebook Post
					break;
				default:
					echo("it broke ?");
					break;
			}
			$response .= '
				</div>
			</div>
			</div>';
		}
		echo($response);
		exit($response);
	} 
	else
		exit('reachedMax');
?>