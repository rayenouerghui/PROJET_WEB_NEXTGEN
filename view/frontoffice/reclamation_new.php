<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header('Location: connexion.php');
    exit;
}

require_once '../../controller/ReclamationController.php';
require_once '../../controller/jeuController.php';

$reclamationController = new ReclamationController();
$jeuController = new JeuController();

$error = '';
$success = '';
$warning = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_jeu = !empty($_POST['id_jeu']) ? (int)$_POST['id_jeu'] : null;
    
    if (empty($type) || empty($description)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        $reclamation = new Reclamation();
        $reclamation->setIdUser($_SESSION['user']['id'])
                   ->setIdJeu($id_jeu)
                   ->setType($type)
                   ->setDescription($description)
                   ->setDateReclamation(date('Y-m-d H:i:s'))
                   ->setStatut('En attente');
        
        $result = $reclamationController->create($reclamation);
        
        if ($result['success']) {
            $success = '✅ Votre réclamation a été envoyée avec succès!';
            if (!empty($result['ai_score'])) {
                $ai_score = round($result['ai_score'] * 100);
                $success .= " (Score qualité: {$ai_score}%)";
            }
            $_POST = [];
        } else {
            $error = $result['message'] ?? 'Une erreur est survenue.';
            if (!empty($result['needs_rewrite'])) {
                $warning = 'Votre message pourrait être amélioré.';
            }
        }
    }
}

$jeux = $jeuController->afficherJeux();
require_once '../../models/Reclamation.php';
?>

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réclamation – NextGen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
  <link rel="stylesheet" href="../assets/green-theme.css">
<body class="h-full bg-gray-50 font-sans" x-data="{ sidebarOpen: true }">

<div class="flex min-h-screen">

  <!-- SIDEBAR -->
  <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
         class="fixed inset-y-0 left-0 bg-white shadow-xl transition-all duration-300 flex flex-col z-50 overflow-y-auto">
    
    <div class="p-6 border-b">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">NG</div>
        <h2 :class="{ 'hidden': !sidebarOpen }" class="text-2xl font-bold text-gray-800">NextGen</h2>
      </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">
      <a href="index.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-teal-50 text-gray-700 hover:text-teal-600">
        <i class="fas fa-home text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Accueil</span>
      </a>

      <a href="catalogue.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-teal-50 text-gray-700 hover:text-teal-600">
        <i class="fas fa-gamepad text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Catalogue</span>
      </a>

      <a href="reclamation.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left bg-teal-50 text-teal-600 font-medium">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Réclamations</span>
      </a>

      <a href="profil.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-teal-50 text-gray-700 hover:text-teal-600">
        <i class="fas fa-user text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Mon Profil</span>
      </a>
    </nav>

    <div class="p-4 border-t space-y-3" :class="{ 'hidden': !sidebarOpen }">
      <a href="logout.php" class="w-full py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium text-center">Déconnexion</a>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="flex-1 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-20'">

    <!-- Header -->
    <header class="bg-white shadow-sm border-b sticky top-0 z-40">
      <div class="px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-teal-600">
            <i class="fas fa-bars text-2xl"></i>
          </button>
          <h1 class="text-2xl font-bold text-gray-800">Nouvelle Réclamation</h1>
        </div>
        <div class="flex items-center space-x-5">
          <p class="text-sm text-gray-600">Besoin d'aide? <a href="#" class="text-teal-600 font-medium">Contact</a></p>
          <img src="https://randomuser.me/api/portraits/women/44.jpg" class="w-10 h-10 rounded-full ring-2 ring-teal-500 object-cover" alt="Profil">
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="p-8 max-w-4xl mx-auto">

      <!-- Alert Messages -->
      <?php if ($error): ?>
      <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start space-x-3">
        <i class="fas fa-exclamation-circle text-red-600 mt-1"></i>
        <div>
          <h3 class="font-semibold text-red-800">Erreur</h3>
          <p class="text-red-700 text-sm"><?= htmlspecialchars($error) ?></p>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($success): ?>
      <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start space-x-3">
        <i class="fas fa-check-circle text-green-600 mt-1"></i>
        <div>
          <h3 class="font-semibold text-green-800">Succès</h3>
          <p class="text-green-700 text-sm"><?= htmlspecialchars($success) ?></p>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($warning): ?>
      <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-start space-x-3">
        <i class="fas fa-warning text-yellow-600 mt-1"></i>
        <div>
          <h3 class="font-semibold text-yellow-800">Attention</h3>
          <p class="text-yellow-700 text-sm"><?= htmlspecialchars($warning) ?></p>
        </div>
      </div>
      <?php endif; ?>

      <!-- Form Card -->
      <div class="bg-white rounded-xl shadow-sm p-8">
        <form method="POST" class="space-y-6">

          <!-- Type -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Type de Réclamation <span class="text-red-500">*</span></label>
            <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
              <option value="">Sélectionnez un type...</option>
              <option value="defaut_produit">Défaut du produit</option>
              <option value="livraison_retard">Livraison en retard</option>
              <option value="produit_manquant">Produit manquant</option>
              <option value="autre">Autre</option>
            </select>
          </div>

          <!-- Jeu Concerné -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Jeu Concerné (optionnel)</label>
            <select name="id_jeu" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
              <option value="">-- Aucun --</option>
              <?php if ($jeux): ?>
                <?php foreach ($jeux as $jeu): ?>
                  <option value="<?= $jeu->getIdJeu() ?>"><?= htmlspecialchars($jeu->getTitre()) ?> (<?= number_format($jeu->getPrix(), 2) ?> TND)</option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Description Détaillée <span class="text-red-500">*</span></label>
            <textarea name="description" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none" 
              placeholder="Décrivez votre réclamation en détail..." required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            <p class="text-xs text-gray-500 mt-1">Minimum 10 caractères, maximum 5000 caractères</p>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transition flex items-center justify-center space-x-2">
              <i class="fas fa-paper-plane"></i>
              <span>Envoyer la Réclamation</span>
            </button>
            <button type="reset" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
              Réinitialiser
            </button>
          </div>
        </form>
      </div>

      <!-- Info Section -->
      <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2 flex items-center space-x-2">
          <i class="fas fa-info-circle"></i>
          <span>Comment fonctionne la validation IA?</span>
        </h3>
        <p class="text-blue-800 text-sm">Votre réclamation est analysée par un système d'intelligence artificielle avancé qui vérifie:</p>
        <ul class="text-blue-800 text-sm mt-2 space-y-1 ml-6">
          <li>✓ La clarté et la cohérence du message</li>
          <li>✓ L'absence de contenu offensant</li>
          <li>✓ La pertinence par rapport à une réclamation</li>
          <li>✓ La structure naturelle du texte</li>
        </ul>
      </div>

    </main>
  </div>
</div>

</body>
</html>
