<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../controller/LivraisonController.php';
$controller = new LivraisonController();

if (isset($_GET['delete'])) {
    $controller->deleteLivraison((int)$_GET['delete']);
    header('Location: admin_livraisons.php?success=1');
    exit;
}

$livraisons = $controller->getAllLivraisons();
$success = isset($_GET['success']);
?>

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Livraisons – NextGen Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    .statut-badge { @apply px-4 py-2 rounded-full text-white font-bold text-sm; }
    .commandee { @apply bg-orange-500; }
    .emballee { @apply bg-purple-600; }
    .en_transit { @apply bg-emerald-500; }
    .livree { @apply bg-pink-600; }
  </style>
</head>
<body class="h-full bg-gray-50 font-sans" x-data="{ sidebarOpen: true, success: <?= $success ? 'true' : 'false' ?> }">

<div class="flex h-full">
  <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-white shadow-xl transition-all duration-300 flex flex-col z-10 sticky top-0 h-screen overflow-y-auto">
    <div class="p-6 border-b">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">NG</div>
        <h2 :class="{ 'hidden': !sidebarOpen }" class="text-2xl font-bold text-gray-800">NextGen Admin</h2>
      </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">
  <?php $current = basename($_SERVER['PHP_SELF']); ?>

  <a href="Accueil.php" 
     class="flex items-center space-x-3 px-4 py-3 rounded-lg transition 
            <?= $current === 'Accueil.php' ? 'bg-teal-50 text-teal-600 font-medium' : 'hover:bg-teal-50 text-gray-700 hover:text-teal-600' ?>">
    <i class="fas fa-tachometer-alt text-xl"></i>
    <span :class="{ 'hidden': !sidebarOpen }">Accueil</span>
  </a>

  <a href="admin_jeux.php" 
     class="flex items-center space-x-3 px-4 py-3 rounded-lg transition 
            <?= $current === 'admin_jeux.php' ? 'bg-teal-50 text-teal-600 font-medium' : 'hover:bg-teal-50 text-gray-700 hover:text-teal-600' ?>">
    <i class="fas fa-gamepad text-xl"></i>
    <span :class="{ 'hidden': !sidebarOpen }">Jeux</span>
  </a>

  <a href="admin_users.php" 
     class="flex items-center space-x-3 px-4 py-3 rounded-lg transition 
            <?= $current === 'admin_users.php' ? 'bg-teal-50 text-teal-600 font-medium' : 'hover:bg-teal-50 text-gray-700 hover:text-teal-600' ?>">
    <i class="fas fa-users text-xl"></i>
    <span :class="{ 'hidden': !sidebarOpen }">Utilisateurs</span>
  </a>

  <a href="admin_categories.php" 
     class="flex items-center space-x-3 px-4 py-3 rounded-lg transition 
            <?= $current === 'admin_categories.php' ? 'bg-teal-50 text-teal-600 font-medium' : 'hover:bg-teal-50 text-gray-700 hover:text-teal-600' ?>">
    <i class="fas fa-tags text-xl"></i>
    <span :class="{ 'hidden': !sidebarOpen }">Catégories</span>
  </a>

  <a href="admin_livraisons.php" 
     class="flex items-center space-x-3 px-4 py-3 rounded-lg transition 
            <?= $current === 'admin_livraisons.php' ? 'bg-teal-50 text-teal-600 font-medium' : 'hover:bg-teal-50 text-gray-700 hover:text-teal-600' ?>">
    <i class="fas fa-truck text-xl"></i>
    <span :class="{ 'hidden': !sidebarOpen }">Livraisons</span>
  </a>
  <a href="admin_historique.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?= $current==='admin_historique.php'?'bg-teal-50 text-teal-600 font-medium':'' ?> hover:bg-teal-50">
  <i class="fas fa-history text-xl"></i>
  <span :class="{ 'hidden': !sidebarOpen }">Historique</span>
</a>
</nav>

    <div class="p-4 border-t space-y-3" :class="{ 'hidden': !sidebarOpen }">
      <a href="../frontoffice/catalogue.php" class="block text-center py-2 px-4 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm">Voir le Catalogue</a>
      <a href="../frontoffice/index.php" class="block text-center py-2 px-4 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm">Voir le Site</a>
      <button onclick="location.href='logout.php'" class="w-full py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium">Déconnexion</button>
    </div>
  </aside>

  <div class="flex-1 flex flex-col">
    <header class="bg-white shadow-sm border-b">
      <div class="px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-teal-600"><i class="fas fa-bars text-2xl"></i></button>
          <h1 class="text-2xl font-bold text-gray-800">Gestion des Livraisons</h1>
        </div>
        <div class="flex items-center space-x-4">
          <div class="relative">
            <input type="text" placeholder="Rechercher..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>
          <img src="https://randomuser.me/api/portraits/women/44.jpg" class="w-10 h-10 rounded-full ring-2 ring-teal-500" alt="Admin">
        </div>
      </div>
    </header>

    <!-- Success Toast -->
    <div x-show="success" x-transition class="fixed top-6 right-6 z-50">
      <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center space-x-3">
        <i class="fas fa-check-circle text-2xl"></i>
        <p class="font-bold">Livraison supprimée avec succès !</p>
        <button @click="success = false" class="text-white"><i class="fas fa-times"></i></button>
      </div>
    </div>

    <main class="flex-1 p-8">
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Client</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Jeu</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Adresse</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Statut</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Date</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php if (empty($livraisons)): ?>
                <tr><td colspan="7" class="text-center py-12 text-gray-500 text-lg">Aucune livraison en cours</td></tr>
              <?php else: foreach ($livraisons as $l): ?>
                <tr class="hover:bg-gray-50 transition">
                  <td class="px-6 py-4 text-sm">#<?= $l->getIdLivraison() ?></td>
                  <td class="px-6 py-4 font-medium"><?= htmlspecialchars($l->prenom_user . ' ' . $l->nom_user) ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($l->nom_jeu) ?></td>
                  <td class="px-6 py-4 text-sm max-w-xs truncate"><?= htmlspecialchars($l->getAdresseComplete()) ?></td>
                  <td class="px-6 py-4">
                    <span class="statut-badge <?= $l->getStatut() ?>">
                      <?= ucfirst(str_replace('_', ' ', $l->getStatut())) ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm"><?= date('d/m/Y H:i', strtotime($l->getDateCommande())) ?></td>
                  <td class="px-6 py-4">
                    <div class="flex space-x-2">
                      <a href="modifier_livraison.php?id=<?= $l->getIdLivraison() ?>" class="text-purple-600 hover:text-purple-800"><i class="fas fa-map-marked-alt"></i> Destination</a>
                      <a href="modifier_trajet.php?id=<?= $l->getIdLivraison() ?>" class="text-emerald-600 hover:text-emerald-800"><i class="fas fa-truck"></i> Livreur</a>
                      <a href="?delete=<?= $l->getIdLivraison() ?>" onclick="return confirm('Supprimer cette livraison ?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i> Supprimer</a>
                      <a href="../frontoffice/tracking.php?id_livraison=<?= $l->getIdLivraison() ?>" target="_blank" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-eye"></i> Suivi</a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>