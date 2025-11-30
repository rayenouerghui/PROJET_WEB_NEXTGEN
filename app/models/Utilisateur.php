<?php
// app/models/Utilisateur.php
class Utilisateur {
    private $pdo;
    private $table = 'utilisateur';

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT id_user,nom,prenom,email,role,credit,photo_profil,statut,date_inscription FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function findById(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id_user=:id");
        $stmt->execute([':id'=>$id]); 
        return $stmt->fetch();
    }

    public function findByEmail(string $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email=:email LIMIT 1");
        $stmt->execute([':email'=>$email]); 
        return $stmt->fetch();
    }

    public function create(array $d): int {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (nom,prenom,email,mot_de_passe,role) VALUES (:nom,:prenom,:email,:password,:role)");
        $stmt->execute([
            ':nom'=>$d['nom'],
            ':prenom'=>$d['prenom'] ?? '',
            ':email'=>$d['email'],
            ':password'=>$d['password'],
            ':role'=>$d['role'] ?? 'user'
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET nom=:nom,prenom=:prenom,email=:email,role=:role WHERE id_user=:id");
        return $stmt->execute([
            ':nom'=>$d['nom'],
            ':prenom'=>$d['prenom'] ?? '',
            ':email'=>$d['email'],
            ':role'=>$d['role'] ?? 'user',
            ':id'=>$id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id_user=:id"); 
        return $stmt->execute([':id'=>$id]);
    }
}
