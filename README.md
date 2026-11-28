# Reganto IT Solution – website

A dynamic IT-solution company website built with **Laravel 12** (PHP 8.2+) and **MySQL**.
The home page is built in **React** (Inertia.js, compiled with Vite); the other public pages and the admin panel are Blade views with CSS and vanilla JS. The React pages are being converted one at a time.

- **Public site:** home, about, services (list + detail), projects (list + detail, filter), team, contact / request-a-service form.
- **Admin panel** (`/admin`): site settings, project requests, services and sub-items, projects with gallery, testimonials, FAQs, stats and client logos.
- **SEO:** per-page meta, canonical and Open Graph tags, `sitemap.xml`, `robots.txt`, a 404 page, automatic image resizing on upload.

## English and Bangla

The site has a language switch in the top bar (English / বাংলা); the choice is remembered in a cookie. The pages are written in English. For a Bangla visitor the finished page is translated on the way out from `lang/bn.json` (key = the English text, value = the Bangla text), see `app/Support/Translator.php`. React pages are built in the browser, so they are translated there from the same file (`resources/js/lib/translate.js`). Anything not in that file simply stays English, so after adding a service, project or FAQ in the admin panel, add one line for it to `lang/bn.json` to get its Bangla version. The admin panel is English only.

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

6. Build the React pages (needs [Node.js](https://nodejs.org) 20 or newer, on your own computer only):

   ```bash
   npm install
   npm run build        # writes public/build; run it again after changing anything in resources/js
   ```

7. Start the site:

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
| Testimonials, FAQs, Stats, Client logos | The content blocks shown on the home page and About page |

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
- Run `npm run build` on your own computer before uploading and include the `public/build/` folder. The server never needs Node or npm. Leave `node_modules/` out of the upload.
- Check `https://your-domain/sitemap.xml` and `/robots.txt`.

## Project layout

```
app/Http/Controllers/            public pages (HomeController, PageController, SeoController)
app/Http/Controllers/Admin/      admin controllers (generic ContentController drives the home blocks)
app/Support/                     Media (image URLs), ImageOptimizer, ContentTypes
database/migrations, seeders     schema and sample content
resources/js/                    React pages (Pages/), layout, home sections, laptop animation (lib/)
resources/views/                 inertia.blade.php (React root), layouts, partials, pages, admin, errors
public/assets/                   css, js, img (shared by Blade and React pages)
public/build/                    compiled React pages (npm run build, not in git)
routes/web.php                   all routes
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
