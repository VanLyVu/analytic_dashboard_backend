# Analytic Dashboard Backend

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
$ bin/console doctrine:fixtures:load
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

