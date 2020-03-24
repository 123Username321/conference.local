CREATE TABLE reports (
	id SERIAL PRIMARY KEY, 
	user_id INTEGER NOT NULL REFERENCES users (id), 
	user_info VARCHAR(256) NOT NULL, 
	category SMALLINT NOT NULL, 
	name VARCHAR(64) NOT NULL, 
	description VARCHAR(256) NOT NULL, 
	speech_file VARCHAR(64) NOT NULL, 
	present_file VARCHAR(64) NOT NULL
);

