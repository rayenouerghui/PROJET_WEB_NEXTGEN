<?php include __DIR__ . '/header.php'; ?>

<div class="admin-container">
    <h1>Modifier la catégorie</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form id="categoryEditForm" class="validate-admin" method="post" action="<?php echo WEB_ROOT; ?>/index.php?c=categorie&amp;a=edit&amp;id=<?php echo $categorie['id_categorie'] ?? ''; ?>" novalidate>
            <div class="form-group">
                <label for="nom_categorie">Nom de la catégorie *</label>
                <input type="text" id="nom_categorie" name="nom_categorie" class="form-control" 
                       value="<?php echo htmlspecialchars($categorie['nom_categorie'] ?? ''); ?>" data-validate="required" data-msg="Le nom de la catégorie est obligatoire." />
                <?php if (!empty($errors['nom_categorie'])): ?>
                    <div class="field-error"><?php echo htmlspecialchars($errors['nom_categorie']); ?></div>
                <?php elseif (isset($categorie) && trim($categorie['nom_categorie'] ?? '') === ''): ?>
                    <div class="field-error">Ce champ est obligatoire.</div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="description_categorie">Description</label>
                <textarea id="description_categorie" name="description_categorie" class="form-control" rows="3"><?php echo htmlspecialchars($categorie['description_categorie']); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="<?php echo WEB_ROOT; ?>/index.php?c=categorie&amp;a=index" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

    </div> <!-- .admin-main -->
    </div> <!-- .admin-layout -->

<script type="text/javascript">
    (function(){
        var form = document.getElementById('categoryEditForm');
        if (!form) return;
        var name = form.querySelector('input[name="nom_categorie"]');

        function showError() {
            var err = name.parentNode.querySelector('.field-error');
            if (!err) {
                err = document.createElement('div');
                err.className = 'field-error';
                err.textContent = 'Ce champ est obligatoire.';
                name.parentNode.appendChild(err);
            }
            name.classList.add('is-invalid');
            name.focus();
        }

        function clearError() {
            var err = name.parentNode.querySelector('.field-error');
            if (err) err.parentNode.removeChild(err);
            name.classList.remove('is-invalid');
        }

        name.addEventListener('input', function(){
            if (name.value.trim() !== '') clearError();
        });

        form.addEventListener('submit', function(e){
            if (name.value.trim() === '') {
                e.preventDefault();
                showError();
            }
        });
    })();
</script>

</body>
</html>

