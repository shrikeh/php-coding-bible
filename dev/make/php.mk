#!make

.composer-%:
	docker compose run --entrypoint="composer $*" "${APP_CONTAINER}";

install:
	$(MAKE) .composer-install;

update:
	$(info [+] Make: Running composer update...)
	$(MAKE) .composer-update;

metrics:
	$(info [+] Make: Generating metrics...)
	$(MAKE) .composer-metrics;

phpcs:
	$(info [+] Make: Running Codesniffer)
	$(MAKE) .composer-phpcs;

behat:
	$(info [+] Make: Running composer behat...)
	$(MAKE) .composer-behat;

fix:
	$(info [+] Make: Running composer fix...)
	$(MAKE) .composer-fix;

phpunit:
	$(info [+] Make: Running composer phpunit...)
	$(MAKE) .composer-phpunit;

psalm:
	$(info [+] Make: Running composer psalm...)
	$(MAKE) .composer-psalm;

infection:
	$(info [+] Make: Running composer infection...)
	$(MAKE) .composer-infection;

.init:
	$(MAKE) .composer-install;

.test:
	$(info [+] Make: 'Running all tests defined in `composer test`)
	$(MAKE) .composer-test;

.quality:
	echo 'Checking branch for quality...'
	$(MAKE) .composer-quality;

.craft:
	$(MAKE) .composer-craft;
