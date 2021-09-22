# Pact Test application
This branch is the version using pact JSON file. If you want to see example using the broker instead,
see the [`with-broker` branch](https://github.com/EmilMassey/pact-tests/tree/with-broker).

## Requirements
* PHP 8.0

## Setup
Run `composer install`

## Run consumer tests
`vendor/bin/phpunit -c phpunit.consumer.xml`

The pact file will be generated in `/pacts`.

## Run provider tests
`vendor/bin/phpunit -c phpunit.provider.xml`