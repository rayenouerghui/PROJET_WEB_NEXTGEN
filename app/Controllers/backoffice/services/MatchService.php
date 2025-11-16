<?php

require_once __DIR__ . '/../../../Models/backoffice/AttenteMatchModel.php';
require_once __DIR__ . '/../../../Models/backoffice/SessionMatchModel.php';

class MatchService {
    private $attenteModel;
    private $sessionModel;

    public function __construct() {
        $this->attenteModel = new AttenteMatchModel();
        $this->sessionModel = new SessionMatchModel();
    }

    /**
     * Vérifie les attentes pour un jeu et crée des sessions par groupes de 2 joueurs.
     * Retourne un tableau avec le nombre de matchs créés.
     */
    public function verifierMatchs(int $idJeu): array {
        $matchsCrees = 0;

        try {
            // On récupère beaucoup d'attentes, puis on regroupe par 2 en PHP.
            // La méthode modèle limite déjà l'ordre par date_ajout ASC.
            $attentes = $this->attenteModel->getAttentesParJeu($idJeu, 100);

            if (empty($attentes) || count($attentes) < 2) {
                return ['matchs_crees' => 0];
            }

            // Regrouper les utilisateurs par paire.
            $idsAttenteUtilises = [];
            $participantsGroupes = [];

            for ($i = 0; $i + 1 < count($attentes); $i += 2) {
                $a1 = $attentes[$i];
                $a2 = $attentes[$i + 1];

                $participantsGroupes[] = [
                    (int)$a1['id_user'],
                    (int)$a2['id_user'],
                ];

                $idsAttenteUtilises[] = (int)$a1['id_attente'];
                $idsAttenteUtilises[] = (int)$a2['id_attente'];
            }

            if (empty($participantsGroupes)) {
                return ['matchs_crees' => 0];
            }

            foreach ($participantsGroupes as $participants) {
                $lienSession = $this->sessionModel->genererLienSession();
                $idSession = $this->sessionModel->creerSession($idJeu, $participants, $lienSession);

                if ($idSession !== false) {
                    $matchsCrees++;
                }
            }

            if (!empty($idsAttenteUtilises)) {
                $this->attenteModel->marquerCommeMatched($idsAttenteUtilises);
            }
        } catch (Exception $e) {
            error_log('Erreur MatchService::verifierMatchs: ' . $e->getMessage());
        }

        return ['matchs_crees' => $matchsCrees];
    }
}
