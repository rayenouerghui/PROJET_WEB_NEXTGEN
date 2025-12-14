<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../config/config.php';
$pdo = Config::getConnexion();

// === SUPPRESSION ===
if (isset($_GET['delete_article'])) {
    $id = (int)$_GET['delete_article'];
    $pdo->prepare("DELETE FROM article WHERE id_article = ?")->execute([$id]);
    header('Location: admin_article.php?success=1');
    exit;
}

if (isset($_GET['delete_categorie'])) {
    $id = (int)$_GET['delete_categorie'];
    $pdo->prepare("DELETE FROM categorie_article WHERE id_categorie = ?")->execute([$id]);
    header('Location: admin_article.php?success=1');
    exit;
}

if (isset($_GET['delete_comment'])) {
    $id = (int)$_GET['delete_comment'];
    $pdo->prepare("DELETE FROM commentaire WHERE id_commentaire = ?")->execute([$id]);
    header('Location: admin_article.php?success=1');
    exit;
}

// === AJOUT ARTICLE (avec image) ===
if (isset($_POST['action']) && $_POST['action'] === 'add_article') {
    $titre = trim($_POST['titre'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $id_auteur = (int)$_SESSION['user']['id'];
    $image = null;

    if ($titre && $content && $categorie) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/articles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = uniqid('art_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = 'public/uploads/articles/' . $fileName;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO article (titre, content, date_publication, categorie, id_auteur, image) VALUES (?, ?, NOW(), ?, ?, ?)");
        $stmt->execute([$titre, $content, $categorie, $id_auteur, $image]);
    }
    header('Location: admin_article.php?success=1');
    exit;
}

// === MODIFIER ARTICLE (avec image) ===
if (isset($_POST['action']) && $_POST['action'] === 'edit_article') {
    $id = (int)$_POST['id'];
    $titre = trim($_POST['titre'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $image = null;

    if ($titre && $content && $categorie) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/articles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = uniqid('art_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = 'public/uploads/articles/' . $fileName;
            }
        }

        if ($image) {
            $stmt = $pdo->prepare("UPDATE article SET titre = ?, content = ?, categorie = ?, image = ? WHERE id_article = ?");
            $stmt->execute([$titre, $content, $categorie, $image, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE article SET titre = ?, content = ?, categorie = ? WHERE id_article = ?");
            $stmt->execute([$titre, $content, $categorie, $id]);
        }
    }
    header('Location: admin_article.php?success=1');
    exit;
}

// === AJOUT CATEGORIE ===
if (isset($_POST['action']) && $_POST['action'] === 'add_categorie') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom) {
        $stmt = $pdo->prepare("INSERT INTO categorie_article (nom, description) VALUES (?, ?)");
        $stmt->execute([$nom, $description]);
    }
    header('Location: admin_article.php?success=1');
    exit;
}

// === MODIFIER CATEGORIE ===
if (isset($_POST['action']) && $_POST['action'] === 'edit_categorie') {
    $id = (int)$_POST['id'];
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom) {
        $stmt = $pdo->prepare("UPDATE categorie_article SET nom = ?, description = ? WHERE id_categorie = ?");
        $stmt->execute([$nom, $description, $id]);
    }
    header('Location: admin_article.php?success=1');
    exit;
}

// === FETCH DATA ===
$articles = $pdo->query("SELECT * FROM article ORDER BY date_publication DESC")->fetchAll(PDO::FETCH_OBJ);
$categories = $pdo->query("SELECT * FROM categorie_article ORDER BY nom")->fetchAll(PDO::FETCH_OBJ);
$comments = $pdo->query("SELECT c.*, a.titre AS article_titre FROM commentaire c LEFT JOIN article a ON c.id_article = a.id_article ORDER BY c.date_commentaire DESC LIMIT 10")->fetchAll(PDO::FETCH_OBJ);

$success = isset($_GET['success']);
?>

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Articles – NextGen Admin</title>
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
    .error-msg { display: block; margin-top: 0.5rem; }

    /* Fix for edit modal image */
    #currentArticleImage {
      max-height: 300px;
      width: auto;
      max-width: 100%;
      object-fit: contain;
      margin: 0 auto;
      display: block;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(139,92,246,0.4);
    }
  </style>
</head>
<body class="h-full" x-data="{ sidebarOpen: true }">

