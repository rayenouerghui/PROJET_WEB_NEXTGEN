<?php
// app/models/Utilisateur.php
class Utilisateur {
    private $pdo;
    private $table = 'utilisateur';

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT id_user,nom,prenom,email,role,credit,photo_profile,statut,date_inscription FROM {$this->table}");
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

    public function search(string $query, ?string $role = null, ?string $statut = null, int $limit = 20, int $offset = 0): array {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($query !== '') {
            $sql .= " AND (nom LIKE :query OR prenom LIKE :query OR email LIKE :query)";
            $params[':query'] = "%$query%";
        }

        if ($role !== null && $role !== '') {
            $sql .= " AND role = :role";
            $params[':role'] = $role;
        }

        if ($statut !== null && $statut !== '') {
            $sql .= " AND statut = :statut";
            $params[':statut'] = $statut;
        }

        $sql .= " ORDER BY date_inscription DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function count(string $query = '', ?string $role = null, ?string $statut = null): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($query !== '') {
            $sql .= " AND (nom LIKE :query OR prenom LIKE :query OR email LIKE :query)";
            $params[':query'] = "%$query%";
        }

        if ($role !== null && $role !== '') {
            $sql .= " AND role = :role";
            $params[':role'] = $role;
        }

        if ($statut !== null && $statut !== '') {
            $sql .= " AND statut = :statut";
            $params[':statut'] = $statut;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetchColumn();
    }

    public function updateStatus(int $id, string $statut): bool {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET statut=:statut WHERE id_user=:id");
        return $stmt->execute([':statut'=>$statut, ':id'=>$id]);
    }

    public function updateCredit(int $id, float $credit): bool {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET credit=:credit WHERE id_user=:id");
        return $stmt->execute([':credit'=>$credit, ':id'=>$id]);
    }
}
