import os
import json
import pymysql
import google.oauth2.credentials
import google_auth_oauthlib.flow
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError
from google_auth_oauthlib.flow import InstalledAppFlow

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
def listSubcriptions(client, **kwargs):
	data = json.dumps(client.subscriptions().list(**kwargs).execute())
	
def getDatabase():
	return pymysql.connect(user='cnctsoci_admin', password='Csc3380!!!', host='155.138.243.181', database='cnctsoci_data')

os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = '1'
client = getAuthentication()
listSubcriptions(client, part='snippet', mine=True)