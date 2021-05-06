# Mobile Subscription Management

## About

This is a web application to manage mobile apps subscriptions which are installed on different devices using Andriod or
IOS

Tech stack used in this project is:

+ Php 7.4
+ Laravel 8
+ MySql
+ Redis

## Installation On localhost

To install and run this project take the following steps:

+ clone the repository
+ cd to project directory
+ type `./app.sh up` (This will build the and starts container)
+ type `./app.sh bash` (This way you can access container bash)
+ Type `php artisan migrade` (This will create required tables for the application)
+ To seed applications table you can type `php artisan db:seed`
+ Api base url will be http://localhost:6001
+ You can access phpmyadmin to see database and tables using http://localhost:6002

