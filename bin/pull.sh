git pull
composer install --no-dev --no-interaction
php bin/console doctrine:migrations:migrate -vv --no-interaction
php bin/console cache:clear
