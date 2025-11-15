# MVC Architecture Documentation

## Project Structure (MVC Pattern)

The entire project now follows the **Model-View-Controller (MVC)** architecture pattern.

### Directory Structure

```
PROJET_WEB_NEXTGEN/
├── backoffice/
│   ├── controllers/          # Controllers (Business Logic)
│   │   ├── BaseController.php
│   │   ├── AttenteMatchController.php
│   │   ├── SessionMatchController.php
│   │   └── MatchmakingAdminController.php
│   ├── models/              # Models (Data Access)
│   │   ├── AttenteMatchModel.php
│   │   └── SessionMatchModel.php
│   ├── services/            # Services (Business Logic)
│   │   ├── MatchService.php
│   │   └── EmailService.php
│   ├── views/               # Views (Presentation)
│   │   ├── _partials/
│   │   │   └── header.php
│   │   ├── matchmaking_admin.php
│   │   └── attente_list.php
│   └── matchmaking.php      # Entry point (uses controller)
│
├── frontoffice/
│   ├── controllers/         # Controllers
│   │   └── MatchController.php
│   ├── views/               # Views
│   │   ├── matchmaking_view.php
│   │   └── session_view.php
│   └── matchmaking.php      # Entry point (uses controller)
│
├── session.php              # Entry point (uses controller)
└── index.php                # Router (API endpoints)
```

## MVC Pattern Explanation

### 1. **Models** (`backoffice/models/`)
- **Purpose**: Handle all database operations
- **Responsibilities**:
  - Database queries
  - Data validation
  - Data transformation
- **Example**: `AttenteMatchModel.php`, `SessionMatchModel.php`

### 2. **Views** (`backoffice/views/`, `frontoffice/views/`)
- **Purpose**: Display data to the user
- **Responsibilities**:
  - HTML/PHP templates
  - Presentation logic only
  - No business logic
- **Example**: `matchmaking_view.php`, `matchmaking_admin.php`

### 3. **Controllers** (`backoffice/controllers/`, `frontoffice/controllers/`)
- **Purpose**: Handle user requests and coordinate between Models and Views
- **Responsibilities**:
  - Process user input
  - Call Models to get data
  - Call Services for business logic
  - Pass data to Views
- **Example**: `MatchController.php`, `MatchmakingAdminController.php`

### 4. **Services** (`backoffice/services/`)
- **Purpose**: Complex business logic that doesn't fit in Models
- **Responsibilities**:
  - Business rules
  - Complex operations
  - Integration with external services
- **Example**: `MatchService.php`, `EmailService.php`

## Entry Points

### Frontend Entry Points
- `frontoffice/matchmaking.php` → Uses `MatchController::afficherPage()`
- `session.php` → Uses `MatchController::afficherSession()`

### Backend Entry Points
- `backoffice/matchmaking.php` → Uses `MatchmakingAdminController::afficherPage()`

### API Entry Points
- `index.php` → Routes to appropriate controllers based on `controller` and `action` parameters

## How It Works

### Example Flow: User visits matchmaking page

1. **User Request**: `frontoffice/matchmaking.php?id_utilisateur=1`
2. **Entry Point**: `frontoffice/matchmaking.php` loads `MatchController`
3. **Controller**: `MatchController::afficherPage()`:
   - Calls `AttenteMatchModel` to get data
   - Calls `MatchService` for business logic
   - Prepares data array
   - Includes view: `matchmaking_view.php`
4. **View**: `matchmaking_view.php` displays the data
5. **Response**: HTML page sent to user

### Example Flow: Admin manages matchmaking

1. **User Request**: `backoffice/matchmaking.php` (POST with action)
2. **Entry Point**: `backoffice/matchmaking.php` loads `MatchmakingAdminController`
3. **Controller**: `MatchmakingAdminController::afficherPage()`:
   - Handles POST actions (delete, verify matches, etc.)
   - Calls Models and Services
   - Prepares data
   - Loads view: `matchmaking_admin.php`
4. **View**: `matchmaking_admin.php` displays admin interface
5. **Response**: HTML page sent to admin

## BaseController

All controllers can extend `BaseController` which provides:
- `model($modelName)` - Load and instantiate a model
- `view($viewName, $data)` - Load a view with data

## Rules for Team Members

### ✅ DO:
- Put controllers in `controllers/` directory
- Put models in `models/` directory
- Put views in `views/` directory
- Put services in `services/` directory
- Use controllers to handle all logic
- Keep views simple (only presentation)

### ❌ DON'T:
- Put business logic in views
- Access models directly from entry points
- Mix database queries with presentation
- Create files outside MVC structure

## Benefits of MVC

1. **Separation of Concerns**: Each component has a single responsibility
2. **Maintainability**: Easy to find and fix bugs
3. **Reusability**: Models and services can be reused
4. **Testability**: Each component can be tested independently
5. **Team Collaboration**: Team members can work on different components without conflicts

## For Your Friends

When creating new modules (Users, Products, Orders), follow this structure:

```
backoffice/
├── controllers/
│   └── YourModuleController.php
├── models/
│   └── YourModuleModel.php
├── services/
│   └── YourModuleService.php (optional)
└── views/
    └── your_module/
        └── list.php
```

Entry point example:
```php
<?php
require_once __DIR__ . '/controllers/YourModuleController.php';
$controller = new YourModuleController();
$controller->afficherPage();
?>
```

