import os
import json
import pymysql
import urllib.request
import google.oauth2.credentials
import google_auth_oauthlib.flow
from datetime import datetime 
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError
from google_auth_oauthlib.flow import InstalledAppFlow

# A lot of this code was sampled from the Youtube API references
# For more info go to the following websites
# https://developers.google.com/youtube/v3/docs/channels/list
# https://developers.google.com/youtube/v3/docs/playlistItems/list
# https://developers.google.com/youtube/v3/docs/subscriptions/list

# The CLIENT_SECRETS_FILE variable specifies the name of a file that contains
# the OAuth 2.0 information for this application, including its client_id and
# client_secret.
clientSecrets = "./client_secret.json"

# This OAuth 2.0 access scope allows for full read/write access to the
# authenticated user's account and requires requests to use an SSL connection.
scopes = ['https://www.googleapis.com/auth/youtube.force-ssl']
apiServiceName = 'youtube'
apiVersion = 'v3'

def getAccessToken():
  flow = InstalledAppFlow.from_client_secrets_file(clientSecrets, scopes)
  credentials = flow.run_local_server()
  return build(apiServiceName, apiVersion, credentials = credentials)

# the parameter **kwargs allows for multiple keyword arguments
def listSusbcriptions(client, **kwargs):
	subscriptionData = client.subscriptions().list(**kwargs).execute()
	#Checks all of the content from an authenticated user's subscriptions, the second parameter is the total number of subscriptions
	
	for i in range(0,subscriptionData['pageInfo']['totalResults']):
		# Retrieves the channel ID from the list of subscriptions
		channelId = subscriptionData['items'][i]['snippet']['resourceId']['channelId']
		
		# Retrieves the upload playlist ID from the youtuber's channel
		playlistId = (subscriptionData['items'][i]['snippet']['resourceId']['channelId']).replace('C', 'U', 2)
		
		# Retrieves the recent videos from the upload playlist of another channel 
		recentVideos = (json.load(urllib.request.urlopen('https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=' + playlistId + '&maxResults=50&key=InsertAPIKeyHere')))
		
		# Retrieves the videoId from the recentVideos Playlist
		videoId = recentVideos['items'][i]['snippet']['resourceId']['videoId']
		
		# Retrieves the videoId from the recentVideos Playlist
		publishedDate = recentVideos['items'][i]['snippet']['publishedAt']
		
		# Retrieves the url for the thumbnail
		# default.jpg is 120x90
		# mqdefault.jpg is 320x180
		# hqdefault.jpg is 480x360
		# sddefault.jpg is 640x480
		# maxresdefault.jpg is 1280x720
		videoThumbnail = "https://i.ytimg.com/vi/" + videoId + "/sddefault.jpg"
		
def insertSubscriptionVideos(chanId, vid, date, vidThumb,db):
	cursor = db.cursor()
	cursor.execute("""INSERT INTO `posts` (`id`, `userid`, `link`, `time`) VALUES (%s, %s, %s, %s) ON DUPLICATE KEY UPDATE `userid` = %s, `link` = %s, `time` = %s;""", ('y' + vid, 'y' + i['user']['id'], ('https://www.youtube.com/watch?v=' + vid), date), 'y' + i['user']['id'], ('https://www.youtube.com/watch?v=' + vid), date)
	cursor.execute("""INSERT INTO `media` (`id`,`postid`, `link`) VALUES (NULL, %s, %s) ON DUPLICATE KEY UPDATE `postid` = %s, `link` = %s;""", (i['images']['standard_resolution']['url'], ('y' + vid) , vidThumb))
	print("cool stuff")
	cursor.close()

def getDatabase():
	return pymysql.connect(user='cnctsoci_admin', password='Csc3380!!!', host='155.138.243.181', database='cnctsoci_data')

db = getDatabase()
# This can be disabled for local use, but don't disable it for commercial use
os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = '1'

# Retrieve authentication
client = getAccessToken()

#Get a list of subscriptions, I'm going to keep maxResults low for testing purposes
listSusbcriptions(client, part='snippet', mine=True, maxResults=5)

insertSubscriptionVideos('UClFSU9_bUb4Rc6OYfTt5SPw', '4ChP2cLdmvw', '2019-04-02 21:00:25', 'https://i.ytimg.com/vi/4ChP2cLdmvw/maxresdefault.jpg', db)

db.commit()
db.close()