<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>NextGen - Gaming & Social</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/user_nextgen/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
<header class="site-header">
  <nav class="nav">
    <a href="/user_nextgen/">Accueil</a>
    
    <?php if (empty($_SESSION['user'])): ?>
      <!-- Menu pour visiteurs non connectés -->
      <a href="/user_nextgen/login">Connexion</a>
      <a href="/user_nextgen/register">S'inscrire</a>
      
    <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
      <!-- Menu pour ADMIN -->
      <a href="/user_nextgen/profile">Profil (<?= htmlspecialchars($_SESSION['user']['nom']) ?>)</a>
      <a href="/user_nextgen/admin/dashboard">Dashboard Admin</a>
      <a href="/user_nextgen/history">Historique</a>
      <a href="/user_nextgen/logout">Déconnexion</a>
      
    <?php else: ?>
      <!-- Menu pour USER -->
      <a href="/user_nextgen/profile">Profil (<?= htmlspecialchars($_SESSION['user']['nom']) ?>)</a>
      <a href="/user_nextgen/logout">Déconnexion</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
