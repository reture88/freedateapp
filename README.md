# freedateapp

Laravel 12 / PHP 8.4 backend skeleton using Docker, MariaDB, Redis, and a
hand-rolled CQRS/DDD module structure.

## Local development

Copy the environment file before first start:

```bash
cp .env.example .env
docker compose up -d
docker compose exec php php artisan key:generate --force
docker compose exec php php artisan migrate
docker compose exec php php artisan db:seed
```

The app is served through nginx at `http://localhost:8080`.

Useful checks:

```bash
docker compose exec php composer test
docker compose exec php composer phpstan
docker compose exec php composer cs-check
```
