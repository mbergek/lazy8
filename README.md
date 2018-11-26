# Lazy8Web

Lazy8Web is a book-keeping ledger program based on the Yii PHP framework.

Copyright (C) 2010 Thomas Dilts

## Requirements

- Mysql
- Web server (e.g. Apache2, Nginx or IIS)
- PHP
- Yii framwork

This version has been tested with PHP 7.2 and Yii version 1.1.20.

## How to install

1. Ensure that you meet the requirements. Your php setup file php.ini must have the row: memory_limit = 32M or better yet memory_limit = 64M. PHP’s default value is normally 16M which is too little for anybody.
2. Download Lazy8Web and unzip it into the web root of your computer/web-hotel. In the same folder you then download the Yii framwork.
3. You should then have the directories lazy8 and yii in the root.
4. Make sure lazy8/assets and lazy8/protected/runtime are read-write.
5. Temporarily make the directory lazy8/protected/config read-write.
6. Create a database within the MySQL server and give the database any name you want but remember the name and the username and the password.
7. Point your web browser to the root directory plus /lazy8 or perhaps /lazy8/index.php
8. Enter into the browser your mysql database information and hit “try to connect.” If the connection is successful it can take a few seconds to initialize everything so be patient.
9. Log in with username admin and password admin
10. Change the login username and password.
11. Make lazy8/protected/config/main.php read-only.
12. Add your users and setup what editing rights your users will have.
