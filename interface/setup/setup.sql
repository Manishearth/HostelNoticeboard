USE test;

DROP TABLE IF EXISTS PI;

create table PI ( 
	PiID int(4) unsigned NOT NULL auto_increment,
	IP varchar(15) NOT NULL,
	Hostel varchar(10) NOT NULL,
	Uid varchar(20) NOT NULL,
	Pass varchar (32) NOT NULL,
	Port int(4) NOT NULL,
	PendLock int(4) NOT NULL DEFAULT 0,
	PRIMARY KEY (PiID),
	UNIQUE KEY IP(IP)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS queue;

create table queue ( 
	ID BIGINT unsigned NOT NULL auto_increment,
	Type varchar(15) NOT NULL,
	Path varchar(100) NOT NULL,
	Date datetime NOT NULL,
	PiID int(4) NOT NULL,
	User varchar(20) NOT NULL,
	Approved boolean NOT NULL default 0,	
	PRIMARY KEY (ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS users;

create table users (
	ID int(8) unsigned NOT NULL auto_increment,
	Permission int(2) NOT NULL DEFAULT 0,
	Name varchar(32) NOT NULL,
	Uid varchar(20) NOT NULL,
	Pass varchar (32) NOT NULL,
	Token varchar (32),
	LastAccessed datetime,
	PRIMARY KEY (ID),
	UNIQUE KEY IP(Uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

