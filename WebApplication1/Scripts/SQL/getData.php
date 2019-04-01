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
				<div class="panel panel-default" style="margin-left:auto; margin-right:auto; width:90%">';

			switch($data['id'][0]) {
				case 'i':
					$response .= '
					<a href="' . $data['link'] . '">
						<div class="row" style="margin-left:15px; margin-right:15px">
							' . $conn->query("SELECT users.username FROM `posts`
							LEFT JOIN `users` ON posts.userid = users.instagramId
							WHERE posts.id LIKE '" . $data['id'] . "'")->fetch_array()['username'] . '
						</div>
					</a>
					<div class="row" style="margin-left:15px; margin-right:15px">
						<div id="carousel' . $data['id'] . '" class="carousel slide" data-interval="false" data-ride="carousel">
							<ol class="carousel-indicators">
    							<li data-target="#carousel' . $data['id'] . '" data-slide-to="0" class="active"></li>';

					$sqlMedia = $conn->query("SELECT media.link FROM `posts`
						LEFT JOIN `media` ON posts.id = media.postid
						WHERE posts.id LIKE '" . $data['id'] . "'
						ORDER BY media.id ASC");

					for($mediaNum = 1; $mediaNum < $sqlMedia->num_rows; $mediaNum++) {
						$response .= '
								<li data-target="#carousel' . $data['id'] . '" data-slide-to="' . $mediaNum . '"></li>
						';
					}

					$mediaNum = 0;
					$response .= '
							</ol>
							<div class="carousel-inner">
								<div class="item active">
									<img src="' . $sqlMedia->fetch_array()['link'] . '" alt = "' . $mediaNum++ . '">
								</div>
					';

					while($media = $sqlMedia->fetch_array()) {
						$response .= '
								<div class="item">
									<img src="' . $media['link'] . '" alt = "' . $mediaNum++ . '">
								</div>
						';
					}

					$response .= '
								<a class="left carousel-control" href="#carousel' . $data['id'] . '" role="button" data-slide="prev">
									<span class="glyphicon glyphicon-chevron-left"></span>
									<span class="sr-only">Previous</span>
								</a>
								<a class="right carousel-control" href="#carousel' . $data['id'] . '" role="button" data-slide="next">
									<span class="glyphicon glyphicon-chevron-right"></span>
									<span class="sr-only">Next</span>
								</a>
							</div>
					';

					$response .= '
						</div>
					</div>
					<a href="' . $data['link'] . '">
						<div class="row" style="margin-left:15px; margin-right:15px">
							' . $data['time'] . '
						</div>
					</a>
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
			</div>';
		}
		exit($response);
	} 
	else
		exit('reachedMax');
?>