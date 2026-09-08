# Laravel EIX Pricing Host

Minimal Laravel 13 reference host for
[`gybra/laravel-eix-pricing`](https://github.com/Gybra/laravel-eix-pricing).
All EIX discovery, ingestion, persistence, scheduling, and quote API behavior
belongs to the package; this repository supplies runtime infrastructure only.

## Requirements

- PHP 8.4+
- Composer 2
- PostgreSQL for deployment

## Docker

``` bash
composer install
cp .env.example .env
php artisan key:generate
docker compose build
docker compose up -d app scheduler
docker compose run --rm app php artisan migrate --force
```

The app is available at `http://localhost:8000`. The scheduler runs the
package-owned import schedule in a separate container. Migrations and manual
imports remain explicit operator actions and are never run automatically at
container startup.

For development without Docker:

``` bash
php artisan migrate
php artisan serve
```

The package is installed from Packagist as `gybra/laravel-eix-pricing:^0.2`.

Check the application and package route:

``` bash
curl http://localhost:8000/up
curl http://localhost:8000/api/quotes/IE000EOFR2K5
```

Run the package importer manually:

``` bash
php artisan eix:import
```

## Supabase PostgreSQL

Set `DB_URL` to a Supabase direct connection or Session Pooler URL and keep
`DB_SSLMODE=require`. Prefer the direct connection when the Docker host has
IPv6 connectivity; otherwise use the Session Pooler on port 5432. Do not use
the Transaction Pooler as the default Laravel connection.

``` dotenv
DB_CONNECTION=pgsql
DB_URL=postgresql://USER:PASSWORD@HOST:5432/postgres
DB_SSLMODE=require
EIX_DB_CONNECTION=pgsql
```

URL-encode reserved characters in the password. The database cache store is
shared by the app and scheduler, allowing package import locks and
single-server scheduling without Redis.

## Cloudflare R2

Create an R2 API token scoped to the private source bucket with object read
and write permissions. Configure Laravel's standard S3-compatible disk:

``` dotenv
AWS_ACCESS_KEY_ID=R2_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY=R2_SECRET_ACCESS_KEY
AWS_DEFAULT_REGION=auto
AWS_BUCKET=R2_BUCKET_NAME
AWS_ENDPOINT=https://ACCOUNT_ID.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=false
EIX_STORAGE_DISK=s3
EIX_STORAGE_PREFIX=eix
```

No public bucket URL is required. EIX source objects are transient: the
package deletes the R2 object and local temporary file after every successful
or failed import attempt.

## Quality checks

``` bash
composer test
composer format:check
composer analyse
composer audit --locked --no-interaction
```

## License

MIT. See [LICENSE](LICENSE).
