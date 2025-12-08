<?php

class Utilisateur {
    public $id_user;
    public $nom;
    public $prenom;
    public $email;
    public $mot_de_passe;
    public $role;
    public $credit;
    public $photo_profile;
    public $statut;
    public $date_inscription;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id_user = $data['id_user'] ?? null;
            $this->nom = $data['nom'] ?? '';
            $this->prenom = $data['prenom'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->mot_de_passe = $data['mot_de_passe'] ?? '';
            $this->role = $data['role'] ?? 'user';
            $this->credit = $data['credit'] ?? 0;
            $this->photo_profile = $data['photo_profile'] ?? null;
            $this->statut = $data['statut'] ?? 'actif';
            $this->date_inscription = $data['date_inscription'] ?? null;
        }
    }
}
