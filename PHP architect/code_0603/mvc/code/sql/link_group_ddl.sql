DROP FUNCTION trig_upd_dates() CASCADE;
CREATE FUNCTION trig_upd_dates() RETURNS TRIGGER
AS 'BEGIN
	new.date_last_chngd := now();	
	RETURN new;
	END;
' LANGUAGE 'plpgsql';

DROP FUNCTION trig_ins_group_ord() CASCADE;
CREATE FUNCTION trig_ins_group_ord() RETURNS TRIGGER
AS 'DECLARE
  	max_ord INTEGER;
  BEGIN
  	SELECT INTO max_ord count(1) 
	  FROM link_group;
	IF max_ord IS NULL THEN
	  max_ord := 1;
	ELSE
	  max_ord := max_ord + 1;
	END IF;
  	new.group_ord := max_ord;
	RETURN new;
  END;
' LANGUAGE 'plpgsql';

--DROP SEQUENCE link_group_link_group_id_seq;
DROP TABLE link_group CASCADE;
CREATE TABLE link_group 
(
	link_group_id	serial			PRIMARY KEY,
	group_name		varchar(50)		UNIQUE NOT NULL,
	group_desc		varchar(255)	NULL,
	group_ord		integer			NULL,
	date_crtd		timestamp(0)
	  	  	  	  	with time zone	DEFAULT CURRENT_TIMESTAMP,
	date_last_chngd	timestamp(0)
	  	  	  	  	with time zone	DEFAULT CURRENT_TIMESTAMP
);
GRANT ALL ON link_group TO GROUP links_admin; 
GRANT ALL ON link_group_link_group_id_seq TO GROUP links_admin;

CREATE TRIGGER link_group_ins_ord
  BEFORE INSERT
  ON link_group
  FOR EACH ROW EXECUTE PROCEDURE trig_ins_group_ord();

CREATE TRIGGER link_group_upd
  BEFORE UPDATE
  ON link_group
  FOR EACH ROW EXECUTE PROCEDURE trig_upd_dates();

DROP FUNCTION add_link_group(VARCHAR, VARCHAR);  
CREATE FUNCTION add_link_group(VARCHAR, VARCHAR) RETURNS INTEGER AS '
  DECLARE
  	ins_group_name ALIAS FOR $1;
	ins_group_desc ALIAS FOR $2;
  BEGIN
  	INSERT INTO link_group (group_name, group_desc)
	  VALUES (ins_group_name, ins_group_desc);
	RETURN 1;
  END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;

DROP FUNCTION upd_link_group(INTEGER, VARCHAR, VARCHAR);  
CREATE FUNCTION upd_link_group(INTEGER, VARCHAR, VARCHAR) RETURNS INTEGER AS '
  DECLARE
  	upd_lg_id ALIAS FOR $1;
  	upd_group_name ALIAS FOR $2;
	upd_group_desc ALIAS FOR $3;
	grouprec link_group%ROWTYPE;
  BEGIN
  	SELECT INTO grouprec * FROM link_group WHERE link_group_id = upd_lg_id;
	IF FOUND THEN
	  IF (grouprec.group_name = upd_group_name
	  AND grouprec.group_desc = upd_group_desc) THEN
  	  	RAISE NOTICE ''all values identical, no update performed'';
	  	RETURN 1;
	  END IF;
	  --link_group exists and value changed, okay to update
	  UPDATE link_group SET
	  	 group_name = upd_group_name
		,group_desc = upd_group_desc
	  WHERE link_group_id = upd_lg_id;
	  RETURN 1;
	ELSE
	  RAISE EXCEPTION ''no link group with id % found to update'',upd_lg_id;
	  RETURN 0;
	END IF;
  END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;

 
DROP FUNCTION del_link_group(INTEGER);  
CREATE FUNCTION del_link_group(INTEGER) RETURNS INTEGER AS '
DECLARE
  del_lg_id ALIAS FOR $1;
  linkcnt INTEGER;
  max_ord INTEGER;
  grouprec link_group%ROWTYPE;
BEGIN
  --additional check for referential integrity
  SELECT INTO linkcnt count(1) as cnt FROM link WHERE link_group_fk = del_lg_id;
  IF linkcnt > 0 THEN
  	RAISE EXCEPTION ''Not okay to delete. % links still map to this group.'',linkcnt ;
   	RETURN 0;
  ELSE
  	SELECT INTO grouprec * FROM link_group WHERE link_group_id = del_lg_id;
	IF FOUND THEN
	  --make sure this group is last to preserve the order sequence
	  SELECT INTO max_ord count(1) FROM link_group;
	  IF grouprec.group_ord < max_ord THEN
	  	PERFORM ord_link_group(del_lg_id, max_ord);
	  END IF;
   	  DELETE FROM link_group WHERE link_group_id = del_lg_id;
  	  RETURN 1;
 	ELSE
	  RAISE EXCEPTION ''No link_group with id % found.'',del_lg_id;
  	  RETURN 0;
	END IF;
  END IF;
END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;

DROP FUNCTION ord_link_group(INTEGER, INTEGER);  
CREATE FUNCTION ord_link_group(INTEGER, INTEGER) RETURNS INTEGER AS '
DECLARE
  ord_lg_id ALIAS FOR $1;
  new_ord   ALIAS FOR $2;
  use_ord INTEGER;
  old_ord INTEGER;
  max_ord INTEGER;
  grouprec link_group%ROWTYPE;
BEGIN
  --verify group to change exists
  SELECT INTO grouprec * FROM link_group WHERE link_group_id = ord_lg_id;
  IF FOUND THEN
  	--verify new order is reasonable
  	IF new_ord < 1 THEN
	  RAISE EXCEPTION ''new ord % is not valid'',new_ord;
	END IF;
	use_ord := new_ord;
	SELECT INTO max_ord count(1) FROM link_group;
	IF new_ord > max_ord THEN
	  use_ord := max_ord;
	END IF;
  	old_ord := grouprec.group_ord;
	IF old_ord = use_ord THEN
	  RAISE NOTICE ''link_group % is already ord %.'',ord_lg_id,use_ord;
	  RETURN 0;
	END IF;
	--perform the reorder operation
	IF old_ord > use_ord THEN --moving up
	  UPDATE link_group 
	  SET group_ord = group_ord + 1
	  WHERE group_ord >= use_ord
	  	AND group_ord < old_ord;
	ELSE --old ord < new ord -- moving down
	  UPDATE link_group
	  SET group_ord = group_ord - 1
	  WHERE group_ord > old_ord
	  	AND group_ord <= use_ord;
	END IF;
	UPDATE link_group
	SET group_ord = use_ord
	WHERE link_group_id = ord_lg_id;
	RETURN 1;
  ELSE
  	RAISE EXCEPTION ''No link_group with id % found.'',ord_lg_id;
	RETURN 0;
  END IF;
END;
' LANGUAGE 'plpgsql'
  SECURITY DEFINER;
 
--create some test data
SELECT add_link_group(
  	 'Application Infrastructure'
	,'Contains links related to technologies used in this application.');
SELECT add_link_group(
  	 'Jason''s Publications'
	,'Contains links to additional articles I wrote.');
SELECT add_link_group(
  	 'Other Stuff'
	,'Contains links to other locations of interest on the internet.');
  