<!-- SIDEBAR -->
<aside :class="sidebarOpen ? 'sidebar-open' : 'sidebar-closed'" class="glass fixed inset-y-0 left-0 z-40 flex flex-col transition-all duration-300 overflow-hidden">
  <div class="p-5 text-center border-b border-purple-500/30">
    <h1 class="text-3xl font-black bg-gradient-to-r from-cyan-400 to-purple-600 bg-clip-text text-transparent">NEXTGEN</h1>
    <p x-show="sidebarOpen" class="text-purple-300 text-sm font-bold mt-1">ADMIN PANEL</p>
  </div>
  <nav class="flex-1 px-3 py-6 space-y-2">
    <?php $current = basename($_SERVER['PHP_SELF']); ?>
    <a href="accueil.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='accueil.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
      <i class="fas fa-tachometer-alt w-8 text-xl"></i><span x-show="sidebarOpen">Tableau de Bord</span>
    </a>
    <a href="admin_jeux.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_jeux.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
      <i class="fas fa-gamepad w-8 text-xl"></i><span x-show="sidebarOpen">Jeux</span>
    </a>
    <a href="admin_users.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_users.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
      <i class="fas fa-users w-8 text-xl"></i><span x-show="sidebarOpen">Utilisateurs</span>
    </a>
    <a href="admin_categories.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_categories.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
      <i class="fas fa-tags w-8 text-xl"></i><span x-show="sidebarOpen">Catégories</span>
    </a>
    <a href="admin_article.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold">
      <i class="fas fa-newspaper w-8 text-xl"></i><span x-show="sidebarOpen">Articles & Blog</span>
    </a>
    <a href="admin_historique.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition <?= $current==='admin_historique.php'?'bg-gradient-to-r from-purple-600 to-pink-600 text-white':'hover:bg-white/10 text-purple-300' ?>">
      <i class="fas fa-history w-8 text-xl"></i><span x-show="sidebarOpen">Historique</span>
    </a>
    <a href="admin_reclamations.php" class="flex items-center space-x-4 px-4 py-3 rounded-lg transition hover:bg-white/10 text-purple-300">
        <i class="fas fa-exclamation-triangle w-8 text-xl"></i><span x-show="sidebarOpen">Réclamations</span>
      </a>
  </nav>
  <div class="p-4 border-t border-purple-500/30">
    <button @click="sidebarOpen = !sidebarOpen" class="w-full py-4 hover:bg-white/5 rounded-lg">
      <i class="fas fa-chevron-left mx-auto text-xl text-purple-300" :class="{'rotate-180': !sidebarOpen}"></i>
    </button>
    <a href="../frontoffice/index.php" class="flex items-center justify-center gap-3 px-4 py-4 mt-3 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-600 text-white font-bold hover:scale-105 transition">
      <i class="fas fa-home"></i><span x-show="sidebarOpen">Retour accueil</span>
    </a>
  </div>
</aside>

