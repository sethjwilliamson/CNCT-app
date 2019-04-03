from datetime import datetime 
import pymysql
import json
import urllib.request

def getAccessToken(userid):
	db = getDatabase()

	cursor = db.cursor()

	cursor.execute("""SELECT `instagramToken` FROM `users` WHERE `id` LIKE %s""", (userid))

	accessToken = cursor.fetchone()[0]

	cursor.close()

	return accessToken

def insertPosts(accessToken): 
	db = getDatabase()

	data = json.load(urllib.request.urlopen('https://api.instagram.com/v1/users/self/media/recent/?access_token=' + accessToken))['data']

	cursor = db.cursor()

	for i in data:
		print(i)

		cursor.execute("""INSERT INTO `posts` (`id`, `userid`, `link`, `time`) VALUES (%s, %s, %s, %s) ON DUPLICATE KEY UPDATE `userid` = %s, `link` = %s, `time` = %s;""", ('i' + i['id'], 'i' + i['user']['id'], i['link'], datetime.utcfromtimestamp(int(i['created_time'])), 'i' + i['user']['id'], i['link'], datetime.utcfromtimestamp(int(i['created_time']))))
		
		cursor.execute("""INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, %s, %s) ON DUPLICATE KEY UPDATE `postid` = %s, `link` = %s;""", ('i' + i['id'], i['images']['standard_resolution']['url'], 'i' + i['id'], i['images']['standard_resolution']['url']))
		
		if i['type'] == 'carousel': # Carousel means there is more than one image associated with one post
			for j in i['carousel_media']:
				cursor.execute("""INSERT INTO `media` (`id`, `postid`, `link`) VALUES (NULL, %s, %s) ON DUPLICATE KEY UPDATE `postid` = %s, `link` = %s;""", ('i' + i['id'], j['images']['standard_resolution']['url'], 'i' + i['id'], j['images']['standard_resolution']['url']))
		
	cursor.close()

def insertInstaAccessToken(userid, url): 
	# After user clicks the authenticate button then they put URL they were sent to in textbox
	db = getDatabase()

	accessToken = url.split('access_token=', 1)[1]
	instaId = json.load(urllib.request.urlopen('https://api.instagram.com/v1/users/self/?access_token=' + accessToken))['data']['id']

	cursor = db.cursor()

	cursor.execute("""UPDATE `users` SET `instagramId` = %s, `instagramToken` = %s WHERE `users`.`id` = %s;""", ('i' +instaId, accessToken, userid))
	
	cursor.close()

def createUserDocument(userid, username): 
	db = getDatabase()

	cursor = db.cursor()

	cursor.execute("""INSERT INTO `users` (`id`, `username`) VALUES (%s, %s);""", (userid, username))

	cursor.close()

def getDatabase():
	return pymysql.connect(user='cnctsoci_admin', password='Csc3380!!!', host='155.138.243.181', database='cnctsoci_data')


#db = getDatabase()

#createUserDocument('123', 'sethjfake', db)
#insertInstaAccessToken('1', 'https://api.instagram.com/v1/users/self/media/recent/?access_token=9460084.acbd4e0.170edecd59674901b4b43551225b8c60', db)
#insertPosts(getAccessToken('1', db), db)
#insertPosts('9460084.acbd4e0.170edecd59674901b4b43551225b8c60', db)

#db.commit()
#db.close()
