<?php
require('./vendor/autoload.php');

$conn = new mysqli('155.138.243.181', 'cnctsoci_admin', 'Csc3380!!!', 'cnctsoci_data');

$userid = $_POST['userid'];
$authCode = $_POST['code'];
$client = new Google_Client();
$client->setApplicationName('API code samples');
$client->setScopes([
    'https://www.googleapis.com/auth/youtube.readonly',
]);
//set this equal to the location of your client secrets json file
$client->setAuthConfig('client_secrets.json');
$client->setAccessType('offline');

// Exchange authorization code for an access token.
$accessTokenOriginal = $client->fetchAccessTokenWithAuthCode($authCode);
$accessToken = strval($accessTokenOriginal['access_token']);
$client->setAccessToken($accessToken);

//Update query with youtubeId
$conn->query("UPDATE `users` SET `youtubeId` = 'y$userid', `youtubeToken` = '$accessToken' WHERE `username` = '$userid';");


$url = "https://www.googleapis.com/youtube/v3/subscriptions?part=snippet&mine=true";

$ch = curl_init();
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL,$url);
    // Set the authorization header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$accessToken));
    // Execute the curl and receive the channel ID
    $subscriptionId = json_decode(curl_exec($ch),true)['items'];
    //

for ($x = 0; $x <= 4; $x++) {
    $channelId = strval($subscriptionId[$x]['snippet']['resourceId']['channelId']);
    // Get the playlist ID from the channel ID
    $playlistId = preg_replace("/UC/", "UU", $channelId, 1);
    

    curl_close($ch);

$url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=" . $playlistId . "&maxresults=1&key=AIzaSyCj-swcbPfRnAHR2kBbv-cZWLvh9oyIOHw";

$ch = curl_init();
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL,$url);
    // Execute the curl to receive a list of recent videos
    $recentVideos = json_decode(curl_exec($ch),true)['items'][0];
    
    //Retrieves the videoID based on the list of recent videos
    $videoId = $recentVideos['snippet']['resourceId']['videoId'];
    
    //Retrieves the date the video was published at
    $publishedDate = substr_replace(str_replace("T", " ",$recentVideos['snippet']['publishedAt']), "", 19);
    
    //Retrieves the thumbnail of a video using the videoID
    $thumbnail = "https://i.ytimg.com/vi/" . $videoId . "/maxresdefault.jpg";
    
    //Retrieves a URL to the video using the videoID
    $videoLink = "https://www.youtube.com/watch?v=" . $videoId;

    //Insert videoId, userId, videoLink, and publishedDate into the query
    $conn->query("INSERT INTO `posts` (`id`, `userid`, `link`, `time`) VALUES ('y$videoId', 'y$userid', '$videoLink', '$publishedDate') ON DUPLICATE KEY UPDATE `userid` = 'y$userid', `link` = '$videoLink', `time` = '$publishedDate';");

    //Insert videoId and thumbnail into the query
    $conn->query("INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, 'y$videoId', '$thumbnail') ON DUPLICATE KEY UPDATE `postid` = 'y$videoId', `link` = '$thumbnail';"); 

    curl_close($ch);
}
// May need to use the code below to utilize the access token	
// Define service object for making API requests.
#$service = new Google_Service_YouTube($client);