<main class="min-h-screen transition-all duration-300" :class="sidebarOpen ? 'lg:ml-64 ml-20' : 'ml-20'">
  <div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-center text-5xl font-black bg-gradient-to-r from-cyan-400 via-purple-500 to-pink-500 bg-clip-text text-transparent neon-text mb-12">
      GESTION DES ARTICLES & BLOG
    </h1>

    <div class="flex flex-col sm:flex-row gap-6 mb-10 glass p-6 rounded-2xl">
      <form method="GET" class="flex-1">
        <div class="relative">
          <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-purple-400"></i>
          <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Rechercher..." class="w-full pl-12 pr-4 py-3 bg-black/40 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
        </div>
      </form>
      <button onclick="document.getElementById('addArticleModal').classList.remove('hidden')" class="btn-neon text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transition">
        + Nouvel Article
      </button>
    </div>

    <?php if ($success): ?>
      <div class="text-center p-4 mb-6 bg-green-900/70 border border-green-500 rounded-xl text-green-300 font-bold">
        Action effectuée avec succès !
      </div>
    <?php endif; ?>

    <!-- Gestion des Catégories -->
    <div class="glass rounded-2xl overflow-hidden mb-10">
      <div class="p-6 border-b border-purple-500/30">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-bold text-purple-300">Gestion des Catégories</h2>
          <button onclick="document.getElementById('addCategorieModal').classList.remove('hidden')" class="btn-neon text-white font-bold py-3 px-6 rounded-xl">
            + Nouvelle Catégorie
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-purple-800 to-pink-800">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">ID</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Nom</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Description</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-cyan-300 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-purple-500/20">
            <?php foreach ($categories as $cat): ?>
              <tr class="hover:bg-white/5 transition">
                <td class="px-6 py-5 text-purple-300 font-medium">#<?= $cat->id_categorie ?></td>
                <td class="px-6 py-5 text-white font-semibold"><?= htmlspecialchars($cat->nom) ?></td>
                <td class="px-6 py-5 text-gray-300 text-sm"><?= htmlspecialchars($cat->description ?: '—') ?></td>
                <td class="px-6 py-5 text-center space-x-6">
                  <button onclick='openEditCategorieModal(<?= $cat->id_categorie ?>, <?= json_encode($cat->nom) ?>, <?= json_encode($cat->description ?? '') ?>)' class="text-cyan-400 hover:text-cyan-200 font-medium">Modifier</button>
                  <a href="?delete_categorie=<?= $cat->id_categorie ?>" onclick="return confirm('Supprimer cette catégorie ?')" class="text-red-400 hover:text-red-300 font-medium">Supprimer</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Gestion des Articles -->
    <div class="glass rounded-2xl overflow-hidden mb-10">
      <div class="p-6 border-b border-purple-500/30">
        <h2 class="text-2xl font-bold text-purple-300">Gestion des Articles</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-purple-800 to-pink-800">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">ID</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Titre</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Catégorie</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Date</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-cyan-300 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-purple-500/20">
            <?php foreach ($articles as $art): ?>
              <tr class="hover:bg-white/5 transition">
                <td class="px-6 py-5 text-purple-300 font-medium">#<?= $art->id_article ?></td>
                <td class="px-6 py-5 text-white font-semibold"><?= htmlspecialchars($art->titre) ?></td>
                <td class="px-6 py-5">
                  <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-600 text-white">
                    <?= htmlspecialchars($art->categorie) ?>
                  </span>
                </td>
                <td class="px-6 py-5 text-yellow-400 text-sm"><?= date('d/m/Y H:i', strtotime($art->date_publication)) ?></td>
                <td class="px-6 py-5 text-center space-x-6">
                  <button onclick='openEditArticleModal(<?= $art->id_article ?>, <?= json_encode($art->titre) ?>, <?= json_encode($art->content) ?>, <?= json_encode($art->categorie) ?>, <?= json_encode($art->image ?? '') ?>)' class="text-cyan-400 hover:text-cyan-200 font-medium">Modifier</button>
                  <a href="?delete_article=<?= $art->id_article ?>" onclick="return confirm('Supprimer cet article ?')" class="text-red-400 hover:text-red-300 font-medium">Supprimer</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Commentaires Récents -->
    <div class="glass rounded-2xl overflow-hidden">
      <div class="p-6 border-b border-purple-500/30">
        <h2 class="text-2xl font-bold text-purple-300">Commentaires Récents</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-purple-800 to-pink-800">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Auteur</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Article</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Commentaire</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-cyan-300 uppercase">Date</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-cyan-300 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-purple-500/20">
            <?php foreach ($comments as $com): ?>
              <tr class="hover:bg-white/5 transition">
                <td class="px-6 py-5">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-lg font-bold text-cyan-400">
                      <?= strtoupper(substr($com->nom_visiteur, 0, 1)) ?>
                    </div>
                    <div class="font-semibold text-white"><?= htmlspecialchars($com->nom_visiteur) ?></div>
                  </div>
                </td>
                <td class="px-6 py-5 text-gray-300 text-sm"><?= htmlspecialchars($com->article_titre ?? '—') ?></td>
                <td class="px-6 py-5 text-gray-300 text-sm max-w-lg truncate"><?= htmlspecialchars($com->contenu) ?></td>
                <td class="px-6 py-5 text-yellow-400 text-sm"><?= date('d/m/Y H:i', strtotime($com->date_commentaire)) ?></td>
                <td class="px-6 py-5 text-center">
                  <a href="?delete_comment=<?= $com->id_commentaire ?>" onclick="return confirm('Supprimer ce commentaire ?')" class="text-red-400 hover:text-red-300 font-medium">Supprimer</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<!-- ADD ARTICLE MODAL -->
