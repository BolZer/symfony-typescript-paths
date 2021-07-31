cs_fix:
	vendor/bin/php-cs-fixer fix ./

lint:
	./vendor/bin/psalm

test:
	./vendor/bin/phpunit ./Tests

docker_build:
	docker build -t ts-path-tests .

docker_tests:
	docker run ts-path-tests