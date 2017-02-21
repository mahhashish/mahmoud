DROP FUNCTION trig_ins_link_ord() CASCADE;
CREATE FUNCTION trig_ins_link_ord() RETURNS TRIGGER
AS 'DECLARE
  	max_ord INTEGER;
  BEGIN
  	SELECT INTO max_ord count(1) 
	  FROM link
	  WHERE link_group_fk = new.link_group_fk;
	IF max_ord IS NULL THEN
	  max_ord := 1;
	ELSE
	  max_ord := max_ord + 1;
	END IF;
  	new.link_ord := max_ord;
	RETURN new;
  END;
' LANGUAGE 'plpgsql';

DROP TABLE link CASCADE;
CREATE TABLE link 
(
	link_id			serial			PRIMARY KEY,
	link_group_fk	integer			REFERENCES link_group 
	  	  	  	  	  	  	  	  	ON UPDATE CASCADE
									ON DELETE NO ACTION
	  	  	  	  	  	  	  	  	NOT NULL,
	name			varchar(50)		NOT NULL,
	url				varchar(255)	NOT NULL,
	link_desc		varchar(255)	NULL,
	link_ord		integer			NULL,
	date_crtd		timestamp(0)
	  	  	  	  	with time zone	DEFAULT CURRENT_TIMESTAMP,
	date_last_chngd	timestamp(0)
	  	  	  	  	with time zone	DEFAULT CURRENT_TIMESTAMP
);
GRANT ALL ON link TO GROUP links_admin; 
GRANT ALL ON link_link_id_seq TO GROUP links_admin;

CREATE INDEX link_idx1 
ON link (link_group_fk, link_ord);

CREATE TRIGGER link_ins_ord
  BEFORE INSERT
  ON link
  FOR EACH ROW EXECUTE PROCEDURE trig_ins_link_ord();
CREATE TRIGGER link_upd
  BEFORE UPDATE
  ON link
  FOR EACH ROW EXECUTE PROCEDURE trig_upd_dates();

DROP FUNCTION add_link(INTEGER, VARCHAR, VARCHAR, VARCHAR);  
CREATE FUNCTION add_link(INTEGER, VARCHAR, VARCHAR, VARCHAR) RETURNS INTEGER AS '
DECLARE
  ins_link_group_fk ALIAS FOR $1;
  ins_name    	  	ALIAS FOR $2;
  ins_url 	  	  	ALIAS FOR $3;
  ins_link_desc   	ALIAS FOR $4;
BEGIN
  INSERT INTO link (link_group_fk, name, url, link_desc)
  VALUES (ins_link_group_fk, ins_name, ins_url, ins_link_desc);
  RETURN 1;
END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;

DROP FUNCTION upd_link(INTEGER, VARCHAR, VARCHAR, VARCHAR);  
CREATE FUNCTION upd_link(INTEGER, VARCHAR, VARCHAR, VARCHAR) RETURNS INTEGER AS '
DECLARE
  upd_link_id 	ALIAS FOR $1;
  upd_name 	  	ALIAS FOR $2;
  upd_url  	  	ALIAS FOR $3;
  upd_link_desc ALIAS FOR $4;
  linkrec link%ROWTYPE;
BEGIN
  SELECT INTO linkrec * FROM link WHERE link_id = upd_link_id;
  IF FOUND THEN
  	IF (linkrec.name = upd_name 
	AND linkrec.url = upd_url
	AND linkrec.link_desc = upd_link_desc) THEN
	  RAISE NOTICE ''all values identical, no update performed'';
	  RETURN 1;
	END IF;
	--link exists and some value changed, okay to update
	UPDATE link SET
	   name = upd_name
	  ,url = upd_url
	  ,link_desc = upd_link_desc
	WHERE link_id = upd_link_id;
	RETURN 1;
  ELSE
  	RAISE EXCEPTION ''no link with id % found to update'',upd_link_id;
  	RETURN 1;
  END IF;
END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;


DROP FUNCTION del_link(INTEGER);  
CREATE FUNCTION del_link(INTEGER) RETURNS INTEGER AS '
DECLARE
  del_link_id ALIAS FOR $1;
  max_ord INTEGER;
  linkrec link%ROWTYPE;
BEGIN
  SELECT INTO linkrec * FROM link WHERE link_id = del_link_id;
  IF FOUND THEN
	  --make sure this group is last to preserve the order sequence
	  SELECT INTO max_ord count(1) FROM link WHERE link_group_fk = linkrec.link_group_fk;
	  IF linkrec.link_ord < max_ord THEN
	  	PERFORM ord_link(del_link_id, max_ord);
	  END IF;
   	DELETE FROM link WHERE link_id = del_link_id;
  	RETURN 1;
  ELSE
  	RAISE EXCEPTION ''No link with that link_id found.'';
  	RETURN 0;
  END IF;
END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;

DROP FUNCTION ord_link(INTEGER, INTEGER);  
CREATE FUNCTION ord_link(INTEGER, INTEGER) RETURNS INTEGER AS '
DECLARE
  ord_link_id ALIAS FOR $1;
  new_ord     ALIAS FOR $2;
  use_ord INTEGER;
  old_ord INTEGER;
  max_ord INTEGER;
  linkrec link%ROWTYPE;