<div id="addArticleModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
  <div class="glass rounded-2xl p-8 max-w-2xl w-full mx-4">
    <h2 class="text-3xl font-bold text-center text-teal-400 neon-text mb-6">Nouvel Article</h2>
    <form method="POST" novalidate id="addArticleForm" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add_article">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-purple-300 mb-2">Titre</label>
          <input type="text" name="titre" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white">
          <small class="error-msg text-red-400 text-sm"></small>
        </div>
        <div>
          <label class="block text-purple-300 mb-2">Catégorie</label>
          <select name="categorie" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white">
            <option value="">-- Choisir une catégorie --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat->nom) ?>"><?= htmlspecialchars($cat->nom) ?></option>
            <?php endforeach; ?>
          </select>
          <small class="error-msg text-red-400 text-sm"></small>
        </div>
      </div>
      <div class="mb-6">
        <label class="block text-purple-300 mb-2">Image (facultatif)</label>
        <input type="file" name="image" accept="image/*" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white">
        <small class="text-gray-400 text-sm block mt-2">Formats acceptés : JPG, PNG, GIF</small>
      </div>
      <div class="mb-6">
        <label class="block text-purple-300 mb-2">Contenu</label>
        <textarea name="content" rows="10" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white"></textarea>
        <small class="error-msg text-red-400 text-sm"></small>
      </div>
      <div class="flex gap-4">
        <button type="submit" class="flex-1 btn-neon text-white font-bold py-4 rounded-xl">Publier</button>
        <button type="button" onclick="document.getElementById('addArticleModal').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-4 rounded-xl">Annuler</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT ARTICLE MODAL -->
<div id="editArticleModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 overflow-y-auto">
  <div class="glass rounded-2xl p-8 max-w-3xl w-full mx-4 my-8 max-h-screen overflow-y-auto">
    <h2 class="text-3xl font-bold text-center text-teal-400 neon-text mb-6">Modifier Article</h2>
    <form method="POST" novalidate id="editArticleForm" enctype="multipart/form-data">
      <input type="hidden" name="action" value="edit_article">
      <input type="hidden" name="id" id="editArticleId">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-purple-300 mb-2">Titre</label>
          <input type="text" name="titre" id="editArticleTitre" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white">
          <small class="error-msg text-red-400 text-sm"></small>
        </div>
        <div>
          <label class="block text-purple-300 mb-2">Catégorie</label>
          <select name="categorie" id="editArticleCategorie" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white">
            <option value="">-- Choisir une catégorie --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat->nom) ?>"><?= htmlspecialchars($cat->nom) ?></option>
            <?php endforeach; ?>
          </select>
          <small class="error-msg text-red-400 text-sm"></small>
        </div>
      </div>

      <div class="mb-6 text-center">
        <label class="block text-purple-300 mb-3 text-lg">Image actuelle</label>
        <img id="currentArticleImage" src="" alt="Image actuelle" style="display:none;">
        <p id="noImageText" class="text-gray-400 italic mt-4" style="display:none;">Aucune image</p>
      </div>

      <div class="mb-6">
        <label class="block text-purple-300 mb-2">Changer l'image (facultatif)</label>
        <input type="file" name="image" accept="image/*" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white">
        <small class="text-gray-400 text-sm block mt-2">Laissez vide pour garder l'image actuelle</small>
      </div>

      <div class="mb-6">
        <label class="block text-purple-300 mb-2">Contenu</label>
        <textarea name="content" rows="12" id="editArticleContent" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white"></textarea>
        <small class="error-msg text-red-400 text-sm"></small>
      </div>

      <div class="flex gap-4">
        <button type="submit" class="flex-1 btn-neon text-white font-bold py-4 rounded-xl">Enregistrer</button>
        <button type="button" onclick="document.getElementById('editArticleModal').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-4 rounded-xl">Annuler</button>
      </div>
    </form>
  </div>
</div>

