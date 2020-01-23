# Test Project for Sun Finance

To install the project, run the following commands

## Installation

````
$ git clone git@github.com:veliseev/sunfinance-vladimir-test.git
````
```
$ cd sunfinance-vladimir-test
```
```
$ docker-compose build
```
```
$ docker-compose up -d 
```
```
$ docker-compose exec php composer install  
```
```
$ docker-compose exec php php bin/console doctrine:migrations:migrate  
```
```
$ docker-compose exec php php bin/console assets:install  
```

And you're ready to go.

Documentation will be available here http://localhost, you'll be available to test endpoints there as well.

## Important Notes
* I didn't have enough time to figure out how to properly configure nginx within docker contanier, that's why
    * application uses port 80, so make sure it's not used in the system, before running containers
*  .env file was commited to the repo with sensitive data, but it's for test purposes only! That's not a good idea, to store credentials in the repos, so that's not how I normally do.
---
Best Regards,

Vladimir.  
 



