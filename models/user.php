<?php

class User
{
    private ?int $id = null;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $telephone;
    private string $mdp;        
    private string $role;       
    private ?string $photo_profil = null;   // ← AJOUTÉ
    private float $credits = 0.00;          // ← AJOUTÉ

    public function __construct(
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $telephone = '',
        string $mdp = '',
        string $role = 'user',   
        ?int $id = null,
        ?string $photo_profil = null,   // ← AJOUTÉ
        float $credits = 0.00           // ← AJOUTÉ
    ) {
        $this->id           = $id;
        $this->nom          = $nom;
        $this->prenom       = $prenom;
        $this->email        = $email;
        $this->telephone    = $telephone;
        $this->mdp          = $mdp;
        $this->role         = $role;
        $this->photo_profil = $photo_profil;
        $this->credits      = $credits;
    }

    // ====== GETTERS ======
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getTelephone(): string { return $this->telephone; }
    public function getMdp(): string { return $this->mdp; }
    public function getRole(): string { return $this->role; }
    public function getPhotoProfil(): ?string { return $this->photo_profil; }   // ← AJOUTÉ
    public function getCredits(): float { return $this->credits; }             // ← AJOUTÉ

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isUser(): bool { return $this->role === 'user'; }

    // ====== SETTERS ======
    public function setId(?int $id): void { $this->id = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setTelephone(string $telephone): void { $this->telephone = $telephone; }
    public function setMdp(string $mdp): void { $this->mdp = $mdp; }
    public function setRole(string $role): void {
        if (in_array($role, ['admin', 'user'])) {
            $this->role = $role;
        } else {
            throw new InvalidArgumentException("Le rôle doit être 'admin' ou 'user'");
        }
    }
    public function setPhotoProfil(?string $photo_profil): void { $this->photo_profil = $photo_profil; }   // ← AJOUTÉ
    public function setCredits(float $credits): void { $this->credits = $credits; }                       // ← AJOUTÉ
}