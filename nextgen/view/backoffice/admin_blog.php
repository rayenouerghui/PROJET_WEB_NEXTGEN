<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../controller/BlogController.php';
require_once '../../controller/CategoryController.php';

$blogController = new BlogController();
$categoryController = new CategoryController();

$articles = $blogController->index();
$categories = $categoryController->getAllCategories();

// Handle actions
$action = $_POST['action'] ?? '';
$message = '';
$errors = [];

// Create article
if ($action === 'create_article' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleData = [
        'titre' => trim($_POST['titre'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'id_categorie' => (int)($_POST['id_categorie'] ?? 0),
        'id_auteur' => 1
    ];
    
    $result = $blogController->create($articleData, $_FILES);
    if ($result['success']) {
        header('Location: admin_blog.php?success=create');
        exit;
    } else {
        $message = $result['message'] ?? 'Erreur lors de la création';
    }
}

// Delete article
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $result = $blogController->delete($id);
    if ($result['success']) {
        header('Location: admin_blog.php?success=delete');
        exit;
    } else {
        $message = $result['message'] ?? 'Erreur lors de la suppression';
    }
}

$success = isset($_GET['success']);
?>

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion du Blog – NextGen Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Exo+2:wght@500;700&display=swap" rel="stylesheet">
  <style>
    body { background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); font-family: 'Exo 2', sans-serif; color: #e0e7ff; }
    .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border: 1px solid rgba(139,92,246,0.3); box-shadow: 0 8px 32px rgba(0,0,0,0.4); }
    .neon-text { text-shadow: 0 0 30px #8b5cf6; }
    .btn-neon { background: linear-gradient(45deg, #8b5cf6, #ec4899); box-shadow: 0 0 30px rgba(139,92,246,0.6); }
    .btn-neon:hover { transform: translateY(-3px); box-shadow: 0 0 40px rgba(139,92,246,0.9); }
    .sidebar-closed { width: 5rem !important; }
    .sidebar-open { width: 16rem; }
  </style>
</head>
<body class="h-full" x-data="{ sidebarOpen: true }">

  <!-- SIDEBAR -->
  <aside :class="sidebarOpen ? 'sidebar-open' : 'sidebar-closed'" 
         class="glass fixed inset-y-0 left-0 z-40 flex flex-col transition-all duration-300 overflow-hidden">
    <div class="p-5 text-center border-b border-purple-500/30">
      <h1 class="text-3xl font-black bg-gradient-to-r from-cyan-400 to-purple-600 bg-clip-text text-transparent">NEXTGEN</h1>
      <p x-show="sidebarOpen" class="text-purple-300 text-sm font-bold mt-1">ADMIN PANEL</p>
    </div>
    <nav class="flex-1 px-3 py-6 space-y-2">
      <?php $current = basename($_SERVER['PHP_SELF']); ?>
      <a href="admin_jeux.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_jeux.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
        <i class="fas fa-gamepad w-8 text-xl"></i><span x-show="sidebarOpen">Jeux</span>
      </a>
      <a href="admin_users.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_users.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
        <i class="fas fa-users w-8 text-xl"></i><span x-show="sidebarOpen">Utilisateurs</span>
      </a>
      <a href="admin_categories.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_categories.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
        <i class="fas fa-tags w-8 text-xl"></i><span x-show="sidebarOpen">Catégories</span>
      </a>
      <a href="admin_historique.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_historique.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
        <i class="fas fa-history w-8 text-xl"></i><span x-show="sidebarOpen">Historique</span>
      </a>
      <a href="admin_reclamations.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_reclamations.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
        <i class="fas fa-exclamation-triangle w-8 text-xl"></i><span x-show="sidebarOpen">Réclamations</span>
      </a>
      <a href="admin_livraisons.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_livraisons.php'?'bg-gradient-to-r from-orange-600 to-amber-600 text-white':'hover:bg-white/10 text-orange-300' ?>">
        <i class="fas fa-truck w-8 text-xl"></i><span x-show="sidebarOpen">Livraisons</span>
      </a>
      <a href="admin_blog.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold">
        <i class="fas fa-blog w-8 text-xl"></i><span x-show="sidebarOpen">Blog</span>
      </a>
      <a href="admin_events.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_events.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
        <i class="fas fa-calendar-alt w-8 text-xl"></i><span x-show="sidebarOpen">Événements</span>
      </a>
    </nav>
    <div class="p-4 border-t border-purple-500/30">
      <button @click="sidebarOpen = !sidebarOpen" class="w-full py-4 hover:bg-white/5 rounded-lg"><i class="fas fa-chevron-left mx-auto text-xl text-purple-300" :class="{'rotate-180': !sidebarOpen}"></i></button>
      <a href="../frontoffice/index.php" class="flex items-center justify-center gap-3 px-4 py-4 mt-3 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-600 text-white font-bold hover:scale-105 transition">
        <i class="fas fa-home"></i><span x-show="sidebarOpen">Retour accueil</span>
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="min-h-screen transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-20'">
    <div class="p-8 max-w-7xl mx-auto">
      <h1 class="text-5xl font-black neon-text text-center mb-12">Gestion du Blog</h1>
      <?php if ($success): ?>
        <div class="glass p-6 rounded-2xl mb-8 text-center text-green-300 font-bold text-xl"><?php
          switch($_GET['success']) {
            case 'create': echo 'Article créé avec succès !'; break;
            case 'delete': echo 'Article supprimé avec succès !'; break;
            default: echo 'Action réussie !';
          }
        ?></div>
      <?php endif; ?>

      <?php if ($message): ?>
        <div class="glass p-6 rounded-2xl mb-8 text-center text-red-300 font-bold text-xl">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <!-- Create Article Form -->
      <div class="glass rounded-2xl p-6 mb-8">
        <h2 class="text-2xl font-bold text-cyan-300 mb-4">Créer un nouvel article</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="action" value="create_article">
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-purple-300 mb-2">Titre *</label>
            <input type="text" name="titre" required class="w-full px-4 py-2 bg-black/40 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-purple-300 mb-2">Catégorie *</label>
            <select name="id_categorie" required class="w-full px-4 py-2 bg-black/40 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
              <option value="">Sélectionner une catégorie</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= isset($cat['id_categorie']) ? $cat['id_categorie'] : (isset($cat->id_categorie) ? $cat->id_categorie : '') ?>">
                  <?= htmlspecialchars(isset($cat['nom']) ? $cat['nom'] : (isset($cat->nom) ? $cat->nom : '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-purple-300 mb-2">Contenu *</label>
            <textarea name="content" required rows="6" class="w-full px-4 py-2 bg-black/40 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-purple-300 mb-2">Image</label>
            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 bg-black/40 border border-purple-500/30 rounded-lg text-white">
          </div>

          <button type="submit" class="btn-neon text-white font-bold py-3 px-8 rounded-xl">
            Créer l'article
          </button>
        </form>
      </div>

      <!-- Articles List -->
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-purple-500/30">
          <h2 class="text-2xl font-bold text-cyan-300">Articles (<?= count($articles) ?>)</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gradient-to-r from-purple-800 to-pink-800">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-cyan-300 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-cyan-300 uppercase">Titre</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-cyan-300 uppercase">Catégorie</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-cyan-300 uppercase">Date</th>
                <th class="px-6 py-3 text-center text-xs font-bold text-cyan-300 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-purple-500/20">
              <?php if (empty($articles)): ?>
                <tr>
                  <td colspan="5" class="px-6 py-8 text-center text-gray-400">Aucun article</td>
                </tr>
              <?php else: ?>
                <?php foreach ($articles as $article): ?>
                  <tr class="hover:bg-white/5 transition">
                    <td class="px-6 py-4 text-sm text-purple-300 font-medium">#<?= $article['id_article'] ?? '' ?></td>
                    <td class="px-6 py-4 text-sm font-semibold text-white"><?= htmlspecialchars($article['titre'] ?? '') ?></td>
                    <td class="px-6 py-4 text-sm text-cyan-300"><?= htmlspecialchars($article['categorie_nom'] ?? 'Non catégorisé') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-400"><?= isset($article['date_publication']) ? date('d/m/Y', strtotime($article['date_publication'])) : '' ?></td>
                    <td class="px-6 py-4 text-center space-x-4">
                      <a href="articleDetail.php?id=<?= $article['id_article'] ?? '' ?>" class="text-cyan-400 hover:text-cyan-200 font-medium">Voir</a>
                      <a href="admin_article.php?id=<?= $article['id_article'] ?? '' ?>" class="text-blue-400 hover:text-blue-200 font-medium">Modifier</a>
                      <a href="?delete=<?= $article['id_article'] ?? '' ?>" onclick="return confirm('Supprimer cet article ?')" class="text-red-400 hover:text-red-300 font-medium">Supprimer</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

</body>
</html>

