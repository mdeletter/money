# Running composer (with docker)
docker build -t money/composer -f Dockerfile_composer .
docker run --rm -v $PWD:/srv money/composer update

# Running phpunit (with docker)
docker build -t money/phpunit -f Dockerfile_phpunit .
docker run --rm -v $PWD:/srv money/phpunit test/MoneyTest.php
