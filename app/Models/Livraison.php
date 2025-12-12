<?php

class Livraison {
    private ?int $id_livraison;
    private ?int $id_commande;
    private string $adresse_complete;
    private string $ville;
    private string $code_postal;
    private string $date_livraison;
    private string $mode_livraison;
    private float $prix_livraison;
    private ?string $transport_type;
    private string $statut;
    private ?string $notes_client;
    private ?float $position_lat;
    private ?float $position_lng;

    public function __construct(
        ?int $id_livraison = null,
        ?int $id_commande = null,
        string $adresse_complete = '',
        string $ville = '',
        string $code_postal = '',
        string $date_livraison = '',
        string $mode_livraison = 'standard',
        float $prix_livraison = 0.0,
        ?string $transport_type = null,
        string $statut = 'preparée',
        ?string $notes_client = null,
        ?float $position_lat = null,
        ?float $position_lng = null
    ) {
        $this->id_livraison = $id_livraison;
        $this->id_commande = $id_commande;
        $this->adresse_complete = $adresse_complete;
        $this->ville = $ville;
        $this->code_postal = $code_postal;
        $this->date_livraison = $date_livraison;
        $this->mode_livraison = $mode_livraison;
        $this->prix_livraison = $prix_livraison;
        $this->transport_type = $transport_type;
        $this->statut = $statut;
        $this->notes_client = $notes_client;
        $this->position_lat = $position_lat;
        $this->position_lng = $position_lng;
    }

    public function getIdLivraison(): ?int {
        return $this->id_livraison;
    }

    public function setIdLivraison(?int $id): void {
        $this->id_livraison = $id;
    }

    public function getIdCommande(): ?int {
        return $this->id_commande;
    }

    public function setIdCommande(?int $id): void {
        $this->id_commande = $id;
    }

    public function getAdresseComplete(): string {
        return $this->adresse_complete;
    }

    public function setAdresseComplete(string $adresse): void {
        $this->adresse_complete = $adresse;
    }

    public function getVille(): string {
        return $this->ville;
    }

    public function setVille(string $ville): void {
        $this->ville = $ville;
    }

    public function getCodePostal(): string {
        return $this->code_postal;
    }

    public function setCodePostal(string $code): void {
        $this->code_postal = $code;
    }

    public function getDateLivraison(): string {
        return $this->date_livraison;
    }

    public function setDateLivraison(string $date): void {
        $this->date_livraison = $date;
    }

    public function getModeLivraison(): string {
        return $this->mode_livraison;
    }

    public function setModeLivraison(string $mode): void {
        $this->mode_livraison = $mode;
    }

    public function getPrixLivraison(): float {
        return $this->prix_livraison;
    }

    public function setPrixLivraison(float $prix): void {
        $this->prix_livraison = $prix;
    }

    public function getTransportType(): ?string {
        return $this->transport_type;
    }

    public function setTransportType(?string $transport): void {
        $this->transport_type = $transport;
    }

    public function getStatut(): string {
        return $this->statut;
    }

    public function setStatut(string $statut): void {
        $this->statut = $statut;
    }

    public function getNotesClient(): ?string {
        return $this->notes_client;
    }

    public function setNotesClient(?string $notes): void {
        $this->notes_client = $notes;
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