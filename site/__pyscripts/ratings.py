import sys

usrid = sys.argv[1]

import numpy as np
import MySQLdb as mdb
from scipy import sparse
from scipy.sparse.linalg import svds
from collections import defaultdict
from operator import itemgetter

def sparse_mean(mat, row = -1, column = -1):
	# function to take means on a sparse matrix
	if row != -1:
		mat = mat[row]
	elif column != -1:
		mat = mat.transpose()[column]

	if mat.count_nonzero() != 0:
		return mat.sum()/mat.count_nonzero()
	else:
		return 0

def recommend(id):
	con = mdb.connect("localhost", "root", "", "recommender") #connection to the database
	data = []

	# getting user x films ratings from the database
	try:
		with con:
			cur = con.cursor()
			query = "SELECT * FROM user_ratings"
			cur.execute(query)
			result = cur.fetchall()

			for r in result:
				data.append(r)

			cur = con.cursor()
			query = "SELECT id FROM users ORDER BY id"
			cur.execute(query)

			result = cur.fetchall()
			user_ids = [user[0] for user in result]
			n_users = len(user_ids)

			cur = con.cursor()
			query = "SELECT COUNT(*) FROM films"
			cur.execute(query)

			n_films = cur.fetchone()[0]
	except Exception:
		print(Exception)

	# prepping the data into a user x movies rating matrix
	uid_matrix = user_ids.index(int(id))

	data = np.array(data, dtype=np.float32)

	res = defaultdict(list)
	for v,u,k in data: res[int(v)].append([u, k])
	
	for i in user_ids:
		if res[i]:
			res[i] = np.array(res[i])
		else:
			res[i] = np.array([[1,4]])

	first_usr = int(data[:,0].min())
	first_mov = int(data[:,1].min())

	user_ratings_mean = []
	for i in res:
		user_ratings_mean.append(sum(res[i][:,1])/len(res[i]))

	urm_array = []
	original_matrix = np.zeros(shape=(n_users, n_films), dtype=np.float32)

	for cur_rating in user_ratings_mean:
		urm_array.append([cur_rating]*n_films)

	i = 0
	for r in res: # users
		for j in res[r]: #movies
			urm_array[i][int(j[0])-1] = original_matrix[i, int(j[0])-1] = j[1]
		i += 1
	
	urm = np.array(urm_array)

	cur.close()
	con.close()

	def unrated(userid, movieid):
		# function to check if user j has rated movie i
		if not original_matrix[userid,movieid]:
			return 1

	original_matrix_sparse = sparse.csr_matrix(original_matrix)
	ratings_mean = sparse_mean(original_matrix_sparse) # mean of all ratings in original_matrix

	film_ratings_mean = []

	for i in range(n_films):
		film_ratings_mean.append(sparse_mean(original_matrix_sparse,-1, i))

	predictions = []

	# naive SVD
	U, S, V = sparse.linalg.svds(sparse.csr_matrix(urm))
	P = S * V.T

	for i in range(first_usr-1, n_users):
		for j in range(first_mov-1, n_films):
			if unrated(i,j):
				p = [i,j,((ratings_mean+(film_ratings_mean[j]-ratings_mean)+(user_ratings_mean[i]-ratings_mean))+(U[i]*P[j]).sum())/2]
				predictions.append(p)

	for prediction in predictions:
		urm[prediction[0], prediction[1]] = prediction[2]

	# prepping data to send
	preds = defaultdict(list)
	for a,b,c in predictions: preds[a].append([b+1,c])

	user_preds = []
	for i in preds[uid_matrix]:
		user_preds.append(i)

	user_preds = sorted(user_preds, key=itemgetter(1), reverse=True)
	print(user_preds[:6])

recommend(usrid)