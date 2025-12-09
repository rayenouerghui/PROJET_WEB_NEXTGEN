<?php

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/jeu.php';
require_once dirname(__DIR__) . '/models/categorie.php';

class JeuController
{
    public function ajouterJeu($jeu)
    {
        $sql = "INSERT INTO jeu (titre, prix, src_img, description, id_categorie) 
                VALUES (:titre, :prix, :src_img, :description, :id_categorie)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':titre', $jeu->getTitre());
            $query->bindValue(':prix', $jeu->getPrix());
            $query->bindValue(':src_img', $jeu->getSrcImg());
            $query->bindValue(':description', $jeu->getDescription());
            $query->bindValue(':id_categorie', $jeu->getIdCategorie());
            $query->execute();

            $ref = new ReflectionProperty($jeu, 'id_jeu');
            $ref->setAccessible(true);
            $ref->setValue($jeu, $db->lastInsertId());
        } catch (Exception $e) {
            error_log('Erreur ajouterJeu: ' . $e->getMessage());
            throw $e;
        }
    }

    public function afficherJeux()
    {
        $sql = "SELECT j.*, c.nom_categorie 
                FROM jeu j 
                LEFT JOIN categorie c ON j.id_categorie = c.id_categorie 
                ORDER BY j.id_jeu DESC";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $results = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $jeu = new Jeu(
                    $row['titre'],
                    $row['prix'],
                    $row['src_img'],
                    $row['id_categorie'],
                    $row['id_jeu'],
                    $row['description'] ?? null  // ← NOUVEAU
                );
                $jeu->nom_categorie = $row['nom_categorie'] ?? null;
                $results[] = $jeu;
            }
            return $results;
        } catch (Exception $e) {
            error_log('Erreur afficherJeux: ' . $e->getMessage());
            return [];
        }
    }

    public function getJeu($id_jeu)
    {
        $sql = "SELECT j.*, c.nom_categorie 
                FROM jeu j 
                LEFT JOIN categorie c ON j.id_categorie = c.id_categorie 
                WHERE j.id_jeu = :id_jeu";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_jeu', $id_jeu, PDO::PARAM_INT);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            $jeu = new Jeu(
                $row['titre'],
                $row['prix'],
                $row['src_img'],
                $row['id_categorie'],
                $row['id_jeu'],
                $row['description'] ?? null  // ← NOUVEAU
            );
            $jeu->nom_categorie = $row['nom_categorie'] ?? null;
            return $jeu;
        } catch (Exception $e) {
            error_log('Erreur getJeu: ' . $e->getMessage());
            return null;
        }
    }

    public function modifierJeu($jeu)
    {
        $sql = "UPDATE jeu 
                SET titre = :titre, 
                    prix = :prix, 
                    src_img = :src_img,
                    description = :description,
                    id_categorie = :id_categorie 
                WHERE id_jeu = :id_jeu";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':titre', $jeu->getTitre());
            $query->bindValue(':prix', $jeu->getPrix());
            $query->bindValue(':src_img', $jeu->getSrcImg());
            $query->bindValue(':description', $jeu->getDescription());
            $query->bindValue(':id_categorie', $jeu->getIdCategorie());
            $query->bindValue(':id_jeu', $jeu->getIdJeu());
            $query->execute();
        } catch (Exception $e) {
            error_log('Erreur modifierJeu: ' . $e->getMessage());
            throw $e;
        }
    }

    public function supprimerJeu($id_jeu)
    {
        $sql = "DELETE FROM jeu WHERE id_jeu = :id_jeu";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_jeu', $id_jeu, PDO::PARAM_INT);
            $query->execute();
            return $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log('Erreur supprimerJeu: ' . $e->getMessage());
            throw $e;
        }
    }
}