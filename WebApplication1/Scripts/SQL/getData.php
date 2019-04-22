<?php
    # Establish connection to mysql database
	$conn = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');
	
	# Get parameters from POST request
	$start = $conn->real_escape_string($_POST['start']);
	$limit = $conn->real_escape_string($_POST['limit']);
	$limit = 2000;
	$query = $_POST['query'];
	
	$queryString = 'WHERE ';

    # Add to query string with social media filter
    # Using == "true" because JS -> PHP seems to turn the boolean into a string 
	if($query['isInstagram'] == "true") { 
		$queryString .= '(posts.id LIKE "i%" ';

		if($query['isYoutube'] == "true") {
			$queryString .= 'OR posts.id LIKE "y%") AND ';
		} else {
			$queryString .= ') AND ';
		}
	} else if($query['isYoutube'] == "true") {
		$queryString .= 'posts.id LIKE "y%" AND ';
	}

    # If parameters for times do not exist, use default values
	if($query['timeStart'] == "")
		$query['timeStart'] = "1111/1/1";
	if($query['timeEnd'] == "")
		$query['timeEnd'] = date("Y/m/d");

    # Add to query string with time filter
	$queryString .= 'posts.time BETWEEN "' . $query['timeStart'] . '" AND "' . $query['timeEnd'] . '" ';
	
	# Query for all posts matching filter criteria
	$sqlPosts = $conn->query("SELECT * FROM `posts`
		" . $queryString . " 
		ORDER BY `time` DESC
		LIMIT $start, $limit");

    # Return max if no results found
	if ($sqlPosts->num_rows > 0) 
	{
		$response = "";

		while($data = $sqlPosts->fetch_array()) {
		    # $response is the html that is returned to the feed page
			$response .= '
			<div class="row">
				<div class="panel panel-default" style="margin-left:auto; margin-right:auto; width:90%">';

            # Switch between Instagram or Youtube
			switch($data['id'][0]) {
				case 'i':
				    # Instagram Post
					$response .= '
					<a href="' . $data['link'] . '" target="_blank">
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

                    # If more than one image is associated with one post, it is added in carousel form here
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
    								<a href="' . $data['link'] . '" target="_blank">
    									<img src="' . $sqlMedia->fetch_array()['link'] . '" alt = "' . $mediaNum++ . '" style="width:100%">
    								</a>
								</div>
					';

					while($media = $sqlMedia->fetch_array()) {
						$response .= '
								<div class="item">
    								<a href="' . $data['link'] . '" target="_blank">
    									<img src="' . $media['link'] . '" alt = "' . $mediaNum++ . '" style="width:100%">
    								</a>
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
					<a href="' . $data['link'] . '" target="_blank">
						<div class="row" style="margin-left:15px; margin-right:15px">
							' . $data['time'] . '
						</div>
					</a>
					';
					break;
				case 'y':
					# Youtube Post
					$response .= '
					<a href="' . $data['link'] . '" target="_blank">
						<div class="row" style="margin-left:15px; margin-right:15px">
							' . $conn->query("SELECT users.username FROM `posts`
							LEFT JOIN `users` ON posts.userid = users.youtubeId
							WHERE posts.id LIKE '" . $data['id'] . "'")->fetch_array()['username'] . '
						</div>
					</a>
					<div class="row" style="margin-left:15px; margin-right:15px">';

					$sqlMedia = $conn->query("SELECT media.link FROM `posts`
						LEFT JOIN `media` ON posts.id = media.postid
						WHERE posts.id LIKE '" . $data['id'] . "'
						ORDER BY media.id ASC");
						
					$response .= '
					    <a href="' . $data['link'] . '" target="_blank">
    						<img src="' . $sqlMedia->fetch_array()['link'] . '" style="width:100%">
						</a>
					</div>
					<a href="' . $data['link'] . '" target="_blank">
						<div class="row" style="margin-left:15px; margin-right:15px">
							' . $data['time'] . '
						</div>
					</a>';

					break;
				default:
				    # Does not match Youtube or Instagram post ID
					echo("Error: Not Youtube or Instagram");
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