# HostList Server

A small PHP and MySQL source for the HostList Local demonstration. It serves a paginated directory of 500 deterministic, fictional IT hosts and JSON endpoints that the Firefox extension can capture.

All people, organisations and opportunities are invented. The profile generator uses a fixed seed and field-specific phrase libraries; no LLM calls or external data sources are used.

## Run locally with Docker

Install Docker Desktop, then run from this directory:

```sh
cp .env.example .env
docker compose up --build
```

Open <http://localhost:8080>. On an empty database, the `seed` service creates the schema and inserts 500 profiles before Apache starts. The MySQL data persists in the `hostlist_mysql` volume. To reset and repopulate the demo, run `docker compose down -v` followed by `docker compose up --build`.

The local `.env` is ignored by Git and is used by Docker Compose for development credentials. It is for local testing only. The PHP app reads `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` from process environment variables first, then from the `.env` file in the project root. `APP_BASE_PATH` can set the public subdirectory explicitly; when unset, it defaults to `/projects/hostlist-server`. Compose sets it to an empty string for `localhost:8080`.

## Synthetic data scripts

The same dataset generator feeds both scripts and always creates 500 records from the fixed seed:

```sh
php scripts/generate-sql.php output/hostlist-seed.sql
php scripts/seed-db.php
```

The repository includes `output/hostlist-seed.sql` as a ready-to-import snapshot of the deterministic dataset. Regenerate it with the command above whenever the profile generator changes. The SQL creates only `hostlist_hosts` and `hostlist_host_details`, then replaces their rows, so import it into the demo database when you want to reset the sample data. The database loader creates the schema and skips insertion when the host table already has data.

## Pages and data contract

- `/` is the directory. It fetches 20 records per page and opens each profile in a new browser tab.
- `/hosts/{id}` is the human-readable detail page.
- `/demo-api/hosts?pageStart=1&pageCount=20` returns `{ "records": [...], "totalRows": 500 }`. `pageStart` is one-based.
- `/demo-api/hosts/{id}` returns one flattened record with its detail fields.

The list uses `id`, `fullname`, `company`, `country`, `city`, `primary_domain`, `secondary_domain`, `primary_country`, `secondary_country`, and `languages`. The detail payload adds `host_experience_years`, `team_size`, `motivation`, `specific_skills`, `offer_extra_info`, and the `business_impact` array.

## Apache deployment

Upload the project files to the Apache document directory with PHP 8.3+, PDO MySQL, and `mod_rewrite` enabled. Allow `.htaccess` overrides so the clean page and API routes reach `index.php`. The app reads database credentials from a private PHP config file outside the public web root, using a relative path from `src/database.php`; it takes `database.dsn`, `database.username`, and `database.password` from the returned array. The local `.env` is only for Docker testing and does not need to be uploaded. If your private config is elsewhere, set `HOSTLIST_PRIVATE_CONFIG` to its filesystem path. Run the data generator on a trusted machine and import its SQL file, or run `php scripts/seed-db.php` where PHP can reach the configured MySQL service.

In `HostList-Local`, `config.json` contains only the configured site origin. The extension is scoped to `https://stefanochiabrando.com/projects/hostlist-server/*` and captures the two JSON endpoints above.

## Automatic FTP deployment

GitHub Actions deploys only `.htaccess`, `index.php`, `app.js`, `styles.css`, and `src/database.php` to the configured `FTP_SERVER_DIR` on every push to `main`; it can also be run manually. Add repository secrets `FTP_SERVER`, `FTP_USERNAME`, and `FTP_PASSWORD`, plus the repository variable `FTP_SERVER_DIR` ending with `/projects/hostlist-server/`. The workflow does not seed or modify MySQL data.
