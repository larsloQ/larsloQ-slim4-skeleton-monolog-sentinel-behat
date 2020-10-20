Slim-4 skeleton with some simple file-based CRUD operations, logging-middleware and behat tests.


## Description
Started as the part of a project, where it acts as backend allowing to make use of JSON-EDITOR (https://github.com/josdejong/jsoneditor)  in the browser: Select a json-file, edit it, save it and set it as "current" file used on a simple website.
So all CRUD operations on the json-file need to be secured (private routes). 

### features
- logging with [monolog](https://github.com/Seldaek/monolog)
- auth with [sentinel](https://cartalyst.com/manual/sentinel/5.x)
- migrations with [davedevelopment/phpmig](https://github.com/davedevelopment/phpmig)
- some [behat](https://docs.behat.org/en/latest/) tests for endpoints and logging


## Install
1. run ```composer install```
2. add a ```.env``` file into project root. 
3. adjust the settings in *config* folder (more instructions there). 
	- *config/settings*: set paths and filenames
	- *config/database*: set database connection
3. migrate the user tables
   - run ```php cli.php status``` take id and run ```php cli.php up {ID}```
   this creates the necessary tables for ***sentinel*** 
   - do the same with ***userdata*** migration file. this adds a user (username/password) (so take care that no one can read it !!)
4. in ```public/.htaccess``` (or Apache/nginx virtual hosts) : adjust *CORS*-headers and mod-rewrite to your current env


## Configure
well there are quite a few things to configure.
1. files in config folder (use the ```.env``` file to use an other config folder)
3. set user credentials in migrations (```migrations/20191105184131_userdata.php```)  and ```tests/behat/bootstrap/UserCredentials```
4. baseUrl in ```tests/behat/behat.yml``` (also in test/python/test_endpoints.py)
4. public/.htaccess: cors, rewrite base

(TODO: make this more easy with a docker-compose or .env files)




## About middleware in Slim

- middleware (MW) in slim following LIFO (last in first out).
- middleware ***MUST*** return a http-response ( an object implementing Psr\Http\Message\ResponseInterface)
- middleware ***SHOULD*** invoke the next middleware (by calling $handler->handle($request)). When this is ***NOT*** done (and http-response is returned instead) still some other middleware might get involved (watch the order (LIFO), think concentric circles around your app) 
- So its is important to watch the order of middleware!

***types of Middleware***
- Application wide middleware
- Middleware for group of Routes / Endpoints
- Middleware for single Routes / Endpoints

***Incoming / Outgoing***

as the [graphic on offical docs](http://www.slimframework.com/docs/v4/concepts/middleware.html) shows the request passes middlewares when coming in ***AND*** going out.  
So when you call the "NEXT" via 
```$response = $handler->handle($request);```
at the end of "process" method you are working at the ***INCOMING*** part.
and when you call "NEXT" at the beginning of your procedure
you run the middleware AFTER the app has done its thing (***OUTGOING*** part).


***Example:***
You want certain routes of your application require a valid authorization, i.e. the client request needs to send a valid Auth-cookie / or bearer-token. So the AuthMiddleware checks the Cookie/Bearer and proceeds when Auth is valid (next method) or immediately returns a HTTP-response with status 403 (not allowed) WITHOUT calling ```$response = $handler->handle($request); return $response;```. That means you are stopping the execution of other procedures.

Anyway, you might want to also log this request, lets say to be able to track log-in attempts. Therefore you implement an outer middleware, which simply checks the status-code of the RESPONSE and make a log-entry when its not 200. 



## Licence
MIT

