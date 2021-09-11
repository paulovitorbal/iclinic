.DEFAULT_GOAL := all

ifeq (composer,$(firstword $(MAKECMDGOALS)))
  COMPOSER_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(COMPOSER_ARGS):;@:)
  PWD := $(shell pwd)
endif

composer:
	docker run --rm --interactive --tty --volume $(PWD):/app composer $(COMPOSER_ARGS)

tests:
	docker compose run php-fpm php vendor/bin/phpunit

psalm:
	docker compose run php-fpm php vendor/bin/psalm

phpcs:
	docker compose run php-fpm php vendor/bin/phpcs

code-analysis: phpcs psalm

all: tests code-analysis

.PHONY: tests code-analysis phpcs psalm
