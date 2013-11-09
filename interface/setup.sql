CREATE DATABASE IF NOT EXISTS NoticeBoard;

USE NoticeBoard;

DROP TABLE IF EXISTS PI;

create table PI ( 
	Id int(4) unsigned NOT NULL auto_increment,
	IP varchar(15) NOT NULL,
	Hostel int(2),
	User varchar(20),
	Pass varchar (32),
	Port int(4),
	PRIMARY KEY (Id),
	UNIQUE KEY IP(IP)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS queue;

create table queue ( 
	Type varchar(15) NOT NULL,
	Path varchar(100) NOT NULL,
	Date datetime NOT NULL,
	Hostel int(4) NOT NULL,
	PRIMARY KEY (Date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

