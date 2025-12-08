<?php
// app/controllers/PasswordResetController.php
class PasswordResetController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    // Afficher le formulaire de demande de réinitialisation
    public function showForgotForm() {
        require_once __DIR__ . '/../views/auth/forgot_password.php';
    }

    // Traiter la demande de réinitialisation
    public function sendResetCode() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/forgot-password');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        $errors = [];
        if ($email === '') {
            $errors['email'] = 'Email requis';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['email' => $email];
            header('Location: /user_nextgen/forgot-password');
            exit;
        }

        // Chercher l'utilisateur - Requête SQL directe
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $userData = $stmt->fetch();

        if (!$userData) {
            // Pour la sécurité, on ne dit pas si l'email existe ou non
            $_SESSION['success'] = 'Si cet email existe, un code de réinitialisation a été envoyé.';
            header('Location: /user_nextgen/forgot-password');
            exit;
        }

        // Générer le code - Requête SQL directe
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+15 seconds'));

        // Supprimer les anciens codes
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE id_user = :uid AND used = 0");
        $stmt->execute([':uid' => $userData['id_user']]);

        // Insérer le nouveau code
        $stmt = $this->pdo->prepare("INSERT INTO password_resets (id_user, code, token, expiration) VALUES (:uid, :code, :token, :exp)");
        $stmt->execute([
            ':uid' => $userData['id_user'],
            ':code' => $code,
            ':token' => $token,
            ':exp' => $expiration
        ]);

        // Envoyer l'email - Enregistrer dans un fichier
        $emailFile = __DIR__ . '/../../emails_sent.txt';
        $emailContent = "\n========================================\n";
        $emailContent .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $emailContent .= "À: " . $email . "\n";
        $emailContent .= "Sujet: Code de réinitialisation - NextGen\n";
        $emailContent .= "CODE: " . $code . "\n";
        $emailContent .= "========================================\n";
        file_put_contents($emailFile, $emailContent, FILE_APPEND);

        // Enregistrer dans l'historique - Requête SQL directe
        $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
        $stmt->execute([
            ':uid' => $userData['id_user'],
            ':action' => 'password_reset_request',
            ':desc' => 'Demande de réinitialisation de mot de passe'
        ]);

        $_SESSION['reset_email'] = $email;
        $_SESSION['success'] = 'Un code de vérification a été envoyé à votre email. Vérifiez votre boîte de réception.';
        header('Location: /user_nextgen/reset-password/verify');
        exit;
    }

    // Afficher le formulaire de vérification du code
    public function showVerifyForm() {
        if (!isset($_SESSION['reset_email'])) {
            header('Location: /user_nextgen/forgot-password');
            exit;
        }

        require_once __DIR__ . '/../views/auth/verify_code.php';
    }

    // Vérifier le code
    public function verifyCode() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/reset-password/verify');
            exit;
        }

        if (!isset($_SESSION['reset_email'])) {
            header('Location: /user_nextgen/forgot-password');
            exit;
        }

        $code = trim($_POST['code'] ?? '');

        $errors = [];
        if ($code === '') {
            $errors['code'] = 'Code requis';
        } elseif (strlen($code) !== 6 || !ctype_digit($code)) {
            $errors['code'] = 'Code invalide (6 chiffres requis)';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /user_nextgen/reset-password/verify');
            exit;
        }

        // Vérifier le code - Requête SQL directe
        $stmt = $this->pdo->prepare("
            SELECT pr.*, u.id_user, u.nom, u.prenom, u.email 
            FROM password_resets pr
            JOIN utilisateur u ON u.id_user = pr.id_user
            WHERE u.email = :email AND pr.code = :code AND pr.used = 0 AND pr.expiration > NOW()
        ");
        $stmt->execute([
            ':email' => $_SESSION['reset_email'],
            ':code' => $code
        ]);
        $resetData = $stmt->fetch();

        if (!$resetData) {
            $_SESSION['errors'] = ['code' => 'Code invalide ou expiré'];
            header('Location: /user_nextgen/reset-password/verify');
            exit;
        }

        // Code valide, passer à l'étape suivante
        $_SESSION['reset_token'] = $resetData['token'];
        $_SESSION['reset_user_id'] = $resetData['id_user'];
        header('Location: /user_nextgen/reset-password/new');
        exit;
    }

    // Afficher le formulaire de nouveau mot de passe
    public function showNewPasswordForm() {
        if (!isset($_SESSION['reset_token']) || !isset($_SESSION['reset_user_id'])) {
            header('Location: /user_nextgen/forgot-password');
            exit;
        }

        require_once __DIR__ . '/../views/auth/new_password.php';
    }

    // Enregistrer le nouveau mot de passe
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/reset-password/new');
            exit;
        }

        if (!isset($_SESSION['reset_token']) || !isset($_SESSION['reset_user_id'])) {
            header('Location: /user_nextgen/forgot-password');
            exit;
        }

        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        $errors = [];
        if (strlen($password) < 6) {
            $errors['password'] = 'Mot de passe minimum 6 caractères';
        }
        if ($password !== $password_confirm) {
            $errors['password_confirm'] = 'Les mots de passe ne correspondent pas';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /user_nextgen/reset-password/new');
            exit;
        }

        // Mettre à jour le mot de passe - Requête SQL directe
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET mot_de_passe = :pwd WHERE id_user = :id");
        $stmt->execute([':pwd' => $hash, ':id' => $_SESSION['reset_user_id']]);
        
        // Marquer le code comme utilisé - Requête SQL directe
        $stmt = $this->pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = :token");
        $stmt->execute([':token' => $_SESSION['reset_token']]);

        // Enregistrer dans l'historique - Requête SQL directe
        $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
        $stmt->execute([
            ':uid' => $_SESSION['reset_user_id'],
            ':action' => 'password_changed',
            ':desc' => 'Mot de passe réinitialisé avec succès'
        ]);

        // Nettoyer la session
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_user_id']);

        $_SESSION['success'] = 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.';
        header('Location: /user_nextgen/login');
        exit;
    }

    // Annuler la réinitialisation
    public function cancel() {
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_user_id']);
        header('Location: /user_nextgen/login');
        exit;
    }
}
