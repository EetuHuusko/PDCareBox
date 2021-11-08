<?php

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
if (strpos($url,'localhost') !== false) {

        
DEFINE('DB_SERVER', "localhost");
DEFINE('DB_USER', "root");
DEFINE('DB_PASS', 'root');
DEFINE('DB_DATABASE', "DATABASE_NAME");

    
} else {
DEFINE('DB_SERVER', "localhost");
DEFINE('DB_USER', "DATABASE_USER");
DEFINE('DB_PASS', 'DATABASE_PASSWORD');
DEFINE('DB_DATABASE', "DATABASE_NAME");


    
}