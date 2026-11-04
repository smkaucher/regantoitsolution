# Reganto IT Solution: Laravel 12 Website Plan

Requirements source: `C:\Users\User\Desktop\New Text Document.txt`
Design source (home page, React): `C:\Users\User\Downloads\saasify\saasify\src`
Reference site: https://regantoitsolution.com/

## Goal

A fully dynamic IT-solution company website.
- **Stack:** Laravel 12, PHP 8.2+ (XAMPP has 8.3, fine), MySQL.
- **Full-stack Laravel, no `npm run dev`:** Blade views, plain CSS and vanilla JS served from `public/`. No Vite, no Node build step.
- **Database:** XAMPP MySQL, phpMyAdmin, database name `regantoitsolution`. All schema changes go through Laravel migrations (models and migrations are kept in the project).
- **Admin panel** controls everything: logo and site settings, services, projects (with photo upload), IT team members, and customer service requests.
- **Front end:** the home page is a pixel-exact Blade port of the React `saasify` design (all images, animations, colors, theme). Every other page uses the same design language.

## What the source React home page contains (to port)

Sections in order: Announce bar, Navbar, Hero, Showcase, Ambition, Logos, Stats, Leader, ServicesDetail, Features, Work, Why, Reviews, Faq, Blogs, Footer, Chat widget.
Styles live in `styles.css` plus per-component CSS. Animations use framer-motion, CountUp, TypingText, RevealWords and `useReplayInView`. In Blade these become CSS plus vanilla JS (IntersectionObserver).
Assets are fetched by `scripts/fetch-assets.mjs` and `/hero.png`; copy them into `public/assets`.

## Data model (migrations and models)

| Table | Main fields |
|---|---|
| `users` | admin login (name, email, password) |
| `settings` | key/value: logo, favicon, site name, email, phone, address, social links, hero text, footer text |
| `services` | title, slug, icon, short description, details (rich), image, sort order, is_active |
| `service_items` | service_id, title (e.g. "Mobile App Design") |
| `projects` | title, slug, category, client, url, description, cover image, is_featured, is_active |
| `project_images` | project_id, image path, sort order (gallery) |
| `team_members` | name, designation, bio, photo, portfolio_url, socials, sort order, is_active |
| `service_requests` | name, email, phone, service_id, message, status (new / contacted / closed) |
| `testimonials`, `faqs`, `blogs`, `stats`, `clients (logos)` | content blocks the home page shows, all admin-editable |

## Steps

Tell me "Step N" and I run only that step, verify it, and show the result in the browser.

### Step 1: Project setup and database
- Create the Laravel 12 project in this folder (Composer).
- Configure `.env`: MySQL `127.0.0.1`, database `regantoitsolution`, user `root`, empty password.
- Start XAMPP MySQL, create the database (visible in phpMyAdmin), run the first migration.
- `php artisan serve`, open the default page in the browser.

### Step 2: Migrations, models and seeders
- Write all migrations and Eloquent models above, with relationships.
- Seeder: admin user, default settings (logo from the reference site), the 3 services from `brand.js`, sample projects, team, FAQs, testimonials.
- Check the tables in phpMyAdmin.

### Step 3: Public assets and base layout
- Copy the React CSS and images into `public/assets`.
- Blade layout `layouts/app.blade.php` with navbar, footer, announce bar and chat widget partials, all driven by `settings`.
- Vanilla JS file replacing the framer-motion behaviours: reveal on scroll, count-up, typing text, reveal words, and the replay-in-view hook.

### Step 4: Home page (Blade port, A to Z)
- One partial per section (Hero ... Blogs), same markup, colors and animations as React.
- Dynamic data: services, projects, team, testimonials, FAQs, stats and logos all come from the DB.
- Compare against the React app (`npm run dev` in `saasify`, used only for comparison) in the browser, side by side.

### Step 5: Inner pages (same design)
- Pages: About, Services (list and detail), Projects (list and detail), Team, Contact / Request a Service, Blog.
- Same navbar and footer, same typography and components.

### Step 6: Service request form (public)
- Visitors submit a service request: validation, CSRF, throttling, success message.
- It is stored in `service_requests`.

### Step 7: Admin authentication and dashboard
- Login at `/admin/login`, middleware-protected `/admin`.
- Dashboard with counts: services, projects, team, new requests.
- Admin layout (clean, consistent with the brand colors).

### Step 8: Admin: Site settings
- Upload logo and favicon, edit site name, contact info, social links, hero and footer texts. Changes show on the site immediately.

### Step 9: Admin: Services
- Add, edit, delete services and their sub-items, with an image or icon, order and active toggle.

### Step 10: Admin: Projects
- Add, edit, delete projects with a cover photo and a multi-photo gallery upload.
- Featured toggle (home page uses these).

### Step 11: Admin: IT team
- Add team members: name, designation, photo, bio, portfolio link and socials.
- Edit and update designation or portfolio at any time; reorder.

### Step 12: Admin: Service requests
- List requests, view details, change status, search and filter, delete.

### Step 13: Admin: Home page content
- Manage testimonials, FAQs, stats, client logos and blogs.

### Step 14: SEO, polish and QA
- Meta tags per page, sitemap, 404 page, image optimisation, responsive checks.
- Full browser run-through of every page and every admin CRUD; fix what breaks.

### Step 15: Delivery
- `php artisan storage:link`, `.env.example`, short README (how to run on XAMPP).
- Optionally initialise git and push a branch (only if you ask).

## Notes and decisions
- **No Node/Vite:** compiled CSS/JS are plain files under `public/assets`, so only `php artisan serve` (or XAMPP Apache) is needed.
- **Pixel-exact home page:** the React app stays in Downloads as the visual reference; I compare screenshots against it.
- **Only the home page exists in React.** The inner pages (Step 5) are designed by me in the same style; tell me if you have references.
- **Images:** uploads go to `storage/app/public` (symlinked), served at `/storage/...`.
- **Dependencies:** none beyond Laravel itself, unless you want a rich-text editor for admin (I'd use a CDN-free option like a plain `contenteditable` or Trix).
