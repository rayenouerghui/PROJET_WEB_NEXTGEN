<?php
require_once 'config/database.php';
require_once 'models/CategorieV.php';
require_once 'controllers/Controller.php';

class CategorieC extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== MÉTHODES BASE DE DONNÉES ====================

    private function getAll()
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT * FROM categoriev ORDER BY id_categorie DESC");
        $categories = [];
        while ($row = $result->fetch()) {
            $cat = new Categoriev($row['nom_categorie'], $row['description_categorie'], $row['id_categorie']);
            $categories[] = $cat;
        }
        return $categories;
    }

    private function getById($id)
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT * FROM categoriev WHERE id_categorie = ?", [$id]);
        $row = $result->fetch();
        if ($row) {
            return new Categoriev($row['nom_categorie'], $row['description_categorie'], $row['id_categorie']);
        }
        return null;
    }

    private function save($categorie)
    {
        $db = Database::getInstance();
        if ($categorie->getIdCategoriev() > 0) {
            // Update
            $db->query(
                "UPDATE categoriev SET nom_categorie = ?, description_categorie = ? WHERE id_categorie = ?",
                [$categorie->getNomCategoriev(), $categorie->getDescriptionCategoriev(), $categorie->getIdCategoriev()]
            );
        } else {
            // Insert
            $db->query(
                "INSERT INTO categoriev (nom_categorie, description_categorie) VALUES (?, ?)",
                [$categorie->getNomCategoriev(), $categorie->getDescriptionCategoriev()]
            );
            $categorie->setIdCategoriev($db->getConnection()->lastInsertId());
        }
        return $categorie;
    }

    private function deleteById($id)
    {
        if ($id > 0) {
            $db = Database::getInstance();
            $db->query("DELETE FROM categoriev WHERE id_categorie = ?", [$id]);
            return true;
        }
        return false;
    }

    private function count()
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) as total FROM categoriev");
        return $result->fetch()['total'];
    }

    private function toArray($categorie)
    {
        return [
            'id_categorie' => $categorie->getIdCategoriev(),
            'nom_categorie' => $categorie->getNomCategoriev(),
            'description_categorie' => $categorie->getDescriptionCategoriev()
        ];
    }

    // ==================== ACTIONS ====================

    public function index()
    {
        try {
            $categories = $this->getAll();
            // Convertir en tableaux pour la vue
            $categories = array_map(function($cat) {
                return $this->toArray($cat);
            }, $categories);
            $data = ['categories' => $categories];
            $this->render('admin/Categories', $data);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Sanitize and normalize input to prevent HTML tags and XSS
                $nom = trim(strip_tags($_POST['nom_categorie'] ?? ''));
                $description = trim(strip_tags($_POST['description_categorie'] ?? ''));

                if (empty($nom)) {
                    // Validation error: prepare field-specific error message and re-render view
                    $this->setFlash('error', 'Le formulaire contient des erreurs.');
                    $categories = $this->getAll();
                    $categories = array_map(function($cat) {
                        return $this->toArray($cat);
                    }, $categories);
                    $data = [
                        'categories' => $categories,
                        'old' => [
                            'nom_categorie' => $nom,
                            'description_categorie' => $description
                        ],
                        'errors' => [
                            'nom_categorie' => 'Le nom de la catégorie est obligatoire.'
                        ]
                    ];
                    $this->render('admin/Categories', $data);
                    return;
                }

                $categorie = new Categoriev($nom, $description);
                $this->save($categorie);

                $this->setFlash('success', 'Catégorie créée avec succès.');
                $this->redirect('/projet/index.php?c=categorie&amp;a=index');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de la création: ' . $e->getMessage());
                $this->redirect('/projet/index.php?c=categorie&amp;a=index');
            }
        } else {
            // If called with ?partial=1, render only the form fragment for modal usage
            if (isset($_GET['partial']) && $_GET['partial']) {
                $data = [ 'old' => [], 'errors' => [] ];
                $this->render('admin/partials/category_form', $data);
                return;
            }
            $this->index();
        }
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $this->redirect('/projet/index.php?c=categorie&amp;a=index');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Sanitize inputs coming from form
                $nom = trim(strip_tags($_POST['nom_categorie'] ?? ''));
                $description = trim(strip_tags($_POST['description_categorie'] ?? ''));

                if (empty($nom)) {
                    $this->setFlash('error', 'Le formulaire contient des erreurs.');
                    $data = [
                        'categorie' => [
                            'id_categorie' => $id,
                            'nom_categorie' => $nom,
                            'description_categorie' => $description
                        ],
                        'errors' => [
                            'nom_categorie' => 'Le nom de la catégorie est obligatoire.'
                        ]
                    ];
                    $this->render('admin/categorie_edit', $data);
                    return;
                }

                $categorie = $this->getById($id);
                if (!$categorie) {
                    $this->setFlash('error', 'Catégorie non trouvée.');
                    $this->redirect('/projet/index.php?c=categorie&amp;a=index');
                }

                $categorie->setNomCategoriev($nom);
                $categorie->setDescriptionCategoriev($description);
                $this->save($categorie);

                $this->setFlash('success', 'Catégorie modifiée avec succès.');
                $this->redirect('/projet/index.php?c=categorie&amp;a=index');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de la modification: ' . $e->getMessage());
                $this->redirect('/projet/index.php?c=categorie&amp;a=edit&amp;id=' . $id);
            }
        } else {
            try {
                $categorie = $this->getById($id);
                if (!$categorie) {
                    $this->redirect('/projet/index.php?c=categorie&amp;a=index');
                }
                $data = ['categorie' => $this->toArray($categorie)];
                $this->render('admin/categorie_edit', $data);
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $this->redirect('/projet/index.php?c=categorie&amp;a=index');
        }

        try {
            $categorie = $this->getById($id);
            if ($categorie) {
                $this->deleteById($id);
                $this->setFlash('success', 'Catégorie supprimée avec succès.');
            } else {
                $this->setFlash('error', 'Catégorie non trouvée.');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }

        $this->redirect('/projet/index.php?c=categorie&amp;a=index');
    }

    // Méthode publique pour être utilisée par d'autres contrôleurs
    public function getAllCategories()
    {
        return $this->getAll();
    }

    public function getCategorieById($id)
    {
        return $this->getById($id);
    }

    public function countCategories()
    {
        return $this->count();
    }
}
?>
