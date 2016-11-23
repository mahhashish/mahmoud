
CREATE TABLE abc_sales (
  id int(11) NOT NULL auto_increment,
  date date NOT NULL default '0000-00-00',
  channel_id int(11) NOT NULL default '0',
  item_id int(11) NOT NULL default '0',
  state char(2) NOT NULL default '',
  qty int(11) NOT NULL default '1',
  rev decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (id),
  KEY date (date)
) TYPE=MyISAM COMMENT='ABC Co. example sales data';

CREATE TABLE abc_catalog (
  id int(11) NOT NULL auto_increment,
  short_desc varchar(10) NOT NULL default '',
  item_desc varchar(30) NOT NULL default '',
  unit_price decimal(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='ABC Co. item catalog';

INSERT INTO abc_catalog VALUES (1, 'A', 'Widget A', '59.95');
INSERT INTO abc_catalog VALUES (2, 'B', 'Widget B', '12.99');
INSERT INTO abc_catalog VALUES (3, 'C', 'Widget C', '195.00');
INSERT INTO abc_catalog VALUES (4, 'D', 'Widget D', '899.00');
INSERT INTO abc_catalog VALUES (5, 'E', 'Widget E', '1499.50');

CREATE TABLE abc_channel (
  id int(11) NOT NULL auto_increment,
  short_desc varchar(20) NOT NULL default '',
  channel varchar(50) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY short_desc (short_desc,channel)
) TYPE=MyISAM COMMENT='ABC Co. example channels';

INSERT INTO abc_channel VALUES (1, 'Web', 'Internet Online Purchase');
INSERT INTO abc_channel VALUES (2, 'Phone', 'Phone Catalog Sales');
INSERT INTO abc_channel VALUES (3, 'Retail', 'Retail Store Purchase');

CREATE TABLE abc_forecast (
  id int(11) NOT NULL auto_increment,
  date date NOT NULL default '0000-00-00',
  channel_id int(11) NOT NULL default '0',
  item_id int(11) NOT NULL default '0',
  region_id int(11) NOT NULL default '0',
  qty int(11) NOT NULL default '1',
  rev decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (id),
  KEY date (date),
  KEY channel_id (channel_id),
  KEY item_id (item_id),
  KEY region_id (region_id)
) TYPE=MyISAM COMMENT='ABC Co. example forecast data';

CREATE TABLE abc_region (
  id int(11) NOT NULL auto_increment,
  region varchar(50) NOT NULL default '',
  map_x float NOT NULL default '0.5',
  map_y float NOT NULL default '0.5',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='ABC Co. - Regions';

INSERT INTO abc_region VALUES (1, 'West', '0.23', '0.35');
INSERT INTO abc_region VALUES (2, 'Central', '0.48', '0.55');
INSERT INTO abc_region VALUES (3, 'North-East', '0.86', '0.33');
INSERT INTO abc_region VALUES (4, 'South', '0.71', '0.58');

CREATE TABLE abc_state_region (
  state_abbr char(2) NOT NULL default '',
  region_id int(11) NOT NULL default '0',
  PRIMARY KEY  (state_abbr),
  KEY region_id (region_id)
) TYPE=MyISAM COMMENT='ABC Co. State to Region Cross Reference';

INSERT INTO abc_state_region VALUES ('AL', 4);
INSERT INTO abc_state_region VALUES ('AR', 2);
INSERT INTO abc_state_region VALUES ('AZ', 1);
INSERT INTO abc_state_region VALUES ('CA', 1);
INSERT INTO abc_state_region VALUES ('CO', 1);
INSERT INTO abc_state_region VALUES ('CT', 3);
INSERT INTO abc_state_region VALUES ('DC', 3);
INSERT INTO abc_state_region VALUES ('DE', 3);
INSERT INTO abc_state_region VALUES ('FL', 4);
INSERT INTO abc_state_region VALUES ('GA', 4);
INSERT INTO abc_state_region VALUES ('IA', 2);
INSERT INTO abc_state_region VALUES ('ID', 1);
INSERT INTO abc_state_region VALUES ('IL', 2);
INSERT INTO abc_state_region VALUES ('IN', 2);
INSERT INTO abc_state_region VALUES ('KS', 2);
INSERT INTO abc_state_region VALUES ('KY', 4);
INSERT INTO abc_state_region VALUES ('LA', 2);
INSERT INTO abc_state_region VALUES ('MA', 3);
INSERT INTO abc_state_region VALUES ('MD', 3);
INSERT INTO abc_state_region VALUES ('ME', 3);
INSERT INTO abc_state_region VALUES ('MI', 2);
INSERT INTO abc_state_region VALUES ('MN', 2);
INSERT INTO abc_state_region VALUES ('MO', 2);
INSERT INTO abc_state_region VALUES ('MS', 2);
INSERT INTO abc_state_region VALUES ('MT', 1);
INSERT INTO abc_state_region VALUES ('NC', 4);
INSERT INTO abc_state_region VALUES ('ND', 2);
INSERT INTO abc_state_region VALUES ('NE', 2);
INSERT INTO abc_state_region VALUES ('NH', 3);
INSERT INTO abc_state_region VALUES ('NJ', 3);
INSERT INTO abc_state_region VALUES ('NM', 1);
INSERT INTO abc_state_region VALUES ('NV', 1);
INSERT INTO abc_state_region VALUES ('NY', 3);
INSERT INTO abc_state_region VALUES ('OH', 2);
INSERT INTO abc_state_region VALUES ('OK', 2);
INSERT INTO abc_state_region VALUES ('OR', 1);
INSERT INTO abc_state_region VALUES ('PA', 3);
INSERT INTO abc_state_region VALUES ('RI', 3);
INSERT INTO abc_state_region VALUES ('SC', 4);
INSERT INTO abc_state_region VALUES ('SD', 2);
INSERT INTO abc_state_region VALUES ('TN', 4);
INSERT INTO abc_state_region VALUES ('TX', 2);
INSERT INTO abc_state_region VALUES ('UT', 1);
INSERT INTO abc_state_region VALUES ('VA', 4);
INSERT INTO abc_state_region VALUES ('VT', 3);
INSERT INTO abc_state_region VALUES ('WA', 1);
INSERT INTO abc_state_region VALUES ('WI', 2);
INSERT INTO abc_state_region VALUES ('WV', 3);
INSERT INTO abc_state_region VALUES ('WY', 1);

    
