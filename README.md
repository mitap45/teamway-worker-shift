### Worker Shift API

This project is sample worker shift api project.
Client can list workers, add worker and add shift to worker

###### Prerequisites

1. Php 8.0^
2. [Symfony CLI] (https://symfony.com/download) (If you use internal symfony server)
3. Composer (Installed globally)
4. Mysql 8.0^
5. Web server (Symfony internal server can be used)

###### Installation

`git clone https://github.com/mitap45/teamway-worker-shift.git`
`composer install`

Now you need to modify DATABASE_URL variable for your local mysql connection

`php bin/console doctrine:database:create` creating db in the DATABASE_URL 
`php bin/console doctrine:migrations:migrate` running migrations to db

###### API
Assuming that you are using symfony internal server which runs on 127.0.0.1:8000

1. GET 127.0.0.1:8000/worker
2. POST 127.0.0.1:8000/worker/new 
2. POST 127.0.0.1:8000/worker/new-shift

for additional info about api see teamway.postman_collection.json