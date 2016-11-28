There are some general steps to follow to restore the source codes:

1. Optional, but strongly recommended) If working locally, create a virtualhost for the each chapter (e.g: chapter1.dev , chapter2.dev etc.), set the root path of the virtualhost to the folder "public" (except chapter8, which describes how to remove the public segment), and set your hosts file to identify the created virtualhost,
2. Upload all files into the virtualhost's/web server's root, and make sure paths are set correctly.
3. open the app/config/app.php , and set the value of the key "'url'" according to your (virtual)host.
4. open the app/config/database.php, and set your database credentials. The authors used MySQL during the session, but it's not forced. You can use any database driver after setting the credentials.
4. Run the command "php artisan migrate" to run all the migrations provided in the source code.
5. Navigate through your (virtual)host from your browser. If everything is set correctly, you are ready to go.

Notes:

* In chapter 7, the host needs to be accessible online for the queue service to access, and an email sending system should be set on the server, so unless they are set, the system won't work totally.
* In chapter 8, the segment "public" is removed, so the (virtual)host root needs to be edited as described in the chapter.
* In chapter 10, after migrating the table, database seeder command needs to be run as described in the chapter.