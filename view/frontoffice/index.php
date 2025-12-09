<?php 
session_start(); 

if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}

require_once '../../controller/jeuController.php';
require_once '../../controller/userController.php';
require_once '../../controller/ReclamationController.php';

$jeuController = new JeuController();
$userController = new userController();
$reclamationController = new ReclamationController();

$totalJeux = count($jeuController->afficherJeux());
$totalUsers = count($userController->getAllUsers());
?>

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NextGen – Accueil</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="h-full bg-gray-50 font-sans" x-data="{ sidebarOpen: true }">

<div class="flex min-h-screen">

  <!-- FIXED SIDEBAR -->
  <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
         class="fixed inset-y-0 left-0 bg-white shadow-xl transition-all duration-300 flex flex-col z-50 overflow-y-auto">
    
    <div class="p-6 border-b">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">NG</div>
        <h2 :class="{ 'hidden': !sidebarOpen }" class="text-2xl font-bold text-gray-800">NextGen</h2>
      </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">
      <a href="index.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left bg-teal-50 text-teal-600 font-medium">
        <i class="fas fa-home text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Accueil</span>
      </a>

      <a href="catalogue.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-teal-50 text-gray-700 hover:text-teal-600">
        <i class="fas fa-gamepad text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Catalogue</span>
      </a>

      <a href="reclamation.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-teal-50 text-gray-700 hover:text-teal-600">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Réclamations</span>
      </a>

      <a href="profil.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-teal-50 text-gray-700 hover:text-teal-600">
        <i class="fas fa-user text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Mon Profil</span>
      </a>
    </nav>

    <div class="p-4 border-t space-y-3" :class="{ 'hidden': !sidebarOpen }">
      <a href="catalogue.php" class="block text-center py-2 px-4 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm">Voir le Catalogue</a>
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
          <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord</h1>
        </div>
        <div class="flex items-center space-x-5">
          <a href="../backoffice/admin_dashboard.php" class="flex items-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-sm">
            <i class="fas fa-crown"></i>
            <span>Admin</span>
          </a>
          <div class="text-right">
            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($_SESSION['user']['prenom'] ?? 'Utilisateur') ?></p>
            <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?></p>
          </div>
          <img src="https://randomuser.me/api/portraits/women/44.jpg" class="w-10 h-10 rounded-full ring-2 ring-teal-500 object-cover" alt="Profil">
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="p-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Stats Cards -->
        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-5">
          <div class="w-14 h-14 bg-teal-100 rounded-full flex items-center justify-center">
            <i class="fas fa-gamepad text-2xl text-teal-600"></i>
          </div>
          <div>
            <h3 class="text-3xl font-bold"><?= $totalJeux ?></h3>
            <p class="text-gray-500">Jeux disponibles</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-5">
          <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
            <i class="fas fa-users text-2xl text-green-600"></i>
          </div>
          <div>
            <h3 class="text-3xl font-bold"><?= $totalUsers ?></h3>
            <p class="text-gray-500">Utilisateurs</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-5">
          <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
          </div>
          <div>
            <h3 class="text-3xl font-bold">24</h3>
            <p class="text-gray-500">Commandes</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-5">
          <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
            <i class="fas fa-exclamation-circle text-2xl text-orange-600"></i>
          </div>
          <div>
            <h3 class="text-3xl font-bold">5</h3>
            <p class="text-gray-500">Réclamations</p>
          </div>
        </div>
      </div>

      <!-- Welcome Section -->
      <div class="bg-gradient-to-r from-teal-500 to-cyan-600 rounded-xl shadow-lg p-8 text-white mb-10">
        <h2 class="text-3xl font-bold mb-2">Bienvenue, <?= htmlspecialchars($_SESSION['user']['prenom'] ?? 'Utilisateur') ?>! 👋</h2>
        <p class="text-teal-100 mb-4">Explorez notre catalogue de jeux, gérez vos commandes et vos réclamations depuis votre tableau de bord.</p>
        <div class="flex gap-4 flex-wrap">
          <a href="catalogue.php" class="bg-white text-teal-600 font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition flex items-center space-x-2">
            <i class="fas fa-gamepad"></i>
            <span>Explorer les Jeux</span>
          </a>
          <a href="reclamation.php" class="bg-teal-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-teal-700 transition flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Nouvelle Réclamation</span>
          </a>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm">
          <h3 class="text-lg font-semibold mb-4 flex items-center space-x-2">
            <i class="fas fa-star text-yellow-500"></i>
            <span>Jeux Populaires</span>
          </h3>
          <div class="space-y-3">
            <a href="#" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
              <div>
                <p class="font-medium text-gray-800">Cyberpunk 2077</p>
                <p class="text-sm text-gray-500">Action • RPG</p>
              </div>
              <span class="text-teal-600 font-bold">59.99 TND</span>
            </a>
            <a href="#" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
              <div>
                <p class="font-medium text-gray-800">The Witcher 3</p>
                <p class="text-sm text-gray-500">RPG • Aventure</p>
              </div>
              <span class="text-teal-600 font-bold">49.99 TND</span>
            </a>
            <a href="#" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
              <div>
                <p class="font-medium text-gray-800">Elden Ring</p>
                <p class="text-sm text-gray-500">Action • RPG</p>
              </div>
              <span class="text-teal-600 font-bold">69.99 TND</span>
            </a>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
          <h3 class="text-lg font-semibold mb-4 flex items-center space-x-2">
            <i class="fas fa-clipboard-list text-teal-600"></i>
            <span>Statut des Commandes</span>
          </h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="text-gray-800 font-medium">Livrées</span>
              </div>
              <span class="text-green-600 font-bold">18</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                <span class="text-gray-800 font-medium">En cours</span>
              </div>
              <span class="text-blue-600 font-bold">4</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                <span class="text-gray-800 font-medium">En attente</span>
              </div>
              <span class="text-yellow-600 font-bold">2</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

</body>
</html>
