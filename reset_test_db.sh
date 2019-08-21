 bin/console doctrine:schema:drop --force --env=test &&
 bin/console doctrine:schema:create --env=test &&
 bin/console doctrine:fixtures:load --env=test
