CREATE TABLE benutzer (
	name VARCHAR(50) PRIMARY KEY,
	password VARCHAR(50) NOT NULL
);

CREATE TABLE nimmtteil (
	benutzer VARCHAR(50),
	chat INT,
	PRIMARY KEY (benutzer, chat),
	FOREIGN KEY (benutzer) REFERENCES benutzer(name)
);

CREATE TABLE nachricht (
	id INT PRIMARY KEY,
	text VARCHAR(600) NOT NULL,
	zeit TIMESTAMP NOT NULL,
	chat INT NOT NULL,
	sender VARCHAR(50) NOT NULL,
	FOREIGN KEY (sender) REFERENCES benutzer(name)
);

CREATE TABLE gelesen (
	nachricht INT,
    benutzer VARCHAR(50),
    zeit timestamp NOT NULL,
    FOREIGN KEY (nachricht) REFERENCES nachricht(id),
    FOREIGN KEY (benutzer) REFERENCES benutzer(name),
    PRIMARY KEY (nachricht, benutzer)
);