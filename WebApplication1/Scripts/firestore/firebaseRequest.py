import json
import urllib.request
from datetime import datetime 
import firebase_admin
from firebase_admin import credentials, firestore	

def getAccessToken(userid, db):
	doc_ref = db.collection(u'users').document(userid)

	doc = doc_ref.get().to_dict()

	print('Access Token = ' + doc['instagramToken'])

	return doc['instagramToken']

def insertPosts(access_token, db):

	data = json.load(urllib.request.urlopen('https://api.instagram.com/v1/users/self/media/recent/?access_token=' + access_token))['data']

	for i in data:
		print(i)

		doc_ref = db.collection(u'posts').document('i' + i['id'])
		doc_ref.set({
		    u'imageLinks' : i['images'],
    		u'isMedia' : True,
    		u'link' : i['link'],
    		u'time' : datetime.utcfromtimestamp(int(i['created_time'])),
    		u'user' : i['user']
		})

def insertAccessToken(userid, url, db):
	# After user clicks the authenticaate button then they put URL they were sent to in textbox
	#db = getDatabase()
	url = url.split('access_token=', 1)[1]

	doc_ref = db.collection(u'users').document(userid)
	print(url)
	doc_ref.update({
		u'instagramToken' : url
	})

def createUserDocument(userid, db):
	doc_ref = db.collection(u'users').document(userid)
	doc_ref.set({
	    u'facebookAt' : None,
		u'facebookToken' : None,
		u'followers' : [],
		u'following' : [],
		u'instagramAt' : None,
		u'instagramToken' : None,
		u'twitterAt' : None,
		u'twitterToken' : None,
		u'username' : "test",
	})

def getDatabase():

	cred = credentials.Certificate('./ServiceAccountKey.json')
	default_app = firebase_admin.initialize_app(cred)
	return firestore.client()

db = getDatabase()

createUserDocument('d35eb40a-7400-470f-82a4-3af020f9d8e7', db)
insertAccessToken('d35eb40a-7400-470f-82a4-3af020f9d8e7', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=9460084.acbd4e0.170edecd59674901b4b43551225b8c60', db)
insertPosts(getAccessToken('d35eb40a-7400-470f-82a4-3af020f9d8e7', db), db)