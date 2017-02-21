DROP VIEW links;
CREATE VIEW links AS
SELECT 
	 lg.group_name	
	,lg.group_desc	
	,lg.group_ord	
	,l.name		
	,l.url			
	,l.link_desc
	,l.link_ord
	,lg.link_group_id
	,l.link_id
	,l.date_crtd
	,l.date_last_chngd
FROM link_group lg JOIN link l ON (lg.link_group_id = l.link_group_fk)
ORDER BY 
	 lg.group_ord
	,l.link_ord
	;

GRANT ALL ON links TO GROUP links_admin;
GRANT SELECT ON links to GROUP links_user;

DROP VIEW groups;
CREATE VIEW groups AS
SELECT
  	 lg.link_group_id
	,lg.group_name
	,lg.group_desc
	,lg.group_ord
	,count(l.link_id) AS link_cnt
	,max(l.date_crtd) AS link_add
	,max(l.date_last_chngd) AS link_upd
FROM link_group lg LEFT JOIN link l ON (lg.link_group_id = l.link_group_fk)
GROUP BY
  	 lg.link_group_id
	,lg.group_name
	,lg.group_desc
	,lg.group_ord
ORDER BY 
	 lg.group_ord;

GRANT ALL ON groups TO GROUP links_admin;
GRANT SELECT ON groups to GROUP links_user;
