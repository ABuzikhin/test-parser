#!/bin/bash -ex

composer install --no-interaction --no-scripts
php bin/console assets:install --no-interaction
php bin/console cache:warmup --no-interaction --no-optional-warmers

## Create DB and load data
until php bin/console doctrine:query:sql "select 1" >/dev/null 2>&1; do
    (echo >&2 "Waiting for DB to be ready...")
    sleep 1
done

# DB
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --append