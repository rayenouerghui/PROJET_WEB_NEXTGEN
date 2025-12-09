<?php
// controller/userController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/user.php';

class userController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Config::getConnexion();
    }

    public function addUser(User $user): bool
    {
        try {
            $sql = "INSERT INTO users (nom, prenom, email, telephone, mdp, role, photo_profil, credits) 
                    VALUES (:nom, :prenom, :email, :telephone, :mdp, :role, 'default.jpg', 0.00)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom'       => $user->getNom(),
                ':prenom'    => $user->getPrenom(),
                ':email'     => $user->getEmail(),
                ':telephone' => $user->getTelephone(),
                ':mdp'       => $user->getMdp(),        
                ':role'      => $user->getRole()
            ]);

            $user->setId($this->pdo->lastInsertId());
            return true;
        } catch (PDOException $e) {
            error_log("Add user error: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser(User $user): bool
    {
        try {
            if (empty($user->getMdp())) {
                $sql = "UPDATE users 
                        SET nom = :nom, prenom = :prenom, email = :email, 
                            telephone = :telephone, role = :role,
                            photo_profil = :photo_profil
                        WHERE id = :id";
                $params = [
                    ':nom'          => $user->getNom(),
                    ':prenom'       => $user->getPrenom(),
                    ':email'        => $user->getEmail(),
                    ':telephone'    => $user->getTelephone(),
                    ':role'         => $user->getRole(),
                    ':photo_profil' => $user->getPhotoProfil() ?? 'default.jpg',
                    ':id'           => $user->getId()
                ];
            } else {
                $sql = "UPDATE users 
                        SET nom = :nom, prenom = :prenom, email = :email, 
                            telephone = :telephone, mdp = :mdp, role = :role,
                            photo_profil = :photo_profil
                        WHERE id = :id";
                $params = [
                    ':nom'          => $user->getNom(),
                    ':prenom'       => $user->getPrenom(),
                    ':email'        => $user->getEmail(),
                    ':telephone'    => $user->getTelephone(),
                    ':mdp'          => $user->getMdp(),
                    ':role'         => $user->getRole(),
                    ':photo_profil' => $user->getPhotoProfil() ?? 'default.jpg',
                    ':id'           => $user->getId()
                ];
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser(int $id): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();

        if (!$data) return null;

        $user = new User(
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['mdp'],
            $data['role'],
            (int)$data['id'],
            $data['photo_profil'] ?? 'default.jpg',
            (float)($data['credits'] ?? 0.00)
        );
        return $user;
    }

    public function getUserById(int $id): ?User
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if (!$data) return null;

        $user = new User(
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['mdp'],
            $data['role'],
            (int)$data['id'],
            $data['photo_profil'] ?? 'default.jpg',
            (float)($data['credits'] ?? 0.00)
        );
        return $user;
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $users = [];
        foreach ($results as $row) {
            $user = new User(
                $row['nom'],
                $row['prenom'],
                $row['email'],
                $row['telephone'],
                $row['mdp'],
                $row['role'],
                (int)$row['id'],
                $row['photo_profil'] ?? 'default.jpg',
                (float)($row['credits'] ?? 0.00)
            );
            $users[] = $user;
        }
        return $users;
    }

           // SAUVEGARDE LA PHOTO EN BDD (appelée après upload)
        public function updateUserPhoto(int $userId, string $photoName): bool
        {
            try {
                $sql = "UPDATE users SET photo_profil = :photo WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':photo' => $photoName,
                    ':id'    => $userId
                ]);
                return true;
            } catch (Exception $e) {
                error_log("Erreur update photo: " . $e->getMessage());
                return false;
            }
        }
        // Remplace la fonction par ça
        function getCurrentProfilePicture($userId) {
            $sql = "SELECT photo_profil FROM users WHERE id = :id";
            $db = Config::getConnexion();   // ← ON UTILISE DIRECTEMENT Config
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['photo_profil'] ?? 'default.jpg';
        }
}