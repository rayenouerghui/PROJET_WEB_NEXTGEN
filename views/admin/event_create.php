<?php if (!isset($_GET['partial'])) { include __DIR__ . '/header.php'; } ?>

<div class="admin-container">
    <h1>Créer un nouvel événement</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form id="eventCreateForm" class="validate-admin" method="post" action="/projet/index.php?c=evenement&amp;a=create" novalidate>
            <div class="form-grid">
                <div class="form-group">
                    <label for="titre">Titre *</label>
                        <input type="text" id="titre" name="titre" class="form-control" value="<?php echo htmlspecialchars($old['titre'] ?? ''); ?>" data-validate="required" data-msg="Le titre est obligatoire." />
                        <?php if (!empty($errors['titre'])): ?>
                            <div class="field-error"><?php echo htmlspecialchars($errors['titre']); ?></div>
                        <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="id_categorie">Catégorie *</label>
                        <select id="id_categorie" name="id_categorie" class="form-control" data-validate="required" data-msg="Veuillez sélectionner une catégorie.">
                        <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id_categorie']; ?>" <?php echo (isset($old['id_categorie']) && $old['id_categorie'] == $cat['id_categorie']) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($cat['nom_categorie']); ?></option>
                            <?php endforeach; ?>
                            <?php if (!empty($errors['id_categorie'])): ?>
                                <option disabled="disabled"><?php echo htmlspecialchars($errors['id_categorie']); ?></option>
                            <?php endif; ?>
                    </select>
                        <?php if (!empty($errors['id_categorie'])): ?>
                            <div class="field-error"><?php echo htmlspecialchars($errors['id_categorie']); ?></div>
                        <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="date_evenement">Date de l'événement *</label>
                        <input type="text" id="date_evenement" name="date_evenement" class="form-control" value="<?php echo htmlspecialchars($old['date_evenement'] ?? ''); ?>" data-validate="required date" data-msg="Veuillez saisir une date valide (YYYY-MM-DD)." />
                        <?php if (!empty($errors['date_evenement'])): ?>
                            <div class="field-error"><?php echo htmlspecialchars($errors['date_evenement']); ?></div>
                        <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="lieu">Lieu *</label>
                        <input type="text" id="lieu" name="lieu" class="form-control" value="<?php echo htmlspecialchars($old['lieu'] ?? ''); ?>" data-validate="required" data-msg="Veuillez saisir un lieu." />
                        <?php if (!empty($errors['lieu'])): ?>
                            <div class="field-error"><?php echo htmlspecialchars($errors['lieu']); ?></div>
                        <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="places_disponibles">Places disponibles</label>
                        <input type="text" id="places_disponibles" name="places_disponibles" class="form-control" value="<?php echo htmlspecialchars($old['places_disponibles'] ?? '0'); ?>" data-validate="number" data-msg="Veuillez saisir un nombre de places valide." />
                        <?php if (!empty($errors['places_disponibles'])): ?>
                            <div class="field-error"><?php echo htmlspecialchars($errors['places_disponibles']); ?></div>
                        <?php endif; ?>
                </div>
                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="5"><?php echo htmlspecialchars($old['description'] ?? ''); ?></textarea>
                </div>

                <!-- Image selector removed — form restored to original version -->
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Créer l'événement</button>
                <a href="/projet/index.php?c=evenement&amp;a=index" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

    </div> <!-- .admin-main -->
    </div> <!-- .admin-layout -->

</body>
</html>

