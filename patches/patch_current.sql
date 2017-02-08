
ALTER TABLE process_serve_main ADD streetnumber varchar(20) NOT NULL default '';

ALTER TABLE process_serve_main ADD complete tinyint(1) NOT NULL default 0;
