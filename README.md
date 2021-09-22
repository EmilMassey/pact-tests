# Pact Test application
This branch is the version using dockerized Pact Broker. If you want to see example without the broker and using JSON file instead, 
see the [`main` branch](https://github.com/EmilMassey/pact-tests).

## Requirements
* PHP 8.0

## Setup
Run 
```
composer install
docker-compose up -d
```

## Run consumer tests
`vendor/bin/phpunit -c phpunit.consumer.xml`

The pact file will be generated in `/pacts`.

## Run provider tests
`vendor/bin/phpunit -c phpunit.provider.xml`