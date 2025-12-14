# NextGen Application Testing Guide

## 🚀 Quick Start

### Base URL
```
http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/
```

---

## 📍 Access Points

### 1. **Base Module (User, Product, Reclamation, Livraison)**

#### Front Office
- **Home Page**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/frontoffice/index.php
  ```
- **Login**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/frontoffice/connexion.php
  ```
- **Registration**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/frontoffice/inscriptiom.php
  ```
- **Catalogue (Games)**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/frontoffice/catalogue.php
  ```
- **My Library**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/frontoffice/my_library.php
  ```
- **Reclamation**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/frontoffice/reclamation.php
  ```
- **Livraison (Delivery)**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/livraison.php
  ```

#### Back Office (Admin)
- **Admin Dashboard**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/accueil.php
  ```
- **Users Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/admin_users.php
  ```
- **Games Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/admin_jeux.php
  ```
- **Categories Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/admin_categories.php
  ```
- **Reclamations Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/admin_reclamations.php
  ```
- **Livraisons Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/admin_livraisons.php
  ```

---

### 2. **Blog Module**

#### Front Office
- **Blog Homepage**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/frontoffice/blog.php
  ```

#### Back Office (Admin)
- **Blog Dashboard**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/DashboardBlog.php
  ```
- **Articles Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/admin_article.php
  ```
- **Article Details**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/articleDetail.php
  ```
- **Category Details**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/categorieDetail.php
  ```
- **Comment Details**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/view/backoffice/commentaireDetail.php
  ```

---

### 3. **Event Module**

#### Front Office (via Router)
- **Events List**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=front&a=events
  ```
- **Event Categories**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=front&a=categories
  ```
- **Make Reservation**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=front&a=reservation
  ```
- **User History**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=front&a=historique
  ```
- **Leaderboard**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=front&a=leaderboard
  ```
- **Points**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=front&a=points
  ```

#### Back Office (Admin via Router)
- **Admin Dashboard**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=admin&a=dashboard
  ```
- **Events Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=evenement&a=index
  ```
- **Create Event**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=evenement&a=create
  ```
- **Categories Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=categorie&a=index
  ```
- **Reservations Management**: 
  ```
  http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/index.php?c=reservation&a=index
  ```

---

## 🧪 Testing Checklist

### Prerequisites
- ✅ XAMPP is running (Apache + MySQL)
- ✅ Database `nextgen_db` is imported
- ✅ All tables exist in the database

### 1. Base Module Testing

#### User Management
- [ ] Register a new user
- [ ] Login with existing user
- [ ] View user profile
- [ ] Update user profile

#### Games/Products
- [ ] Browse game catalogue
- [ ] View game details
- [ ] Purchase a game (if credits available)
- [ ] View purchased games in "My Library"
- [ ] Play a game from library

#### Reclamations
- [ ] Create a reclamation
- [ ] View my reclamations
- [ ] Admin: View all reclamations
- [ ] Admin: Process a reclamation

#### Livraisons (Deliveries)
- [ ] Create a delivery order
- [ ] Track delivery status
- [ ] Admin: View all deliveries
- [ ] Admin: Update delivery status

### 2. Blog Module Testing

#### Articles
- [ ] View blog homepage with articles
- [ ] View single article
- [ ] Filter articles by category
- [ ] Search articles
- [ ] Admin: Create new article
- [ ] Admin: Edit article
- [ ] Admin: Delete article

#### Comments
- [ ] Add comment to article
- [ ] Reply to a comment
- [ ] Like a comment
- [ ] Admin: View all comments
- [ ] Admin: Delete comment

#### Categories
- [ ] View article categories
- [ ] Admin: Create category
- [ ] Admin: Edit category
- [ ] Admin: Delete category

#### Ratings
- [ ] Rate an article (1-5 stars)
- [ ] View article rating statistics

### 3. Event Module Testing

#### Events
- [ ] View all events
- [ ] Filter events by category
- [ ] View event details
- [ ] Admin: Create new event
- [ ] Admin: Edit event
- [ ] Admin: Delete event

#### Reservations
- [ ] Make a reservation for an event
- [ ] View my reservations
- [ ] Admin: View all reservations
- [ ] Admin: Manage reservations

#### Categories
- [ ] View event categories
- [ ] Admin: Create event category
- [ ] Admin: Edit event category
- [ ] Admin: Delete event category

---

## 🔐 Default Admin Credentials

Based on the database, you should have an admin user:
- **Email**: `dhia@gmail.com`
- **Password**: `0` (zero)
- **Role**: `admin`

**Note**: You may need to update the password for security.

---

## 🐛 Troubleshooting

### Issue: "Database connection error"
**Solution**: 
- Check if MySQL is running in XAMPP
- Verify database name is `nextgen_db`
- Check credentials in `config/config.php`, `config/database.php`, and `config/db.php`

### Issue: "404 Not Found" or "Page not found"
**Solution**: 
- Verify the URL path matches your XAMPP htdocs structure
- Check if `index.php` exists in the nextgen folder
- Ensure Apache is running

### Issue: "Session errors"
**Solution**: 
- Make sure `session_start()` is called before any output
- Check `config/session.php` is included properly

### Issue: "Foreign key constraint errors"
**Solution**: 
- Ensure all tables were created successfully
- Check that parent tables exist before child tables
- Verify foreign key relationships in the database

### Issue: "Images/CSS not loading"
**Solution**: 
- Check `config/paths.php` - `WEB_ROOT` constant
- Verify file paths in views use `WEB_ROOT` constant
- Ensure `public/` folder exists with assets

---

## 📝 Quick Test Script

Create a test file `test_connection.php` in the nextgen folder:

```php
<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/db.php';

echo "<h1>NextGen Connection Test</h1>";

// Test config.php
try {
    $pdo1 = config::getConnexion();
    echo "✅ config.php connection: OK<br>";
} catch (Exception $e) {
    echo "❌ config.php connection: " . $e->getMessage() . "<br>";
}

// Test database.php
try {
    $db = Database::getInstance();
    $pdo2 = $db->getConnection();
    echo "✅ database.php connection: OK<br>";
} catch (Exception $e) {
    echo "❌ database.php connection: " . $e->getMessage() . "<br>";
}

// Test db.php
try {
    $db2 = Database::getInstance();
    $pdo3 = $db2->getConnection();
    echo "✅ db.php connection: OK<br>";
} catch (Exception $e) {
    echo "❌ db.php connection: " . $e->getMessage() . "<br>";
}

// Test tables
try {
    $pdo = config::getConnexion();
    $tables = ['users', 'article', 'evenement', 'categorie_article', 'categoriev'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table': EXISTS<br>";
        } else {
            echo "❌ Table '$table': NOT FOUND<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Table check error: " . $e->getMessage() . "<br>";
}
?>
```

Access it at:
```
http://localhost/user+produit+reclamation+laivrasion+evenment+blog/nextgen/test_connection.php
```

---

## 🎯 Recommended Testing Order

1. **Start with Base Module** (most stable)
   - Test user registration/login
   - Test game browsing
   - Test basic CRUD operations

2. **Then Blog Module**
   - Test article viewing
   - Test comment system
   - Test admin article management

3. **Finally Event Module**
   - Test event listing
   - Test reservation system
   - Test admin event management

---

## 📞 Need Help?

If you encounter issues:
1. Check browser console for JavaScript errors
2. Check PHP error logs in XAMPP
3. Enable error reporting in PHP (temporarily) to see detailed errors
4. Verify all file paths are correct

Good luck with testing! 🚀

