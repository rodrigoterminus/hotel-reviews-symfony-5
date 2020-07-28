Hotel Reviews API
===

This repo is a fictional Hotel Reviews REST API built with, 
[PHP 7.4](https://www.php.net/releases/7_4_0.php), [Symfony Framework 5.1](https://symfony.com/releases/5.1), [MySQL](https://www.mysql.com), and [Docker](https://www.docker.com/).

## How to run it
    
    $ cd docker
    $ docker-compose up -d
    
### Loading dependencies
    
    $ docker-compose exec php composer install
    
### Creating database and loading fixtures
    
    $ docker-compose exec php bin/console doctrine:database:create
    $ docker-compose exec php bin/console doctrine:schema:create
    $ docker-compose exec php bin/console doctrine:fixture:load --no-interaction
    
### Testing

    $ docker-compose exec php bin/phpunit
    
### Stoping it

    $ docker-compose down

## API

### Overtime

    GET /api/hotels/{id}/reviews/overtime
    
#### Params

Parameter | Description | Param Type | Data Type
----------|-------------|------------|----------
 **id*** | Hotel ID | path | integer
 **starting_date*** | Starting date for the date range _(YYYY-MM-DD)_ | query | string
 **ending_date*** | Ending date for the date range _(YYYY-MM-DD)_ | query | string
 
 #### Response
 
    HTTP/1.1 200 OK
    Content-Type: application/json
    
    [
      {
        "review-count": 1,
        "average-score": 77,
        "date-group": "2020-06-03"
      }
    ]

---

### Benchmark

    GET /api/hotels/{id}/benchmark
    
#### Params

Parameter | Description | Param Type | Data Type
----------|-------------|------------|----------
 **id*** | Hotel ID | path | integer
 **starting_date*** | Starting date for the date range _(YYYY-MM-DD)_ | query | string
 **ending_date*** | Ending date for the date range _(YYYY-MM-DD)_ | query | string
 
 #### Response
 
    HTTP/1.1 200 OK
    Content-Type: application/json
    
    {
      "hotel-average": 55.0645,
      "total-average": 53.10118,
      "quarter": "top"
    }
    
## Todo
- [ ] Extend ApiTrait to validate request params (query and body)
- [ ] Write ApiTrait tests
- [ ] Write KebabCaseNameCoverter tests
- [ ] Write functional tests
- [ ] Write Statistics tests
- [ ] Write Repository tests
- [ ] Abstract query building logic into separate classes