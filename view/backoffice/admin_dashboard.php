<?php
session_start();

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
  <title>NextGen Admin – Tableau de Bord</title>
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
        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">AD</div>
        <h2 :class="{ 'hidden': !sidebarOpen }" class="text-2xl font-bold text-gray-800">NextGen Admin</h2>
      </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">
      <a href="admin_dashboard.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left bg-red-50 text-red-600 font-medium">
        <i class="fas fa-tachometer-alt text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Tableau de Bord</span>
      </a>

      <a href="admin_jeux.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-red-50 text-gray-700 hover:text-red-600">
        <i class="fas fa-gamepad text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Jeux</span>
      </a>

      <a href="admin_users.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-red-50 text-gray-700 hover:text-red-600">
        <i class="fas fa-users text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Utilisateurs</span>
      </a>

      <a href="admin_categories.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-red-50 text-gray-700 hover:text-red-600">
        <i class="fas fa-tags text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Catégories</span>
      </a>

      <a href="admin_livraisons.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-red-50 text-gray-700 hover:text-red-600">
        <i class="fas fa-truck text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Livraisons</span>
      </a>

      <a href="admin_reclamations.php" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition text-left hover:bg-red-50 text-gray-700 hover:text-red-600">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <span :class="{ 'hidden': !sidebarOpen }">Réclamations</span>
      </a>
    </nav>

    <div class="p-4 border-t space-y-3" :class="{ 'hidden': !sidebarOpen }">
      <a href="../frontoffice/catalogue.php" class="block text-center py-2 px-4 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm">Voir le Catalogue</a>
      <a href="logout.php" class="w-full py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium text-center">Déconnexion</a>
    </div>
  </aside>

  <!-- MAIN CONTENT – DYNAMIC MARGIN -->
  <div class="flex-1 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-20'">

    <!-- Header -->
    <header class="bg-white shadow-sm border-b sticky top-0 z-40">
      <div class="px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-red-600">
            <i class="fas fa-bars text-2xl"></i>
          </button>
          <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord Admin</h1>
        </div>
        <div class="flex items-center space-x-5">
          <div class="text-right">
            <p class="text-sm font-medium text-gray-800">Administrateur</p>
            <p class="text-xs text-gray-500">System Admin</p>
          </div>
          <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-10 h-10 rounded-full ring-2 ring-red-500 object-cover" alt="Admin">
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="p-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Stats Cards -->
        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-5 hover:shadow-md transition">
          <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
            <i class="fas fa-gamepad text-2xl text-red-600"></i>
          </div>
          <div>
            <h3 class="text-3xl font-bold"><?= $totalJeux ?></h3>
            <p class="text-gray-500">Jeux</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-5 hover:shadow-md transition">
          <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
            <i class="fas fa-users text-2xl text-green-600"></i>
          </div>
          <div>
            <h3 class="text-3xl font-bold"><?= $totalUsers ?></h3>
            <p class="text-gray-500">Utilisateurs</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-5 hover:shadow-md transition">
          <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-truck text-2xl text-blue-600"></i>
          </div>
          <div>
            <h3 class="text-3xl font-bold">12</h3>
            <p class="text-gray-500">Livraisons</p>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-5 hover:shadow-md transition">
          <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
            <i class="fas fa-exclamation-circle text-2xl text-orange-600"></i>
          </div>
          <div>
            <h3 class="text-3xl font-bold">8</h3>
            <p class="text-gray-500">Réclamations</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Add New Items -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="text-lg font-semibold mb-4 flex items-center space-x-2">
            <i class="fas fa-plus-circle text-red-600"></i>
            <span>Actions Rapides</span>
          </h3>
          <div class="space-y-3">
            <a href="ajouter_jeux.php" class="flex items-center justify-between p-3 bg-red-50 hover:bg-red-100 rounded-lg transition">
              <div class="flex items-center space-x-2">
                <i class="fas fa-plus text-red-600"></i>
                <span class="font-medium text-gray-800">Ajouter un Jeu</span>
              </div>
              <i class="fas fa-arrow-right text-red-600"></i>
            </a>
            <a href="ajouter_users.php" class="flex items-center justify-between p-3 bg-green-50 hover:bg-green-100 rounded-lg transition">
              <div class="flex items-center space-x-2">
                <i class="fas fa-plus text-green-600"></i>
                <span class="font-medium text-gray-800">Ajouter un Utilisateur</span>
              </div>
              <i class="fas fa-arrow-right text-green-600"></i>
            </a>
            <a href="ajouter_categories.php" class="flex items-center justify-between p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
              <div class="flex items-center space-x-2">
                <i class="fas fa-plus text-blue-600"></i>
                <span class="font-medium text-gray-800">Ajouter une Catégorie</span>
              </div>
              <i class="fas fa-arrow-right text-blue-600"></i>
            </a>
          </div>
        </div>

        <!-- Recent Stats -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="text-lg font-semibold mb-4 flex items-center space-x-2">
            <i class="fas fa-chart-bar text-blue-600"></i>
            <span>État du Système</span>
          </h3>
          <div class="space-y-4">
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Jeux Actifs</span>
                <span class="text-sm font-bold text-red-600"><?= round(($totalJeux / 100) * 100) ?>%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-red-600 h-2 rounded-full" style="width: <?= min(($totalJeux / 50) * 100, 100) ?>%"></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Utilisateurs Enregistrés</span>
                <span class="text-sm font-bold text-green-600"><?= round(($totalUsers / 200) * 100) ?>%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: <?= min(($totalUsers / 200) * 100, 100) ?>%"></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Système IA Actif</span>
                <span class="text-sm font-bold text-green-600">100%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Management Sections -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Jeux Management -->
        <a href="admin_jeux.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition group">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Gestion des Jeux</h3>
            <i class="fas fa-gamepad text-2xl text-red-600 group-hover:scale-110 transition"></i>
          </div>
          <p class="text-gray-500 text-sm mb-4">Ajouter, modifier ou supprimer des jeux du catalogue</p>
          <div class="text-red-600 font-medium flex items-center space-x-1 group-hover:space-x-3 transition">
            <span>Accéder</span>
            <i class="fas fa-arrow-right"></i>
          </div>
        </a>

        <!-- Users Management -->
        <a href="admin_users.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition group">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Gestion des Utilisateurs</h3>
            <i class="fas fa-users text-2xl text-green-600 group-hover:scale-110 transition"></i>
          </div>
          <p class="text-gray-500 text-sm mb-4">Gérer les profils et permissions des utilisateurs</p>
          <div class="text-green-600 font-medium flex items-center space-x-1 group-hover:space-x-3 transition">
            <span>Accéder</span>
            <i class="fas fa-arrow-right"></i>
          </div>
        </a>

        <!-- Categories Management -->
        <a href="admin_categories.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition group">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Gestion des Catégories</h3>
            <i class="fas fa-tags text-2xl text-blue-600 group-hover:scale-110 transition"></i>
          </div>
          <p class="text-gray-500 text-sm mb-4">Organiser les catégories de jeux</p>
          <div class="text-blue-600 font-medium flex items-center space-x-1 group-hover:space-x-3 transition">
            <span>Accéder</span>
            <i class="fas fa-arrow-right"></i>
          </div>
        </a>

        <!-- Deliveries Management -->
        <a href="admin_livraisons.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition group">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Gestion des Livraisons</h3>
            <i class="fas fa-truck text-2xl text-orange-600 group-hover:scale-110 transition"></i>
          </div>
          <p class="text-gray-500 text-sm mb-4">Tracker et gérer les livraisons</p>
          <div class="text-orange-600 font-medium flex items-center space-x-1 group-hover:space-x-3 transition">
            <span>Accéder</span>
            <i class="fas fa-arrow-right"></i>
          </div>
        </a>

        <!-- Complaints Management -->
        <a href="admin_reclamations.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition group">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Gestion des Réclamations</h3>
            <i class="fas fa-exclamation-circle text-2xl text-yellow-600 group-hover:scale-110 transition"></i>
          </div>
          <p class="text-gray-500 text-sm mb-4">Voir et traiter les réclamations des clients</p>
          <div class="text-yellow-600 font-medium flex items-center space-x-1 group-hover:space-x-3 transition">
            <span>Accéder</span>
            <i class="fas fa-arrow-right"></i>
          </div>
        </a>

        <!-- System Info -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Système</h3>
            <i class="fas fa-cogs text-2xl text-purple-600"></i>
          </div>
          <p class="text-gray-500 text-sm mb-4">Version: 1.0.0 | IA: Actif ✓</p>
          <div class="text-purple-600 font-medium flex items-center space-x-1">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
            <span>Système opérationnel</span>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

</body>
</html>
