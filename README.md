# gc-communicator

Steps after clone -
Install Composer - https://getcomposer.org/download/
Install Symfony - https://symfony.com/download

1. cd into working directory of repo.
2. Run "composer install".
3. Create /.env.local file.
4. Copy mysql database config line from /.env into file and fill database username, password, ip, and database name.
5. Run "php bin/console doctrine:fixtures:load".


For tests -

1. cd into working directory of repo.
2. Create /.env.test.local file.
3. Copy mysql database line from /.env.local. All credentials should remain the same.
4. Run "php bin/console --env=test doctrine:database:create".
5. Run "php bin/console --env=test doctrine:schema:create".
6. Use "php ./vendor/bin/phpunit" to run all tests.
