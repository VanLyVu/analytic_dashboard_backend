# Analytic Dashboard Backend

## Design API

### 1. API get hotel list

#### Endpoint

```
GET:/api/hotel_reports/hotels
```

Depend on detail requirement we can move this api to `/api/hotels`

#### Request
```
Nothing
```

#### Response
```
{
    [
        {
            "id" : 1,
            "name" : "Hotel 1"
        },
        {
            "id" : 1,
            "name" : "Hotel 1"
        },
    ]
}
```

### 2. Get hotel review report

#### Endpoint

```
GET:/api/hotel_reports/show
```

#### Request params
```
[
    "hotel" : hotel_id,
    "date_from" : Start date of report,
    "date_to: : End date of report    
]
```

#### Response 

```
{
    "hotel_id" : 1,
    "date_from" : "2021-03-04",
    "date_to" : "2021-03-30",
    "date_group" : "daily", // "weekly", or "monthly"
    "review_dates" : [
        {
            "date" : "2021-03-04", // date|start date of week|start date of month
            "review_count" : 140,
            "average_score" : 87.00
        },
        {
            "date" : "2021-03-05",
            "review_count" : 57,
            "average_score" : 90.50
        }
    ]
}

```

## Database

### table `hotel`

```
{
    id: int,
    name: varchar(255)
}
charset: utf8mb4
data size: 1 ~ 10000
```

### tables `review`

```
{
    id: int,
    hotel_id: int, index
    score: int (1 to 100),
    comment: text,
    created_date: datetime, index
}
charset: utf8mb4
data size: 100 ~ 100000 per hotel
```

## How to install and run backend

**Assume that you are in folder that will contains the source code**

### 0. Prerequisite

You have to install [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git) 
or [Docker](https://docs.docker.com/get-docker/) first.


### 1. Create project folder
```
$ mkdir vanlyvu && cd vanlyvu
``` 

### 2. Clone source code and cd to source code
```
$ git clone https://github.com/VanLyVu/analytic_dashboard_backend.git
$ cd analytic_dashboard_backend
```

### 3. Build docker
```
$ docker-compose up -d --build
```

### 4. Run composer install to install vendor libraries
```
$ docker exec vvly-backend-php composer install
```

### 5. Migrate DB and populate sample data
```
$ docker exec -it vvly-backend-php bash
$ bin/console doctrine:migrations:migrate
choose yes
$ bin/console doctrine:fixtures:load
choose yes
$ exit
```

### 6. Create test database for unittest

I did setup the `/docker-entrypoint-initdb.d` inside `docker-composer.yaml` file
but somehow it didn't work. I don't have time to look into it so we have 
to login to db container to run it manually
```
$docker exec -it vvly-backend-db bash
$mysql -uroot -proot
DROP DATABASE IF EXISTS analytic_test;
CREATE DATABASE analytic_test CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
GRANT ALL PRIVILEGES ON analytic_test.* TO 'analytic'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
exit;
$exit
``` 

### 7. Run Unittest
```
docker exec vvly-backend-php ./vendor/bin/phpunit
```

### 8. Check API
go to browser/Postman and check 2 api below:

http://127.0.0.1:8000/api/hotel_reports/hotels

http://127.0.0.1:8000/api/hotel_reports/show?hotel_id=11&date_from=2021-10-01&date_to=2021-11-17   
(11 is hotel_id from above API)

## PROBLEMS AND IMPROVEMENT
* This is the first I make a project with Symfony so the code may not be clean. Please comment anything so I will refactor later.

* Because the data from review table will become bigger along the time but rarely change the past data, so we can create a batch to calculate the review of hotel per day. Therefor, for each hotel we will have maximum: 10 year * 365 days = 3650 records. That's not too big.

* Incase of Laravel, Tayler Otwell make a cool framework `laravel/octane` based on `Swoole` and `Loadrunner`, so I think we can use `Swoole` and `Loadrunner` to boot the booting time of Symfony framework.

* I will write more Unit Test to cover the logic of application

* Integrate with Github Action to run CI for project