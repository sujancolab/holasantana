# Render Deployment

This app is ready to deploy on Render as a Docker web service using `render.yaml`.

Before the first deploy, set `APP_KEY` in Render to a Laravel key generated locally:

```sh
php artisan key:generate --show
```

The default blueprint uses SQLite and runs migrations on startup with `RUN_MIGRATIONS=true`. To load the initial admin user, languages, menu, and pages, temporarily set `RUN_SEEDERS=true` for the first deploy, then set it back to `false`.

Set `APP_URL` and `ASSET_URL` to your HTTPS Render URL:

```text
APP_URL=https://holasantana.onrender.com
ASSET_URL=https://holasantana.onrender.com
```

For persistent production data, attach a Render disk for the SQLite database or switch the Render env vars to a managed database connection.

If the deployed page appears unstyled, open this URL in your browser:

```text
https://your-service-name.onrender.com/build/manifest.json
```

If it returns 404, Render is still running an old image or the frontend build did not finish. Push the latest Docker changes and trigger a manual deploy with "Clear build cache & deploy".
