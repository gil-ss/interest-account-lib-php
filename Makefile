# Makefile for Interest Account Library

.PHONY: build up down shell test coverage clean

DOCKER_COMPOSE = docker compose

# Build the Docker container
build:
	$(DOCKER_COMPOSE) build

# Start the container in detached mode
up:
	$(DOCKER_COMPOSE) up -d

# Stop and remove the container
down:
	$(DOCKER_COMPOSE) down

# Open an interactive shell inside the container
shell:
	docker exec -it interest-account-library sh

# Run PHPUnit tests
test:
	$(DOCKER_COMPOSE) exec php vendor/bin/phpunit tests

# Run PHPUnit test coverage
coverage:
	$(DOCKER_COMPOSE) exec php vendor/bin/phpunit --coverage-clover=coverage.xml --coverage-html coverage-report

# Run PHPUnit test coverage
coverage:
	$(DOCKER_COMPOSE) exec php vendor/bin/phpunit --coverage-html coverage-report

# Clean up vendor directory and remove containers
clean:
	rm -rf vendor composer.lock
	$(DOCKER_COMPOSE) down --rmi all --volumes --remove-orphans

# Show up container's logs
logs:
	$(DOCKER_COMPOSE) logs php

# Run composer install
install:
	$(DOCKER_COMPOSE) exec php composer install