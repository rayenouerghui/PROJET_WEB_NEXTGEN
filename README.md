# NextGen Platform
- <img width="940" height="788" alt="logo" src="https://github.com/user-attachments/assets/594b4613-e83d-4bfb-a73f-bbfdf92d2917" />

Modern PHP application for gaming, deliveries, events, blog, and reclamations, organized with an MVC-style architecture and modular features.

## Overview
- Modular features: Games library, Deliveries (tracking), Events & Reservations, Blog & Ratings, Reclamations.
- Two UI domains: Frontoffice (user-facing) and Backoffice (admin).
- REST-like endpoints for certain features alongside classic server-rendered pages.
- Mapping and real-time tracking with MapLibre GL and Leaflet.

## Architecture (MVC)
- Controllers: `controller/` — orchestrate requests and call models and views
  - Examples: `FrontC.php`, `AdminC.php`, `LivraisonController.php`, `ReservationC.php`, `BlogController.php`
- Models: `models/` — domain objects and DB queries
  - Examples: `User.php`, `Jeu.php`, `Livraison.php`, `Trajet.php`, `Reservation.php`, `Evenement.php`, `Article.php`
- Views:
  - Front/back pages under `views/` (primary rendering root for `View.php`)
  - Additional legacy and feature views under `view/` (frontoffice/backoffice pages, e.g. `view/livraison_gaming.php`)
  - Shared partials (navigation, head, titles) under `view/backoffice/partials/` and `view/frontoffice/includes/`
- Services: `services/` — reusable logic (e.g., tracking math, routing fallbacks)
  - `TrackingService.php` computes progress, routes and ETA
- Config: `config/` — DB connection, session, path constants (`WEB_ROOT`, routing helpers)
- Public assets and APIs:
  - `public/assets/` CSS/JS/Images/Fonts
  - `public/api/` quick endpoints for public features
  - `api/` and `view/api/` specialized endpoints (e.g. `trajet.php`)
- Entry point and routing:
  - `index.php` reads `c` (controller) and `a` (action) query params, instantiates controller, and calls the method
  - `View.php` renders `views/<path>.php` with data extracted

## Routing
- Example URLs:
  - Front pages: `index.php?c=front&a=index`, `index.php?c=front&a=events`
  - Admin pages: `index.php?c=admin&a=dashboard`
  - Categories: `index.php?c=categorie&a=index`
  - Events: `index.php?c=evenement&a=index`
  - Reservations: `index.php?c=reservation&a=index`
- Simple routing plus dedicated API scripts for specific modules (e.g., `view/api/trajet.php`).

## Database
- SQL dump: `nextgen_db_complete.sql` — includes all tables for modules
- Main tables:
  - Users: `users` — auth and profile (role, credits, verification, status)
  - Games: `jeu`, ownership `jeux_owned`, categories `categorie`
  - Deliveries: `livraisons` (address, geo, price, status), `trajets` (route JSON, current index)
  - Reclamations: `reclamation`, `traitement`, `historique`, `password_resets`
  - Blog: `categorie_article`, `article`, `commentaire`, `article_rating`
  - Events: `categoriev`, `evenement`, `reservation`
- Connection:
  - `config/database.php` (PDO, UTF-8, exceptions, prepared statements)
  - Database name: `nextgen_db` (configurable)

## Technologies
- Language: PHP (PDO, server-rendered views), JavaScript (frontend UI)
- Mapping:
  - MapLibre GL JS (`https://unpkg.com/maplibre-gl`) with style JSON (`https://demotiles.maplibre.org/style.json`)
  - Leaflet (`https://unpkg.com/leaflet`) for address picker, tiles via OpenStreetMap
- Geocoding/routing: Nominatim (reverse geocoding), OSRM (routing)
- UI:
  - Fonts: Inter (`https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap`)
  - Icons: Bootstrap Icons CDN, Remixicon (packaged)
  - Alerts: SweetAlert2
- Email: PHPMailer (`vendor/PHPMailer`)
- AI module: `ai_module/` (Python scripts for reclamation analysis: Naive Bayes, Markov, Word2Vec)

## Front/Back Structure
- Front header: `views/front/header.php` (navigation, meta, global CSS)
- Back header: `views/header.php` (admin layout, validation scripts)
- Navigation include: `view/frontoffice/includes/navigation.php`
- Mobile responsiveness:
  - Meta tags set in heads for viewport, PWA capabilities, theme color
  - Global mobile CSS: `public/css/mobile-responsive.css`

## Delivery & Tracking
- User delivery page: `view/livraison_gaming.php`
  - Address selection via Leaflet picker
  - Tracking maps with MapLibre GL (mobile-aware zoom, pitch, controls)
  - Real-time progress computed in `services/TrackingService.php`
  - Fullscreen tracking: `view/tracking.php` (linked from cards)

## Setup
1. Requirements:
   - PHP 8+, MySQL 5.7+/8+, Composer (for PHPMailer if updated), Node (optional for asset tooling)
   - Local stack: XAMPP on Windows
2. Clone into `c:\xampp\htdocs\.../nextgen`
3. Create database and import:
   - Create `nextgen_db` and import `nextgen_db_complete.sql`
4. Configure DB:
   - Edit `config/database.php` credentials (host, db, user, password)
5. Paths:
   - `config/paths.php` sets `WEB_ROOT`, `WEB_BASE`, `WEB_API` based on script location
6. Run:
   - Visit `http://localhost/nextgen/index.php`
   - Front pages use `?c=front&a=index`, Admin uses `?c=admin&a=dashboard`

## Development Notes
- Views live in both `views/` and `view/`; newer rendering stacks use `View.php` targeting `views/`.
- Keep meta and mobile CSS in shared headers to ensure mobile responsiveness across pages.
- Avoid committing secrets; never log credentials; use prepared statements everywhere.

## API Endpoints (examples)
- Trajet data: `view/api/trajet.php`, `api/trajet.php` — serve tracking updates
- Public API samples: `public/api/auth.php`, `public/api/games.php`

## Testing
- Manual testing via browser:
  - Front navigation, events listing, reservations, blog pages
  - Delivery planning and tracking map interactions
- Add unit/integration tests depending on preferred PHP framework (e.g., PHPUnit) if needed.

## Security
- Escaping in views via `htmlspecialchars` and `View::escape`
- Prepared statements for DB access
- Basic session management in `config/session.php`
- Password reset flows stored in `password_resets`

## Assets
- CSS: `public/assets/css/*`, legacy `public/css/*`
- Images: `public/assets/images/*`, `public/articles/*`
- Fonts: `public/assets/fonts/*`

## Contributing
- Follow MVC file placement: controllers/models/views/services/config
- Reuse shared partials and styles
- Keep mobile-first styles and meta tags consistent across all pages

## our website

- <img width="1842" height="986" alt="page d&#39;acceuil" src="https://github.com/user-attachments/assets/fc83ccb3-3ff1-4b99-b23d-b264bc0f7a47" />
- <img width="1850" height="892" alt="liste des produits" src="https://github.com/user-attachments/assets/290a2c88-ef93-4234-961b-45ecce5f9cee" />
- <img width="1856" height="982" alt="espace livraison" src="https://github.com/user-attachments/assets/96f0b07a-ce23-4c71-9d10-44e4aedd185b" />