<!-- ADD CATEGORIE MODAL -->
<div id="addCategorieModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
  <div class="glass rounded-2xl p-8 max-w-md w-full mx-4">
    <h2 class="text-3xl font-bold text-center text-teal-400 neon-text mb-6">Nouvelle Catégorie</h2>
    <form method="POST" novalidate id="addCategorieForm">
      <input type="hidden" name="action" value="add_categorie">
      <div class="mb-6">
        <label class="block text-purple-300 mb-2">Nom</label>
        <input type="text" name="nom" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white">
        <small class="error-msg text-red-400 text-sm"></small>
      </div>
      <div class="mb-6">
        <label class="block text-purple-300 mb-2">Description</label>
        <textarea name="description" rows="4" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white"></textarea>
      </div>
      <div class="flex gap-4">
        <button type="submit" class="flex-1 btn-neon text-white font-bold py-4 rounded-xl">Ajouter</button>
        <button type="button" onclick="document.getElementById('addCategorieModal').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-4 rounded-xl">Annuler</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT CATEGORIE MODAL -->
<div id="editCategorieModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
  <div class="glass rounded-2xl p-8 max-w-md w-full mx-4">
    <h2 class="text-3xl font-bold text-center text-teal-400 neon-text mb-6">Modifier Catégorie</h2>
    <form method="POST" novalidate id="editCategorieForm">
      <input type="hidden" name="action" value="edit_categorie">
      <input type="hidden" name="id" id="editCategorieId">
      <div class="mb-6">
        <label class="block text-purple-300 mb-2">Nom</label>
        <input type="text" name="nom" id="editCategorieNom" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white">
        <small class="error-msg text-red-400 text-sm"></small>
      </div>
      <div class="mb-6">
        <label class="block text-purple-300 mb-2">Description</label>
        <textarea name="description" rows="4" id="editCategorieDesc" class="w-full px-5 py-3 bg-black/40 border border-purple-500 rounded-xl text-white"></textarea>
      </div>
      <div class="flex gap-4">
        <button type="submit" class="flex-1 btn-neon text-white font-bold py-4 rounded-xl">Enregistrer</button>
        <button type="button" onclick="document.getElementById('editCategorieModal').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-4 rounded-xl">Annuler</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditArticleModal(id, titre, content, categorie, currentImage = '') {
  document.getElementById('editArticleId').value = id;
  document.getElementById('editArticleTitre').value = titre;
  document.getElementById('editArticleContent').value = content;
  document.getElementById('editArticleCategorie').value = categorie;

  const imgEl = document.getElementById('currentArticleImage');
  const noImgText = document.getElementById('noImageText');

  if (currentImage && currentImage.trim() !== '') {
    imgEl.src = '../../' + currentImage;
    imgEl.style.display = 'block';
    noImgText.style.display = 'none';
  } else {
    imgEl.style.display = 'none';
    noImgText.style.display = 'block';
  }

  document.getElementById('editArticleModal').classList.remove('hidden');
}

function openEditCategorieModal(id, nom, description) {
  document.getElementById('editCategorieId').value = id;
  document.getElementById('editCategorieNom').value = nom;
  document.getElementById('editCategorieDesc').value = description;
  document.getElementById('editCategorieModal').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('form[novalidate]').forEach(f => f.setAttribute('novalidate', 'novalidate'));

  function validateForm(form) {
    let valid = true;
    form.querySelectorAll('.error-msg').forEach(el => el.textContent = '');

    const titre = form.querySelector('[name="titre"]');
    const categorie = form.querySelector('[name="categorie"]');
    const content = form.querySelector('[name="content"]');
    const nom = form.querySelector('[name="nom"]');

    if (titre && !titre.value.trim()) {
      titre.nextElementSibling.textContent = 'Le titre est obligatoire';
      valid = false;
    }
    if (categorie && !categorie.value) {
      categorie.nextElementSibling.textContent = 'Veuillez choisir une catégorie';
      valid = false;
    }
    if (content && !content.value.trim()) {
      content.nextElementSibling.textContent = 'Le contenu est obligatoire';
      valid = false;
    }
    if (nom && !nom.value.trim()) {
      nom.nextElementSibling.textContent = 'Le nom est obligatoire';
      valid = false;
    }

    return valid;
  }

  document.getElementById('addArticleForm')?.addEventListener('submit', function(e) {
    if (!validateForm(this)) e.preventDefault();
  });

  document.getElementById('editArticleForm')?.addEventListener('submit', function(e) {
    if (!validateForm(this)) e.preventDefault();
  });

  document.getElementById('addCategorieForm')?.addEventListener('submit', function(e) {
    if (!validateForm(this)) e.preventDefault();
  });

  document.getElementById('editCategorieForm')?.addEventListener('submit', function(e) {
    if (!validateForm(this)) e.preventDefault();
  });
});
</script>
</body>
</html>