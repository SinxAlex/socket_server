### Multithreaded server for communication via tcp sockets

### Instalization with composer
```
composer require sinxalex/socket_server:@dev
```

>Create a file in your project directory index.php
>Specify the path to the file autoload.php
### Example for using
```
<?php
use A\A\server\Server;

require __DIR__.'\vendor\autoload.php';

$server=new Server('127.0.0.1',12500);
$server->Start();

```