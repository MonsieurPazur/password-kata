drop table if exists users, user_validation_tokens;
create table users
(
	id serial
		constraint users_pk
			primary key,
	email varchar(255) not null,
	password varchar(255) not null
);
create unique index users_email_uindex
	on users (email);
create table user_validation_tokens
(
	id serial
		constraint user_validation_tokens_pk
			primary key,
	user_id int not null
		constraint user_validation_tokens_users_id_fk
			references users
				on update cascade on delete cascade,
	token varchar(255) not null,
	expires_at date not null
);

