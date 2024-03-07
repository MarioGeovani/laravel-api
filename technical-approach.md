# Technical Approach

## Laravel Sail
For local hosting and development, Laravel Sail was chosen.
It enables a fast way to spin up an environment without concerns.
For more information, refer to the [Laravel Sail documentation](https://laravel.com/docs/10.x/sail).

## Authentication with Laravel Sanctum
At the architectural level, since a REST API was requested, the login method was added to provide the authentication layer to the API.
This was possible by using [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum).

## Development Approaches
At the development level, several approaches were applied:

### Dependency Injection
Dependency Injection greatly assists in improving test coverage.

### Repository Pattern
The Repository Pattern was used to create an abstraction between the controller and logic.
For detailed information, refer to this [article](https://www.twilio.com/en-us/blog/repository-pattern-in-laravel-application).

### Form Request
Form Request is a powerful way to control validation requests.
In this case, all validations are organized into small code lines.

### Small Controllers
Small Controllers are a consequence of using Form Request and the repository pattern.
Ultimately, this leads to improved readability in the code.

### Method Chaining
In some cases, method chaining was applied to enhance readability and organization.

## Test coverage
Tests: 27 passed (75 assertions)

### Packages:
For this development was used the Laravel Excel a well known package to handle Excel files:
- [Details](https://docs.laravel-excel.com/3.0/getting-started/)
- [Installation](https://docs.laravel-excel.com/3.0/getting-started/installation.html)


## API Consumers
All requests and Endpoints are documented on [Postman](https://documenter.getpostman.com/view/6976430/2sA2xfXYWR)