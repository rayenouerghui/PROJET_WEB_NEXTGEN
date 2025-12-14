<?php include __DIR__ . '/header.php'; ?>

<div class="admin-container">
    <div class="admin-page-header">
        <h1>Gestion des Catégories</h1>
        <a href="<?php echo WEB_ROOT; ?>/index.php?c=categorie&amp;a=create" class="btn btn-add">+ Ajouter une catégorie</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['info'])): ?>
        <div class="alert alert-info" style="background:#d1ecf1;border:1px solid #bee5eb;color:#0c5460;padding:12px;border-radius:5px;margin-bottom:20px;"><?php echo htmlspecialchars($_SESSION['info']); unset($_SESSION['info']); ?></div>
    <?php endif; ?>

    <div class="table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="4">Aucune catégorie</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id_categorie']; ?></td>
                            <td><?php echo htmlspecialchars($cat['nom_categorie']); ?></td>
                            <td><?php echo htmlspecialchars($cat['description_categorie']); ?></td>
                            <td>
                                <a href="<?php echo WEB_ROOT; ?>/index.php?c=categorie&amp;a=edit&amp;id=<?php echo $cat['id_categorie']; ?>" class="btn-admin btn-edit">Modifier</a>
                                          <a href="<?php echo WEB_ROOT; ?>/index.php?c=categorie&amp;a=delete&amp;id=<?php echo $cat['id_categorie']; ?>" 
                                              class="btn-admin btn-delete">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Inline create form removed; use the "+ Ajouter une catégorie" button to open the modal form -->
    </div>

    </div> <!-- .admin-main -->
    </div> <!-- .admin-layout -->

</body>
</html>

