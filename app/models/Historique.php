<?php
// app/models/Historique.php
class Historique {
    private $pdo;
    private $table = 'historique';

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAllWithUser(): array {
        $sql = "SELECT h.*, u.nom as user_nom, u.prenom as user_prenom, u.email as user_email 
                FROM {$this->table} h 
                JOIN utilisateur u ON u.id_user=h.id_user 
                ORDER BY h.date_action DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getByUserId(int $uid): array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id_user=:uid ORDER BY date_action DESC");
        $stmt->execute([':uid'=>$uid]); 
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id_historique=:id");
        $stmt->execute([':id'=>$id]); 
        return $stmt->fetch();
    }

    public function create(array $d): int {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (id_user,type_action,description) VALUES (:uid,:action,:description)");
        $stmt->execute([
            ':uid'=>$d['user_id'],
            ':action'=>$d['action'],
            ':description'=>$d['note'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET type_action=:action,description=:description WHERE id_historique=:id");
        return $stmt->execute([
            ':action'=>$d['action'],
            ':description'=>$d['note'],
            ':id'=>$id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id_historique=:id"); 
        return $stmt->execute([':id'=>$id]);
    }

    public function getUsersList(): array {
        $stmt = $this->pdo->query("SELECT id_user,nom,prenom FROM utilisateur ORDER BY nom"); 
        return $stmt->fetchAll();
    }
}
