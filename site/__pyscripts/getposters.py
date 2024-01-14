import MySQLdb as mdb
import urllib.request, json
import shutil

con = mdb.connect('localhost', 'root', '', 'recommender');

try:
	with con:
		cur = con.cursor()
		query = "SELECT imdbID FROM films"
		cur.execute(query)
		result = cur.fetchall()

		imdbIDs = [film[0] for film in result]
except Exception:
	print(Exception)

for i in range(250):
	with urllib.request.urlopen("http://www.omdbapi.com/?apikey=bbcbf298&i=" + imdbIDs[i]) as url:
	    data = json.loads(url.read().decode())
	    p = data['Poster']
	    title = data["Title"]
	    
	    print("Downloading " + title + "... ")

	    with urllib.request.urlopen(p) as response, open("../img/posters/"+imdbIDs[i]+".jpg", 'wb') as out_file:
	    	shutil.copyfileobj(response, out_file)