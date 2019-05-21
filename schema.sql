/**
@author evilnapsis
@brief Modelo de base de datos
**/
create database kalabashare;
use kalabashare;

create table user(
	id int not null auto_increment primary key,
	name varchar(50) ,
	lastname varchar(50) ,
	username varchar(50),
	email varchar(255) ,
	password varchar(60) ,
	is_active boolean  default 1,
	is_admin boolean  default 0,
	created_at datetime 
);


insert into user(name,lastname,username,email,password,is_active,is_admin,created_at) value("Admin","","admin","",sha1(md5("admin")),1,1,NOW());


create table category (
	id int not null auto_increment primary key,
	name varchar(200) ,
	short_name varchar(200) ,
	in_home boolean  default 0,
	in_menu boolean  default 0,
	is_active boolean  default 0
);

insert into category (name,short_name,is_active) value ("Basico","basico",1);

create table client (
	id int not null auto_increment primary key,
	name varchar(50),
	lastname varchar(50),
	email varchar(255),
	phone varchar(255),
	address varchar(255),
	password varchar(60),
	is_active boolean default 1,
	created_at datetime
);


create table post (
	id int not null auto_increment primary key,
	short_name varchar(20) ,
	name varchar(200) ,
	code varchar(200) ,
	description varchar(1000) ,
	offer_txt varchar(1000) ,
	image varchar(255),	
	link varchar(255),	
	file varchar(255),	
	is_featured boolean  default 0,
	is_public boolean  default 0,
	created_at datetime ,
	order_at datetime ,
	category_id int,
	client_id int,
	foreign key(category_id) references category(id),
	foreign key(client_id) references client(id)
);



create table post_view(
	id int not null auto_increment primary key,
	viewer_id int,
	post_id int,
	created_at datetime,
	realip varchar(16),
	foreign key (viewer_id) references user(id),
	foreign key (post_id) references post(id)
);

/*
create table client (
	id int not null auto_increment primary key,
	name varchar(50) ,
	lastname varchar(50) ,
	email varchar(255) ,
	phone varchar(255) ,
	address varchar(255) ,
	password varchar(60) ,
	is_active boolean  default 1,
	created_at datetime 
);
*/

create table slide (
	id int not null auto_increment primary key,
	title varchar(200) ,
	image varchar(255),	
	is_public boolean  default 0,
	position int ,
	created_at datetime 
);
/**
kind:
1- texto
2- entero
3- checkbox
4- reference
**/


create table configuration(
	id int not null auto_increment primary key,
	name varchar(100) unique,
	label varchar(200),
	kind int,
	val text,
	cfg_id int default 1
);

insert into configuration(name,label,kind,val) value ("general_main_title","Titulo Principal",1,"KALABASHARE");
