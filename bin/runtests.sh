php bin/console doctrine:schema:drop --force --env=test
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:fixtures:load --env=test -n
php bin/phpunit
