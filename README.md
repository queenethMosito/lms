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

