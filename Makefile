
ifeq (composer,$(firstword $(MAKECMDGOALS)))
  COMPOSER_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(COMPOSER_ARGS):;@:)
  PWD := $(shell pwd)
endif

composer:
	docker run --rm --interactive --tty --volume $(PWD):/app composer $(COMPOSER_ARGS)

tests:
	docker compose run php-fpm php vendor/bin/phpunit

.PHONY: tests
