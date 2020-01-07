<?php

define('DEBUGG', true);

define('DB_NAME', 'ruah'); //database name
define('DB_USER', 'root'); //database user
define('DB_PASSWORD', ''); //database password
define('DB_HOST', '127.0.0.1'); //database host (use IP address to avoid DNS lookup)

define('DEFAULT_CONTROLLER', 'Home'); //default controller if there isn't one defined in the url
define('DEFAULT_LAYOUT', 'default'); //if no layout is set in the controller use this layout

define('PROOT', '/ruah/'); //set this to '/' for a live server, PROOT=> project root

define('SITE_TITLE', 'RUAH MVC Framework'); //this will be used if no site title is set
define('MENU_BRAND', 'RUAH'); //brand text in the menu

define('CURRENT_USER_SESSION_NAME', 'kowBoieXIksiZielaQIETB'); //session name for logged in user
define('REMEMBER_ME_COOKIE_NAME', 'EPIJBS904klsJOJkabozroLIO43'); //cookie name for logged in user remember  me
define('REMEMBER_ME_COOKIE_EXPIRY', 2592000); //30 days in seconds, for remmeber me cookie to live

define('ACCESS_RESTRICTED', 'Restricted'); //controller name for the restricted redirect