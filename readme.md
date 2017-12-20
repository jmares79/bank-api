# I Soft Bet API challenge

To create a Laravel project to simulate an bank transaction item process line that creates them, and be a data supplier to a Front End to show/manage those transactions.

It consists of a REST JSON API to handle the creation and loading of transactions & some static users for authentication.

## Structure of the project

ISoft REST API is based on the [Laravel framework](https://laravel.com/), using several models to map back end tables.

The reason for using Laravel is that is an up-to-date modern MVC framework, that has all needed capabilites for building, maintaining documenting and testing any project in a painless way.

It also has Symfony components, which takes the best parts of another great framework, and last but not least, it has an amazing documentation, both [written](https://laravel.com/) or in [video lessons](https://laracasts.com/)

### Data models

The project is structured with the following resources, as follows:

* __Transaction resource__ Which provides both a controller and a service for managing all transactions
* __User resource__ Which handles all login and auth 
* Several __Models__ Which handles the business info for and to the DDBB
* __Migrations && Factory seeders__ Which helps creating all the DDBB Schema AND provides fake data to test the API

In a nutshell, __Transaction__  is a master table, that contains (in a proper production enviromnet) all the core data for the business to run. It usually get updates only, as the product base is supposedly to grow.

__Users__ is the table where the equivalent of customers are, kept to a minimum for scoping purposes.

### Controllers

The requests are handled by `App\Http\Controllers\*` that provides a set of actions to be called on every request.

As in every Laravel project, each action is mapped to a route in `routes\api.php`. The routes and mappings are self explanatory, but I'll explain them:

* HTTP GET `api/transactions/{customerId}` - Returns all transactions for a certain customer
* HTTP GET `api/transaction/{customerId}/{transactionId}` - Returns a transactions for a certain customer and transaction id
* HTTP GET `api/transactions/{customerId}/{amount}/{year}/{month}/{day}/{offset}/{limit}` - Returns a transactions list according for a certain filters passed as parameters.
* HTTP POST `api/transaction` - Creates a new transaction
* HTTP PUT `api/transaction` - Updates a new transaction
* HTTP DELETE `api/transaction/{transactionId}` - Deletes an existing transaction

## Installation

Just clone this repo to any desired folder (either a XAMPP htdocs, Docker PHP container or anything that suits you) and execute `composer install` in the command line.

Start your web server & MySQL server (for developing purposes I use the built in that PHP has) typing `php artisan serve` in the project folder command line and using any MySQL server you prefer; then create both a `bet-bank` & `bet-bank-testing` database for migrations to run properly.

After that, create the schema in the desired DDBB and run `php artisan migrate` to run migrations and create schema tables.
If desired, a `php artisan db:seed` could be executed, in order to populate the DDBB with fake data

```
If any of those DDBBs seems incorrect, just change the names in the .env file located at the root of the structure. Also, there are some dummy seeds created for testing purposes.
```
I developed and QA test it using [Postman](https://www.getpostman.com/postman)
End with an example of getting some data out of the system or using it for a little demo

## Tests

In order to accomplish proper refactoring of the code, several integration test were provided.

For a matter of timing, not class-to-class unit tests were developed, but it's importan to point out that a high rate of code coverage is paramount when developing a medium to big size software.

The provide test are in `test\Feature` folder, using the API that Laravel provides (Is a combination of PHPUNIT functions and some core JSON handling methods).

The tests make some HTTP requests to all and every single endpoint of the project, and checks whether the HTTP status returned is the corresponding to the type of payload/request, and also checks that the response structure is correct.

For more details check the tests, are pretty self explanatory.

The execution of the tests are in the form `vendor/phpunit/phpunit/phpunit` for executing all tests, of using the `vendor/phpunit/phpunit/phpunit --filter <pattern>` for a single test.

Please check the [PHPUnit documentation](https://phpunit.de/manual/5.7/en/index.html) for more details.

### Why those tests?

I always like to test as much as I can every single application, in order (obviously) to prevent any single error or bug to reach production stage.

However, sometimes these assignments lacks the proper amount of time for create all the desired tests (Unit, Behavioural & Integration), so, which tests I can show that in a nutshell tells wheter the API is working properly or not?

I decided to create a set of __integration tests__ to properly check in a quick loop whether the API is returning the results it should, or something nasty happened in between.

The tests performs an AJAX request to a certain endpoint (GET Transaction, POST Transaction, etc), with a specific payload that iterates for each test. That means, each test receives a different series of payloads, some valid and some not, to properly test that both the response body and HTTP Status are the correct ones.

For example, `testCreateTransaction` receives several options for `$payload`, `$httpStatus` and `$expectedResponse`, in order to execute every possible path of execution, asserting them agains the returned values.

```
public function testCreateTransaction($payload, $httpStatus, $expectedResponse)
```

Out of Scope I left Unit tests & behavioural tests  (like the ones Behat suite provides), but let's give a quick example of what would I test with them:

__TransactionService__ has a set of public methods that handles the business logic of creation/updating/retrieval and deletion of a transaction. 

We can create a single unit test for each method, mocking all necessary data and checking that the return of the method matches the desired functionality.

One nice, although slow, method would be create a single test file for each set of methods (GET, POST, etc), in order to follow somehow an `Open/Closed` principle of not modify a test case one it's working, polluting it with more tests and fake data, which is phrone to errors __(This idea was left outside of the scope with the actual tests for lack of time)__


