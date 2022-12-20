cs_fix:
	PHP_CS_FIXER_IGNORE_ENV=true vendor/bin/php-cs-fixer fix ./

lint:
	./vendor/bin/psalm

test:
	./vendor/bin/phpunit ./Tests

test_js:
	npm run test

coverage:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html

docker_build:
	docker build -t ts-path-tests .

docker_tests:
	docker run ts-path-tests