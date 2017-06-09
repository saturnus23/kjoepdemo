use kjoepdemo;

/* verwijder de tabellen in reverse order van foreign keys */
/*
DROP TABLE IF EXISTS courseowner;
DROP TABLE IF EXISTS portfolio;
DROP TABLE IF EXISTS course;
DROP TABLE IF EXISTS profile;
DROP TABLE IF EXISTS association;
DROP TABLE IF EXISTS role;
DROP TABLE IF EXISTS roletype;
DROP TABLE IF EXISTS person;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS school;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS mentorship;
*/

/* maak tabellen en geef initiele vulling */
/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS user
(	name	varchar(20)		PRIMARY KEY
,	status	varchar(10)		not null
);

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS person
(	id			int				AUTO_INCREMENT		PRIMARY KEY
,	username	varchar(20)		not null
,	CONSTRAINT	FOREIGN KEY		person_user (username)		REFERENCES		user(name)
);

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS roletype
(	name	varchar(20)		PRIMARY KEY
,	status	varchar(10)		not null
);

INSERT INTO roletype VALUES ('Leerling', 'active');
INSERT INTO roletype VALUES ('Mentor', 'active');
INSERT INTO roletype VALUES ('Docent', 'active');
INSERT INTO roletype VALUES ('Ouder', 'active');
INSERT INTO roletype VALUES ('Staflid', 'active');

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS school
(	id			int				AUTO_INCREMENT		PRIMARY KEY
,	name		varchar(50)		not null
);

INSERT INTO school VALUES (null, 'Zweinstein College');
INSERT INTO school VALUES (null, 'Linneaus Gymnasium');
INSERT INTO school VALUES (null, 'Veluwse Scholengemeenschap');

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS role
(	id			int			AUTO_INCREMENT		PRIMARY KEY
,	personid	int			NOT NULL
,	role		varchar(20)	NOT NULL
,	CONSTRAINT	FOREIGN KEY	role_person (personid)		REFERENCES		person(id)
,	CONSTRAINT	FOREIGN KEY	role_roletype (role)		REFERENCES		roletype(name)
);

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS association
(	id			int		AUTO_INCREMENT		PRIMARY KEY
,	schoolid	int		NOT NULL
,	roleid		int		NOT NULL
,	CONSTRAINT	FOREIGN KEY	association_school (schoolid)	REFERENCES	school(id)
,	CONSTRAINT	FOREIGN KEY	association_role (roleid)		REFERENCES	role(id)
);

CREATE TABLE IF NOT EXISTS course
(	id			int				AUTO_INCREMENT		PRIMARY KEY
,	name		varchar(200)	not null
,	start		varchar(20)		not null 	/* nog even geen input met datumformaten*/
,	weight		smallint		null
,	description varchar(4000)	null
);

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS courseowner
(	id			int			AUTO_INCREMENT		PRIMARY KEY
,	courseid	int			not null
,	roleid		int			not null
);

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS portfolio
(	id			int			AUTO_INCREMENT		PRIMARY KEY
,	courseid	int			not null
,	pupilid		int			not null
,	approved	char(3)		null
,	result		char(6)		null
,	marks		smallint	null	/* only valid with result=passed */
);

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS message
(	id			int			AUTO_INCREMENT		PRIMARY KEY
,	fromid		int			not null
,	toid		int			not null
,	subject		varchar(80)		null
,	text		varchar(4000)	null
);

/* -------------------------------------- */
CREATE TABLE IF NOT EXISTS mentorship
(	id			int			AUTO_INCREMENT		PRIMARY KEY
,	pupilid		int			not null
,	mentorid	int			not null
);

CREATE TABLE `profile` (
  id int(11) 		NOT NULL AUTO_INCREMENT,
  personid 			int(11) NOT NULL,
  allmotto 			varchar(200) DEFAULT NULL,
  allfavsport 		varchar(20) DEFAULT NULL,
  pupillevel 		varchar(20) DEFAULT NULL,
  pupilfavsubject 	varchar(20) DEFAULT NULL,
  teachersubject 	varchar(20) DEFAULT NULL,
  mentorhairstyle 	varchar(20) DEFAULT NULL,
  parentnumkids 	smallint(6) DEFAULT NULL,
  stafffavcolor 	varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
