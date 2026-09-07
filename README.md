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

The package is temporarily resolved from its public Composer repository at
`dev-main`. The host will pin `^0.1` after the first package release.

Check the application and package route:

``` bash
curl http://localhost:8000/up
curl http://localhost:8000/api/quotes/IE000EOFR2K5
```

Run the package importer manually:

``` bash
php artisan eix:import
```

## Quality checks

``` bash
composer test
composer format:check
composer analyse
composer audit --locked --no-interaction
```

Supabase PostgreSQL and Cloudflare R2 environment setup are added in the next
host deployment slice.

## License

MIT. See [LICENSE](LICENSE).
