<?php

class Trajet {
    private ?int $id_trajet;
    private ?int $id_livraison;
    private string $fournisseur_api;
    private string $identifiant_suivi;
    private string $statut_realtime;
    private ?float $position_lat;
    private ?float $position_lng;

    public function __construct(
        ?int $id_trajet = null,
        ?int $id_livraison = null,
        string $fournisseur_api = '',
        string $identifiant_suivi = '',
        string $statut_realtime = '',
        ?float $position_lat = null,
        ?float $position_lng = null
    ) {
        $this->id_trajet = $id_trajet;
        $this->id_livraison = $id_livraison;
        $this->fournisseur_api = $fournisseur_api;
        $this->identifiant_suivi = $identifiant_suivi;
        $this->statut_realtime = $statut_realtime;
        $this->position_lat = $position_lat;
        $this->position_lng = $position_lng;
    }

    public function getIdTrajet(): ?int {
        return $this->id_trajet;
    }

    public function setIdTrajet(?int $id): void {
        $this->id_trajet = $id;
    }

    public function getIdLivraison(): ?int {
        return $this->id_livraison;
    }

    public function setIdLivraison(?int $id): void {
        $this->id_livraison = $id;
    }

    public function getFournisseurApi(): string {
        return $this->fournisseur_api;
    }

    public function setFournisseurApi(string $fournisseur): void {
        $this->fournisseur_api = $fournisseur;
    }

    public function getIdentifiantSuivi(): string {
        return $this->identifiant_suivi;
    }

    public function setIdentifiantSuivi(string $identifiant): void {
        $this->identifiant_suivi = $identifiant;
    }

    public function getStatutRealtime(): string {
        return $this->statut_realtime;
    }

    public function setStatutRealtime(string $statut): void {
        $this->statut_realtime = $statut;
    }

    public function getPositionLat(): ?float {
        return $this->position_lat;
    }

    public function setPositionLat(?float $lat): void {
        $this->position_lat = $lat;
    }

    public function getPositionLng(): ?float {
        return $this->position_lng;
    }

    public function setPositionLng(?float $lng): void {
        $this->position_lng = $lng;
    }
}

?>