BEGIN
  --verify link to change exists
  SELECT INTO linkrec * FROM link WHERE link_id = ord_link_id;
  IF FOUND THEN
  	--verify new order is reasonable
  	IF new_ord < 1 THEN
	  RAISE EXCEPTION ''new ord % is not valid'',new_ord;
	END IF;
	use_ord := new_ord;
	SELECT INTO max_ord count(1) FROM link WHERE link_group_fk = linkrec.link_group_fk;
	IF new_ord > max_ord THEN
	  use_ord := max_ord;
	END IF;
  	old_ord := linkrec.link_ord;
	IF old_ord = use_ord THEN
	  RAISE NOTICE ''link % is already ord %.'',ord_link_id,use_ord;
	  RETURN 0;
	END IF;
	--perform the reorder operation
	IF old_ord > use_ord THEN --moving up
	  UPDATE link 
	  SET link_ord = link_ord + 1
	  WHERE link_ord >= use_ord
	  	AND link_ord < old_ord
		AND link_group_fk = linkrec.link_group_fk;
	ELSE --old ord < new ord -- moving down
	  UPDATE link
	  SET link_ord = link_ord - 1
	  WHERE link_ord > old_ord
	  	AND link_ord <= use_ord
		AND link_group_fk = linkrec.link_group_fk;
	END IF;
	UPDATE link
	SET link_ord = use_ord
	WHERE link_id = ord_link_id;
	RETURN 1;
  ELSE
  	RAISE EXCEPTION ''No link with id % found.'',ord_link_id;
	RETURN 0;
  END IF;
END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;

DROP FUNCTION chgrp_link(INTEGER, INTEGER);
CREATE FUNCTION chgrp_link(INTEGER, INTEGER) RETURNS INTEGER AS '
DECLARE
  ch_link_id  ALIAS FOR $1;
  ch_group_id ALIAS FOR $2;
  max_ord INTEGER;
  linkrec link%ROWTYPE;
  grouprec link_group%ROWTYPE;
BEGIN
  SELECT INTO linkrec * FROM link WHERE link_id = ch_link_id;
  IF FOUND THEN
  	IF linkrec.link_group_fk = ch_group_id THEN
	  RAISE NOTICE ''link % is already in group %'',ch_link_id,ch_group_id;
	  RETURN 0;
	END IF;
  	SELECT INTO grouprec * FROM link_group WHERE link_group_id = ch_group_id;
	IF FOUND THEN
	  SELECT INTO max_ord count(1) FROM link WHERE link_group_fk = linkrec.link_group_fk;
	  IF linkrec.link_ord < max_ord THEN
	  	PERFORM ord_link(ch_link_id, max_ord);
	  END IF;
	  SELECT INTO max_ord count(1) FROM link WHERE link_group_fk = ch_group_id;
	  UPDATE link
	  SET link_group_fk = ch_group_id
	  	  ,link_ord = max_ord + 1
	  WHERE link_id = ch_link_id;
	  RETURN 1;
	ELSE
	  RAISE EXCEPTION ''no group with id % found'',ch_group_id;
	  RETURN 0;
	END IF;
  ELSE
  	RAISE EXCEPTION ''no link with id % found'',ch_link_id;
	RETURN 0;
  END IF;
END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;

SELECT add_link(1, 'PHP', 'http://www.php.net/', 'The PHP web site');
SELECT add_link(1, 'Phrame', 'http://phrame.itsd.ttu.edu/', 'The Phrame MVC architecture');
SELECT add_link(1, 'ADOdb', 'http://php.weblogs.com/adodb/', 'The ADOdb database abstraction layer');
SELECT add_link(1, 'Smarty', 'http://smarty.php.net/', 'The Smarty template system');
SELECT add_link(1, 'Eclipse', 'http://www.sunlight.tmfweb.nl/eclipse/', 'The Eclipse OO library');
SELECT add_link(2, 'PEAR::DB and Smarty', 'http://www.zend.com/zend/tut/tutsweatpart1.php', 'An introduction to database abstraction and the Smarty template system');
SELECT add_link(2, 'PEAR::DB and Smarty-Part 2', 'http://www.zend.com/zend/tut/tutsweatpart2.php', 'A follow up to the first article with a general web application framework described');
SELECT add_link(2, 'Developing Professional Quality Graphs with PHP', 'http://www.zend.com/zend/tut/tutsweat3.php', 'A tutorial on the use of the JpGraph library');
SELECT add_link(2, 'Coding PHP with register_globals Off', 'http://www.zend.com/zend/art/art-sweat4.php', 'An article geared to help people with the transition to register_globals off, a good primer for how to talk to PHP');
SELECT add_link(2, 'PHP Graphics Handbook', 'http://www.wrox.com/dynamic/books/download.aspx?isbn=1861008368', 'Published by Wrox, who immediatly went bankrupt, Sorry if the link does not work');
SELECT add_link(2, 'Advanced Features in JpGraph', 'http://www.phparch.com/issuedata/2003/apr/sample.php', 'Delves into the "Power Features" of the JpGraph library');
SELECT add_link(2, 'An Introduction to MVC Using PHP', 'http://www.phparch.com/issuedata/2003/may/', 'Part one of this series on using the Model-View-Controller design pattern');
SELECT add_link(3, 'PHP|Architect', 'http://www.phparch.com/', 'A great source for phpinfo()');
SELECT add_link(3, 'LinuxLaboratory.org', 'http://www.linuxlaboratory.org/', 'A site geared toward production Linux administrators, though all are welcome.');
