# NextGen – Integrated Web Platform (PHP MVC)

NextGen is an integrated **PHP MVC** web application that combines multiple business modules into one consistent platform. It is designed as a complete front office experience for end users and a back office experience for administrators.

## What the website does

### Front Office (User Side)

- **Gaming Products Catalogue**
  - Browse products (games) with search/filter/sort.
  - View product cards and details.

- **Blog**
  - Read blog posts and interact with content from the front office.

- **Events & Reservations (Solidarity Program)**
  - Browse events and filter by category.
  - Reserve an event using a reservation form.
  - Earn **solidarity points** when making reservations.

- **Points & Donations**
  - View the current points balance.
  - Convert points into donations (impact-oriented feature).

- **Delivery & Other Linked Modules**
  - Access linked pages/modules (delivery, etc.) from the shared navigation.

### Back Office (Admin Side)

Administrators can manage platform data through back office pages such as:

- **Users management** (e.g., `admin_users.php`)
- **Events management** (CRUD)
- **Categories management** (CRUD)
- Other administrative pages included in the `nextgen/view/backoffice/` area.

## Tech stack

- **PHP** (MVC pattern)
- **MySQL** (accessed using **PDO**, no MySQLi)
- **HTML / CSS / JavaScript**
- **XAMPP** (Apache + MySQL) for local execution

## Project structure (high level)

- `nextgen/` : the main integrated application
  - `controller/` : controllers (request handling)
  - `models/` : models (data + business logic)
  - `views/` + `view/` : front office and back office views
  - `config/` : configuration (paths + DB)
  - `public/` : assets (CSS/JS/images)
- The repository root may also contain other legacy modules, but **NextGen** is the module intended for evaluation and demo.

## Local setup (XAMPP)

1. Copy this repository into your XAMPP htdocs folder, for example:
   - `C:\xampp\htdocs\user+produit+reclamation+laivrasion+evenment+blog`
2. Start **Apache** and **MySQL** in XAMPP.
3. Import the provided database dump(s) into MySQL.
4. Open the application:
   - `http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/`

## Routing

- Main entry point/router:
  - `nextgen/index.php`
- Example routes:
  - `index.php?c=front&a=events`
  - `index.php?c=front&a=categories`

## Front office navigation

A shared navbar is included from:
- `nextgen/view/frontoffice/includes/navigation.php`

## Validation checklist (project requirements)

- **MVC architecture**: Controllers handle requests, Models access data, Views render HTML.
- **PDO only**: Database access is done through **PDO**.
- **Same-machine demo**: Designed for a local demo environment (XAMPP).
- **Git history**: Commit history should show incremental work.
- **English deliverables**: README + poster + presentation should be in English.
