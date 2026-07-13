API_PLACEMARKERS_DATABASE_DIR := $(patsubst %/,%,$(dir $(abspath $(lastword $(MAKEFILE_LIST)))))
PROJECT_ROOT := $(abspath $(API_PLACEMARKERS_DATABASE_DIR)/../..)
include $(PROJECT_ROOT)/config.mk

.PHONY: api-placemarkers-database-init api-placemarkers-database-build api-placemarkers-database-up api-placemarkers-database-down api-placemarkers-database-test-unit

api-placemarkers-database-init:
	@echo "composer зависимости"
	docker compose -f $(API_PLACEMARKERS_DATABASE_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-database-php-cli composer install --optimize-autoloader --no-interaction
	@echo 'Обновляю автозагрузчик Composer...';
	docker compose -f $(API_PLACEMARKERS_DATABASE_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-database-php-cli composer dump-autoload --optimize;
	@echo 'Генерирую JWT ключи...';
	docker compose -f $(API_PLACEMARKERS_DATABASE_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-database-php-cli php bin/console lexik:jwt:generate-keypair --skip-if-exists

api-placemarkers-database-build:
	@echo build api-placemarkers-database
	docker compose -f $(API_PLACEMARKERS_DATABASE_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) build

api-placemarkers-database-up:
	@echo up api-placemarkers-database
	docker compose -f $(API_PLACEMARKERS_DATABASE_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) up -d api-placemarkers-database

api-placemarkers-database-down:
	@echo down api-placemarkers-database
	docker compose -f $(API_PLACEMARKERS_DATABASE_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) down -v

api-placemarkers-database-test-unit:
	docker compose -f $(API_PLACEMARKERS_DATABASE_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-database-php-cli vendor/bin/phpunit
