<?php 
	db = getDatabase()
	accessToken = url.split('access_token=', 1)[1]
	instaId = json.load(urllib.request.urlopen('https://api.instagram.com/v1/users/self/?access_token=' + accessToken))['data']['id']

	cursor = db.cursor()

	cursor.execute("""UPDATE `users` SET `instagramId` = %s, `instagramToken` = %s WHERE `users`.`id` = %s;""", ('i' +instaId, accessToken, userid))
	
	cursor.close()