# Mobile Subscription Management

## About

This is a web application to manage mobile apps subscriptions which are installed on different devices using Andriod or
IOS

Tech stack used in this project is:

* Php 7.4
* Laravel 8
* MySql
* Redis

## Installation On localhost

To install and run this project take the following steps:

* clone the repository
* cd to project directory
* type `./app.sh up` (This will build the and starts container)
* type `./app.sh bash` (This way you can access container bash)
* Type `php artisan migrade` (This will create required tables for the application)
* To seed applications table you can type `php artisan db:seed`
* Api base url will be http://localhost:6001
* You can access phpmyadmin to see database and tables using http://localhost:6002

## Resources

* Postman collection is located [here](https://github.com/hosseinko/subscription-management/tree/master/postman)
* Database schema is located [here](https://github.com/hosseinko/subscription-management/tree/master/schema)

## Further reading

* All parts of the application are designed and implemented as required
* Using Docker makes it happen to scale the application easily when required. Although it would not be enough and other
  solutions like clustering and load-balancing are required.
* There is the Redis cache on some endpoints of this application (ex: API Check Subscription) to improve application
  performance
* There are different cron jobs to call different command and run jobs in parallel
* The main worker of the application is being called by a cron job every minute, and it uses its lock mechanism to
  prevent overlapping
* When hitting rate limits a job will be created for subscription record to be checked in the future again
* When a callback operation fails, a job will be created to send data in future
* Factory design pattern is used when it comes to working with different stores API to make code more both readable and
  maintainable
* Repository design pattern is used for caching mechanism which makes it easy for the application to become integrated
  with other caching solutions

