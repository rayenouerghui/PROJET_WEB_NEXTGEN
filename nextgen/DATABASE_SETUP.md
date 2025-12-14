# NextGen Database Setup Guide

## Database Location

The `nextgen_db` database **does not exist yet** - you need to create it.

## SQL Files Available

1. **`nextgen_db_complete.sql`** (RECOMMENDED) - Complete unified database with all tables
   - ✅ Base module tables (users, categorie, jeu, livraisons, etc.)
   - ✅ Blog module tables (article, commentaire, categorie_article, article_rating)
   - ✅ Event module tables (evenement, categoriev, reservation)
   - ✅ All foreign key relationships

2. **`nextgen_db (6).sql`** - Original base database (incomplete)
   - Missing: reclamation, traitement, blog tables, event tables

3. **`evenment/playforchange.sql`** - Event module database (separate)
4. **`user+produit+reclamation+blog/testing.sql`** - Blog module database (separate)

## How to Create the Database

### Option 1: Using phpMyAdmin (Recommended)

1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Click on "New" in the left sidebar to create a new database
3. Enter database name: `nextgen_db`
4. Choose collation: `utf8mb4_general_ci`
5. Click "Create"
6. Select the `nextgen_db` database
7. Click on the "Import" tab
8. Click "Choose File" and select `nextgen/nextgen_db_complete.sql`
9. Click "Go" to import

### Option 2: Using MySQL Command Line

```bash
# Navigate to the nextgen folder
cd C:\xampp\htdocs\user+produit+reclamation+laivrasion+evenment+blog\nextgen

# Import the database
mysql -u root -p < nextgen_db_complete.sql
```

Or step by step:
```sql
CREATE DATABASE nextgen_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE nextgen_db;
SOURCE nextgen_db_complete.sql;
```

### Option 3: Manual Creation

1. Create database `nextgen_db` in phpMyAdmin
2. Copy and paste the contents of `nextgen_db_complete.sql` into the SQL tab
3. Execute the SQL

## Database Configuration

After creating the database, verify that your config files point to it:

- **`config/config.php`**: Uses `nextgen_db` ✅
- **`config/database.php`**: Uses `nextgen_db` ✅
- **`config/db.php`**: Uses `nextgen_db` ✅

## Tables Included

### Base Module
- `users` - User accounts
- `categorie` - Game categories
- `jeu` - Games/products
- `jeux_owned` - User-owned games
- `livraisons` - Deliveries
- `trajets` - Delivery tracking routes
- `reclamation` - Complaints/claims
- `traitement` - Complaint treatments
- `historique` - User activity history
- `password_resets` - Password reset tokens

### Blog Module
- `categorie_article` - Blog article categories
- `article` - Blog articles
- `commentaire` - Article comments
- `article_rating` - Article ratings

### Event Module
- `categoriev` - Event categories
- `evenement` - Events
- `reservation` - Event reservations

## Verification

After importing, verify the database:

```sql
USE nextgen_db;
SHOW TABLES;
```

You should see all the tables listed above.

## Notes

- The unified database uses `nextgen_db` as the name
- All foreign key relationships are properly configured
- The database uses `utf8mb4` character set for full Unicode support
- Auto-increment values are set appropriately

