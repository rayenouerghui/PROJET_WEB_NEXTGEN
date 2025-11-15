# MVC Architecture Documentation

## Project Structure (MVC Pattern)

The entire project now follows the **Model-View-Controller (MVC)** architecture pattern.

### Directory Structure

```
PROJET_WEB_NEXTGEN/
├── app/
│   ├── Models/
│   │   └── backoffice/      # Models (Data Access)
│   │       ├── AttenteMatchModel.php
│   │       └── SessionMatchModel.php
│   ├── Controllers/
│   │   ├── backoffice/      # Admin Controllers
│   │   │   ├── BaseController.php
│   │   │   ├── AttenteMatchController.php
│   │   │   ├── SessionMatchController.php
│   │   │   └── MatchmakingAdminController.php
│   │   └── frontoffice/     # User Controllers
│   │       └── MatchController.php
│   ├── Services/            # Services (Business Logic)
│   │   ├── MatchService.php
│   │   └── EmailService.php
│   └── Views/
│       ├── backoffice/      # Admin Views
│       │   ├── _partials/
│       │   │   └── header.php
│       │   └── matchmaking_admin.php
│       └── frontoffice/     # User Views
│           ├── matchmaking_view.php
│           └── session_view.php
│
├── public/
│   └── css/
│       ├── backoffice/
│       │   └── backoffice.css
│       └── frontoffice/
│           └── frontoffice.css
│
├── config/                  # Configuration files
│   ├── database.sql
│   ├── database_LOCAL_TEST.sql
│   ├── db.php
│   └── discord.php
│
├── backoffice/
│   └── matchmaking.php      # Admin entry point
│
│
├── api/                     # API endpoints
│   ├── matchmaking.php
│   └── admin/
│       └── matchmaking.php
│
├── cron/                    # Scheduled tasks
│   └── check_matches.php
│
├── frontoffice/
│   ├── matchmaking.php      # User entry point
│   └── session.php          # Session display entry point
└── index.php                # Main router (API endpoints)
```

## MVC Pattern Explanation

### 1. **Models** (`app/Models/backoffice/`)
- **Purpose**: Handle all database operations
- **Responsibilities**:
  - Database queries
  - Data validation
  - Data transformation
- **Example**: `AttenteMatchModel.php`, `SessionMatchModel.php`

### 2. **Views** (`app/Views/backoffice/`, `app/Views/frontoffice/`)
- **Purpose**: Display data to the user
- **Responsibilities**:
  - HTML/PHP templates
  - Presentation logic only
  - No business logic
- **Example**: `matchmaking_view.php`, `matchmaking_admin.php`

### 3. **Controllers** (`app/Controllers/backoffice/`, `app/Controllers/frontoffice/`)
- **Purpose**: Handle user requests and coordinate between Models and Views
- **Responsibilities**:
  - Process user input
  - Call Models to get data
  - Call Services for business logic
  - Pass data to Views
- **Example**: `MatchController.php`, `MatchmakingAdminController.php`

### 4. **Services** (`app/Services/`)
- **Purpose**: Complex business logic that doesn't fit in Models
- **Responsibilities**:
  - Business rules
  - Complex operations
  - Integration with external services
- **Example**: `MatchService.php`, `EmailService.php`

## Entry Points

### Frontend Entry Points
- `frontoffice/matchmaking.php` → Uses `app/Controllers/frontoffice/MatchController::afficherPage()`
- `frontoffice/session.php` → Uses `app/Controllers/frontoffice/MatchController::afficherSession()`

### Backend Entry Points
- `backoffice/matchmaking.php` → Uses `app/Controllers/backoffice/MatchmakingAdminController::afficherPage()`

### API Entry Points
- `index.php` → Routes to appropriate controllers based on `controller` and `action` parameters

## How It Works

### Example Flow: User visits matchmaking page

1. **User Request**: `frontoffice/matchmaking.php?id_utilisateur=1`
2. **Entry Point**: `frontoffice/matchmaking.php` loads `app/Controllers/frontoffice/MatchController`
3. **Controller**: `MatchController::afficherPage()`:
   - Calls `app/Models/backoffice/AttenteMatchModel` to get data
   - Calls `app/Services/MatchService` for business logic
   - Prepares data array
   - Includes view: `app/Views/frontoffice/matchmaking_view.php`
4. **View**: `matchmaking_view.php` displays the data
5. **Response**: HTML page sent to user

### Example Flow: Admin manages matchmaking

1. **User Request**: `backoffice/matchmaking.php` (POST with action)
2. **Entry Point**: `backoffice/matchmaking.php` loads `app/Controllers/backoffice/MatchmakingAdminController`
3. **Controller**: `MatchmakingAdminController::afficherPage()`:
   - Handles POST actions (delete, verify matches, etc.)
   - Calls Models and Services
   - Prepares data
   - Loads view: `app/Views/backoffice/matchmaking_admin.php`
4. **View**: `matchmaking_admin.php` displays admin interface
5. **Response**: HTML page sent to admin

## BaseController

All backoffice controllers can extend `BaseController` (located in `app/Controllers/backoffice/BaseController.php`) which provides:
- `model($modelName)` - Load and instantiate a model from `app/Models/backoffice/`
- `view($viewName, $data)` - Load a view from `app/Views/backoffice/`

## Rules for Team Members

### ✅ DO:
- Put controllers in `app/Controllers/backoffice/` or `app/Controllers/frontoffice/`
- Put models in `app/Models/backoffice/`
- Put views in `app/Views/backoffice/` or `app/Views/frontoffice/`
- Put services in `app/Services/`
- Put CSS in `public/css/backoffice/` or `public/css/frontoffice/`
- Put config files in `config/`
- Use controllers to handle all logic
- Keep views simple (only presentation)

### ❌ DON'T:
- Put business logic in views
- Access models directly from entry points
- Mix database queries with presentation
- Create files outside MVC structure
- Create duplicate files in old locations

## Benefits of MVC

1. **Separation of Concerns**: Each component has a single responsibility
2. **Maintainability**: Easy to find and fix bugs
3. **Reusability**: Models and services can be reused
4. **Testability**: Each component can be tested independently
5. **Team Collaboration**: Team members can work on different components without conflicts

## For Your Friends

When creating new modules (Users, Products, Orders), follow this structure:

```
app/
├── Controllers/
│   ├── backoffice/
│   │   └── YourModuleController.php
│   └── frontoffice/
│       └── YourModuleController.php (if needed)
├── Models/
│   └── backoffice/
│       └── YourModuleModel.php
├── Services/
│   └── YourModuleService.php (optional)
└── Views/
    ├── backoffice/
    │   └── your_module/
    │       └── list.php
    └── frontoffice/
        └── your_module/
            └── list.php (if needed)
```

Entry point example (backoffice):
```php
<?php
require_once __DIR__ . '/../app/Controllers/backoffice/YourModuleController.php';
$controller = new YourModuleController();
$controller->afficherPage();
?>
```

Entry point example (frontoffice):
```php
<?php
require_once __DIR__ . '/../app/Controllers/frontoffice/YourModuleController.php';
$controller = new YourModuleController();
$controller->afficherPage();
?>
```


