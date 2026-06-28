# Render Deployment

This app is ready to deploy on Render as a Docker web service using `render.yaml`.

Before the first deploy, set `APP_KEY` in Render to a Laravel key generated locally:

```sh
php artisan key:generate --show
```

The default blueprint uses SQLite and runs migrations on startup with `RUN_MIGRATIONS=true`. To load the initial admin user, languages, menu, and pages, temporarily set `RUN_SEEDERS=true` for the first deploy, then set it back to `false`.

For persistent production data, attach a Render disk for the SQLite database or switch the Render env vars to a managed database connection.
