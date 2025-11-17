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

    public function verifierMatchs(int $idJeu): array {
        $matchsCrees = 0;

        try {
            $attentes = $this->attenteModel->getAttentesParJeu($idJeu, 100);

            if (empty($attentes) || count($attentes) < 2) {
                return ['matchs_crees' => 0];
            }

            $idsAttenteUtilises = [];
            $participantsGroupes = [];
            $nombreAttentes = count($attentes);

            for ($i = 0; $i + 1 < $nombreAttentes; $i += 2) {
                $participant1 = $attentes[$i];
                $participant2 = $attentes[$i + 1];

                $participantsGroupes[] = [
                    (int)$participant1['id_user'],
                    (int)$participant2['id_user']
                ];

                $idsAttenteUtilises[] = (int)$participant1['id_attente'];
                $idsAttenteUtilises[] = (int)$participant2['id_attente'];
            }

            if (empty($participantsGroupes)) {
                return ['matchs_crees' => 0];
            }

            foreach ($participantsGroupes as $groupeParticipants) {
                $lienSession = $this->sessionModel->genererLienSession();
                $idSession = $this->sessionModel->creerSession($idJeu, $groupeParticipants, $lienSession);

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