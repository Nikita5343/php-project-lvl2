install:
	composer install
lint:
	composer run-script phpcs -- --standard=PSR12 bin src tests
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml