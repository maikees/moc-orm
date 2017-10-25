create table tb_log
(
	id serial not null
		constraint tb_log_id_pk
			primary key,
	session text,
	prev text,
	next text,
	dt_reg timestamp default now(),
	function varchar(100),
	name varchar(100),
	ip varchar(50)
)
;

create unique index tb_log_id_uindex
	on tb_log (id)
;

