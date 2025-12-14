<?php
require_once __DIR__ . '/../../controller/BlogController.php';

$blogController = new BlogController();

// Lightweight GET JSON endpoint for a single article (used by client-side print)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax_get']) && $_GET['ajax_get'] === 'article') {
    header('Content-Type: application/json');
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => "ID article invalide"]);
        exit;
    }
    $data = $blogController->show($id);
    $ok = is_array($data) && empty($data['error']);
    echo json_encode(['success' => $ok, 'article' => $data]);
    exit;
}

// Utiliser la méthode publique déjà employée dans le dashboard
$articles = $blogController->index();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
    <title>Tous les Articles</title>
    <style>
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        /* Print layout for single-article export */
        #printArea { display: none; }
        #printArea .article-title { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        #printArea .article-meta { color: #6c757d; font-size: 13px; margin-bottom: 16px; }
        #printArea .article-image { margin: 16px 0 24px; text-align: center; }
        #printArea .article-image img { max-width: 100%; height: auto; border-radius: 8px; }
        #printArea .article-content { font-size: 15px; line-height: 1.7; color: #222; }
        /* Note: We now print via an offscreen iframe to avoid blank pages.
           Keeping minimal print rules here for safety when needed directly. */
        @media print {
            @page { margin: 12mm; }
            html, body { margin: 0; padding: 0; }
        }
    </style>
    
</head>
<body>
<div class="wrapper">
    <?php include('partials/topbar.php'); ?>
    <?php include('partials/sidenav.php'); ?>

    <div class="page-content">
        <div class="page-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Tous les Articles</h3>
                <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="ri-arrow-left-line me-1"></i> Retour au tableau de bord</a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Date de publication</th>
                                <th>Auteur</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($articles)): ?>
                                <tr><td colspan="5" class="text-center py-4">Aucun article trouvé</td></tr>
                            <?php else: ?>
                                <?php foreach ($articles as $a): ?>
                                    <?php
                                    $id = htmlspecialchars((string)($a['id_article'] ?? ''));
                                    $titre = htmlspecialchars($a['titre'] ?? '');
                                    $date = htmlspecialchars($a['date_publication'] ?? '');
                                    $catName = htmlspecialchars($a['categorie'] ?? '');
                                    $auteur = htmlspecialchars($a['auteur'] ?? ($a['id_auteur'] ?? ''));
                                    ?>
                                    <tr>
                                        <td><?= $id ?></td>
                                        <td style="max-width:420px" class="text-truncate-2" title="<?= $titre ?>"><?= $titre ?></td>
                                        <td><span class="badge bg-primary-subtle text-primary"><?= $catName ?: '—' ?></span></td>
                                        <td><span class="text-muted fs-12"><?= $date ?></span></td>
                                        <td><?= $auteur ?: '—' ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportArticlePDF(<?= (int)($a['id_article'] ?? 0) ?>)">
                                                <i class="ri-file-pdf-line me-1"></i> Exporter PDF
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <?php include('partials/footer.php'); ?>
        
        <!-- Hidden print container (filled dynamically) -->
        <div id="printArea" class="container py-3">
            <div class="article-title" id="pa-title"></div>
            <div class="article-meta" id="pa-meta"></div>
            <div class="article-image" id="pa-image" style="display:none;">
                <img id="pa-img" src="" alt="Image de l'article">
            </div>
            <div class="article-content" id="pa-content"></div>
        </div>
    </div>
</div>

<?php include('partials/footer-scripts.php'); ?>
<script>
    function resolveImageUrl(imagePath) {
        if (!imagePath) return '';
        try {
            if (/^https?:\/\//i.test(imagePath)) return imagePath;
            // Assume relative to /public
            return '../../../public/' + String(imagePath).replace(/^\/+/, '');
        } catch(e) { return ''; }
    }

    function sanitize(text) { return text == null ? '' : String(text); }

    function escapeHTML(text) {
        return sanitize(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function buildPrintHtml({ titre, categorie, auteur, date, imageUrl, contentHtml }) {
        const safeTitle = escapeHTML(titre);
        const safeCategorie = escapeHTML(categorie);
        const safeAuteur = escapeHTML(auteur);
        const safeDate = escapeHTML(date);

        return `<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title>${safeTitle} - PDF</title>
  <style>
    @page { margin: 12mm; }
    html, body { margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif; color:#111; }
    .wrap { padding: 8mm 10mm; }
    .article-title { font-size: 28px; font-weight: 700; margin: 0 0 8px; }
    .article-meta { color: #666; font-size: 12px; margin: 0 0 16px; }
    .article-meta span{ margin-right: 10px; }
    .article-image { margin: 16px 0 24px; text-align: center; }
    .article-image img { max-width: 100%; height: auto; border-radius: 8px; }
    .article-content { font-size: 15px; line-height: 1.7; }
    img { max-width: 100%; }
  </style>
  </head>
  <body>
    <div class="wrap">
      <h1 class="article-title">${safeTitle}</h1>
      <div class="article-meta">
        <span>${safeCategorie}</span>
        <span>${safeAuteur}</span>
        <span>${safeDate}</span>
      </div>
      ${imageUrl ? `<div class="article-image"><img src="${imageUrl}" alt="Image" /></div>` : ''}
      <div class="article-content">${sanitize(contentHtml)}</div>
    </div>
  </body>
</html>`;
    }

    function printInIframe(html) {
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);

        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write(html);
        doc.close();

        // Wait for images/fonts to load, then print
        const images = Array.from(doc.images || []);
        const waitImages = Promise.all(images.map(img => img.complete ? Promise.resolve() : new Promise(res => { img.onload = img.onerror = res; })));
        waitImages.then(() => {
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                // Clean up after a short delay
                setTimeout(() => { document.body.removeChild(iframe); }, 1200);
            }, 50);
        });
    }

    function exportArticlePDF(id) {
        if (!id || id <= 0) { alert('ID article invalide'); return; }
        const url = new URL(window.location.href);
        url.searchParams.set('ajax_get', 'article');
        url.searchParams.set('id', String(id));

        fetch(url.toString(), { method: 'GET', headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data || !data.success || !data.article) {
                    alert(data && data.message ? data.message : "Impossible de charger l'article");
                    return;
                }

                const a = data.article;
                const titre = sanitize(a.titre);
                const categorie = sanitize(a.categorie || a.categorie_nom || '—');
                const auteur = sanitize(a.auteur || a.id_auteur || '—');
                const date = sanitize(a.date_publication || '');
                const image = sanitize(a.image || '');
                const imgUrl = resolveImageUrl(image);

                const html = buildPrintHtml({
                    titre, categorie, auteur, date, imageUrl: imgUrl, contentHtml: a.content || ''
                });

                printInIframe(html);
            })
            .catch(err => {
                console.error(err);
                alert('Erreur lors du chargement de l\'article');
            });
    }
</script>
</body>
</html>
