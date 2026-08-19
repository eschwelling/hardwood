# Deploying Rafters

The app runs as a Docker container and needs a Postgres database. The container
runs migrations and the tag seeder on every boot, then serves on `$PORT`.

## Database configuration

The app reads a single environment variable for the database:

```
DB_URL=postgresql://USER:PASSWORD@HOST:PORT/DATABASE?sslmode=require
```

Laravel parses the URL into host, port, database, username, password, and
sslmode, so `DB_URL` is the only database variable you need — don't set
`DB_HOST`/`DB_USERNAME`/etc. alongside it.

The container image bakes a `.env` from `.env.example` at build time for local
Docker Compose use. Real environment variables always take precedence over that
file (Laravel loads dotenv in immutable mode), so values set by the host
platform win.

## Required environment variables

| Variable   | Value                                                  |
|------------|--------------------------------------------------------|
| `APP_KEY`  | `php artisan key:generate --show` output (base64:...)  |
| `APP_ENV`  | `production`                                           |
| `APP_DEBUG`| `false`                                                |
| `APP_URL`  | The service's public URL                               |
| `DB_CONNECTION` | `pgsql`                                           |
| `DB_URL`   | See above                                              |

## Using Supabase as the database

Supabase's **direct** connection (`db.<ref>.supabase.co`) is IPv6-only on new
projects. Most hosts (Render included) need IPv4, so use the **connection
pooler** instead.

Prefer the **session pooler** (port `5432`) over the transaction pooler
(port `6543`): session mode supports prepared statements, which Laravel's PDO
driver and `artisan migrate` rely on. Transaction mode requires disabling
prepared statements and can fail during migrations.

To get the string: Supabase dashboard → your project → **Connect** → **Session
pooler** → copy the URI. If you don't know the database password (for example
the project was created via API), reset it under **Project Settings → Database
→ Reset database password**, then substitute it into the URI.

## Deploying on Render

The service is a Docker web service built from the repo's `Dockerfile`; no
build or start command is needed. Set the environment variables above under the
service's **Environment** tab. Render supplies `PORT` automatically.

Note that Render's free tier allows only one free Postgres instance per
account, which is why an external database is used here.
