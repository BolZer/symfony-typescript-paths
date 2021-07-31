cs_fix:
	vendor/bin/php-cs-fixer fix ./

lint:
	./vendor/bin/psalm

test:
	./vendor/bin/phpunit ./Tests

docker_build:
	docker build -t php_tests .

docker_tests:
	docker run php_tests