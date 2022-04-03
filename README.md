# RFTF Backend
Built with LaravelPHP 9.x and PHP 8.1<br />
Doc: https://laravel.com/docs/9.x/<br />

To start this project with ease, kindly start your docker instance and follow the instructions below :)

## Local Setup

```
git clone { repo url }

composer install

cp .env.example .env

./vendor/bin/sail up -d

./vendor/bin/sail artisan migrate:fresh --seed

```

## API Collection
Find the collection and the environment named fincra_collection and fincra_collection_env respectively in the root of the project.

If you need clarifications email me @ rabo.sandbox@gmail.com

## Issues faced during Development
Time.
## Assumptions
None.
## ToDo(s)
TDD 
## Feedbacks
I could have used contracts more to make my service hyper-powerful.

