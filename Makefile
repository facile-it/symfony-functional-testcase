pre-commit-check: rector code-style-fix phpstan test

rector:
	@vendor/bin/rector

phpstan:
	@vendor/bin/phpstan analyse

code-style-fix:
	@vendor/bin/php-cs-fixer fix --verbose --ansi

test:
	@vendor/bin/phpunit

test-stop-on-failure:
	@vendor/bin/phpunit --stop-on-failure
