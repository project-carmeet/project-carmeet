bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:update --force
bin/console doctrine:fixtures:load --no-interaction
