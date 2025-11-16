<?php
require_once __DIR__ . "/_partials/header.php";
require_once __DIR__ . "/../Controllers/frontoffice/AuthController.php";
require_once __DIR__ . "/../Models/frontoffice/UserModel.php";

$authController = new AuthController();
$authController->requireAuth();

$currentUser = $authController->getCurrentUser();
$userModel = new UserModel();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (empty($nom) || empty($prenom) || empty($email)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        // Check if email is already taken by another user
        $existingUser = $userModel->getUserByEmail($email);
        if ($existingUser && $existingUser['id_user'] != $currentUser['id']) {
            $error = 'Cet email est déjà utilisé';
        } else {
            if ($userModel->updateUser($currentUser['id'], $nom, $prenom, $email)) {
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email'] = $email;
                $message = 'Informations mises à jour avec succès';
                $currentUser = $authController->getCurrentUser();
            } else {
                $error = 'Erreur lors de la mise à jour';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les informations - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <section class="account-section">
        <div class="container">
            <h1 class="page-title">Modifier les informations</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="account-content">
                <div class="account-main">
                    <form method="POST" id="settingsForm">
                        <div class="form-group">
                            <label>Prénom *</label>
                            <input type="text" name="prenom" value="<?php echo htmlspecialchars($currentUser['prenom']); ?>" required>
                            <span class="error-message" id="prenomError"></span>
                        </div>
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="nom" value="<?php echo htmlspecialchars($currentUser['nom']); ?>" required>
                            <span class="error-message" id="nomError"></span>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                            <span class="error-message" id="emailError"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <style>
        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .error-message {
            display: block;
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</body>
</html>

