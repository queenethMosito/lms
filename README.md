NB: 1.rename folder from lms-master to lms 
    2. Git keeps ignoring the .htaccess file so i added it to a zip file, please just remove from zip file and save under the root path( lms/.htaccess)

Setup on local machine
1 Database setup
Username: dev
Password: dev
Database: lms

Ensure user dev has full priviledges on the database
Also remeber to flush priviledges 

2 Httpd file setup
Open up apache's httpd.conf file
Add the following to the bottom:

<VirtualHost 127.0.0.1:80>
  ServerName lms.localhost.com 
  DocumentRoot "C:\xampp\htdocs\lms"
  DirectoryIndex index.php
  <Directory "C:\xampp\htdocs\lms">
    AllowOverride All
    Allow from All
  </Directory>
</VirtualHost>


Restart apache service

3 hosts file

Open C:\Windows\System32\drivers\etc\hosts

Add the following entry: 127.0.0.1       lms.localhost.com

This will allow you to type in lms.localhost.com in the web browser
which will point to your local machine for the host. The virtual host
entry above will tell apache where to find the code for the incoming
domain of lms.localhost.com

4.Log in details
Username:admin@admin.co.za
Password:123456789
