# https://www.gnu.org/software/make/manual/make.html

DOCKER   = docker-compose
WEB      = $(DOCKER) exec php
COMMAND  = bin/console
SYMFONY  = $(WEB) $(COMMAND)
COMPOSER = $(WEB) composer

##
## PROJECT
## -------

start: ## start project
	$(DOCKER) up --build --remove-orphans --force-recreate --detach

stop: ## stop project
	$(DOCKER) stop

kill:
	$(DOCKER) kill
	$(DOCKER) down --volumes --remove-orphans

restart: kill start ## restart project

enter: ## install vendor packages
	$(WEB) bash

composer: ## install vendor packages
	$(COMPOSER) install

db: ## prepare database from scratch
	$(SYMFONY) doctrine:database:drop --force --if-exists
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:migrations:migrate -n -q
	$(SYMFONY) doctrine:schema:validate
	$(SYMFONY) doctrine:fixtures:load --append --group=dev

nd: ## run command without docker (for example `make nd some-command`)
	$(eval DOCKER := \#)
	$(eval WEB := )

.PHONY: start stop kill restart composer db nd

#
# HELP
# ----

help:
	@cat $(MAKEFILE_LIST) | grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-24s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/' && printf "\n"

.PHONY: help

.DEFAULT_GOAL := help

-include Makefile.override.mk
