curl -i http://localhost:8888/project
HTTP/1.1 200 OK
Host: localhost:8888
Connection: close
X-Powered-By: PHP/5.4.40
Set-Cookie: PHPSESSID=qunrdbi0no2700edkj0uhf88m5; path=/
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Pragma: no-cache
Content-Type: application/hal+json

{"_links":{"self":{"href":"http:\/\/localhost:8888\/project"}},"_embedded":{"project":[{"projectId":1,"accountId":1,"projectName":"Test project","created":"2015-08-05 10:59:43","modified":"2015-08-05 10:59:43"},{"projectId":2,"accountId":1,"projectName":"Oh, I shouldn\u0027t like THAT!\u0027 \u0027Oh, you.","created":"2011-03-19 14:00:16","modified":"2014-01-19 05:41:46"},{"projectId":4,"accountId":1,"projectName":"Soup of the birds hurried off at once: one.","created":"2008-02-12 15:25:04","modified":"2005-09-06 16:31:10"},{"projectId":5,"accountId":1,"projectName":"YOU are, first.\u0027 \u0027Why?\u0027 said the King,.","created":"2011-12-06 08:15:18","modified":"2012-07-01 05:14:45"},{"projectId":6,"accountId":1,"projectName":"For instance, if you could keep it to be a.","created":"2008-07-04 15:47:04","modified":"2012-09-21 14:51:26"},{"projectId":9,"accountId":1,"projectName":"Panther received knife and fork with a.","created":"2014-05-09 14:15:22","modified":"2006-07-11 13:33:27"},{"projectId":10,"accountId":1,"projectName":"Hatter. \u0027I told you butter wouldn\u0027t suit.","created":"2013-09-12 15:48:23","modified":"2013-11-16 11:12:13"},{"projectId":13,"accountId":1,"projectName":"Just at this moment Five, who had spoken.","created":"2013-08-29 12:50:03","modified":"2015-05-22 10:53:11"},{"projectId":15,"accountId":1,"projectName":"The baby grunted again, so that it seemed.","created":"2008-09-23 11:14:47","modified":"2011-04-26 19:34:24"},{"projectId":18,"accountId":1,"projectName":"Alice felt dreadfully puzzled. The Hatter\u0027s.","created":"2005-11-01 18:50:13","modified":"2006-02-23 00:13:45"},{"projectId":21,"accountId":1,"projectName":"Alice, thinking it was too much pepper in.","created":"2012-08-27 04:46:29","modified":"2009-11-01 12:44:33"},{"projectId":23,"accountId":1,"projectName":"An obstacle that came between Him, and.","created":"2008-02-01 05:49:03","modified":"2010-06-06 12:16:13"},{"projectId":9728,"accountId":1,"projectName":"Alice; \u0027that\u0027s not at all a proper way of.","created":"2014-05-04 11:15:05","modified":"2007-01-07 17:27:28"},{"projectId":15717,"accountId":1,"projectName":"Mary Ann, what ARE you talking to?\u0027 said.","created":"2013-11-16 11:09:43","modified":"2010-09-21 13:58:59"}]},"total_items":14}

curl -i http://localhost:8888/project/4
HTTP/1.1 200 OK
Host: localhost:8888
Connection: close
X-Powered-By: PHP/5.4.40
Set-Cookie: PHPSESSID=a8e8v0tanve5128vdthj43es20; path=/
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Pragma: no-cache
Content-Type: application/hal+json

{"projectId":4,"accountId":1,"projectName":"Soup of the birds hurried off at once: one.","created":"2008-02-12 15:25:04","modified":"2005-09-06 16:31:10","_links":{"self":{"href":"http:\/\/localhost:8888\/project\/4"}}}
