<?php include __DIR__ . '/header.php'; ?>

<div class="admin-container">
    <h1>Ajouter une réservation</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form id="reservationCreateForm" class="validate-admin" method="post" action="/projet/index.php?c=reservation&amp;a=create" novalidate>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nom_complet">Nom complet</label>
                    <input type="text" id="nom_complet" name="nom_complet" class="form-control" value="<?php echo htmlspecialchars($old['nom_complet'] ?? ''); ?>" data-validate="required" data-msg="Le nom complet est obligatoire." />
                    <?php if (!empty($errors['nom_complet'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['nom_complet']); ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" data-validate="required email" data-msg="Veuillez saisir un email valide." />
                    <?php if (!empty($errors['email'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['email']); ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="text" id="telephone" name="telephone" class="form-control" value="<?php echo htmlspecialchars($old['telephone'] ?? ''); ?>" data-validate="required phone" data-msg="Veuillez saisir un numéro de téléphone valide." />
                    <?php if (!empty($errors['telephone'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['telephone']); ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="id_evenement">Événement</label>
                    <select id="id_evenement" name="id_evenement" class="form-control" data-validate="required" data-msg="Veuillez sélectionner un événement.">
                        <option value="">Sélectionner un événement</option>
                        <?php foreach ($evenements as $evt): ?>
                            <option value="<?php echo $evt['id_evenement']; ?>" <?php echo (isset($old['id_evenement']) && $old['id_evenement'] == $evt['id_evenement']) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($evt['titre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['id_evenement'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['id_evenement']); ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="nombre_places">Nombre de places</label>
                    <input type="text" id="nombre_places" name="nombre_places" class="form-control" value="<?php echo htmlspecialchars($old['nombre_places'] ?? '1'); ?>" data-validate="required number" data-msg="Veuillez saisir un nombre de places valide." />
                    <?php if (!empty($errors['nombre_places'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['nombre_places']); ?></div><?php endif; ?>
                </div>


                <div class="form-group full-width">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" class="form-control" rows="4"><?php echo htmlspecialchars($old['message'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Créer la réservation</button>
                <a href="/projet/index.php?c=reservation&amp;a=index" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

    </div> <!-- .admin-main -->
    </div> <!-- .admin-layout -->

</body>
</html>
