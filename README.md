# Pact Test application
## Requirements
* PHP 8.0

## Setup
Run `composer install`

## Run consumer tests
`vendor/bin/phpunit -c phpunit.consumer.xml`

The pact file will be generated in `/pacts`.

## Run provider tests
`vendor/bin/phpunit -c phpunit.provider.xml`