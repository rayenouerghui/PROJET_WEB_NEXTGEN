<?php
// models/Livraison.php

class Livraison
{
    private ?int $id_livraison = null;
    private int $id_user;
    private int $id_jeu;
    private string $adresse_complete;
    private float $position_lat;
    private float $position_lng;
    private string $mode_paiement;
    private float $prix_livraison = 8.000;
    private string $statut = 'commandee';
    private string $date_commande;

    // Propriétés virtuelles pour l'affichage (nom du jeu, image, etc.)
    public $nom_jeu;
    public $src_img;
    public $prenom_user;
    public $nom_user;

    public function __construct(
        int $id_user,
        int $id_jeu,
        string $adresse_complete,
        float $position_lat,
        float $position_lng,
        string $mode_paiement,
        float $prix_livraison = 8.000,
        string $statut = 'commandee',
        ?int $id_livraison = null,
        ?string $date_commande = null
    ) {
        $this->id_livraison = $id_livraison;
        $this->id_user = $id_user;
        $this->id_jeu = $id_jeu;
        $this->adresse_complete = $adresse_complete;
        $this->position_lat = $position_lat;
        $this->position_lng = $position_lng;
        $this->mode_paiement = $mode_paiement;
        $this->prix_livraison = $prix_livraison;
        $this->statut = $statut;
        $this->date_commande = $date_commande ?? date('Y-m-d H:i:s');
    }

    // ====== GETTERS ======
    public function getIdLivraison(): ?int { return $this->id_livraison; }
    public function getIdUser(): int { return $this->id_user; }
    public function getIdJeu(): int { return $this->id_jeu; }
    public function getAdresseComplete(): string { return $this->adresse_complete; }
    public function getPositionLat(): float { return $this->position_lat; }
    public function getPositionLng(): float { return $this->position_lng; }
    public function getModePaiement(): string { return $this->mode_paiement; }
    public function getPrixLivraison(): float { return $this->prix_livraison; }
    public function getStatut(): string { return $this->statut; }
    public function getDateCommande(): string { return $this->date_commande; }

    // ====== SETTERS (seulement ceux utiles pour l'admin) ======
    public function setStatut(string $statut): void
    {
        $allowed = ['commandee', 'emballee', 'en_transit', 'livree'];
        if (in_array($statut, $allowed)) {
            $this->statut = $statut;
        }
    }
}