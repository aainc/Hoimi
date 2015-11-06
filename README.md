# Hoimi

## description

Hoimi is yet another micro mvc framework.
The feature is URL Routing only.
It is specialized for Web API Server.

1. Fast
2. Simple
3. Minimum

Hoimi is inspired by [dietcake](http://dietcake.github.io).
Hoimi is depend on composer/autoload.php

## manual

### keywords

- ActionClass
- Router
- Response

#### ActionClass

ActionClass is "Entity of URL".
it need to extends \Hoimi\BaseAction.
it will have methods. GET, POST, DELETE, PUT.
if the URL need not to have a few methods, you need not implement them.
Requests for them will be 404 NotFound.

for example routing URL: "/path", ActionClass: "\App\action\Index"

```php
<?php namespace App\action;
class Index extends \Hoimi\BaseAction
{
    public function get ()
    {
        return new \Hoimi\Response\JSON(array('data' => 'this is get method'));
    }

    public function post ()
    {
        return new \Hoimi\Response\JSON(array('data' => 'this is post method'));
    }

    public function put ()
    {
        return new \Hoimi\Response\JSON(array('data' => 'this is put method'));
    }

    public function delete ()
    {
        return new \Hoimi\Response\JSON(array('data' => 'this is delete method'));
    }
}
```

this is Tests with curl command.

```command
curl http://localhost/path
>>> {"data":"this is get method"}

curl http://localhost/path -d "key=val"
>>> {"data":"this is post method"}

curl -X put http://localhost/path -d "key=val"
>>> {"data":"this is put method"}

curl -X delete http://localhost/path -d "key=val"
>>> {"data":"this is put method"}
```


#### Router

Router is routing "URL to ClassPath".
You need to implement Routing configration.
like this.

```php
<?php
\Hoimi\Router::getInstance()->setRoutes(array(
    // 'path' => 'className',
    '/batch_request' => 'Hoimi\BatchRequest',
    '/path' => '\App\actions\Index.php',
));
```


#### Response

ActionClass's methods need to return "Response Object".
Resposne Object is instance of class implemented "\Hoimi\Response" interface.
but I think it is return "\Hoimi\Response\JSON" normally.
if you want a any response, you see namespace "\Hoimi\Response".


### directories

Recommended directory structure in use Hoimi is this.

- ./
    - src
        - app
            - actions (ActionClasses)
            - classes (model, dao, util)
            - resources
                - config.php
            - boostrap.php
        - lib
            - Hoimi/src/Hoimi(Hoimi root)
            - Mahotora/src/Mahotora(Mahotora root)
        - vendor(for Composer)
        - composer.json
    - docroot
        - index.php

### Sapmple Source

#### docroot/index.php

this is index.php
it is processing all requests with mod_rewrite(Apache) or nginx config

```php
<?php
$router = require realpath(__DIR__ . '/../src/app/bootstrap.php');
$config = require realpath(__DIR__ . '/../src/app/resources/config.php');
$request = new \Hoimi\Request($_SERVER, $_REQUEST);
$response = null;
try {
    list($action, $method) = $router->run($request);
    $action->setConfig($config);
    $action->setRequest($request);
    if ($action->useSessionVariables()) {
        $session = \Hoimi\Session::getInstance($request, $config);
        $action->setSession($session);
        $session->start();
        $response = $action->$method();
        $session->flush();
    } else {
        $response = $action->$method();
    }
} catch (\Hoimi\Exception $e) {
    $response = $e->buildResponse();
} catch (\Exception $e) {
    $response = new \Hoimi\Response\Error($e);
}
foreach ($response->getHeaders() as $header) {
    header($header);
}
echo $response->getContent();
```

#### app/boostrap.php

this file is Routing Settings.

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new \Exception("STRICT: $errno $errstr $errfile $errline");
});
require realpath(__DIR__ . '/../vendor/autoload.php');
return \Hoimi\Router::getInstance()->setRoutes(array(
    // 'path' => 'className',
    '/batch_request' => 'Hoimi\BatchRequest',
    '/helloworld' => '\HW\actions\HelloWorld',
));
```

#### app/resources/config.php

this is configration file.
for example  Database Setting, Session Setting or O

```php
<?php
$config = new \Hoimi\Config();
return $config
    ->setConfig(array(
        // 'SESSION_ID_NAME' => 'hoge',
        // 'SESSION_MAX_LIFETIME' => 1000000 ,
        // 'SESSION_STORE' => 'DB',
        'DB_HOST' => 'hostname',
        'DB_USER' => 'username',
        'DB_PASS' => 'apssword',
        'DB_NAME' => 'dtabaseName',
    ));
```
#### app/actions/HelloWorld.php

this is ActionClass.

```php
<?php nampespace HW\actions;
class HelloWorld extends \Hoimi\BaseAction
{
    public function get ()
    {
        return \Hoimi\Response\JSON(array('data' => 'Hello,World'));
    }
}
```

## See Also

- Mahotora(Minimum Database Library)
- Scruit(Generator, Compressor for Hoimi and Mahotora)
- Loula(curl wrapper)

## License

The MIT License

Copyright (c) 2005-2015 Allied Architects Co.,Ltd.

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
