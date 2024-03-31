<?php
namespace  app;

use A\A\server\Server;

require 'vendor/autoload.php';
set_time_limit(0);
$server=new Server('127.0.0.1','12500');
$server->Start();

