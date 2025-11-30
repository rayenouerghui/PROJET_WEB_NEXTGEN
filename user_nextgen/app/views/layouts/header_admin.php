<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>NextGen - Admin BackOffice</title>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;900&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/user_nextgen/assets/css/admin.css">
</head>
<body>

<div class="back-container">
  <div class="back-card">
    <div class="back-header">
      <div class="logo">🎮 NextGen</div>
      <div style="font-size: 1.1rem; opacity: 0.9; margin-top: 10px;">
        Bienvenue, <strong><?= htmlspecialchars($_SESSION['user']['nom']) ?> <?= htmlspecialchars($_SESSION['user']['prenom']) ?></strong>
      </div>
      <div style="margin-top: 20px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
        <a href="/user_nextgen/" style="color: white; text-decoration: none; padding: 8px 20px; background: rgba(255,255,255,0.2); border-radius: 20px; transition: all 0.3s;">🏠 Accueil</a>
        <a href="/user_nextgen/admin/dashboard" style="color: white; text-decoration: none; padding: 8px 20px; background: rgba(255,255,255,0.2); border-radius: 20px; transition: all 0.3s;">📊 Dashboard</a>
        <a href="/user_nextgen/admin/users" style="color: white; text-decoration: none; padding: 8px 20px; background: rgba(255,255,255,0.2); border-radius: 20px; transition: all 0.3s;">👥 Utilisateurs</a>
        <a href="/user_nextgen/history" style="color: white; text-decoration: none; padding: 8px 20px; background: rgba(255,255,255,0.2); border-radius: 20px; transition: all 0.3s;">📋 Historique</a>
        <a href="/user_nextgen/profile" style="color: white; text-decoration: none; padding: 8px 20px; background: rgba(255,255,255,0.2); border-radius: 20px; transition: all 0.3s;">👤 Mon Profil</a>
        <a href="/user_nextgen/logout" style="color: white; text-decoration: none; padding: 8px 20px; background: rgba(220,53,69,0.8); border-radius: 20px; transition: all 0.3s;">🚪 Déconnexion</a>
      </div>
    </div>
    <div class="back-body">
