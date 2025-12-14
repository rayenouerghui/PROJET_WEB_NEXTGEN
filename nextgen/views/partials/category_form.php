<?php
// Partial form for creating a category (rendered inside admin modal)
// Expects optional $old and $errors arrays
?>
<div class="form-card">
    <h2>Ajouter une nouvelle catégorie</h2>
    <form id="categoryForm" class="validate-admin" method="post" action="<?php echo WEB_ROOT; ?>/index.php?c=categorie&amp;a=create" novalidate>
        <div class="form-group">
            <label for="nom_categorie">Nom de la catégorie *</label>
            <input type="text" id="nom_categorie" name="nom_categorie" class="form-control" value="<?php echo htmlspecialchars($old['nom_categorie'] ?? ''); ?>" data-validate="required" data-msg="Le nom de la catégorie est obligatoire." />
            <?php if (!empty($errors['nom_categorie'])): ?>
                <div class="field-error"><?php echo htmlspecialchars($errors['nom_categorie']); ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="description_categorie">Description</label>
            <textarea id="description_categorie" name="description_categorie" class="form-control" rows="3"><?php echo htmlspecialchars($old['description_categorie'] ?? ''); ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer la catégorie</button>
            <button type="reset" class="btn btn-secondary">Réinitialiser</button>
        </div>
    </form>
</div>
