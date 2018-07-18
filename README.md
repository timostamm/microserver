# PHP Microserver

Minimal web server based on Symfony components.

It is very simple to use: 

```PHP
$server = new Server();
$server->addController(SimpleController::class);
$server->serve()->send();
```

For an example how to use composer autoloading, see htdocs/index.php


Features:
* Support for symfony `@Route` annotations
* Support for route parameters as controller arguments (scalar types)
* Robust exception handling
* JSON exception formatting (by using JsonServer)
* Controller constructor arguments can be provided by calling addController() with a factory function as second argument
* Nothing else 