-- BEGIN APPENDIX --

-- Create tables

CREATE TABLE salesmen(
id int AUTO_INCREMENT PRIMARY KEY,
years_exp int NOT NULL,
education enum ('none', 'diploma', 'degree', 'doctorate') NOT NULL,
name varchar(255) NOT NULL
);

CREATE TABLE stockitems(
id int AUTO_INCREMENT PRIMARY KEY,
price double NOT NULL,
name varchar(255) NOT NULL
);

CREATE TABLE clients(
id int AUTO_INCREMENT PRIMARY KEY,
sector enum ('goverment', 'industry', 'commerce'),
name varchar(255) NOT NULL
);

CREATE TABLE sales(
id int AUTO_INCREMENT PRIMARY KEY,
salesmanid int NOT NULL REFERENCES salesmen(id),
stockitemid int NOT NULL REFERENCES stockitems(id),
clientid int NOT NULL REFERENCES clients(id),
qty smallint UNSIGNED NOT NULL
);

-- Insert data

INSERT INTO salesmen(years_exp, education, name) VALUES(1, 'none', 'Smith');
INSERT INTO salesmen(years_exp, education, name) VALUES(5, 'none', 'Jones');
INSERT INTO salesmen(years_exp, education, name) VALUES(2, 'diploma', 
'Hicks');
INSERT INTO salesmen(years_exp, education, name) VALUES(8, 'diploma', 
'Flannery');
INSERT INTO salesmen(years_exp, education, name) VALUES(4, 'degree', 
'Jorm');
INSERT INTO salesmen(years_exp, education, name) VALUES(1, 'degree', 
'Mordecai');
INSERT INTO salesmen(years_exp, education, name) VALUES(2, 'doctorate', 
'Simmons');
INSERT INTO salesmen(years_exp, education, name) VALUES(11, 'doctorate', 
'Brookes');

INSERT INTO stockitems (price, name) VALUES(250.50, 'CPU');
INSERT INTO stockitems (price, name) VALUES(180, 'RAM');
INSERT INTO stockitems (price, name) VALUES(610.20, 'Monitor');
INSERT INTO stockitems (price, name) VALUES(34.95, 'Sound Card');
INSERT INTO stockitems (price, name) VALUES(1100, 'Low end System');
INSERT INTO stockitems (price, name) VALUES(1999, 'High end System');

INSERT INTO clients(sector, name) VALUES('government', 'Fisheries Dept.');
INSERT INTO clients(sector, name) VALUES('government', 'Tax Office');
INSERT INTO clients(sector, name) VALUES('industry', 'ABC Manufacturing');
INSERT INTO clients(sector, name) VALUES('industry', 'XYZ Shipping');
INSERT INTO clients(sector, name) VALUES('commerce', 'Con Consulting');
INSERT INTO clients(sector, name) VALUES('commerce', 'Megacorp');

INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(1, 1, 1, 
1);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(1, 6, 3, 
11);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(1, 4, 5, 
4);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(1, 2, 1, 
2);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(2, 2, 2, 
3);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(2, 2, 6, 
4);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(2, 5, 5, 
7);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(2, 6, 5, 
1);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(2, 2, 3, 
7);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(2, 3, 3, 
2);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(2, 4, 4, 
10);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(3, 1, 4, 
1);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(3, 2, 1, 
2);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(3, 6, 5, 
18);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(4, 1, 2, 
3);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(4, 3, 4, 
4);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(4, 2, 6, 
6);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(4, 4, 4, 
7);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(4, 6, 2, 
2);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(4, 5, 1, 
1);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(5, 6, 3, 
3);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(5, 5, 5, 
4);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(5, 4, 3, 
9);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(6, 3, 2, 
6);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(6, 2, 2, 
5);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(6, 1, 2, 
13);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(6, 2, 4, 
21);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(6, 4, 6, 
3);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(6, 6, 6, 
19);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(6, 5, 5, 
1);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(6, 3, 2, 
1);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 1, 3, 
20);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 3, 5, 
5);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 3, 1, 
7);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 4, 3, 
11);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 5, 2, 
12);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 6, 4, 
3);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 6, 5, 
1);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 1, 5, 
12);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 1, 3, 
14);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 5, 1, 
17);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 5, 3, 
8);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 5, 2, 
9);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(7, 6, 2, 
10);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(8, 1, 2, 
10);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(8, 2, 1, 
20);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(8, 4, 2, 
6);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(8, 3, 3, 
7);
INSERT INTO sales(salesmanid, stockitemid, clientid, qty) VALUES(8, 3, 4, 
3);


-- END APPENDIX --
