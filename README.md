# Reganto IT Solution – website

A dynamic IT-solution company website built with **Laravel 12** (PHP 8.2+) and **MySQL**.
Plain Blade views, CSS and vanilla JS, so there is **no Node or Vite build step**.

- **Public site:** home, about, services (list + detail), projects (list + detail, filter), team, blog (list + detail), contact / request-a-service form.
- **Admin panel** (`/admin`): site settings and logo, services and sub-items, projects with gallery, testimonials, FAQs, stats, client logos and blog articles.
- **SEO:** per-page meta, canonical and Open Graph tags, `sitemap.xml`, `robots.txt`, a 404 page, automatic image resizing on upload.

## Run it on XAMPP (Windows)

Requirements: XAMPP with **PHP 8.2 or newer** (8.3 is fine) and MySQL, plus [Composer](https://getcomposer.org). The PHP extensions `gd`, `mbstring`, `pdo_mysql`, `fileinfo` and `exif` must be on (XAMPP enables them by default).

1. Start **MySQL** in the XAMPP control panel.
2. Create an empty database named `regantoitsolution` in phpMyAdmin (`http://localhost/phpmyadmin`), collation `utf8mb4_unicode_ci`.
3. In this folder:

   ```bash
   composer install
   copy .env.example .env          # on Git Bash / macOS / Linux: cp .env.example .env
   php artisan key:generate
   ```

4. Open `.env` and set `ADMIN_EMAIL` and a strong `ADMIN_PASSWORD` (the first admin login). If you leave `ADMIN_PASSWORD` empty, the seeder generates a random one and prints it once. Change `DB_*` only if your MySQL differs from the XAMPP defaults.
5. Create the tables, sample content and the admin account, and link the uploads folder:

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

6. Start the site:

   ```bash
   php artisan serve
   ```

   - Website: <http://127.0.0.1:8000>
   - Admin: <http://127.0.0.1:8000/admin/login>

You can also serve the folder with XAMPP Apache instead of `artisan serve`: point a virtual host's `DocumentRoot` at the `public/` folder.

> The admin account is only created if it does not exist, so re-seeding never resets its password. But `php artisan db:seed` also re-creates the sample services, projects, team, FAQs and so on, so do not re-seed a site that already has real content.

## Using the admin panel

Log in at `/admin/login`, then use the sidebar:

| Menu | What it controls |
|---|---|
| Site settings | Logo, favicon, site name, tagline, contact details, social links, home hero text, footer text |
| Services | Services and their sub-items (home tabs and the service page), image, order, show/hide |
| Projects | Projects with cover photo, multi-photo gallery, category, live URL, featured (home page) |
| Testimonials, FAQs, Stats, Client logos, Blogs | The content blocks shown on the home page and blog |

Notes:
- The home page shows **featured** projects (or all visible ones if none are featured) and the first **4** active stats.
- Hiding an item removes it from the site but keeps it in the admin.
- Uploaded images are shrunk to at most 1600 px (team photos and logos 800 px) and stored in `storage/app/public`, served from `/storage/...`.
- Only files uploaded through the admin are ever deleted by it. Images referenced by a full URL are left alone.

## Going live checklist

- `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, real DB credentials, a strong `ADMIN_PASSWORD`.
- Make sure the web root is the `public/` folder, and `storage/` plus `bootstrap/cache/` are writable.
- Run `php artisan storage:link` on the server.
- Optional speed-up: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
- Check `https://your-domain/sitemap.xml` and `/robots.txt`.

## Project layout

```
app/Http/Controllers/            public pages (HomeController, PageController, SeoController)
app/Http/Controllers/Admin/      admin controllers (generic ContentController drives the home blocks)
app/Support/                     Media (image URLs), ImageOptimizer, ContentTypes
database/migrations, seeders     schema and sample content
resources/views/                 layouts, partials, home sections, pages, admin, errors
public/assets/                   css, js, img (no build step)
routes/web.php                   all routes
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
